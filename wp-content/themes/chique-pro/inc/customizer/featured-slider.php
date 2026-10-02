<?php
/**
 * Featured Slider Options
 *
 * @package Chique
 */

/**
 * Add hero content options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_slider_options( $wp_customize ) {
	$wp_customize->add_section( 'chique_featured_slider', array(
			'panel' => 'chique_theme_options',
			'title' => esc_html__( 'Featured Slider', 'chique-pro' ),
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_slider_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique-pro' ),
			'section'           => 'chique_featured_slider',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_slider_layout',
			'default'           => '1',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_slider_active',
			'choices'           => array(
				'1' => esc_html__( '1 column', 'chique-pro' ),
				'2' => esc_html__( '2 columns', 'chique-pro' ),
				'3' => esc_html__( '3 columns', 'chique-pro' ),
				'4' => esc_html__( '4 columns', 'chique-pro' ),
			),
			'label'             => esc_html__( 'Select Layout', 'chique-pro' ),
			'section'           => 'chique_featured_slider',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_slider_content_align',
			'default'           => 'content-aligned-center',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_slider_active',
			'choices'           => array(
				'content-aligned-center' => esc_html__( 'Center', 'chique-pro' ),
				'content-aligned-right'  => esc_html__( 'Right', 'chique-pro' ),
				'content-aligned-left'   => esc_html__( 'Left', 'chique-pro' ),
			),
			'label'             => esc_html__( 'Content Position', 'chique-pro' ),
			'section'           => 'chique_featured_slider',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_slider_text_align',
			'default'           => 'text-aligned-center',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_slider_active',
			'choices'           => array(
				'text-aligned-right'  => esc_html__( 'Right', 'chique-pro' ),
				'text-aligned-center' => esc_html__( 'Center', 'chique-pro' ),
				'text-aligned-left'   => esc_html__( 'Left', 'chique-pro' ),
			),
			'label'             => esc_html__( 'Text Alignment', 'chique-pro' ),
			'section'           => 'chique_featured_slider',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_slider_transition_in',
			'default'           => 'default',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_slider_layout_1',
			'choices'           => chique_transition_effects(),
			'label'             => esc_html__( 'Transition In', 'chique-pro' ),
			'section'           => 'chique_featured_slider',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_slider_transition_out',
			'default'           => 'default',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_slider_layout_1',
			'choices'           => chique_transition_effects(),
			'label'             => esc_html__( 'Transition Out', 'chique-pro' ),
			'section'           => 'chique_featured_slider',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_slider_nav',
			'default'           => 1,
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_slider_active',
			'label'             => esc_html__( 'Display nav arrows', 'chique-pro' ),
			'section'           => 'chique_featured_slider',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_slider_dots',
			'default'           => 1,
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_slider_active',
			'label'             => esc_html__( 'Display nav dots', 'chique-pro' ),
			'section'           => 'chique_featured_slider',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_slider_autoplay',
			'default'           => 1,
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_slider_active',
			'label'             => esc_html__( 'Autoplay', 'chique-pro' ),
			'section'           => 'chique_featured_slider',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_slider_loop',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_slider_active',
			'label'             => esc_html__( 'Loop (Last to first)', 'chique-pro' ),
			'section'           => 'chique_featured_slider',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_slider_transition_timeout',
			'default'           => 4,
			'sanitize_callback' => 'chique_sanitize_number_range',
			'active_callback'   => 'chique_is_slider_active',
			'input_attrs'       => array(
				'style'       => 'width: 100px;',
				'min'         => 0,
			),
			'label'             => esc_html__( 'Transition timeout', 'chique-pro' ),
			'section'           => 'chique_featured_slider',
			'type'              => 'number',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_slider_scroll_down',
			'default'			=> 1,
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_slider_active',
			'label'             => esc_html__( 'Scroll Link', 'chique-pro' ),
			'section'           => 'chique_featured_slider',
			'custom_control'	=> 'Chique_Toggle_Control',
		)
	);

	$type = chique_section_type_options();

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_slider_type',
			'default'           => 'category',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_slider_active',
			'choices'           => $type,
			'label'             => esc_html__( 'Select Slider Type', 'chique-pro' ),
			'section'           => 'chique_featured_slider',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_slider_number',
			'default'           => '4',
			'sanitize_callback' => 'chique_sanitize_number_range',

			'active_callback'   => 'chique_is_slider_active',
			'description'       => esc_html__( 'Save and refresh the page if No. of Slides is changed (Max no of slides is 20)', 'chique-pro' ),
			'input_attrs'       => array(
				'style' => 'width: 100px;',
				'min'   => 0,
				'max'   => 20,
				'step'  => 1,
			),
			'label'             => esc_html__( 'No of Slides', 'chique-pro' ),
			'section'           => 'chique_featured_slider',
			'type'              => 'number',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_slider_content_show',
			'default'           => 'hide-content',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_image_slider_inactive',
			'choices'           => chique_content_show(),
			'label'             => esc_html__( 'Display Content', 'chique-pro' ),
			'section'           => 'chique_featured_slider',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_exclude_slider_post',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_post_slider_active',
			'label'             => esc_html__( 'Exclude Slider post from Homepage posts', 'chique-pro' ),
			'section'           => 'chique_featured_slider',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_slider_select_category',
			'sanitize_callback' => 'chique_sanitize_category_list',
			'custom_control'    => 'Chique_Multi_Cat',
			'active_callback'   => 'chique_is_category_slider_active',
			'label'             => esc_html__( 'Select Categories', 'chique-pro' ),
			'name'              => 'chique_slider_select_category',
			'section'           => 'chique_featured_slider',
			'type'              => 'dropdown-categories',
		)
	);

	$slider_number = get_theme_mod( 'chique_slider_number', 4 );

	for ( $i = 1; $i <= $slider_number ; $i++ ) {

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_slider_logo_' .$i,
				'sanitize_callback' => 'chique_sanitize_image',
				'active_callback'   => 'chique_is_category_slider_inactive',
				'custom_control'    => 'WP_Customize_Image_Control',
				'label'             => esc_html__( 'Slider Logo', 'chique-pro' ) . ' # ' . $i,
				'section'           => 'chique_featured_slider',
			)
		);

		// Post Sliders
		chique_register_option( $wp_customize, array(
				'name'              => 'chique_slider_post_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'active_callback'   => 'chique_is_post_slider_active',
				'input_attrs'       => array(
					'style' => 'width: 80px;',
				),
				'label'             => esc_html__( 'Post', 'chique-pro' ) . ' # ' . $i,
				'section'           => 'chique_featured_slider',
				'choices'           => chique_generate_post_array(),
				'type'              => 'select',
			)
		);

		// Page Sliders
		chique_register_option( $wp_customize, array(
				'name'              => 'chique_slider_page_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'active_callback'   => 'chique_is_page_slider_active',
				'label'             => esc_html__( 'Page', 'chique-pro' ) . ' # ' . $i,
				'section'           => 'chique_featured_slider',
				'type'              => 'dropdown-pages',
			)
		);

		// Image Sliders
		chique_register_option( $wp_customize, array(
				'name'              => 'chique_slider_note_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'custom_control'    => 'Chique_Note_Control',
				'active_callback'   => 'chique_is_image_slider_active',
				'label'             => esc_html__( 'Slide #', 'chique-pro' ) . $i,
				'section'           => 'chique_featured_slider',
				'type'              => 'description',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_slider_image_' . $i,
				'sanitize_callback' => 'chique_sanitize_image',
				'custom_control'    => 'WP_Customize_Image_Control',
				'active_callback'   => 'chique_is_image_slider_active',
				'label'             => esc_html__( 'Image', 'chique-pro' ),
				'section'           => 'chique_featured_slider',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_slider_link_' . $i,
				'sanitize_callback' => 'esc_url_raw',
				'active_callback'   => 'chique_is_image_slider_active',
				'label'             => esc_html__( 'Link', 'chique-pro' ),
				'section'           => 'chique_featured_slider',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_slider_target_' . $i,
				'sanitize_callback' => 'chique_sanitize_checkbox',
				'active_callback'   => 'chique_is_image_slider_active',
				'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
				'section'           => 'chique_featured_slider',
				'custom_control'    => 'Chique_Toggle_Control',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_slider_title_' . $i,
				'sanitize_callback' => 'wp_kses_post',
				'active_callback'   => 'chique_is_image_slider_active',
				'label'             => esc_html__( 'Title', 'chique-pro' ),
				'section'           => 'chique_featured_slider',
				'type'              => 'text',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_slider_sub_title_' . $i,
				'sanitize_callback' => 'wp_kses_post',
				'active_callback'   => 'chique_is_image_slider_active',
				'label'             => esc_html__( 'Sub Title', 'chique-pro' ),
				'section'           => 'chique_featured_slider',
				'type'              => 'text',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_slider_content_' . $i,
				'sanitize_callback' => 'wp_kses_post',
				'active_callback'   => 'chique_is_image_slider_active',
				'label'             => esc_html__( 'Content', 'chique-pro' ),
				'section'           => 'chique_featured_slider',
				'type'              => 'textarea',
			)
		);
		chique_register_option( $wp_customize, array(
				'name'              => 'chique_featured_slider_button_text_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'active_callback'   => 'chique_is_image_slider_active',
				'label'             => esc_html__( 'Button Text', 'chique-pro' ),
				'section'           => 'chique_featured_slider',
				'type'              => 'text',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_featured_slider_button_link_' . $i,
				'sanitize_callback' => 'esc_url_raw',
				'active_callback'   => 'chique_is_image_slider_active',
				'label'             => esc_html__( 'Button Link', 'chique-pro' ),
				'section'           => 'chique_featured_slider',
			)
		);
	} // End for().
}
add_action( 'customize_register', 'chique_slider_options' );

/** Active Callback Functions */

if ( ! function_exists( 'chique_is_slider_active' ) ) :
	/**
	* Return true if slider is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_slider_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_slider_option' )->value();

		//return true only if previwed page on customizer matches the type option selected
		return chique_check_section( $enable );
	}
endif;

if ( ! function_exists( 'chique_is_slider_layout_1' ) ) :
	/**
	* Return true layout 1 is selected
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_slider_layout_1( $control ) {
		$layout = $control->manager->get_setting( 'chique_slider_layout' )->value();

		//return true only if previwed page on customizer matches the type of slider option selected and is or is not selected type
		return ( chique_is_slider_active( $control ) && 1 === $layout );
	}
endif;

if ( ! function_exists( 'chique_is_post_slider_active' ) ) :
	/**
	* Return true if page slider is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_post_slider_active( $control ) {
		$type = $control->manager->get_setting( 'chique_slider_type' )->value();

		//return true only if previwed page on customizer matches the type of slider option selected and is or is not selected type
		return ( chique_is_slider_active( $control ) && 'post' === $type );
	}
endif;

if ( ! function_exists( 'chique_is_page_slider_active' ) ) :
	/**
	* Return true if page slider is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_page_slider_active( $control ) {
		$type = $control->manager->get_setting( 'chique_slider_type' )->value();

		//return true only if previwed page on customizer matches the type of slider option selected and is or is not selected type
		return ( chique_is_slider_active( $control ) && 'page' === $type );
	}
endif;

if ( ! function_exists( 'chique_is_category_slider_active' ) ) :
	/**
	* Return true if page slider is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_category_slider_active( $control ) {
		$type = $control->manager->get_setting( 'chique_slider_type' )->value();

		//return true only if previwed page on customizer matches the type of slider option selected and is or is not selected type
		return ( chique_is_slider_active( $control ) && 'category' === $type );
	}
endif;

if ( ! function_exists( 'chique_is_image_slider_active' ) ) :
	/**
	* Return true if image slider is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_image_slider_active( $control ) {
		$type = $control->manager->get_setting( 'chique_slider_type' )->value();

		//return true only if previwed page on customizer matches the type of slider option selected and is or is not selected type
		return ( chique_is_slider_active( $control ) && 'custom' === $type );
	}
endif;

if ( ! function_exists( 'chique_is_image_slider_inactive' ) ) :
	/**
	* Return true if image slider is inactive
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_image_slider_inactive( $control ) {
		$type = $control->manager->get_setting( 'chique_slider_type' )->value();

		//return true only if previwed page on customizer matches the type of slider option selected and is or is not selected type
		return ( chique_is_slider_active( $control ) && 'custom' !== $type );
	}
endif;

if ( ! function_exists( 'chique_is_category_slider_inactive' ) ) :
	/**
	* Return true if page slider is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_category_slider_inactive( $control ) {
		$type = $control->manager->get_setting( 'chique_slider_type' )->value();

		//return true only if previwed page on customizer matches the type of slider option selected and is or is not selected type
		return ( chique_is_slider_active( $control ) && 'category' !== $type );
	}
endif;
