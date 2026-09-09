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
            'label'             => sprintf( esc_html__( 'For all Services Options for Chique Theme, go %1$shere%2$s', 'chique' ),
                '<a href="javascript:wp.customize.section( \'chique_service\' ).focus();">',
                 '</a>'
            ),
           'section'            => 'services',
            'type'              => 'description',
            'priority'          => 1,
        )
    );

    $wp_customize->add_section( 'chique_service', array(
			'title' => esc_html__( 'Services', 'chique' ),
			'panel' => 'chique_theme_options',
		)
	);

	$action = 'install-plugin';
    $slug   = 'essential-content-types';

    $install_url = wp_nonce_url(
        add_query_arg(
            array(
                'action' => $action,
                'plugin' => $slug
            ),
            admin_url( 'update.php' )
        ),
        $action . '_' . $slug
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_service_jetpack_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Chique_Note_Control',
            'active_callback'   => 'chique_is_ect_services_inactive',
            /* translators: 1: <a>/link tag start, 2: </a>/link tag close. */
            'label'             => sprintf( esc_html__( 'For Services, install %1$sEssential Content Types%2$s Plugin with Service Type Enabled', 'chique' ),
                '<a target="_blank" href="' . esc_url( $install_url ) . '">',
                '</a>'

            ),
           'section'            => 'chique_service',
            'type'              => 'description',
            'priority'          => 1,
        )
    );

	// Add color scheme setting and control.
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_service_option',
			'default'           => 'disabled',
			'active_callback'   => 'chique_is_ect_services_active',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique' ),
			'section'           => 'chique_service',
			'type'              => 'select',
		)
	);

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_service_cpt_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Chique_Note_Control',
            'active_callback'   => 'chique_is_services_active',
            /* translators: 1: <a>/link tag start, 2: </a>/link tag close. */
			'label'             => sprintf( esc_html__( 'For CPT heading and sub-heading, go %1$shere%2$s', 'chique' ),
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
			'description'       => esc_html__( 'Save and refresh the page if No. of Services is changed (Max no of Services is 20)', 'chique' ),
			'input_attrs'       => array(
				'style' => 'width: 100px;',
				'min'   => 0,
			),
			'label'             => esc_html__( 'No of items', 'chique' ),
			'section'           => 'chique_service',
			'type'              => 'number',
			'transport'         => 'postMessage',
		)
	);

	$number = get_theme_mod( 'chique_service_number', 3 );

	//loop for services post content
	for ( $i = 1; $i <= $number ; $i++ ) {

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_service_cpt_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'active_callback'   => 'chique_is_services_active',
				'label'             => esc_html__( 'Services', 'chique' ) . ' ' . $i ,
				'section'           => 'chique_service',
				'type'              => 'select',
                'choices'           => chique_generate_post_array( 'ect-service' ),
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
	* @since Chique 0.1
	*/
	function chique_is_services_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_service_option' )->value();

		//return true only if previewed page on customizer matches the type of content option selected
		return ( chique_is_ect_services_active( $control ) &&  chique_check_section( $enable ) );
	}
endif;

if ( ! function_exists( 'chique_is_ect_services_inactive' ) ) :
    /**
    * Return true if service is active
    *
    * @since Chique 1.0
    */
    function chique_is_ect_services_inactive( $control ) {
        return ! ( class_exists( 'Essential_Content_Service' ) || class_exists( 'Essential_Content_Pro_Service' ) );
    }
endif;

if ( ! function_exists( 'chique_is_ect_services_active' ) ) :
    /**
    * Return true if service is active
    *
    * @since Chique 1.0
    */
    function chique_is_ect_services_active( $control ) {
        return ( class_exists( 'Essential_Content_Service' ) || class_exists( 'Essential_Content_Pro_Service' ) );
    }
endif;
