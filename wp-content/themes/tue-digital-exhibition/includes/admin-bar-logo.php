<?php
/**
 * White TU/e logo in the admin bar.
 *
 * WordPress shows the Customizer site icon (the black TU/e logo, also used as favicon)
 * next to the site name in the admin bar. The bar is black, so the logo disappears.
 * Swap it for a white copy that ships with the theme; the favicon stays untouched.
 *
 * @package TUe_Digital_Exhibition
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action(
	'admin_bar_menu',
	function ( $wp_admin_bar ) {
		$node = $wp_admin_bar->get_node( 'site-name' );

		if ( ! $node || false === strpos( $node->title, 'class="site-icon"' ) ) {
			return;
		}

		// The logo is 90x36, so keep its proportions instead of WordPress' 20x20 box.
		$logo = sprintf(
			'<img class="site-icon" src="%s" alt="" width="50" height="20" style="width:auto;height:20px;" />',
			esc_url( get_template_directory_uri() . '/assets/images/tue-white.svg' )
		);

		$node->title = preg_replace( '/<img class="site-icon"[^>]*>/', $logo, $node->title, 1 );
		$wp_admin_bar->add_node( $node );
	},
	100 // After WordPress adds the site-name node (priority 30).
);
