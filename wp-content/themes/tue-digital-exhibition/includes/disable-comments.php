<?php
/**
 * Disable comments and trackbacks site-wide.
 *
 * The site has no use for comments: content is edited in WordPress and shown in the
 * Nuxt app. This turns them off everywhere — post types, front end, admin, REST API,
 * XML-RPC and feeds — so nothing can be posted and no comment UI is shown.
 *
 * @package TUe_Digital_Exhibition
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * No post type supports comments or trackbacks.
 */
add_action(
	'init',
	function () {
		foreach ( get_post_types() as $post_type ) {
			if ( post_type_supports( $post_type, 'comments' ) ) {
				remove_post_type_support( $post_type, 'comments' );
			}
			if ( post_type_supports( $post_type, 'trackbacks' ) ) {
				remove_post_type_support( $post_type, 'trackbacks' );
			}
		}
	},
	100
);

// Always closed, and hide any comments that already exist.
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 20 );
add_filter( 'get_comments_number', '__return_zero', 20 );

// Refuse direct POSTs to wp-comments-post.php.
add_action(
	'pre_comment_on_post',
	function () {
		wp_die( esc_html__( 'Comments are disabled on this site.', 'tue-digital-exhibition' ), '', array( 'response' => 403 ) );
	}
);

/**
 * Admin: remove every comment entry point.
 */
add_action(
	'admin_menu',
	function () {
		remove_menu_page( 'edit-comments.php' );
		remove_submenu_page( 'options-general.php', 'options-discussion.php' );
	},
	999
);

add_action(
	'admin_init',
	function () {
		global $pagenow;

		if ( in_array( $pagenow, array( 'edit-comments.php', 'options-discussion.php' ), true ) ) {
			wp_safe_redirect( admin_url() );
			exit;
		}

		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
		remove_meta_box( 'commentsdiv', null, 'normal' );
		remove_meta_box( 'commentstatusdiv', null, 'normal' );
		remove_meta_box( 'trackbacksdiv', null, 'normal' );
	}
);

// Admin bar: the comments bubble, both on the front end and in the admin.
add_action(
	'admin_bar_menu',
	function ( $wp_admin_bar ) {
		$wp_admin_bar->remove_node( 'comments' );
	},
	999
);

/**
 * Front end: no comment widget, no comment feeds, no pingback header.
 */
add_action(
	'widgets_init',
	function () {
		unregister_widget( 'WP_Widget_Recent_Comments' );
	}
);

add_filter( 'feed_links_show_comments_feed', '__return_false' );

add_filter(
	'wp_headers',
	function ( $headers ) {
		unset( $headers['X-Pingback'] );
		return $headers;
	}
);

/**
 * REST API and XML-RPC: the endpoints would otherwise stay reachable.
 */
add_filter(
	'rest_endpoints',
	function ( $endpoints ) {
		foreach ( array_keys( $endpoints ) as $route ) {
			if ( 0 === strpos( $route, '/wp/v2/comments' ) ) {
				unset( $endpoints[ $route ] );
			}
		}
		return $endpoints;
	}
);

add_filter(
	'xmlrpc_methods',
	function ( $methods ) {
		unset( $methods['pingback.ping'], $methods['pingback.extensions.getPingbacks'] );
		return $methods;
	}
);
