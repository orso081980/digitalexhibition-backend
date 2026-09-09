<?php
/**
* The template for adding Timeline Settings in Customizer
*
* @package Chique
*/

/**
 * Add timeline options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_timeline_options( $wp_customize ) {
	$wp_customize->add_section( 'chique_timeline', array(
			'panel' => 'chique_theme_options',
			'title' => esc_html__( 'Timeline', 'chique-pro' ),
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_timeline_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique-pro' ),
			'section'           => 'chique_timeline',
			'type'              => 'select',
		)
	);

	$choices = chique_section_type_options();

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_timeline_type',
			'default'           => 'category',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_timeline_active',
			'choices'           => $choices,
			'label'             => esc_html__( 'Select Content Type', 'chique-pro' ),
			'section'           => 'chique_timeline',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_timeline_headline',
			'default'           => esc_html( 'Timeline', 'chique-pro' ),
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_demo_timeline_inactive',
			'label'             => esc_html__( 'Headline', 'chique-pro' ),
			'section'           => 'chique_timeline',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_timeline_subheadline',
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_demo_timeline_inactive',
			'label'             => esc_html__( 'Sub-headline', 'chique-pro' ),
			'section'           => 'chique_timeline',
			'type'              => 'textarea',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_timeline_number',
			'default'           => 4,
			'sanitize_callback' => 'chique_sanitize_number_range',
			'active_callback'   => 'chique_is_demo_timeline_inactive',
			'description'       => esc_html__( 'Save and refresh the page if No. of items', 'chique-pro' ),
			'input_attrs'       => array(
				'style' => 'width: 45px;',
				'min'   => 0,
			),
			'label'             => esc_html__( 'No of items', 'chique-pro' ),
			'section'           => 'chique_timeline',
			'type'              => 'number',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_timeline_display_title',
			'default'           => 1,
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_post_page_cagetory_timeline_active',
			'label'             => esc_html__( 'Display Title', 'chique-pro' ),
			'section'           => 'chique_timeline',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_timeline_display_date',
			'default'           => 1,
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_post_page_cagetory_timeline_active',
			'label'             => esc_html__( 'Display Date', 'chique-pro' ),
			'section'           => 'chique_timeline',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_timeline_show',
			'default'           => 'full-content',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_post_page_cagetory_timeline_active',
			'choices'           => chique_content_show(),
			'label'             => esc_html__( 'Display Content', 'chique-pro' ),
			'section'           => 'chique_timeline',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_timeline_select_category',
			'sanitize_callback' => 'chique_sanitize_category_list',
			'custom_control'    => 'Chique_Multi_Categories_Control',
			'active_callback'   => 'chique_is_category_timeline_active',
			'label'             => esc_html__( 'Select Categories', 'chique-pro' ),
			'name'              => 'chique_timeline_select_category',
			'section'           => 'chique_timeline',
			'settings'          => 'chique_timeline_select_category',
			'type'              => 'dropdown-categories',
		)
	);

	$number = get_theme_mod( 'chique_timeline_number', 4 );

	for ( $i=1; $i <= $number; $i++ ) {
		chique_register_option( $wp_customize, array(
				'name'              => 'chique_events_timeline_date_'. $i,
				'sanitize_callback' => 'sanitize_text_field',
				'active_callback'   => 'chique_is_demo_timeline_inactive',
				'label'             => esc_html__( 'Date #' . $i, 'chique-pro' ),
				'section'           => 'chique_timeline',
				'type'              => 'date',
			)
		);

		//for post timeline
		chique_register_option( $wp_customize, array(
				'name'              => 'chique_timeline_post_'. $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'active_callback'   => 'chique_is_post_timeline_active',
				'input_attrs'       => array(
					'style' => 'width: 100px;'
				),
				'label'             => esc_html__( 'Post', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_timeline',
				'choices'           => chique_generate_post_array(),
				'type'              => 'select',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_timeline_page_'. $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'active_callback'   => 'chique_is_page_timeline_active',
				'label'             => esc_html__( 'Page', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_timeline',
				'type'              => 'dropdown-pages',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_timeline_note_'. $i,
				'sanitize_callback' => 'sanitize_text_field',
				'custom_control'    => 'Chique_Note_Control',
				'active_callback'   => 'chique_is_custom_timeline_active',
				'label'             => esc_html__( 'Event #', 'chique-pro' ) .  $i,
				'section'           => 'chique_timeline',
				'type'              => 'description',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_events_timeline_image_' . $i,
				'sanitize_callback' => 'chique_sanitize_image',
				'custom_control'    => 'WP_Customize_Image_Control',
				'active_callback'   => 'chique_is_custom_timeline_active',
				'label'             => esc_html__( 'Image', 'chique-pro' ),
				'section'           => 'chique_timeline',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_timeline_link_'. $i,
				'default'           => '#',
				'sanitize_callback' => 'esc_url_raw',
				'active_callback'   => 'chique_is_custom_timeline_active',
				'label'             => esc_html__( 'Link', 'chique-pro' ),
				'section'           => 'chique_timeline',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_timeline_target_'. $i,
				'sanitize_callback' => 'chique_sanitize_checkbox',
				'active_callback'   => 'chique_is_custom_timeline_active',
				'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
				'section'           => 'chique_timeline',
				'custom_control'    => 'Chique_Toggle_Control',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_timeline_title_'. $i,
				'sanitize_callback' => 'sanitize_text_field',
				'active_callback'   => 'chique_is_custom_timeline_active',
				'label'             => esc_html__( 'Title', 'chique-pro' ),
				'section'           => 'chique_timeline',
				'type'              => 'text',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_timeline_date_day_'. $i,
				'sanitize_callback' => 'chique_sanitize_number_range',
				'default'           => '1',
				'active_callback'   => 'chique_is_custom_timeline_active',
				'label'             => esc_html__( 'Date Day', 'chique-pro' ),
				'section'           => 'chique_timeline',
				'type'              => 'number',
				'input_attrs'       => array(
					'style' => 'width: 45px;',
					'min'   => 1,
					'max'   => 31
				),
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_timeline_date_month_'. $i,
				'sanitize_callback' => 'chique_sanitize_number_range',
				'default'           => '1',
				'active_callback'   => 'chique_is_custom_timeline_active',
				'label'             => esc_html__( 'Date Month', 'chique-pro' ),
				'section'           => 'chique_timeline',
				'type'              => 'number',
				'input_attrs'       => array(
					'style' => 'width: 45px;',
					'min'   => 1,
					'max'   => 12
				),
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_timeline_content_'. $i,
				'sanitize_callback' => 'wp_kses_post',
				'active_callback'   => 'chique_is_custom_timeline_active',
				'label'             => esc_html__( 'Content', 'chique-pro' ),
				'section'           => 'chique_timeline',
				'type'              => 'textarea',
			)
		);
	}
}
add_action( 'customize_register', 'chique_timeline_options', 10 );

/** Active Callbacks **/
if ( ! function_exists( 'chique_is_timeline_active' ) ) :
	/**
	* Return true if timeline is active
	*
	* @since  Chique Pro Pro 1.0
	*/
	function chique_is_timeline_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_timeline_option' )->value();

		return ( chique_check_section( $enable ) );
	}
endif;


if ( ! function_exists( 'chique_is_demo_timeline_inactive' ) ) :
	/**
	* Return true if demo timeline is inactive
	*
	* @since  Chique Pro Pro 1.0
	*/
	function chique_is_demo_timeline_inactive( $control ) {
		$type = $control->manager->get_setting( 'chique_timeline_type' )->value();

		return ( chique_is_timeline_active( $control ) && 'demo' !== $type );
	}
endif;


if ( ! function_exists( 'chique_is_post_timeline_active' ) ) :
	/**
	* Return true if page timeline is active
	*
	* @since  Chique Pro Pro 1.0
	*/
	function chique_is_post_timeline_active( $control ) {
		$type = $control->manager->get_setting( 'chique_timeline_type' )->value();

		return ( chique_is_timeline_active( $control ) && 'post' === $type );
	}
endif;


if ( ! function_exists( 'chique_is_page_timeline_active' ) ) :
	/**
	* Return true if page timeline is active
	*
	* @since  Chique Pro Pro 1.0
	*/
	function chique_is_page_timeline_active( $control ) {
		$type = $control->manager->get_setting( 'chique_timeline_type' )->value();

		return ( chique_is_timeline_active( $control ) && 'page' === $type );
	}
endif;


if ( ! function_exists( 'chique_is_category_timeline_active' ) ) :
	/**
	* Return true if page timeline is active
	*
	* @since  Chique Pro Pro 1.0
	*/
	function chique_is_category_timeline_active( $control ) {
		$type = $control->manager->get_setting( 'chique_timeline_type' )->value();

		return ( chique_is_timeline_active( $control ) && 'category' === $type );
	}
endif;


if ( ! function_exists( 'chique_is_post_page_cagetory_timeline_active' ) ) :
	/**
	* Return true if post/page/category timeline is active
	*
	* @since  Chique Pro Pro 1.0
	*/
	function chique_is_post_page_cagetory_timeline_active( $control ) {
		$type = $control->manager->get_setting( 'chique_timeline_type' )->value();

		return ( chique_is_timeline_active( $control ) && ( 'post' == $type || 'page' == $type || 'category' == $type ) );
	}
endif;


if ( ! function_exists( 'chique_is_custom_timeline_active' ) ) :
	/**
	* Return true if image timeline is active
	*
	* @since  Chique Pro Pro 1.0
	*/
	function chique_is_custom_timeline_active( $control ) {
		$type = $control->manager->get_setting( 'chique_timeline_type' )->value();

		return ( chique_is_timeline_active( $control ) && 'custom' === $type );
	}
endif;
