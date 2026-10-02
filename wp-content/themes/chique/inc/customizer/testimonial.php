<?php
/**
 * Add Testimonial Settings in Customizer
 *
 * @package Chique
*/

/**
 * Add testimonial options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_testimonial_options( $wp_customize ) {
    // Add note to Jetpack Testimonial Section
    chique_register_option( $wp_customize, array(
            'name'              => 'chique_jetpack_testimonial_cpt_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Chique_Note_Control',
            'label'             => sprintf( esc_html__( 'For Testimonial Options for Chique Theme, go %1$shere%2$s', 'chique' ),
                '<a href="javascript:wp.customize.section( \'chique_testimonials\' ).focus();">',
                 '</a>'
            ),
           'section'            => 'jetpack_testimonials',
            'type'              => 'description',
            'priority'          => 1,
        )
    );

    $wp_customize->add_section( 'chique_testimonials', array(
            'panel'    => 'chique_theme_options',
            'title'    => esc_html__( 'Testimonials', 'chique' ),
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
            'name'              => 'chique_testimonial_jetpack_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Chique_Note_Control',
            'active_callback'   => 'chique_is_ect_testimonial_inactive',
            /* translators: 1: <a>/link tag start, 2: </a>/link tag close. */
            'label'             => sprintf( esc_html__( 'For Testimonial, install %1$sEssential Content Types%2$s Plugin with testimonial Type Enabled', 'chique' ),
                '<a target="_blank" href="' . esc_url( $install_url ) . '">',
                '</a>'

            ),
           'section'            => 'chique_testimonials',
            'type'              => 'description',
            'priority'          => 1,
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_testimonial_option',
            'default'           => 'disabled',
            'active_callback'   => 'chique_is_ect_testimonial_active',
            'sanitize_callback' => 'chique_sanitize_select',
            'choices'           => chique_section_visibility_options(),
            'label'             => esc_html__( 'Enable on', 'chique' ),
            'section'           => 'chique_testimonials',
            'type'              => 'select',
            'priority'          => 1,
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_testimonial_cpt_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Chique_Note_Control',
            'active_callback'   => 'chique_is_testimonial_active',
            /* translators: 1: <a>/link tag start, 2: </a>/link tag close. */
            'label'             => sprintf( esc_html__( 'For CPT heading and sub-heading, go %1$shere%2$s', 'chique' ),
                '<a href="javascript:wp.customize.section( \'jetpack_testimonials\' ).focus();">',
                '</a>'
            ),
            'section'           => 'chique_testimonials',
            'type'              => 'description',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_testimonial_number',
            'default'           => '5',
            'sanitize_callback' => 'chique_sanitize_number_range',
            'active_callback'   => 'chique_is_testimonial_active',
            'label'             => esc_html__( 'Number of items', 'chique' ),
            'section'           => 'chique_testimonials',
            'type'              => 'number',
            'input_attrs'       => array(
                'style'             => 'width: 100px;',
                'min'               => 0,
            ),
        )
    );

    $number = get_theme_mod( 'chique_testimonial_number', 5 );

    for ( $i = 1; $i <= $number ; $i++ ) {
        //for CPT
        chique_register_option( $wp_customize, array(
                'name'              => 'chique_testimonial_cpt_' . $i,
                'sanitize_callback' => 'chique_sanitize_post',
                'active_callback'   => 'chique_is_testimonial_active',
                'label'             => esc_html__( 'Testimonial', 'chique' ) . ' ' . $i ,
                'section'           => 'chique_testimonials',
                'type'              => 'select',
                'choices'           => chique_generate_post_array( 'jetpack-testimonial' ),
            )
        );
    } // End for().
}
add_action( 'customize_register', 'chique_testimonial_options' );

/**
 * Active Callback Functions
 */
if ( ! function_exists( 'chique_is_testimonial_active' ) ) :
    /**
    * Return true if testimonial is active
    *
    * @since Chique 1.0
    */
    function chique_is_testimonial_active( $control ) {
        $enable = $control->manager->get_setting( 'chique_testimonial_option' )->value();

        //return true only if previwed page on customizer matches the type of content option selected
        return chique_check_section( $enable );
    }
endif;

if ( ! function_exists( 'chique_is_ect_testimonial_inactive' ) ) :
    /**
    *
    * @since Chique 1.0
    */
    function chique_is_ect_testimonial_inactive( $control ) {
        return ! ( class_exists( 'Essential_Content_Jetpack_testimonial' ) || class_exists( 'Essential_Content_Pro_Jetpack_testimonial' ) );
    }
endif;

if ( ! function_exists( 'chique_is_ect_testimonial_active' ) ) :
    /**
    *
    * @since Chique 1.0
    */
    function chique_is_ect_testimonial_active( $control ) {
        return ( class_exists( 'Essential_Content_Jetpack_testimonial' ) || class_exists( 'Essential_Content_Pro_Jetpack_testimonial' ) );
    }
endif;
