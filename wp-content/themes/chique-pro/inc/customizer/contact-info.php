<?php
/**
 * Contact Info options
 *
 * @package Chique
 */

/**
 * Add contact options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_contact_options( $wp_customize ) {
    $wp_customize->add_section( 'chique_contact', array(
			'title' => esc_html__( 'Contact Info', 'chique-pro' ),
			'panel' => 'chique_theme_options',
		)
	);

	// Add color scheme setting and control.
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_contact_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique-pro' ),
			'section'           => 'chique_contact',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_contact_title',
			'default'           => esc_html__( 'Say Hello', 'chique-pro' ),
			'sanitize_callback' => 'wp_kses_data',
			'active_callback'   => 'chique_is_contact_active',
			'label'             => esc_html__( 'Title', 'chique-pro' ),
			'section'           => 'chique_contact',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_contact_description',
			'sanitize_callback' => 'wp_kses_data',
			'active_callback'   => 'chique_is_contact_active',
			'label'             => esc_html__( 'Description', 'chique-pro' ),
			'section'           => 'chique_contact',
			'type'              => 'textarea',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_contact_email_label',
			'default'           => 'Email',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_contact_active',
			'label'             => esc_html__( 'Email Label', 'chique-pro' ),
			'section'           => 'chique_contact',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_contact_email',
			'default'           => 'someone@somewhere.com',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_contact_active',
			'label'             => esc_html__( 'Email', 'chique-pro' ),
			'section'           => 'chique_contact',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_contact_phone_label',
			'default'           => 'Phone',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_contact_active',
			'label'             => esc_html__( 'Phone Label', 'chique-pro' ),
			'section'           => 'chique_contact',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_contact_phone',
			'default'           => '123-456-7890',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_contact_active',
			'label'             => esc_html__( 'Phone', 'chique-pro' ),
			'section'           => 'chique_contact',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_contact_address_label',
			'default'           => 'Location',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_contact_active',
			'label'             => esc_html__( 'Address Label', 'chique-pro' ),
			'section'           => 'chique_contact',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_contact_address',
			'default'           => 'Boston, MA, USA',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_contact_active',
			'label'             => esc_html__( 'Address', 'chique-pro' ),
			'section'           => 'chique_contact',
			'type'              => 'textarea',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_contact_address_link',
			'sanitize_callback' => 'esc_url_raw',
			'active_callback'   => 'chique_is_contact_active',
			'label'             => esc_html__( 'Link', 'chique-pro' ),
			'section'           => 'chique_contact',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_contact_address_target',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_contact_active',
			'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
			'section'           => 'chique_contact',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_contact_info_note',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_contact_active',
			'custom_control'    => 'Chique_Note_Control',
			'label'             => sprintf( esc_html__( 'Click %1$shere%2$s to set Social Menu', 'chique-pro' ),
                '<a href="javascript:wp.customize.control( \'nav_menu_locations[social-contact]\' ).focus();">',
                 '</a>'
            ),
			'section'           => 'chique_contact',
			'type'              => 'description',
        )
    );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_display_contact_form',
			'default'			=> 1,
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_contact_active',
			'label'             => esc_html__( 'Contact Form', 'chique-pro' ),
			'section'           => 'chique_contact',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	$type = chique_section_type_options();

	unset( $type['category'] );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_contact_form_type',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_contact_form_active',
			'default'			=> 'custom',
            'choices'           => $type,
			'label'             => esc_html__( 'Type', 'chique-pro' ),
			'section'           => 'chique_contact',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_contact_form_post',
			'sanitize_callback' => 'chique_sanitize_post',
			'default'           => 0,
			'active_callback'   => 'chique_is_contact_form_post_active',
			'input_attrs'       => array(
				'style' => 'width: 40px;'
			),
			'label'             => esc_html__( 'Post', 'chique-pro' ),
			'section'           => 'chique_contact',
			'choices'           => chique_generate_post_array(),
			'type'              => 'select'
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_contact_form_page',
			'sanitize_callback' => 'chique_sanitize_post',
			'active_callback'   => 'chique_is_contact_form_page_active',
			'label'             => esc_html__( 'Page', 'chique-pro' ),
			'section'           => 'chique_contact',
			'type'              => 'dropdown-pages',
			'allow_addition'    => true,
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_contact_form_custom',
			'sanitize_callback' => 'wp_kses_post',
			'description'       => esc_html__( 'Add custom shortcodes from Contact Form 7 or Jetpack', 'chique-pro' ),
			'active_callback'   => 'chique_is_contact_form_custom_active',
			'label'             => esc_html__( 'Custom Content', 'chique-pro' ),
			'section'           => 'chique_contact',
			'type'              => 'textarea',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_contact_map',
			'sanitize_callback' => 'chique_sanitize_image',
			'custom_control'    => 'WP_Customize_Image_Control',
			'active_callback'   => 'chique_is_contact_active',
			'label'             => esc_html__( 'Map Image', 'chique-pro' ),
			'section'           => 'chique_contact',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_contact_map_link',
			'sanitize_callback' => 'esc_url_raw',
			'active_callback'   => 'chique_is_contact_active',
			'label'             => esc_html__( 'Link', 'chique-pro' ),
			'section'           => 'chique_contact',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_contact_map_target',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_contact_active',
			'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
			'section'           => 'chique_contact',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);
}
add_action( 'customize_register', 'chique_contact_options', 10 );

/** Active Callback Functions **/
if ( ! function_exists( 'chique_is_contact_active' ) ) :
	/**
	* Return true if contact is active
	*
	* @since Catch Vogue Pro 1.0
	*/
	function chique_is_contact_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_contact_option' )->value();

		//return true only if previewed page on customizer matches the type of content option selected
		return ( chique_check_section( $enable ) );
	}
endif;

if ( ! function_exists( 'chique_is_contact_form_post_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Foodoholic Pro 1.0
	*/
	function chique_is_contact_form_post_active( $control ) {
		$type = $control->manager->get_setting( 'chique_contact_form_type' )->value();
		$form_active = $control->manager->get_setting( 'chique_display_contact_form' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_contact_active( $control ) && 'post' === $type &&  $form_active );
	}
endif;

if ( ! function_exists( 'chique_is_contact_form_page_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Foodoholic Pro 1.0
	*/
	function chique_is_contact_form_page_active( $control ) {
		$type = $control->manager->get_setting( 'chique_contact_form_type' )->value();
		$form_active = $control->manager->get_setting( 'chique_display_contact_form' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_contact_active( $control ) && 'page' === $type && $form_active );
	}
endif;

if ( ! function_exists( 'chique_is_contact_form_custom_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Foodoholic Pro 1.0
	*/
	function chique_is_contact_form_custom_active( $control ) {
		$type = $control->manager->get_setting( 'chique_contact_form_type' )->value();
		$form_active = $control->manager->get_setting( 'chique_display_contact_form' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_contact_active( $control ) && 'custom' === $type && $form_active );
	}
endif;

if ( ! function_exists( 'chique_is_contact_form_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Foodoholic Pro 1.0
	*/
	function chique_is_contact_form_active( $control ) {
		$form_active = $control->manager->get_setting( 'chique_display_contact_form' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_contact_active( $control ) && $form_active );
	}
endif;
