<?php
/**
 * Plugin Name: Uploads fallback to R2
 * Description: Redirects a request for an uploads file that is not on this server to the R2 bucket.
 *
 * Some plugins (DearFlip PDF sources and thumbnails, hard-coded Elementor URLs, ...) store the full
 * local URL of a file instead of an attachment ID, so the media offloader cannot rewrite it. On
 * environments without local media (localhost, staging) that URL is a 404. Apache already hands
 * missing files to WordPress, so we redirect those to the bucket here.
 *
 * Active only where ADVMO_CLOUDFLARE_R2_DOMAIN is defined. Live omits it and keeps serving its own files,
 * and a file that exists locally never reaches this code.
 */

add_action(
	'template_redirect',
	static function () {
		if ( ! is_404() || ! defined( 'ADVMO_CLOUDFLARE_R2_DOMAIN' ) || ! ADVMO_CLOUDFLARE_R2_DOMAIN ) {
			return;
		}

		$request = wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$uploads = wp_parse_url( wp_get_upload_dir()['baseurl'], PHP_URL_PATH );
		if ( ! $request || ! $uploads || 0 !== strpos( $request, $uploads . '/' ) ) {
			return;
		}

		$relative = ltrim( substr( rawurldecode( $request ), strlen( $uploads ) ), '/' );

		// Elementor's generated CSS is local-only; a stale copy from the bucket would be wrong.
		if ( '' === $relative || false !== strpos( $relative, '..' ) || 0 === strpos( $relative, 'elementor/' ) ) {
			return;
		}

		$settings = get_option( 'advmo_settings', array() );
		$prefix   = ! empty( $settings['path_prefix_active'] ) && ! empty( $settings['path_prefix'] ) ? trim( $settings['path_prefix'], '/' ) . '/' : '';

		wp_redirect( rtrim( ADVMO_CLOUDFLARE_R2_DOMAIN, '/' ) . '/' . $prefix . implode( '/', array_map( 'rawurlencode', explode( '/', $relative ) ) ), 302 ); // phpcs:ignore WordPress.Security.SafeRedirect
		exit;
	},
	0
);
