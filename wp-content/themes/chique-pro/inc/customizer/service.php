<?php
/**
 * Services options
 *
 * @package Chique
 */

/**
 * Add services content options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_service_options( $wp_customize ) {
	// Add note to Jetpack Testimonial Section
    chique_register_option( $wp_customize, array(
            'name'              => 'chique_service_jetpack_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Chique_Note_Control',
            'label'             => sprintf( esc_html__( 'For all Services Options for Chique Theme, go %1$shere%2$s', 'chique-pro' ),
                '<a href="javascript:wp.customize.section( \'chique_service\' ).focus();">',
                 '</a>'
            ),
           'section'            => 'services',
            'type'              => 'description',
            'priority'          => 1,
        )
    );

    $wp_customize->add_section( 'chique_service', array(
			'title' => esc_html__( 'Services', 'chique-pro' ),
			'panel' => 'chique_theme_options',
		)
	);

	// Add color scheme setting and control.
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_service_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique-pro' ),
			'section'           => 'chique_service',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_service_layout',
			'default'           => 'layout-one',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_services_active',
			'choices'           =>  array(
				'layout-one'    => esc_html__( '1 column', 'chique-pro' ),
				'layout-two'    => esc_html__( '2 columns', 'chique-pro' ),
			),
			'label'             => esc_html__( 'Select Layout', 'chique-pro' ),
			'section'           => 'chique_service',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_service_archive_title',
			'default'           => esc_html__( 'Services', 'chique-pro' ),
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_cpt_services_inactive',
			'label'             => esc_html__( 'Headline', 'chique-pro' ),
			'section'           => 'chique_service',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_service_sub_title',
			'default'           => esc_html__( 'What We Do', 'chique-pro' ),
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_cpt_services_inactive',
			'label'             => esc_html__( 'Sub headline', 'chique-pro' ),
			'section'           => 'chique_service',
			'type'              => 'textarea',
		)
	);

	$type = chique_section_type_options();

	$type['ect-service'] = esc_html__( 'Custom Post Type', 'chique-pro' );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_service_type',
			'default'           => 'category',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_services_active',
			'description'       => sprintf( esc_html__( 'For Custom Post Type Content, install %1$sEssential Content Types%2$s Plugin with Services Type Enabled', 'chique-pro' ),
                '<a target="_blank" href="https://wordpress.org/plugins/essential-content-types/">',
                '</a>'
            ),
            'choices'           => $type,
			'label'             => esc_html__( 'Type', 'chique-pro' ),
			'section'           => 'chique_service',
			'type'              => 'select',
		)
	);

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_service_cpt_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Chique_Note_Control',
            'active_callback'   => 'chique_is_services_cpt_content_active',
            /* translators: 1: <a>/link tag start, 2: </a>/link tag close. */
			'label'             => sprintf( esc_html__( 'For CPT heading and sub-heading, go %1$shere%2$s', 'chique-pro' ),
                 '<a href="javascript:wp.customize.control( \'ect_service_title\' ).focus();">',
                 '</a>'
            ),
            'section'           => 'chique_service',
            'type'              => 'description',
        )
    );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_service_number',
			'default'           => 3,
			'sanitize_callback' => 'chique_sanitize_number_range',
			'active_callback'   => 'chique_is_services_active',
			'description'       => esc_html__( 'Save and refresh the page if No. of Services is changed (Max no of Services is 20)', 'chique-pro' ),
			'input_attrs'       => array(
				'style' => 'width: 100px;',
				'min'   => 0,
			),
			'label'             => esc_html__( 'No of items', 'chique-pro' ),
			'section'           => 'chique_service',
			'type'              => 'number',
			'transport'         => 'postMessage',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_service_show',
			'default'           => 'excerpt',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_services_post_page_category_cpt_content_active',
			'choices'           => chique_content_show(),
			'label'             => esc_html__( 'Display Content', 'chique-pro' ),
			'section'           => 'chique_service',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_service_meta_show',
			'default'           => 'hide-meta',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_services_post_page_category_cpt_content_active',
			'choices'           => chique_meta_show(),
			'label'             => esc_html__( 'Display Meta', 'chique-pro' ),
			'section'           => 'chique_service',
			'type'              => 'select',
		)
	);


	chique_register_option( $wp_customize, array(
			'name'              => 'chique_service_select_category',
			'sanitize_callback' => 'chique_sanitize_category_list',
			'custom_control'    => 'Chique_Multi_Cat',
			'active_callback'   => 'chique_is_services_category_content_active',
			'label'             => esc_html__( 'Select Categories', 'chique-pro' ),
			'name'              => 'chique_service_select_category',
			'section'           => 'chique_service',
			'type'              => 'dropdown-categories',
		)
	);

	$number = get_theme_mod( 'chique_service_number', 3 );

	//loop for services post content
	for ( $i = 1; $i <= $number ; $i++ ) {
		chique_register_option( $wp_customize, array(
				'name'              => 'chique_service_post_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'default'           => 0,
				'active_callback'   => 'chique_is_services_post_content_active',
				'input_attrs'       => array(
					'style'             => 'width: 40px;'
				),
				'label'             => esc_html__( 'Post', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_service',
				'choices'           => chique_generate_post_array(),
				'type'              => 'select'
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_service_page_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'active_callback'   => 'chique_is_services_page_content_active',
				'label'             => esc_html__( 'Services Page', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_service',
				'type'              => 'dropdown-pages',
				'allow_addition'    => true,
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_service_cpt_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'active_callback'   => 'chique_is_services_cpt_content_active',
				'label'             => esc_html__( 'Services', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_service',
				'type'              => 'select',
                'choices'           => chique_generate_post_array( 'ect-service' ),
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_service_note_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'custom_control'    => 'Chique_Note_Control',
				'active_callback'   => 'chique_is_services_image_content_active',
				'label'             => esc_html__( 'Services #', 'chique-pro' ) .  $i,
				'section'           => 'chique_service',
				'type'              => 'description',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_service_image_' . $i,
				'sanitize_callback' => 'chique_sanitize_image',
				'custom_control'    => 'WP_Customize_Image_Control',
				'active_callback'   => 'chique_is_services_image_content_active',
				'label'             => esc_html__( 'Image', 'chique-pro' ),
				'section'           => 'chique_service',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_service_link_' . $i,
				'sanitize_callback' => 'esc_url_raw',
				'active_callback'   => 'chique_is_services_image_content_active',
				'label'             => esc_html__( 'Link', 'chique-pro' ),
				'section'           => 'chique_service',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_service_target_' . $i,
				'sanitize_callback' => 'chique_sanitize_checkbox',
				'active_callback'   => 'chique_is_services_image_content_active',
				'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
				'section'           => 'chique_service',
				'custom_control'    => 'Chique_Toggle_Control',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_service_title_' . $i,
				'sanitize_callback' => 'wp_kses_post',
				'active_callback'   => 'chique_is_services_image_content_active',
				'label'             => esc_html__( 'Title', 'chique-pro' ),
				'section'           => 'chique_service',
				'type'              => 'text',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_service_content_' . $i,
				'sanitize_callback' => 'wp_kses_post',
				'active_callback'   => 'chique_is_services_image_content_active',
				'label'             => esc_html__( 'Content', 'chique-pro' ),
				'section'           => 'chique_service',
				'type'              => 'textarea',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_service_more_button_text_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'active_callback'   => 'chique_is_services_image_content_active',
				'label'             => esc_html__( 'More Button Text', 'chique-pro' ),
				'section'           => 'chique_service',
				'type'              => 'text',
			)
		);
	} // End for().
}
add_action( 'customize_register', 'chique_service_options', 10 );

/** Active Callback Functions **/
if ( ! function_exists( 'chique_is_services_active' ) ) :
	/**
	* Return true if services content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_services_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_service_option' )->value();

		//return true only if previewed page on customizer matches the type of content option selected
		return ( chique_check_section( $enable ) );
	}
endif;

if ( ! function_exists( 'chique_is_cpt_services_inactive' ) ) :
	/**
	* Return true if cpt services is inactive
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_cpt_services_inactive( $control ) {
		$type = $control->manager->get_setting( 'chique_service_type' )->value();

		return ( chique_is_services_active( $control ) && 'ect-service' !== $type );
	}
endif;

if ( ! function_exists( 'chique_is_services_post_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_services_post_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_service_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_services_active( $control ) && ( 'post' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_services_page_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_services_page_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_service_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_services_active( $control ) && ( 'page' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_services_cpt_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_services_cpt_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_service_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_services_active( $control ) && ( 'ect-service' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_services_category_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_services_category_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_service_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_services_active( $control ) && ( 'category' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_services_post_page_category_cpt_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_services_post_page_category_cpt_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_service_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_services_active( $control ) && ( 'category' === $type || 'ect-service' === $type || 'page' === $type || 'post' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_services_image_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_services_image_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_service_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_services_active( $control ) && ( 'custom' === $type ) );
	}
endif;
