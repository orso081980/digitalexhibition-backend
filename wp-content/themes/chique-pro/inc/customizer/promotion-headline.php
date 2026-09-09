<?php
/**
 * Promotion Headline Options
 *
 * @package Chique
 */

/**
 * Add promotion headline options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_promotion_headline_options( $wp_customize ) {
	$wp_customize->add_section( 'chique_promotion_headline_options', array(
			'title' => esc_html__( 'Promotion Headline', 'chique-pro' ),
			'panel' => 'chique_theme_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_headline_visibility',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique-pro' ),
			'section'           => 'chique_promotion_headline_options',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_headline_type',
			'default'           => 'page',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_promotion_headline_active',
			'choices'           => chique_section_type_options(),
			'label'             => esc_html__( 'Type', 'chique-pro' ),
			'section'           => 'chique_promotion_headline_options',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_headline_page',
			'default'           => '0',
			'sanitize_callback' => 'chique_sanitize_post',
			'active_callback'   => 'chique_is_page_promotion_headline_active',
			'label'             => esc_html__( 'Page', 'chique-pro' ),
			'section'           => 'chique_promotion_headline_options',
			'type'              => 'dropdown-pages',
			'allow_addition'    => true,
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_headline_post',
			'default'           => 0,
			'sanitize_callback' => 'chique_sanitize_post',
			'active_callback'   => 'chique_is_post_promotion_headline_active',
			'label'             => esc_html__( 'Post', 'chique-pro' ),
			'section'           => 'chique_promotion_headline_options',
			'choices'           => chique_generate_post_array(),
			'type'              => 'select',
		)
	);

	// create an empty array.
	$cats = array();

	$cats['0'] = esc_html__( '-- Select --', 'chique-pro' );

	// we loop over the categories and set the names and
	// labels we need.
	foreach ( get_categories() as $categories => $category ) {
		$cats[ $category->term_id ] = $category->name;
	}

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_headline_category',
			'default'           => '0',
			'sanitize_callback' => 'absint',
			'active_callback'   => 'chique_is_category_promotion_headline_active',
			'label'             => esc_html__( 'Category', 'chique-pro' ),
			'type'              => 'select',
			'choices'           => $cats,
			'section'           => 'chique_promotion_headline_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_display_promotion_headline_title',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'default'           => 1,
			'active_callback'   => 'chique_is_post_page_category_promotion_headline_active',
			'label'             => esc_html__( 'Display Title', 'chique-pro' ),
			'section'           => 'chique_promotion_headline_options',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_headline_subtitle',
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_promotion_headline_active',
			'label'             => esc_html__( 'Subtitle', 'chique-pro' ),
			'section'           => 'chique_promotion_headline_options',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_headline_show',
			'default'           => 'full-content',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_post_page_category_promotion_headline_active',
			'choices'           => chique_content_show(),
			'label'             => esc_html__( 'Display Content', 'chique-pro' ),
			'section'           => 'chique_promotion_headline_options',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_headline_align',
			'default'           => 'content-aligned-right',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_promotion_headline_active',
			'choices'           => array(
				'content-aligned-right'  => esc_html__( 'Right', 'chique-pro' ),
				'content-aligned-left'   => esc_html__( 'Left', 'chique-pro' ),
				'content-aligned-center' => esc_html__( 'Center', 'chique-pro' ),
			),
			'label'             => esc_html__( 'Content Position', 'chique-pro' ),
			'section'           => 'chique_promotion_headline_options',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_headline_text_align',
			'default'           => 'text-aligned-center',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_promotion_headline_active',
			'choices'           => array(
				'text-aligned-right'  => esc_html__( 'Right', 'chique-pro' ),
				'text-aligned-center' => esc_html__( 'Center', 'chique-pro' ),
				'text-aligned-left'   => esc_html__( 'Left', 'chique-pro' ),
			),
			'label'             => esc_html__( 'Text Alignment', 'chique-pro' ),
			'section'           => 'chique_promotion_headline_options',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_headline_content_frame',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'default'           => 0,
			'active_callback'   => 'chique_is_promotion_headline_active',
			'label'             => esc_html__( 'Content Frame', 'chique-pro' ),
			'section'           => 'chique_promotion_headline_options',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_headline_title',
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_custom_promotion_headline_active',
			'label'             => esc_html__( 'Title', 'chique-pro' ),
			'section'           => 'chique_promotion_headline_options',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_headline',
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_custom_promotion_headline_active',
			'label'             => esc_html__( 'Content', 'chique-pro' ),
			'section'           => 'chique_promotion_headline_options',
			'type'              => 'textarea',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_headline_image',
			'sanitize_callback' => 'chique_sanitize_image',
			'custom_control'    => 'WP_Customize_Image_Control',
			'active_callback'   => 'chique_is_custom_promotion_headline_active',
			'label'             => esc_html__( 'Image', 'chique-pro' ),
			'section'           => 'chique_promotion_headline_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_headline_more_text',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_custom_promotion_headline_active',
			'label'             => esc_html__( 'Button Text', 'chique-pro' ),
			'section'           => 'chique_promotion_headline_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_headline_more_link',
			'sanitize_callback' => 'esc_url_raw',
			'active_callback'   => 'chique_is_custom_promotion_headline_active',
			'label'             => esc_html__( 'Button Link', 'chique-pro' ),
			'section'           => 'chique_promotion_headline_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_headline_more_target',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_custom_promotion_headline_active',
			'label'             => esc_html__( 'Open Button Link in New Window/Tab', 'chique-pro' ),
			'section'           => 'chique_promotion_headline_options',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);
}
add_action( 'customize_register', 'chique_promotion_headline_options' );

/** Active Callback Functions **/
if ( ! function_exists( 'chique_is_promotion_headline_active' ) ) :
	/**
	* Return true if promotion headline is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_promotion_headline_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_promotion_headline_visibility' )->value();

		//return true only if previewed page on customizer matches the type of content option selected
		return ( chique_check_section( $enable ) );
	}
endif;

if ( ! function_exists( 'chique_is_post_promotion_headline_active' ) ) :
	/**
	* Return true if post content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_post_promotion_headline_active( $control ) {
		$type = $control->manager->get_setting( 'chique_promotion_headline_type' )->value();

		return ( chique_is_promotion_headline_active( $control ) && 'post' === $type );
	}
endif;

if ( ! function_exists( 'chique_is_page_promotion_headline_active' ) ) :
	/**
	* Return true if hero page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_page_promotion_headline_active( $control ) {
		$type = $control->manager->get_setting( 'chique_promotion_headline_type' )->value();

		return ( chique_is_promotion_headline_active( $control ) && 'page' === $type );
	}
endif;

if ( ! function_exists( 'chique_is_category_promotion_headline_active' ) ) :
	/**
	* Return true if hero category content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_category_promotion_headline_active( $control ) {
		$type = $control->manager->get_setting( 'chique_promotion_headline_type' )->value();

		return ( chique_is_promotion_headline_active( $control ) && 'category' === $type );
	}
endif;

if ( ! function_exists( 'chique_is_post_page_category_promotion_headline_active' ) ) :
	/**
	* Return true if hero post/page/category content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_post_page_category_promotion_headline_active( $control ) {
		$type = $control->manager->get_setting( 'chique_promotion_headline_type' )->value();

		return ( chique_is_promotion_headline_active( $control ) && ( 'page' === $type || 'post' === $type || 'category' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_custom_promotion_headline_active' ) ) :
	/**
	* Return true if hero custom content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_custom_promotion_headline_active( $control ) {
		$type = $control->manager->get_setting( 'chique_promotion_headline_type' )->value();

		return ( chique_is_promotion_headline_active( $control ) && ( 'custom' === $type ) );
	}
endif;
