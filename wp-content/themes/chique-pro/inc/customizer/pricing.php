<?php
/**
 * Pricing options
 *
 * @package Chique
 */

/**
 * Add pricing content options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_pricing_options( $wp_customize ) {
    $wp_customize->add_section( 'chique_pricing', array(
			'title' => esc_html__( 'Pricing', 'chique-pro' ),
			'panel' => 'chique_theme_options',
		)
	);

	// Add color scheme setting and control.
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_pricing_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique-pro' ),
			'section'           => 'chique_pricing',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_pricing_layout',
			'default'           => 'layout-three',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_pricing_active',
			'choices'           => chique_sections_layout_options(),
			'label'             => esc_html__( 'Select Layout', 'chique-pro' ),
			'section'           => 'chique_pricing',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_pricing_archive_title',
			'default'           => esc_html__( 'Pricing Table', 'chique-pro' ),
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_pricing_active',
			'label'             => esc_html__( 'Headline', 'chique-pro' ),
			'section'           => 'chique_pricing',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_pricing_sub_title',
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_pricing_active',
			'label'             => esc_html__( 'Sub headline', 'chique-pro' ),
			'section'           => 'chique_pricing',
			'type'              => 'textarea',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_pricing_type',
			'default'           => 'category',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_pricing_active',
            'choices'           => chique_section_type_options(),
			'label'             => esc_html__( 'Type', 'chique-pro' ),
			'section'           => 'chique_pricing',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_pricing_number',
			'default'           => 3,
			'sanitize_callback' => 'chique_sanitize_number_range',
			'active_callback'   => 'chique_is_pricing_active',
			'description'       => esc_html__( 'Save and refresh the page if No. of Pricing is changed (Max no of Pricing is 20)', 'chique-pro' ),
			'input_attrs'       => array(
				'style' => 'width: 100px;',
				'min'   => 0,
			),
			'label'             => esc_html__( 'No of items', 'chique-pro' ),
			'section'           => 'chique_pricing',
			'type'              => 'number',
			'transport'         => 'postMessage',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_pricing_show',
			'default'           => 'full-content',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_pricing_post_page_category_content_active',
			'choices'           => chique_content_show(),
			'label'             => esc_html__( 'Display Content', 'chique-pro' ),
			'section'           => 'chique_pricing',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_pricing_meta_show',
			'default'           => 'hide-meta',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_pricing_post_page_category_content_active',
			'choices'           => chique_meta_show(),
			'label'             => esc_html__( 'Display Meta', 'chique-pro' ),
			'section'           => 'chique_pricing',
			'type'              => 'select',
		)
	);


	chique_register_option( $wp_customize, array(
			'name'              => 'chique_pricing_select_category',
			'sanitize_callback' => 'chique_sanitize_category_list',
			'custom_control'    => 'Chique_Multi_Cat',
			'active_callback'   => 'chique_is_pricing_category_content_active',
			'label'             => esc_html__( 'Select Categories', 'chique-pro' ),
			'name'              => 'chique_pricing_select_category',
			'section'           => 'chique_pricing',
			'type'              => 'dropdown-categories',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_pricing_currency',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_pricing_active',
			'label'             => esc_html__( 'Currency', 'chique-pro' ),
			'section'           => 'chique_pricing',
			'type'              => 'text',
		)
	);

	$number = get_theme_mod( 'chique_pricing_number', 3 );

	//loop for pricing post content
	for ( $i = 1; $i <= $number ; $i++ ) {
		chique_register_option( $wp_customize, array(
				'name'              => 'chique_pricing_highlight_' . $i,
				'sanitize_callback' => 'chique_sanitize_checkbox',
				'active_callback'   => 'chique_is_pricing_active',
				'label'             => esc_html__( 'Highlight item', 'chique-pro' ) . ' #' . $i,
				'section'           => 'chique_pricing',
				'custom_control'    => 'Chique_Toggle_Control',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_pricing_amount_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'active_callback'   => 'chique_is_pricing_active',
				'label'             => esc_html__( 'Amount', 'chique-pro' ) . ' #' . $i,
				'section'           => 'chique_pricing',
				'type'              => 'text',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_pricing_amount_remarks_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'active_callback'   => 'chique_is_pricing_active',
				'label'             => esc_html__( 'Amount Remarks (per month)', 'chique-pro' ) . ' #' . $i,
				'section'           => 'chique_pricing',
				'type'              => 'text',
			)
		);
		chique_register_option( $wp_customize, array(
				'name'              => 'chique_pricing_post_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'default'           => 0,
				'active_callback'   => 'chique_is_pricing_post_content_active',
				'input_attrs'       => array(
					'style'             => 'width: 40px;'
				),
				'label'             => esc_html__( 'Post', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_pricing',
				'choices'           => chique_generate_post_array(),
				'type'              => 'select'
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_pricing_page_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'active_callback'   => 'chique_is_pricing_page_content_active',
				'label'             => esc_html__( 'Pricing Page', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_pricing',
				'type'              => 'dropdown-pages',
				'allow_addition'    => true,
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_pricing_note_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'custom_control'    => 'Chique_Note_Control',
				'active_callback'   => 'chique_is_pricing_image_content_active',
				'label'             => esc_html__( 'Pricing #', 'chique-pro' ) .  $i,
				'section'           => 'chique_pricing',
				'type'              => 'description',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_pricing_image_' . $i,
				'sanitize_callback' => 'chique_sanitize_image',
				'custom_control'    => 'WP_Customize_Image_Control',
				'active_callback'   => 'chique_is_pricing_image_content_active',
				'label'             => esc_html__( 'Image', 'chique-pro' ),
				'section'           => 'chique_pricing',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_pricing_link_' . $i,
				'sanitize_callback' => 'esc_url_raw',
				'active_callback'   => 'chique_is_pricing_image_content_active',
				'label'             => esc_html__( 'Link', 'chique-pro' ),
				'section'           => 'chique_pricing',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_pricing_target_' . $i,
				'sanitize_callback' => 'chique_sanitize_checkbox',
				'active_callback'   => 'chique_is_pricing_image_content_active',
				'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
				'section'           => 'chique_pricing',
				'custom_control'    => 'Chique_Toggle_Control',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_pricing_archive_title_' . $i,
				'sanitize_callback' => 'wp_kses_post',
				'active_callback'   => 'chique_is_pricing_image_content_active',
				'label'             => esc_html__( 'Title', 'chique-pro' ),
				'section'           => 'chique_pricing',
				'type'              => 'text',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_pricing_content_' . $i,
				'sanitize_callback' => 'wp_kses_post',
				'active_callback'   => 'chique_is_pricing_image_content_active',
				'label'             => esc_html__( 'Content', 'chique-pro' ),
				'section'           => 'chique_pricing',
				'type'              => 'textarea',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_pricing_more_button_text_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'active_callback'   => 'chique_is_pricing_image_content_active',
				'label'             => esc_html__( 'More Button Text', 'chique-pro' ),
				'section'           => 'chique_pricing',
				'type'              => 'text',
			)
		);
	} // End for().
}
add_action( 'customize_register', 'chique_pricing_options', 10 );

/** Active Callback Functions **/
if ( ! function_exists( 'chique_is_pricing_active' ) ) :
	/**
	* Return true if pricing content is active
	*
	* @since Catch Vogue Pro 1.0
	*/
	function chique_is_pricing_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_pricing_option' )->value();

		//return true only if previewed page on customizer matches the type of content option selected
		return ( chique_check_section( $enable ) );
	}
endif;

if ( ! function_exists( 'chique_is_pricing_post_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Catch Vogue Pro 1.0
	*/
	function chique_is_pricing_post_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_pricing_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_pricing_active( $control ) && ( 'post' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_pricing_page_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Catch Vogue Pro 1.0
	*/
	function chique_is_pricing_page_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_pricing_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_pricing_active( $control ) && ( 'page' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_pricing_category_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Catch Vogue Pro 1.0
	*/
	function chique_is_pricing_category_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_pricing_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_pricing_active( $control ) && ( 'category' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_pricing_post_page_category_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Catch Vogue Pro 1.0
	*/
	function chique_is_pricing_post_page_category_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_pricing_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_pricing_active( $control ) && ( 'category' === $type || 'page' === $type || 'post' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_pricing_image_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Catch Vogue Pro 1.0
	*/
	function chique_is_pricing_image_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_pricing_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_pricing_active( $control ) && ( 'custom' === $type ) );
	}
endif;
