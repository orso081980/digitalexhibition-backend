<?php
/**
 * Plugin Name: Local Dev – Serve images from the live site
 * Description: While this WordPress runs on https://website-test (localhost/MAMP),
 *              every /wp-content/uploads/ image URL is rewritten to the production
 *              server so media keeps loading without copying the uploads folder.
 *              Delete this file to disable.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LOCAL_DEV_LIVE', 'https://digitalexhibition.arch.tue.nl' );
define( 'LOCAL_DEV_LIVE_UPLOADS', LOCAL_DEV_LIVE . '/wp-content/uploads' );

/**
 * 1. Rewrite the uploads base URL for everything WordPress builds itself
 *    (wp_get_attachment_url, the_post_thumbnail, srcset, custom logo, REST...).
 *    Filesystem paths stay local so new uploads still save on this machine.
 */
add_filter( 'upload_dir', function ( $uploads ) {
	$live = LOCAL_DEV_LIVE_UPLOADS;

	if ( ! empty( $uploads['baseurl'] ) ) {
		$uploads['url']     = str_replace( $uploads['baseurl'], $live, $uploads['url'] );
		$uploads['baseurl'] = $live;
	} else {
		$uploads['baseurl'] = $live;
		$uploads['url']     = $live . ( isset( $uploads['subdir'] ) ? $uploads['subdir'] : '' );
	}

	return $uploads;
}, 99 );

/**
 * 2. Catch-all: rewrite any remaining local uploads URL in the final HTML.
 *    Covers Elementor widgets, cached element markup, theme output, srcset,
 *    inline CSS url(), root-relative ("/wp-content/uploads/...") references.
 */
function local_dev_rewrite_uploads_html( $html ) {
	if ( ! is_string( $html )
		|| ( strpos( $html, '/wp-content/uploads/' ) === false
			&& strpos( $html, '\/wp-content\/uploads\/' ) === false ) ) {
		return $html;
	}

	$live         = LOCAL_DEV_LIVE_UPLOADS;                  // https://digitalexhibition.arch.tue.nl/wp-content/uploads
	$live_escaped = str_replace( '/', '\/', $live );         // https:\/\/…\/wp-content\/uploads

	// 1. Any absolute local URL (this dev host) -> live host. No doubling risk.
	$html = str_replace(
		array(
			'https://website-test/wp-content/uploads',
			'http://website-test/wp-content/uploads',
			'https:\/\/website-test\/wp-content\/uploads',
			'http:\/\/website-test\/wp-content\/uploads',
		),
		array( $live, $live, $live_escaped, $live_escaped ),
		$html
	);

	// 2. Root-relative "/wp-content/uploads/..." -> live, ONLY where it clearly
	//    starts a URL (preceded by a quote, paren, comma, equals or whitespace),
	//    so a path that already has a host in front of it is left untouched.
	$html = preg_replace(
		'#([\"\'\s,(=])/wp-content/uploads/#',
		'${1}' . $live . '/',
		$html
	);

	// 3. Same rule for JSON / JS-escaped slashes: "\/wp-content\/uploads\/..."
	$html = preg_replace_callback(
		'#([\"\'\s,(=])\\\\/wp-content\\\\/uploads\\\\/#',
		static function ( $m ) use ( $live_escaped ) {
			return $m[1] . $live_escaped . '\/';
		},
		$html
	);

	return $html;
}

add_action( 'template_redirect', function () {
	if ( is_admin() ) {
		return;
	}
	ob_start( 'local_dev_rewrite_uploads_html' );
}, 0 );
