<?php
/**
 * Easy Digital Downloads Theme Updater
 *
 * @package Chique
 */

// Includes the files needed for the theme updater
if ( ! class_exists( 'EDD_Theme_Updater_Admin' ) ) {
	include( dirname( __FILE__ ) . '/theme-updater-admin.php' );
}

$theme = wp_get_theme( get_template() );

// Loads the updater classes
$updater = new EDD_Theme_Updater_Admin(

	// Config settings
	$config = array(
		'remote_api_url' => 'https://catchthemes.com', // Site where EDD is hosted
		'item_name'      => $theme->get( 'Name' ), // Name of theme
		'theme_slug'     => $theme->get( 'TextDomain' ), // Theme slug
		'version'        => $theme->get( 'Version' ), // The current version of this theme
		'author'         => $theme->get( 'Author' ), // The author of this theme
		'download_id'    => '', // Optional, used for generating a license renewal link
		'renew_url'      => '', // Optional, allows for a custom license renewal link
		'beta'           => false, // Optional, set to true to opt into beta versions
		'base_url'       => admin_url( add_query_arg( array( 'page' => 'chique-about', 'tab' => 'license' ), 'themes.php' ) ),
	),

	// Strings
	$strings = array(
		'enter-key'                 => esc_html__( 'Enter your theme license key.', 'chique-pro' ),
		'license-key'               => esc_html__( 'License Key', 'chique-pro' ),
		'license-action'            => esc_html__( 'License Action', 'chique-pro' ),
		'deactivate-license'        => esc_html__( 'Deactivate License', 'chique-pro' ),
		'activate-license'          => esc_html__( 'Activate License', 'chique-pro' ),
		'status-unknown'            => esc_html__( 'License status is unknown.', 'chique-pro' ),
		'renew'                     => esc_html__( 'Renew?', 'chique-pro' ),
		'unlimited'                 => esc_html__( 'unlimited', 'chique-pro' ),
		'license-key-is-active'     => esc_html__( 'License key is active.', 'chique-pro' ),
		'expires%s'                 => esc_html__( 'Expires %s.', 'chique-pro' ),
		'expires-never'             => esc_html__( 'Lifetime License.', 'chique-pro' ),
		'%1$s/%2$-sites'            => esc_html__( 'You have %1$s / %2$s sites activated.', 'chique-pro' ),
		'license-key-expired-%s'    => esc_html__( 'License key expired %s.', 'chique-pro' ),
		'license-key-expired'       => esc_html__( 'License key has expired.', 'chique-pro' ),
		'license-keys-do-not-match' => esc_html__( 'License keys do not match.', 'chique-pro' ),
		'license-is-inactive'       => esc_html__( 'License is inactive.', 'chique-pro' ),
		'license-key-is-disabled'   => esc_html__( 'License key is disabled.', 'chique-pro' ),
		'site-is-inactive'          => esc_html__( 'Site is inactive.', 'chique-pro' ),
		'license-status-unknown'    => esc_html__( 'License status is unknown.', 'chique-pro' ),
		'update-notice'             => esc_html__( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update.", 'chique-pro' ),
		'update-available'          => esc_html__('<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4$s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.', 'chique-pro' ),
	)

);
