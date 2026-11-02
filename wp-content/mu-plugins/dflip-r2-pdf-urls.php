<?php
/**
 * Plugin Name: DearFlip / R2 PDF URLs
 * Description: Serves flipbook PDFs from the R2 bucket when the file is no longer in this site's uploads folder.
 *
 * DearFlip stores a flipbook's PDF as a plain URL string (`_dflip_data.pdf_source`,
 * e.g. https://<host>/wp-content/uploads/2024/05/x.pdf), not as an attachment, so the
 * Advanced Media Offloader never rewrites it. Once a site's uploads folder is gone
 * (staging, localhost) the viewer fails with "Error: Cannot access file!".
 *
 * The R2 object key is the path after /wp-content/ (uploads/2024/05/x.pdf), so the
 * URL is rewritten to ADVMO_CLOUDFLARE_R2_DOMAIN/uploads/…, but only when the file is
 * missing locally. A site that still has its own copy (live, for now) is untouched.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'tue_dflip_r2_pdf_url' ) ) {
	/**
	 * @param string $url A PDF URL pointing at one of this project's WordPress hosts.
	 * @return string The R2 URL, or $url unchanged when it doesn't apply.
	 */
	function tue_dflip_r2_pdf_url( $url ) {
		if ( ! is_string( $url ) || ! defined( 'ADVMO_CLOUDFLARE_R2_DOMAIN' ) || '' === ADVMO_CLOUDFLARE_R2_DOMAIN ) {
			return $url;
		}

		$parts = wp_parse_url( $url );
		if ( empty( $parts['host'] ) || empty( $parts['path'] ) || ! preg_match( '#/wp-content/uploads/(.+)$#', $parts['path'], $m ) ) {
			return $url;
		}

		$hosts = apply_filters(
			'tue_dflip_r2_hosts',
			array(
				wp_parse_url( home_url(), PHP_URL_HOST ),
				'digitalexhibition.arch.tue.nl',
				'staging.digitalexhibition.arch.tue.nl',
			)
		);
		if ( ! in_array( $parts['host'], $hosts, true ) ) {
			return $url;
		}

		$relative = rawurldecode( $m[1] );
		if ( false !== strpos( $relative, '..' ) ) {
			return $url;
		}
		$uploads = wp_get_upload_dir();
		if ( file_exists( trailingslashit( $uploads['basedir'] ) . $relative ) ) {
			return $url;
		}

		$query = isset( $parts['query'] ) ? '?' . $parts['query'] : '';
		return rtrim( ADVMO_CLOUDFLARE_R2_DOMAIN, '/' ) . '/uploads/' . $m[1] . $query;
	}
}

// Every [dflip id="…"] flipbook. DearFlip only runs its own `dflip_get_post_data_*`
// filter for non-dflip post types, so the rendered output is rewritten instead: the
// `source` inside the inline `window.df_option_N = {…}` JSON (slashes are escaped as \/).
// The stored `_dflip_data` is left alone, so wp-admin never saves an R2 URL back.
add_filter(
	'do_shortcode_tag',
	static function ( $output, $tag ) {
		if ( 'dflip' !== $tag || false === stripos( $output, '.pdf' ) ) {
			return $output;
		}
		return preg_replace_callback(
			'#"source":"(https?:[^"]+?\.pdf)"#i',
			static function ( $match ) {
				$url = tue_dflip_r2_pdf_url( str_replace( '\/', '/', $match[1] ) );
				return '"source":"' . str_replace( '/', '\/', $url ) . '"';
			},
			$output
		);
	},
	10,
	2
);

// Plain <a href="….pdf"> links typed into post content.
add_filter(
	'the_content',
	static function ( $content ) {
		if ( false === stripos( $content, '.pdf' ) ) {
			return $content;
		}
		return preg_replace_callback(
			'#https?://[^"\'\s<>]+/wp-content/uploads/[^"\'\s<>]+?\.pdf\b#i',
			static function ( $match ) {
				return tue_dflip_r2_pdf_url( $match[0] );
			},
			$content
		);
	},
	20
);
