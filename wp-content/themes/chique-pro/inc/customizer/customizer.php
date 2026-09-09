<?php
/**
 * Theme Customizer
 *
 * @package Chique
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport            = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport     = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport    = 'postMessage';
	$wp_customize->get_setting( 'header_image' )->transport        = 'refresh';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector' => '.site-title a',
			'container_inclusive' => false,
			'render_callback' => 'chique_customize_partial_blogname',
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector' => '.site-description',
			'container_inclusive' => false,
			'render_callback' => 'chique_customize_partial_blogdescription',
		) );
	}

	// Important Links.
	$wp_customize->add_section( 'chique_important_links', array(
		'priority'      => 999,
		'title'         => esc_html__( 'Important Links', 'chique-pro' ),
	) );

	// Has dummy Sanitizaition function as it contains no value to be sanitized.
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_important_links',
			'sanitize_callback' => 'sanitize_text_field',
			'custom_control'    => 'Chique_Important_Links',
			'label'             => esc_html__( 'Important Links', 'chique-pro' ),
			'section'           => 'chique_important_links',
			'type'              => 'chique_important_links',
		)
	);
	// Important Links End.
}
add_action( 'customize_register', 'chique_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function chique_customize_preview_js() {
	$min  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$path = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'assets/js/source/' : 'assets/js/';
	
	wp_enqueue_script( 'chique-customize-preview', trailingslashit( esc_url ( get_template_directory_uri() ) ) . $path . 'customizer' . $min . '.js', array( 'customize-preview' ), '20170816', true );
}
add_action( 'customize_preview_init', 'chique_customize_preview_js' );

/**
 * Include Custom Controls
 */
require get_parent_theme_file_path( 'inc/customizer/custom-controls.php' );

/**
 * Include Color Scheme
 */
require get_parent_theme_file_path( 'inc/customizer/color-scheme.php' );

/**
 * Include Header Media Options
 */
require get_parent_theme_file_path( 'inc/customizer/header-media.php' );

/**
 * Include Sticky Playlist
 */
require get_parent_theme_file_path( 'inc/customizer/sticky-playlist.php' );

/**
 * Include Playlist
 */
require get_parent_theme_file_path( 'inc/customizer/playlist.php' );

/**
 * Include Events
 */
require get_parent_theme_file_path( 'inc/customizer/venue.php' );

/**
 * Include Events
 */
require get_parent_theme_file_path( 'inc/customizer/events.php' );

/**
 * Include Featured Video
 */
require get_parent_theme_file_path( 'inc/customizer/featured-video.php' );

/**
 * Include Album
 */
require get_parent_theme_file_path( 'inc/customizer/album.php' );

/**
 * Include Timeline
 */
require get_parent_theme_file_path( 'inc/customizer/timeline.php' );

/**
 * Include Countdown
 */
require get_parent_theme_file_path( 'inc/customizer/countdown.php' );

/**
 * Include Theme Options
 */
require get_parent_theme_file_path( 'inc/customizer/theme-options.php' );

/**
 * Include Hero Content
 */
require get_parent_theme_file_path( 'inc/customizer/hero-content.php' );

/**
 * Include Contact Info
 */
require get_parent_theme_file_path( 'inc/customizer/contact-info.php' );

/**
 * Include Pricing
 */
require get_parent_theme_file_path( 'inc/customizer/pricing.php' );

/**
 * Include Promotion Headline
 */
require get_parent_theme_file_path( 'inc/customizer/promotion-headline.php' );

/**
 * Include Promotion Sale
 */
require get_parent_theme_file_path( 'inc/customizer/promotion-sale.php' );

/**
 * Include Reservation
 */
require get_parent_theme_file_path( 'inc/customizer/reservation.php' );

/**
 * Include Skills
 */
require get_parent_theme_file_path( 'inc/customizer/skills.php' );

/**
 * Include Logo Slider
 */
require get_parent_theme_file_path( 'inc/customizer/logo-slider.php' );

/**
 * Include Testimonial
 */
require get_parent_theme_file_path( 'inc/customizer/testimonial.php' );

/**
 * Include Featured Slider
 */
require get_parent_theme_file_path( 'inc/customizer/featured-slider.php' );

/**
 * Include Featured Content
 */
require get_parent_theme_file_path( 'inc/customizer/featured-content.php' );

/**
 * Include Service
 */
require get_parent_theme_file_path( 'inc/customizer/service.php' );

/**
 * Include Gallery
 */
require get_parent_theme_file_path( 'inc/customizer/gallery.php' );

/**
 * Include Team
 */
require get_parent_theme_file_path( 'inc/customizer/team.php' );

/**
 * Include Stats
 */
require get_parent_theme_file_path( 'inc/customizer/stats.php' );

/**
 * Include Portfolio
 */
require get_parent_theme_file_path( 'inc/customizer/portfolio.php' );

/**
 * Include Why Choose Us
 */
require get_parent_theme_file_path( 'inc/customizer/why-choose-us.php' );

/**
 * Include Customizer Helper Functions
 */
require get_parent_theme_file_path( 'inc/customizer/helpers.php' );

/**
 * Include Sanitization functions
 */
require get_parent_theme_file_path( 'inc/customizer/sanitize-functions.php' );

/**
 * Include Reset Button
 */
require get_parent_theme_file_path( 'inc/customizer/reset.php' );
