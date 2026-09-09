<?php
/**
 * Promotion Sale Options
 *
 * @package Chique
 */

/**
 * Add promotion sale options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_promotion_sale_options( $wp_customize ) {
	$wp_customize->add_section( 'chique_promotion_sale_options', array(
			'title' => esc_html__( 'Promotion Sale', 'chique-pro' ),
			'panel' => 'chique_theme_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_sale_visibility',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique-pro' ),
			'section'           => 'chique_promotion_sale_options',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_sale_type',
			'default'           => 'page',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_promotion_sale_active',
			'choices'           => chique_section_type_options(),
			'label'             => esc_html__( 'Type', 'chique-pro' ),
			'section'           => 'chique_promotion_sale_options',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_sale',
			'default'           => '0',
			'sanitize_callback' => 'chique_sanitize_post',
			'active_callback'   => 'chique_is_promotion_sale_page_content_active',
			'label'             => esc_html__( 'Page', 'chique-pro' ),
			'section'           => 'chique_promotion_sale_options',
			'type'              => 'dropdown-pages',
			'allow_addition'    => true,
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_sale_post',
			'default'           => 0,
			'sanitize_callback' => 'chique_sanitize_post',
			'active_callback'   => 'chique_is_promotion_sale_post_content_active',
			'label'             => esc_html__( 'Post', 'chique-pro' ),
			'section'           => 'chique_promotion_sale_options',
			'choices'           => chique_generate_post_array(),
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_sale_title_image',
			'sanitize_callback' => 'chique_sanitize_image',
			'active_callback'   => 'chique_is_promotion_sale_active',
			'custom_control'    => 'WP_Customize_Image_Control',
			'label'             => esc_html__( 'Title Image', 'chique-pro' ),
			'section'           => 'chique_promotion_sale_options',
			'mime_type'         => 'image',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_sale_subtitle',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_promotion_sale_active',
			'label'             => esc_html__( 'Subtitle', 'chique-pro' ),
			'section'           => 'chique_promotion_sale_options',
			'type'              => 'text',
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
			'name'              => 'chique_promotion_sale_category',
			'default'           => '0',
			'sanitize_callback' => 'absint',
			'active_callback'   => 'chique_is_promotion_sale_category_content_active',
			'label'             => esc_html__( 'Category', 'chique-pro' ),
			'type'              => 'select',
			'choices'           => $cats,
			'section'           => 'chique_promotion_sale_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_display_promotion_sale_title',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'default'           => 1,
			'active_callback'   => 'chique_is_promotion_sale_post_page_category_content_active',
			'label'             => esc_html__( 'Display Title', 'chique-pro' ),
			'section'           => 'chique_promotion_sale_options',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_sale_show',
			'default'           => 'full-content',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_promotion_sale_post_page_category_content_active',
			'choices'           => chique_content_show(),
			'label'             => esc_html__( 'Display Content', 'chique-pro' ),
			'section'           => 'chique_promotion_sale_options',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_sale_position',
			'default'           => 'content-aligned-right',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_promotion_sale_active',
			'choices'           => array(
				'content-aligned-right'  => esc_html__( 'Right', 'chique-pro' ),
				'content-aligned-left'   => esc_html__( 'Left', 'chique-pro' ),
			),
			'label'             => esc_html__( 'Content Position', 'chique-pro' ),
			'section'           => 'chique_promotion_sale_options',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_sale_text_align',
			'default'           => 'text-aligned-left',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_promotion_sale_active',
			'choices'           => array(
				'text-aligned-right'  => esc_html__( 'Right', 'chique-pro' ),
				'text-aligned-center' => esc_html__( 'Center', 'chique-pro' ),
				'text-aligned-left'   => esc_html__( 'Left', 'chique-pro' ),
			),
			'label'             => esc_html__( 'Text Alignment', 'chique-pro' ),
			'section'           => 'chique_promotion_sale_options',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_sale_title',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_promotion_sale_custom_content_active',
			'label'             => esc_html__( 'Title', 'chique-pro' ),
			'section'           => 'chique_promotion_sale_options',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_sale_content',
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_promotion_sale_custom_content_active',
			'label'             => esc_html__( 'Content', 'chique-pro' ),
			'section'           => 'chique_promotion_sale_options',
			'type'              => 'textarea',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_sale_image',
			'sanitize_callback' => 'chique_sanitize_image',
			'custom_control'    => 'WP_Customize_Image_Control',
			'active_callback'   => 'chique_is_promotion_sale_custom_content_active',
			'label'             => esc_html__( 'Image', 'chique-pro' ),
			'section'           => 'chique_promotion_sale_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_sale_link',
			'sanitize_callback' => 'esc_url_raw',
			'active_callback'   => 'chique_is_promotion_sale_custom_content_active',
			'label'             => esc_html__( 'Image Link', 'chique-pro' ),
			'section'           => 'chique_promotion_sale_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_sale_target',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_promotion_sale_custom_content_active',
			'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
			'section'           => 'chique_promotion_sale_options',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_sale_more_text',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_promotion_sale_custom_content_active',
			'label'             => esc_html__( 'Button Text', 'chique-pro' ),
			'section'           => 'chique_promotion_sale_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_sale_more_link',
			'sanitize_callback' => 'esc_url_raw',
			'active_callback'   => 'chique_is_promotion_sale_custom_content_active',
			'label'             => esc_html__( 'Button Link', 'chique-pro' ),
			'section'           => 'chique_promotion_sale_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_promotion_sale_more_target',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_promotion_sale_custom_content_active',
			'label'             => esc_html__( 'Open Button Link in New Window/Tab', 'chique-pro' ),
			'section'           => 'chique_promotion_sale_options',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);
}
add_action( 'customize_register', 'chique_promotion_sale_options' );

/** Active Callback Functions **/
if ( ! function_exists( 'chique_is_promotion_sale_active' ) ) :
	/**
	* Return true if promotion sale is active
	*
	* @since Catch Vogue Pro 1.0
	*/
	function chique_is_promotion_sale_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_promotion_sale_visibility' )->value();

		//return true only if previewed page on customizer matches the type of content option selected
		return ( chique_check_section( $enable ) );
	}
endif;

if ( ! function_exists( 'chique_is_promotion_sale_post_content_active' ) ) :
	/**
	* Return true if post content is active
	*
	* @since Catch Vogue Pro 1.0
	*/
	function chique_is_promotion_sale_post_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_promotion_sale_type' )->value();

		return ( chique_is_promotion_sale_active( $control ) && 'post' == $type );
	}
endif;

if ( ! function_exists( 'chique_is_promotion_sale_page_content_active' ) ) :
	/**
	* Return true if promotion sale page content is active
	*
	* @since Catch Vogue Pro 1.0
	*/
	function chique_is_promotion_sale_page_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_promotion_sale_type' )->value();

		return ( chique_is_promotion_sale_active( $control ) && 'page' == $type );
	}
endif;

if ( ! function_exists( 'chique_is_promotion_sale_category_content_active' ) ) :
	/**
	* Return true if promotion sale category content is active
	*
	* @since Catch Vogue Pro 1.0
	*/
	function chique_is_promotion_sale_category_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_promotion_sale_type' )->value();

		return ( chique_is_promotion_sale_active( $control ) && 'category' == $type );
	}
endif;

if ( ! function_exists( 'chique_is_promotion_sale_post_page_category_content_active' ) ) :
	/**
	* Return true if promotion sale post/page/category content is active
	*
	* @since Catch Vogue Pro 1.0
	*/
	function chique_is_promotion_sale_post_page_category_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_promotion_sale_type' )->value();

		return ( chique_is_promotion_sale_active( $control ) && ( 'page' == $type || 'post' == $type || 'category' == $type )
			);
	}
endif;

if ( ! function_exists( 'chique_is_promotion_sale_custom_content_active' ) ) :
	/**
	* Return true if promotion sale custom content is active
	*
	* @since Catch Vogue Pro 1.0
	*/
	function chique_is_promotion_sale_custom_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_promotion_sale_type' )->value();

		return ( chique_is_promotion_sale_active( $control ) && ( 'custom' == $type )
			);
	}
endif;
