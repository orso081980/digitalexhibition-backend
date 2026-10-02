<?php
/**
 * Why Choose Us options
 *
 * @package Chique
 */

/**
 * Add why choose us content options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_why_choose_us_options( $wp_customize ) {

    $wp_customize->add_section( 'chique_why_choose_us', array(
			'title' => esc_html__( 'Why Choose Us', 'chique-pro' ),
			'panel' => 'chique_theme_options',
		)
	);

	// Add color scheme setting and control.
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_why_choose_us_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique-pro' ),
			'section'           => 'chique_why_choose_us',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_why_choose_us_title',
			'default'           => esc_html__( 'Why Choose Us', 'chique-pro' ),
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_cpt_why_choose_us_inactive',
			'label'             => esc_html__( 'Headline', 'chique-pro' ),
			'section'           => 'chique_why_choose_us',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_why_choose_us_sub_title',
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_cpt_why_choose_us_inactive',
			'label'             => esc_html__( 'Sub headline', 'chique-pro' ),
			'section'           => 'chique_why_choose_us',
			'type'              => 'textarea',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_why_choose_us_style',
			'default'           => 'modern',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_why_choose_us_active',
            'choices'           => array(
            	'classic'		=> esc_html__( 'Classic', 'chique-pro'),
            	'modern'		=> esc_html__( 'Modern', 'chique-pro')),
			'label'             => esc_html__( 'Style', 'chique-pro' ),
			'section'           => 'chique_why_choose_us',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_why_choose_us_layout',
			'default'           => 'layout-three',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_why_choose_us_classic_style_active',
			'choices'           => chique_sections_layout_options(),
			'label'             => esc_html__( 'Select Layout', 'chique-pro' ),
			'section'           => 'chique_why_choose_us',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_why_choose_us_content_align',
			'default'           => 'content-aligned-center',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_why_choose_us_classic_style_active',
			'choices'           => array(
				'content-aligned-center' => esc_html__( 'Center', 'chique-pro' ),
				'content-aligned-right'  => esc_html__( 'Right', 'chique-pro' ),
				'content-aligned-left'   => esc_html__( 'Left', 'chique-pro' ),
			),
			'label'             => esc_html__( 'Content Position', 'chique-pro' ),
			'section'           => 'chique_why_choose_us',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_why_choose_us_type',
			'default'           => 'category',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_why_choose_us_active',
            'choices'           => chique_section_type_options(),
			'label'             => esc_html__( 'Type', 'chique-pro' ),
			'section'           => 'chique_why_choose_us',
			'type'              => 'select',
		)
	);

    chique_register_option( $wp_customize, array(
			'name'              => 'chique_why_choose_us_number',
			'default'           => 3,
			'sanitize_callback' => 'chique_sanitize_number_range',
			'active_callback'   => 'chique_is_why_choose_us_active',
			'description'       => esc_html__( 'Save and refresh the page if No. of Items is changed', 'chique-pro' ),
			'input_attrs'       => array(
				'style' => 'width: 100px;',
				'min'   => 0,
			),
			'label'             => esc_html__( 'No of Items', 'chique-pro' ),
			'section'           => 'chique_why_choose_us',
			'type'              => 'number',
			'transport'         => 'postMessage',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_why_choose_us_show',
			'default'           => 'excerpt',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_why_choose_us_post_page_category_cpt_content_active',
			'choices'           => chique_content_show(),
			'label'             => esc_html__( 'Display Content', 'chique-pro' ),
			'section'           => 'chique_why_choose_us',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_why_choose_us_select_category',
			'sanitize_callback' => 'chique_sanitize_category_list',
			'custom_control'    => 'Chique_Multi_Cat',
			'active_callback'   => 'chique_is_why_choose_us_category_content_active',
			'label'             => esc_html__( 'Select Categories', 'chique-pro' ),
			'name'              => 'chique_why_choose_us_select_category',
			'section'           => 'chique_why_choose_us',
			'type'              => 'dropdown-categories',
		)
	);

	$number = get_theme_mod( 'chique_why_choose_us_number', 3 );

	//loop for why choose us post content
	for ( $i = 1; $i <= $number ; $i++ ) {
		chique_register_option( $wp_customize, array(
				'name'              => 'chique_why_choose_us_post_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'default'           => 0,
				'active_callback'   => 'chique_is_why_choose_us_post_content_active',
				'input_attrs'       => array(
					'style'             => 'width: 40px;'
				),
				'label'             => esc_html__( 'Post', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_why_choose_us',
				'choices'           => chique_generate_post_array(),
				'type'              => 'select'
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_why_choose_us_page_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'active_callback'   => 'chique_is_why_choose_us_page_content_active',
				'label'             => esc_html__( 'Why Choose Us Page', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_why_choose_us',
				'type'              => 'dropdown-pages',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_why_choose_us_note_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'custom_control'    => 'Chique_Note_Control',
				'active_callback'   => 'chique_is_why_choose_us_custom_content_active',
				'label'             => esc_html__( 'Why Choose Us #', 'chique-pro' ) .  $i,
				'section'           => 'chique_why_choose_us',
				'type'              => 'description',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_why_choose_us_image_' . $i,
				'sanitize_callback' => 'chique_sanitize_image',
				'custom_control'    => 'WP_Customize_Image_Control',
				'active_callback'   => 'chique_is_why_choose_us_custom_content_active',
				'label'             => esc_html__( 'Image', 'chique-pro' ),
				'section'           => 'chique_why_choose_us',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_why_choose_us_link_' . $i,
				'sanitize_callback' => 'esc_url_raw',
				'active_callback'   => 'chique_is_why_choose_us_custom_content_active',
				'label'             => esc_html__( 'Link', 'chique-pro' ),
				'section'           => 'chique_why_choose_us',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_why_choose_us_target_' . $i,
				'sanitize_callback' => 'chique_sanitize_checkbox',
				'active_callback'   => 'chique_is_why_choose_us_custom_content_active',
				'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
				'section'           => 'chique_why_choose_us',
				'custom_control'    => 'Chique_Toggle_Control',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_why_choose_us_title_' . $i,
				'sanitize_callback' => 'wp_kses_post',
				'active_callback'   => 'chique_is_why_choose_us_custom_content_active',
				'label'             => esc_html__( 'Title', 'chique-pro' ),
				'section'           => 'chique_why_choose_us',
				'type'              => 'text',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_why_choose_us_content_' . $i,
				'sanitize_callback' => 'wp_kses_post',
				'active_callback'   => 'chique_is_why_choose_us_custom_content_active',
				'label'             => esc_html__( 'Content', 'chique-pro' ),
				'section'           => 'chique_why_choose_us',
				'type'              => 'textarea',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_why_choose_us_more_button_text_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'active_callback'   => 'chique_is_why_choose_us_custom_content_active',
				'label'             => esc_html__( 'More Button Text', 'chique-pro' ),
				'section'           => 'chique_why_choose_us',
				'type'              => 'text',
			)
		);
	} // End for().

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_why_choose_us_text',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_why_choose_us_active',
			'label'             => esc_html__( 'Button Text', 'chique-pro' ),
			'section'           => 'chique_why_choose_us',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_why_choose_us_link',
			'sanitize_callback' => 'esc_url_raw',
			'active_callback'   => 'chique_is_why_choose_us_active',
			'label'             => esc_html__( 'Button Link', 'chique-pro' ),
			'section'           => 'chique_why_choose_us',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_why_choose_us_target',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_why_choose_us_active',
			'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
			'section'           => 'chique_why_choose_us',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);
}
add_action( 'customize_register', 'chique_why_choose_us_options', 10 );

/** Active Callback Functions **/
if ( ! function_exists( 'chique_is_why_choose_us_active' ) ) :
	/**
	* Return true if why choose us content is active
	*
	* @since Solid Construction Pro 1.0
	*/
	function chique_is_why_choose_us_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_why_choose_us_option' )->value();

		//return true only if previewed page on customizer matches the type of content option selected
		return ( chique_check_section( $enable ) );
	}
endif;

if ( ! function_exists( 'chique_is_cpt_why_choose_us_inactive' ) ) :
	/**
	* Return true if CPT why choose us content is inactive
	*
	* @since Solid Construction Pro 1.0
	*/

	function chique_is_cpt_why_choose_us_inactive( $control ) {
		$type = $control->manager->get_setting( 'chique_why_choose_us_type' )->value();

		return ( chique_is_why_choose_us_active( $control ) && 'ect-service' !== $type );
	}
endif;

if ( ! function_exists( 'chique_is_why_choose_us_post_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Solid Construction Pro 1.0
	*/
	function chique_is_why_choose_us_post_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_why_choose_us_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_why_choose_us_active( $control ) && ( 'post' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_why_choose_us_page_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Solid Construction Pro 1.0
	*/
	function chique_is_why_choose_us_page_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_why_choose_us_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_why_choose_us_active( $control ) && ( 'page' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_why_choose_us_cpt_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Solid Construction Pro 1.0
	*/
	function chique_is_why_choose_us_cpt_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_why_choose_us_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_why_choose_us_active( $control ) && ( 'ect-service' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_why_choose_us_category_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Solid Construction Pro 1.0
	*/
	function chique_is_why_choose_us_category_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_why_choose_us_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_why_choose_us_active( $control ) && ( 'category' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_why_choose_us_post_page_category_cpt_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Solid Construction Pro 1.0
	*/
	function chique_is_why_choose_us_post_page_category_cpt_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_why_choose_us_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_why_choose_us_active( $control ) && ( 'category' === $type || 'page' === $type || 'post' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_why_choose_us_custom_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Solid Construction Pro 1.0
	*/
	function chique_is_why_choose_us_custom_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_why_choose_us_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_why_choose_us_active( $control ) && 'custom' === $type );
	}
endif;

if ( ! function_exists( 'chique_is_why_choose_us_classic_style_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Solid Construction Pro 1.0
	*/
	function chique_is_why_choose_us_classic_style_active( $control ) {
		$style = $control->manager->get_setting( 'chique_why_choose_us_style' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_why_choose_us_active( $control ) && 'classic' === $style );
	}
endif;
