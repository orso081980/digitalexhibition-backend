<?php
/**
 * Playlist Options
 *
 * @package Chique
 */

/**
 * Add playlist options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_playlist( $wp_customize ) {
	$wp_customize->add_section( 'chique_playlist', array(
			'title' => esc_html__( 'Playlist', 'chique-pro' ),
			'panel' => 'chique_theme_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_playlist_visibility',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique-pro' ),
			'section'           => 'chique_playlist',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_playlist_section_title',
			'default'           => esc_html__( 'New Releases', 'chique-pro' ),
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_playlist_active',
			'label'             => esc_html__( 'Section Title', 'chique-pro' ),
			'section'           => 'chique_playlist',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_playlist_section_sub_title',
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_playlist_active',
			'label'             => esc_html__( 'Playlist Sub Title', 'chique-pro' ),
			'section'           => 'chique_playlist',
			'type'              => 'textarea',
		)
	);

	$types = chique_section_type_options();

	unset( $types['custom'] );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_playlist_type',
			'default'           => 'page',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_playlist_active',
			'choices'           => $types,
			'label'             => esc_html__( 'Type', 'chique-pro' ),
			'section'           => 'chique_playlist',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_playlist',
			'default'           => '0',
			'sanitize_callback' => 'chique_sanitize_post',
			'active_callback'   => 'chique_is_page_playlist_active',
			'label'             => esc_html__( 'Page', 'chique-pro' ),
			'section'           => 'chique_playlist',
			'type'              => 'dropdown-pages',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_playlist_post',
			'default'           => 0,
			'sanitize_callback' => 'chique_sanitize_post',
			'active_callback'   => 'chique_is_post_playlist_active',
			'label'             => esc_html__( 'Post', 'chique-pro' ),
			'section'           => 'chique_playlist',
			'choices'           => chique_generate_post_array(),
			'type'              => 'select',
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
			'name'              => 'chique_playlist_category',
			'default'           => '0',
			'sanitize_callback' => 'absint',
			'active_callback'   => 'chique_is_category_playlist_active',
			'label'             => esc_html__( 'Category', 'chique-pro' ),
			'type'              => 'select',
			'choices'           => $cats,
			'section'           => 'chique_playlist',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_disable_playlist_title',
			'default'           => '1',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_post_page_category_playlist_active',
			'label'             => esc_html__( 'Display title', 'chique-pro' ),
			'section'           => 'chique_playlist',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_playlist_image_alignment',
			'default'           => 'content-align-right',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_playlist_active',
			'choices'           => array(
				'content-align-right' => esc_html__( 'Right', 'chique-pro' ),
				'content-align-left'  => esc_html__( 'Left', 'chique-pro' ),
			),
			'label'             => esc_html__( 'Content Alignment', 'chique-pro' ),
			'section'           => 'chique_playlist',
			'type'              => 'radio',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_playlist_button_text',
			'sanitize_callback' => 'sanitize_text_field',
			'default'			=> 'View Album',
			'active_callback'   => 'chique_is_playlist_active',
			'label'             => esc_html__( 'Button Text', 'chique-pro' ),
			'section'           => 'chique_playlist',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_playlist_button_link',
			'sanitize_callback' => 'esc_url_raw',
			'active_callback'   => 'chique_is_playlist_active',
			'label'             => esc_html__( 'Button Link', 'chique-pro' ),
			'section'           => 'chique_playlist',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_playlist_button_link_target',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_playlist_active',
			'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
			'section'           => 'chique_playlist',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);
}
add_action( 'customize_register', 'chique_playlist' );

/** Active Callback Functions **/
if ( ! function_exists( 'chique_is_playlist_active' ) ) :
	/**
	* Return true if playlist is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_playlist_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_playlist_visibility' )->value();

		return chique_check_section( $enable );
	}
endif;

if ( ! function_exists( 'chique_is_post_playlist_active' ) ) :
	/**
	* Return true if post content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_post_playlist_active( $control ) {
		$type = $control->manager->get_setting( 'chique_playlist_type' )->value();

		return ( chique_is_playlist_active( $control ) && 'post' == $type );
	}
endif;

if ( ! function_exists( 'chique_is_page_playlist_active' ) ) :
	/**
	* Return true if page playlist is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_page_playlist_active( $control ) {
		$type = $control->manager->get_setting( 'chique_playlist_type' )->value();

		return ( chique_is_playlist_active( $control ) && 'page' == $type );
	}
endif;

if ( ! function_exists( 'chique_is_category_playlist_active' ) ) :
	/**
	* Return true if category playlist is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_category_playlist_active( $control ) {
		$type = $control->manager->get_setting( 'chique_playlist_type' )->value();

		return ( chique_is_playlist_active( $control ) && 'category' == $type );
	}
endif;

if ( ! function_exists( 'chique_is_post_page_category_playlist_active' ) ) :
	/**
	* Return true if post/page/category playlist is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_post_page_category_playlist_active( $control ) {
		$type = $control->manager->get_setting( 'chique_playlist_type' )->value();

		return ( chique_is_playlist_active( $control ) && ( 'page' == $type || 'post' == $type || 'category' == $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_playlist_custom_active' ) ) :
	/**
	* Return true if custom playlist is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_playlist_custom_active( $control ) {
		$type = $control->manager->get_setting( 'chique_playlist_type' )->value();

		return ( chique_is_playlist_active( $control ) && 'custom' == $type );
	}
endif;
