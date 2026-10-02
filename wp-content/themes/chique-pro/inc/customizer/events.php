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
function chique_events_options( $wp_customize ) {
	$wp_customize->add_section( 'chique_events', array(
			'panel' => 'chique_theme_options',
			'title' => esc_html__( 'Events', 'chique-pro' ),
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_events_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique-pro' ),
			'section'           => 'chique_events',
			'type'              => 'select',
		)
	);

	$type = chique_section_type_options();

	$type['ect-event'] = esc_html__( 'Custom Post Type', 'chique-pro' );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_events_type',
			'default'           => 'category',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_events_active',
			'choices'           => $type,
			'label'             => esc_html__( 'Select Slider Type', 'chique-pro' ),
			'section'           => 'chique_events',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_events_number',
			'default'           => '4',
			'sanitize_callback' => 'chique_sanitize_number_range',

			'active_callback'   => 'chique_is_events_active',
			'description'       => esc_html__( 'Save and refresh the page if No. of Slides is changed (Max no of slides is 20)', 'chique-pro' ),
			'input_attrs'       => array(
				'style' => 'width: 100px;',
				'min'   => 0,
				'max'   => 20,
				'step'  => 1,
			),
			'label'             => esc_html__( 'No of Slides', 'chique-pro' ),
			'section'           => 'chique_events',
			'type'              => 'number',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_events_content_show',
			'default'           => 'hide-content',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_image_events_inactive',
			'choices'           => chique_content_show(),
			'label'             => esc_html__( 'Display Content', 'chique-pro' ),
			'section'           => 'chique_events',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_events_select_category',
			'sanitize_callback' => 'chique_sanitize_category_list',
			'custom_control'    => 'Chique_Multi_Cat',
			'active_callback'   => 'chique_is_category_events_active',
			'label'             => esc_html__( 'Select Categories', 'chique-pro' ),
			'name'              => 'chique_events_select_category',
			'section'           => 'chique_events',
			'type'              => 'dropdown-categories',
		)
	);

	$events_number = get_theme_mod( 'chique_events_number', 4 );

	for ( $i = 1; $i <= $events_number ; $i++ ) {

		// Post Sliders
		chique_register_option( $wp_customize, array(
				'name'              => 'chique_events_post_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'active_callback'   => 'chique_is_post_events_active',
				'input_attrs'       => array(
					'style' => 'width: 80px;',
				),
				'label'             => esc_html__( 'Post', 'chique-pro' ) . ' # ' . $i,
				'section'           => 'chique_events',
				'choices'           => chique_generate_post_array(),
				'type'              => 'select',
			)
		);

		// Page Sliders
		chique_register_option( $wp_customize, array(
				'name'              => 'chique_events_page_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'active_callback'   => 'chique_is_page_events_active',
				'label'             => esc_html__( 'Page', 'chique-pro' ) . ' # ' . $i,
				'section'           => 'chique_events',
				'type'              => 'dropdown-pages',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_events_cpt_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'active_callback'   => 'chique_is_events_cpt_active',
				'label'             => esc_html__( 'Events', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_events',
				'type'              => 'select',
                'choices'           => chique_generate_post_array( 'ect-event' ),
			)
		);

		// Image Sliders
		chique_register_option( $wp_customize, array(
				'name'              => 'chique_events_note_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'custom_control'    => 'Chique_Note_Control',
				'active_callback'   => 'chique_is_image_events_active',
				'label'             => esc_html__( 'Slide #', 'chique-pro' ) . $i,
				'section'           => 'chique_events',
				'type'              => 'description',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_events_image_' . $i,
				'sanitize_callback' => 'chique_sanitize_image',
				'custom_control'    => 'WP_Customize_Image_Control',
				'active_callback'   => 'chique_is_image_events_active',
				'label'             => esc_html__( 'Image', 'chique-pro' ),
				'section'           => 'chique_events',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_events_link_' . $i,
				'sanitize_callback' => 'esc_url_raw',
				'default'			=> '#',
				'active_callback'   => 'chique_is_image_events_active',
				'label'             => esc_html__( 'Link', 'chique-pro' ),
				'section'           => 'chique_events',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_events_tabs_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'active_callback'   => 'chique_is_events_cpt_inactive',
				'label'             => esc_html__( 'Tab Heading', 'chique-pro' ) . ' ' . $i,
				'section'           => 'chique_events',
				'type'              => 'text',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_events_date_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'active_callback'   => 'chique_is_events_cpt_inactive',
				'label'             => esc_html__( 'Event Date for item', 'chique-pro' ) . ' ' . $i,
				'section'           => 'chique_events',
				'type'              => 'text',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_events_time_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'active_callback'   => 'chique_is_events_cpt_inactive',
				'label'             => esc_html__( 'Event Time for item', 'chique-pro' ) . ' ' . $i,
				'section'           => 'chique_events',
				'type'              => 'text',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_events_target_' . $i,
				'sanitize_callback' => 'chique_sanitize_checkbox',
				'active_callback'   => 'chique_is_image_events_active',
				'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
				'section'           => 'chique_events',
				'custom_control'    => 'Chique_Toggle_Control',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_events_title_' . $i,
				'sanitize_callback' => 'wp_kses_post',
				'active_callback'   => 'chique_is_image_events_active',
				'label'             => esc_html__( 'Title', 'chique-pro' ),
				'section'           => 'chique_events',
				'type'              => 'text',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_events_content_' . $i,
				'sanitize_callback' => 'wp_kses_post',
				'active_callback'   => 'chique_is_image_events_active',
				'label'             => esc_html__( 'Content', 'chique-pro' ),
				'section'           => 'chique_events',
				'type'              => 'textarea',
			)
		);
	} // End for().
}
add_action( 'customize_register', 'chique_events_options' );

/** Active Callback Functions */

if ( ! function_exists( 'chique_is_events_active' ) ) :
	/**
	* Return true if events is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_events_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_events_option' )->value();

		//return true only if previwed page on customizer matches the type option selected
		return chique_check_section( $enable );
	}
endif;

if ( ! function_exists( 'chique_is_post_events_active' ) ) :
	/**
	* Return true if page events is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_post_events_active( $control ) {
		$type = $control->manager->get_setting( 'chique_events_type' )->value();

		//return true only if previwed page on customizer matches the type of events option selected and is or is not selected type
		return ( chique_is_events_active( $control ) && 'post' === $type );
	}
endif;

if ( ! function_exists( 'chique_is_page_events_active' ) ) :
	/**
	* Return true if page events is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_page_events_active( $control ) {
		$type = $control->manager->get_setting( 'chique_events_type' )->value();

		//return true only if previwed page on customizer matches the type of events option selected and is or is not selected type
		return ( chique_is_events_active( $control ) && 'page' === $type );
	}
endif;

if ( ! function_exists( 'chique_is_category_events_active' ) ) :
	/**
	* Return true if page events is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_category_events_active( $control ) {
		$type = $control->manager->get_setting( 'chique_events_type' )->value();

		//return true only if previwed page on customizer matches the type of events option selected and is or is not selected type
		return ( chique_is_events_active( $control ) && 'category' === $type );
	}
endif;

if ( ! function_exists( 'chique_is_image_events_active' ) ) :
	/**
	* Return true if image events is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_image_events_active( $control ) {
		$type = $control->manager->get_setting( 'chique_events_type' )->value();

		//return true only if previwed page on customizer matches the type of events option selected and is or is not selected type
		return ( chique_is_events_active( $control ) && 'custom' === $type );
	}
endif;

if ( ! function_exists( 'chique_is_image_events_inactive' ) ) :
	/**
	* Return true if image events is inactive
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_image_events_inactive( $control ) {
		$type = $control->manager->get_setting( 'chique_events_type' )->value();

		//return true only if previwed page on customizer matches the type of events option selected and is or is not selected type
		return ( chique_is_events_active( $control ) && 'custom' !== $type );
	}
endif;

if ( ! function_exists( 'chique_is_category_events_inactive' ) ) :
	/**
	* Return true if page events is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_category_events_inactive( $control ) {
		$type = $control->manager->get_setting( 'chique_events_type' )->value();

		//return true only if previwed page on customizer matches the type of events option selected and is or is not selected type
		return ( chique_is_events_active( $control ) && 'category' !== $type );
	}
endif;

if ( ! function_exists( 'chique_is_events_cpt_active' ) ) :
	/**
	* Return true if image events is inactive
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_events_cpt_active( $control ) {
		$type = $control->manager->get_setting( 'chique_events_type' )->value();

		//return true only if previwed page on customizer matches the type of events option selected and is or is not selected type
		return ( chique_is_events_active( $control ) && 'ect-event' == $type );
	}
endif;

if ( ! function_exists( 'chique_is_events_cpt_inactive' ) ) :
	/**
	* Return true if image events is inactive
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_events_cpt_inactive( $control ) {
		$type = $control->manager->get_setting( 'chique_events_type' )->value();

		//return true only if previwed page on customizer matches the type of events option selected and is or is not selected type
		return ( chique_is_events_active( $control ) && 'ect-event' !== $type );
	}
endif;

