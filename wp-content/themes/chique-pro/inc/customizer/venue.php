<?php
/**
 * Venue options
 *
 * @package Chique
 */

/**
 * Add venue options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_venue_options( $wp_customize ) {
	$wp_customize->add_section( 'chique_venue', array(
			'title' => esc_html__( 'Venue', 'chique-pro' ),
			'panel' => 'chique_theme_options',
		)
	);

	// Add color scheme setting and control.
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_venue_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique-pro' ),
			'section'           => 'chique_venue',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_venue_layout',
			'default'           => 'layout-three',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_venue_active',
			'choices'           => chique_sections_layout_options(),
			'label'             => esc_html__( 'Layout', 'chique-pro' ),
			'section'           => 'chique_venue',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_venue_title',
			'default'           => esc_html__( 'Wedding Details', 'chique-pro' ),
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_demo_venue_inactive',
			'label'             => esc_html__( 'Title', 'chique-pro' ),
			'section'           => 'chique_venue',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_venue_sub_title',
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_demo_venue_inactive',
			'label'             => esc_html__( 'Sub Title', 'chique-pro' ),
			'section'           => 'chique_venue',
			'type'              => 'textarea',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_venue_type',
			'default'           => 'category',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_venue_active',
			'choices'           => chique_section_type_options(),
			'label'             => esc_html__( 'Type', 'chique-pro' ),
			'section'           => 'chique_venue',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_venue_number',
			'default'           => 3,
			'sanitize_callback' => 'chique_sanitize_number_range',
			'active_callback'   => 'chique_is_venue_active',
			'description'       => esc_html__( 'Save and refresh the page if No of items is changed', 'chique-pro' ),
			'input_attrs'       => array(
				'style' => 'width: 100px;',
				'min'   => 0,
			),
			'label'             => esc_html__( 'No of Items', 'chique-pro' ),
			'section'           => 'chique_venue',
			'type'              => 'number',
			'transport'         => 'postMessage',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_venue_show',
			'default'           => 'excerpt',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_post_category_venue_active',
			'choices'           => chique_content_show(),
			'label'             => esc_html__( 'Display Content', 'chique-pro' ),
			'section'           => 'chique_venue',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_venue_select_category',
			'sanitize_callback' => 'chique_sanitize_category_list',
			'custom_control'    => 'chique_Multi_Categories_Control',
			'active_callback'   => 'chique_is_category_venue_active',
			'label'             => esc_html__( 'Select Categories', 'chique-pro' ),
			'name'              => 'chique_venue_select_category',
			'section'           => 'chique_venue',
			'type'              => 'dropdown-categories',
		)
	);

	$number = get_theme_mod( 'chique_venue_number', 3 );

	for ( $i = 1; $i <= $number ; $i++ ) {
		chique_register_option( $wp_customize, array(
				'name'              => 'chique_venue_post_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'default'           => 0,
				'active_callback'   => 'chique_is_post_venue_active',
				'input_attrs'       => array(
					'style'             => 'width: 40px;'
				),
				'label'             => esc_html__( 'Post', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_venue',
				'choices'           => chique_generate_post_array(),
				'type'              => 'select'
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_venue_page_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'active_callback'   => 'chique_is_page_venue_active',
				'label'             => esc_html__( 'Featured Page', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_venue',
				'type'              => 'dropdown-pages',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_venue_note_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'custom_control'    => 'chique_Note_Control',
				'active_callback'   => 'chique_is_image_venue_active',
				'label'             => esc_html__( 'Item #', 'chique-pro' ) .  $i,
				'section'           => 'chique_venue',
				'type'              => 'description',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_venue_image_' . $i,
				'sanitize_callback' => 'chique_sanitize_image',
				'custom_control'    => 'WP_Customize_Image_Control',
				'active_callback'   => 'chique_is_image_venue_active',
				'label'             => esc_html__( 'Image', 'chique-pro' ),
				'section'           => 'chique_venue',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_venue_link_' . $i,
				'sanitize_callback' => 'esc_url_raw',
				'active_callback'   => 'chique_is_image_venue_active',
				'label'             => esc_html__( 'Link', 'chique-pro' ),
				'section'           => 'chique_venue',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_venue_target_' . $i,
				'sanitize_callback' => 'chique_sanitize_checkbox',
				'active_callback'   => 'chique_is_image_venue_active',
				'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
				'section'           => 'chique_venue',
				'custom_control'    => 'Chique_Toggle_Control',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_venue_title_' . $i,
				'sanitize_callback' => 'wp_kses_post',
				'active_callback'   => 'chique_is_image_venue_active',
				'label'             => esc_html__( 'Title', 'chique-pro' ),
				'section'           => 'chique_venue',
				'type'              => 'text',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_venue_content_' . $i,
				'sanitize_callback' => 'wp_kses_post',
				'active_callback'   => 'chique_is_image_venue_active',
				'label'             => esc_html__( 'Content', 'chique-pro' ),
				'section'           => 'chique_venue',
				'type'              => 'textarea',
			)
		);
	} // End for().

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_venue_text',
			'default'           => esc_html__( 'See Map Direction', 'chique-pro' ),
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_venue_active',
			'label'             => esc_html__( 'Button Text', 'chique-pro' ),
			'section'           => 'chique_venue',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_venue_link',
			'sanitize_callback' => 'esc_url_raw',
			'active_callback'   => 'chique_is_venue_active',
			'label'             => esc_html__( 'Button Link', 'chique-pro' ),
			'section'           => 'chique_venue',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_venue_target',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_venue_active',
			'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
			'section'           => 'chique_venue',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);
}
add_action( 'customize_register', 'chique_venue_options', 10 );

/** Active Callback Functions **/
if( ! function_exists( 'chique_is_venue_active' ) ) :
	/**
	* Return true if venue is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_venue_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_venue_option' )->value();

		return ( chique_check_section( $enable ) );
	}
endif;


if( ! function_exists( 'chique_is_demo_venue_inactive' ) ) :
	/**
	* Return true if demo venue is inactive
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_demo_venue_inactive( $control ) {
		$type = $control->manager->get_setting( 'chique_venue_type' )->value();

		return ( chique_is_venue_active( $control ) && 'demo' !== $type );
	}
endif;


if( ! function_exists( 'chique_is_post_venue_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_post_venue_active( $control ) {
		$type = $control->manager->get_setting( 'chique_venue_type' )->value();

		return ( chique_is_venue_active( $control ) && 'post' === $type );
	}
endif;


if( ! function_exists( 'chique_is_page_venue_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_page_venue_active( $control ) {
		$type = $control->manager->get_setting( 'chique_venue_type' )->value();

		return ( chique_is_venue_active( $control ) && 'page' === $type );
	}
endif;


if( ! function_exists( 'chique_is_category_venue_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_category_venue_active( $control ) {
		$type = $control->manager->get_setting( 'chique_venue_type' )->value();

		return ( chique_is_venue_active( $control ) && 'category' === $type );
	}
endif;

if( ! function_exists( 'chique_is_post_category_venue_active' ) ) :
	/**
	* Return true if page/post/category/image content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_post_category_venue_active( $control ) {
		$type = $control->manager->get_setting( 'chique_venue_type' )->value();

		return ( chique_is_venue_active( $control ) && ( 'category' === $type || 'post' === $type ) );
	}
endif;


if( ! function_exists( 'chique_is_image_venue_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_image_venue_active( $control ) {
		$type = $control->manager->get_setting( 'chique_venue_type' )->value();

		return ( chique_is_venue_active( $control ) && 'custom' === $type );
	}
endif;
