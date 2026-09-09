<?php
/**
 * Playlist Options
 *
 * @package Chique
 */

/**
 * Add sticky_playlist options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_sticky_playlist( $wp_customize ) {
	$wp_customize->add_section( 'chique_sticky_playlist', array(
			'title' => esc_html__( 'Sticky Playlist', 'chique-pro' ),
			'panel' => 'chique_theme_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_sticky_playlist_visibility',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique-pro' ),
			'section'           => 'chique_sticky_playlist',
			'type'              => 'select',
		)
	);

	$types = chique_section_type_options();

	unset( $types['custom'] );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_sticky_playlist_type',
			'default'           => 'page',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_sticky_playlist_active',
			'choices'           => $types,
			'label'             => esc_html__( 'Type', 'chique-pro' ),
			'section'           => 'chique_sticky_playlist',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_sticky_playlist',
			'default'           => '0',
			'sanitize_callback' => 'chique_sanitize_post',
			'active_callback'   => 'chique_is_page_sticky_playlist_active',
			'label'             => esc_html__( 'Page', 'chique-pro' ),
			'section'           => 'chique_sticky_playlist',
			'type'              => 'dropdown-pages',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_sticky_playlist_post',
			'default'           => 0,
			'sanitize_callback' => 'chique_sanitize_post',
			'active_callback'   => 'chique_is_post_sticky_playlist_active',
			'label'             => esc_html__( 'Post', 'chique-pro' ),
			'section'           => 'chique_sticky_playlist',
			'choices'           => chique_generate_post_array(),
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
	        'name'              => 'chique_sticky_playlist_position',
	        'sanitize_callback' => 'wp_kses_post',
	        'default'			=> 'top',
	        'label'             => esc_html__( 'Position', 'chique-pro' ),
	        'active_callback'   => 'chique_is_sticky_playlist_active',
	        'section'           => 'chique_sticky_playlist',
	        'type'              => 'radio',
	        'choices'           => array(
	        	'top' => esc_html__( 'Top', 'chique-pro' ),
	        	'bottom' => esc_html__( 'Bottom', 'chique-pro' ),
	        ),
	    )
	);

	// Create an empty array.
	$cats = array();

	$cats['0'] = esc_html__( '-- Select --', 'chique-pro' );

	// We loop over the categories and set the names and labels we need.
	foreach ( get_categories() as $categories => $category ) {
		$cats[ $category->term_id ] = $category->name;
	}

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_sticky_playlist_category',
			'default'           => '0',
			'sanitize_callback' => 'absint',
			'active_callback'   => 'chique_is_category_sticky_playlist_active',
			'label'             => esc_html__( 'Category', 'chique-pro' ),
			'type'              => 'select',
			'choices'           => $cats,
			'section'           => 'chique_sticky_playlist',
		)
	);
}
add_action( 'customize_register', 'chique_sticky_playlist' );

/** Active Callback Functions **/
if ( ! function_exists( 'chique_is_sticky_playlist_active' ) ) :
	/**
	* Return true if sticky_playlist is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_sticky_playlist_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_sticky_playlist_visibility' )->value();

		return chique_check_section( $enable );
	}
endif;

if ( ! function_exists( 'chique_is_post_sticky_playlist_active' ) ) :
	/**
	* Return true if post content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_post_sticky_playlist_active( $control ) {
		$type = $control->manager->get_setting( 'chique_sticky_playlist_type' )->value();

		return ( chique_is_sticky_playlist_active( $control ) && 'post' == $type );
	}
endif;

if ( ! function_exists( 'chique_is_page_sticky_playlist_active' ) ) :
	/**
	* Return true if page sticky_playlist is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_page_sticky_playlist_active( $control ) {
		$type = $control->manager->get_setting( 'chique_sticky_playlist_type' )->value();

		return ( chique_is_sticky_playlist_active( $control ) && 'page' == $type );
	}
endif;

if ( ! function_exists( 'chique_is_category_sticky_playlist_active' ) ) :
	/**
	* Return true if category sticky_playlist is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_category_sticky_playlist_active( $control ) {
		$type = $control->manager->get_setting( 'chique_sticky_playlist_type' )->value();

		return ( chique_is_sticky_playlist_active( $control ) && 'category' == $type );
	}
endif;
