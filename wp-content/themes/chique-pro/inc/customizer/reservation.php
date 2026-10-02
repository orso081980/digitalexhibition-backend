<?php
/**
 * Reservation options
 *
 * @package Chique
 */

/**
 * Add reservation options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_reservation_options( $wp_customize ) {
    $wp_customize->add_section( 'chique_reservation', array(
			'title' => esc_html__( 'Reservation', 'chique-pro' ),
			'panel' => 'chique_theme_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_reservation_bg_image',
			'sanitize_callback' => 'chique_sanitize_image',
			'active_callback'   => 'chique_is_reservation_active',
			'custom_control'    => 'WP_Customize_Image_Control',
			'label'             => esc_html__( 'Background Image', 'chique-pro' ),
			'section'           => 'chique_reservation',
		)
	);

	// Reservation Info Start.
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_reservation_left_note',
			'sanitize_callback' => 'sanitize_text_field',
			'custom_control'    => 'Chique_Note_Control',
			'label'             => esc_html__( 'Reservation Info( Left Section )', 'chique-pro' ),
			'section'           => 'chique_reservation',
			'type'              => 'description',
        )
    );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_reservation_info_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable Reservation Info on', 'chique-pro' ),
			'section'           => 'chique_reservation',
			'type'              => 'select',
		)
	);

    chique_register_option( $wp_customize, array(
			'name'              => 'chique_reservation_info_title',
			'default'           => esc_html__( 'Time', 'chique-pro' ),
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_reservation_info_active',
			'label'             => esc_html__( 'Description Title', 'chique-pro' ),
			'section'           => 'chique_reservation',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_reservation_info_subtitle',
			'default'           => esc_html__( 'Open', 'chique-pro' ),
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_reservation_info_active',
			'label'             => esc_html__( 'Description Subtitle', 'chique-pro' ),
			'section'           => 'chique_reservation',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_reservation_weekdays_title',
			'default'           => esc_html__( 'Mon-Fri', 'chique-pro' ),
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_reservation_info_active',
			'label'             => esc_html__( 'Weekdays Title', 'chique-pro' ),
			'section'           => 'chique_reservation',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_reservation_weekdays_desc',
			'default'           => esc_html__( '7AM - 11 AM (Breakfast)&#13;&#10;11AM - 10PM (Lunch/Dinner)', 'chique-pro' ),
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_reservation_info_active',
			'label'             => esc_html__( 'Weekdays Description', 'chique-pro' ),
			'section'           => 'chique_reservation',
			'type'              => 'textarea',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_reservation_weekends_title',
			'default'           => esc_html__( 'Sat-Sun', 'chique-pro' ),
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_reservation_info_active',
			'label'             => esc_html__( 'Weekends Title', 'chique-pro' ),
			'section'           => 'chique_reservation',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_reservation_weekends_desc',
			'default'           => esc_html__( '8AM - 1PM (Brunch)&#13;&#10;1PM - 9PM (Lunch/Dinner)', 'chique-pro' ),
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_reservation_info_active',
			'label'             => esc_html__( 'Weekends Description', 'chique-pro' ),
			'section'           => 'chique_reservation',
			'type'              => 'textarea',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_reservation_contact_info',
			'default'           => esc_html__( '+1 3234 567 8901', 'chique-pro' ),
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_reservation_info_active',
			'label'             => esc_html__( 'Contact Info', 'chique-pro' ),
			'section'           => 'chique_reservation',
			'type'              => 'textarea',
		)
	);
	// Reservation Info End.

	// Reservation Form Start.
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_reservation_right_note',
			'sanitize_callback' => 'sanitize_text_field',
			'custom_control'    => 'Chique_Note_Control',
			'label'             => esc_html__( 'Reservation Form( Right Section )', 'chique-pro' ),
			'section'           => 'chique_reservation',
			'type'              => 'description',
        )
    );
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_reservation_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable Reservation Form on', 'chique-pro' ),
			'section'           => 'chique_reservation',
			'type'              => 'select',
		)
	);

	$type = chique_section_type_options();

	unset( $type['category'] );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_reservation_type',
			'default'           => 'post',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_reservation_form_active',
            'choices'           => $type,
			'label'             => esc_html__( 'Type', 'chique-pro' ),
			'section'           => 'chique_reservation',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_reservation_align',
			'default'           => 'reservation-aligned-right',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_reservation_info_inactive_form_active',
			'choices'           => array(
				'reservation-aligned-right'  => esc_html__( 'Right', 'chique-pro' ),
				'reservation-aligned-left'   => esc_html__( 'Left', 'chique-pro' ),
				'reservation-aligned-center' => esc_html__( 'Center', 'chique-pro' ),
			),
			'label'             => esc_html__( 'Reservation Form Position', 'chique-pro' ),
			'section'           => 'chique_reservation',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_reservation_highlight_text',
			'default'           => wp_kses_post( __( 'or call us at <span>+123456789</span>', 'chique-pro' ) ),
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_reservation_info_inactive_form_active',
			'label'             => esc_html__( 'Highlight Text', 'chique-pro' ),
			'section'           => 'chique_reservation',
			'type'              => 'textarea',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_reservation_title',
			'default'           => esc_html__( 'Reservation', 'chique-pro' ),
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_reservation_form_active',
			'label'             => esc_html__( 'Title', 'chique-pro' ),
			'section'           => 'chique_reservation',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_reservation_subtitle',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_reservation_form_active',
			'label'             => esc_html__( 'Subtitle', 'chique-pro' ),
			'section'           => 'chique_reservation',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_reservation_post',
			'sanitize_callback' => 'chique_sanitize_post',
			'default'           => 0,
			'active_callback'   => 'chique_is_reservation_post_active',
			'input_attrs'       => array(
				'style' => 'width: 40px;'
			),
			'label'             => esc_html__( 'Post', 'chique-pro' ),
			'section'           => 'chique_reservation',
			'choices'           => chique_generate_post_array(),
			'type'              => 'select'
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_reservation_page',
			'sanitize_callback' => 'chique_sanitize_post',
			'active_callback'   => 'chique_is_reservation_page_active',
			'label'             => esc_html__( 'Page', 'chique-pro' ),
			'section'           => 'chique_reservation',
			'type'              => 'dropdown-pages',
			'allow_addition'    => true,
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
			'name'              => 'chique_reservation_category',
			'default'           => '0',
			'sanitize_callback' => 'absint',
			'active_callback'   => 'chique_is_reservation_category_active',
			'label'             => esc_html__( 'Category', 'chique-pro' ),
			'type'              => 'select',
			'choices'           => $cats,
			'section'           => 'chique_reservation',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_reservation_custom',
			'sanitize_callback' => 'wp_kses_post',
			'description'       => esc_html__( 'Add custom shortcodes from Contact Form 7 or Jetpack', 'chique-pro' ),
			'active_callback'   => 'chique_is_reservation_custom_active',
			'label'             => esc_html__( 'Custom Content', 'chique-pro' ),
			'section'           => 'chique_reservation',
			'type'              => 'textarea',
		)
	);
	// Reservation Form End.
}
add_action( 'customize_register', 'chique_reservation_options', 10 );

/** Active Callback Functions **/
if ( ! function_exists( 'chique_is_reservation_active' ) ) :
	/**
	* Return true if reservation is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_reservation_active( $control ) {
		$enable_info = $control->manager->get_setting( 'chique_reservation_info_option' )->value();
		$enable_form = $control->manager->get_setting( 'chique_reservation_option' )->value();

		return ( chique_check_section( $enable_info ) || chique_check_section( $enable_form ) );
	}
endif;

if ( ! function_exists( 'chique_is_reservation_info_active' ) ) :
	/**
	* Return true if reservation is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_reservation_info_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_reservation_info_option' )->value();

		//return true only if previewed page on customizer matches the type of content option selected
		return ( chique_check_section( $enable ) );
	}
endif;

if ( ! function_exists( 'chique_is_reservation_info_inactive_form_active' ) ) :
	/**
	* Return true if reservation is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_reservation_info_inactive_form_active( $control ) {
		$enable_info = $control->manager->get_setting( 'chique_reservation_info_option' )->value();
		$enable_form = $control->manager->get_setting( 'chique_reservation_option' )->value();

		//return true only if previewed page on customizer matches the type of content option selected
		return ( ! chique_check_section( $enable_info ) && chique_check_section( $enable_form ) );
	}
endif;

if ( ! function_exists( 'chique_is_reservation_form_active' ) ) :
	/**
	* Return true if reservation is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_reservation_form_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_reservation_option' )->value();

		//return true only if previewed page on customizer matches the type of content option selected
		return ( chique_check_section( $enable ) );
	}
endif;

if ( ! function_exists( 'chique_is_reservation_post_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_reservation_post_active( $control ) {
		$type = $control->manager->get_setting( 'chique_reservation_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_reservation_form_active( $control ) && 'post' === $type );
	}
endif;

if ( ! function_exists( 'chique_is_reservation_page_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_reservation_page_active( $control ) {
		$type = $control->manager->get_setting( 'chique_reservation_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_reservation_form_active( $control ) && 'page' === $type );
	}
endif;

if ( ! function_exists( 'chique_is_reservation_category_active' ) ) :
	/**
	* Return true if page category is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_reservation_category_active( $control ) {
		$type = $control->manager->get_setting( 'chique_reservation_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_reservation_form_active( $control ) && 'category' === $type );
	}
endif;

if ( ! function_exists( 'chique_is_reservation_custom_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_reservation_custom_active( $control ) {
		$type = $control->manager->get_setting( 'chique_reservation_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_reservation_form_active( $control ) && 'custom' === $type );
	}
endif;
