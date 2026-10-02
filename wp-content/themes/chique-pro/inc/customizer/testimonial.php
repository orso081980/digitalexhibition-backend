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
            'label'             => sprintf( esc_html__( 'For Testimonial Options for Chique Theme, go %1$shere%2$s', 'chique-pro' ),
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
            'title'    => esc_html__( 'Testimonials', 'chique-pro' ),
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_testimonial_option',
            'default'           => 'disabled',
            'sanitize_callback' => 'chique_sanitize_select',
            'choices'           => chique_section_visibility_options(),
            'label'             => esc_html__( 'Enable on', 'chique-pro' ),
            'section'           => 'chique_testimonials',
            'type'              => 'select',
            'priority'          => 1,
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_testimonial_slider',
            'default'           => 1,
            'sanitize_callback' => 'chique_sanitize_checkbox',
            'active_callback'   => 'chique_is_testimonial_active',
            'label'             => esc_html__( 'Display slider', 'chique-pro' ),
            'section'           => 'chique_testimonials',
            'custom_control'    => 'Chique_Toggle_Control',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_testimonial_transition_in',
            'default'           => 'default',
            'sanitize_callback' => 'chique_sanitize_select',
            'active_callback'   => 'chique_is_testimonial_active',
            'choices'           => chique_transition_effects(),
            'label'             => esc_html__( 'Transition In', 'chique-pro' ),
            'section'           => 'chique_testimonials',
            'type'              => 'select',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_testimonial_transition_out',
            'default'           => 'default',
            'sanitize_callback' => 'chique_sanitize_select',
            'active_callback'   => 'chique_is_testimonial_active',
            'choices'           => chique_transition_effects(),
            'label'             => esc_html__( 'Transition Out', 'chique-pro' ),
            'section'           => 'chique_testimonials',
            'type'              => 'select',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_testimonial_nav',
            'default'           => 1,
            'sanitize_callback' => 'chique_sanitize_checkbox',
            'active_callback'   => 'chique_is_testimonial_slider_active',
            'label'             => esc_html__( 'Display nav arrows', 'chique-pro' ),
            'section'           => 'chique_testimonials',
            'custom_control'    => 'Chique_Toggle_Control',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_testimonial_dots',
            'default'           => 1,
            'sanitize_callback' => 'chique_sanitize_checkbox',
            'active_callback'   => 'chique_is_testimonial_slider_active',
            'label'             => esc_html__( 'Display nav dots', 'chique-pro' ),
            'section'           => 'chique_testimonials',
            'custom_control'    => 'Chique_Toggle_Control',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_testimonial_autoplay',
            'default'           => 0,
            'sanitize_callback' => 'chique_sanitize_checkbox',
            'active_callback'   => 'chique_is_testimonial_slider_active',
            'label'             => esc_html__( 'Autoplay', 'chique-pro' ),
            'section'           => 'chique_testimonials',
            'custom_control'    => 'Chique_Toggle_Control',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_testimonial_loop',
            'default'           => 1,
            'sanitize_callback' => 'chique_sanitize_checkbox',
            'active_callback'   => 'chique_is_testimonial_slider_active',
            'label'             => esc_html__( 'Loop (Last to first)', 'chique-pro' ),
            'section'           => 'chique_testimonials',
            'custom_control'    => 'Chique_Toggle_Control',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_testimonial_transition_timeout',
            'default'           => 4,
            'sanitize_callback' => 'chique_sanitize_number_range',
            'active_callback'   => 'chique_is_testimonial_slider_active',
            'input_attrs'       => array(
                'style'       => 'width: 100px;',
                'min'         => 0,
            ),
            'label'             => esc_html__( 'Transition timeout', 'chique-pro' ),
            'section'           => 'chique_testimonials',
            'type'              => 'number',
        )
    );

    $type = chique_section_type_options();

    $type['jetpack-testimonial'] = esc_html__( 'Custom Post Type', 'chique-pro' );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_testimonial_type',
            'default'           => 'category',
            'sanitize_callback' => 'chique_sanitize_select',
            'active_callback'   => 'chique_is_testimonial_active',
            'choices'           => $type,
            'description'       => sprintf( esc_html__( 'For Custom Post Type Content, install %1$sEssential Content Types%2$s Plugin with Testimonial Content Type Enabled', 'chique-pro' ),
                '<a target="_blank" href="https://wordpress.org/plugins/essential-content-types/">',
                '</a>'
            ),
            'label'             => esc_html__( 'Select Type', 'chique-pro' ),
            'section'           => 'chique_testimonials',
            'type'              => 'select',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_testimonial_cpt_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Chique_Note_Control',
            'active_callback'   => 'chique_is_jetpack_testimonial_active',
            /* translators: 1: <a>/link tag start, 2: </a>/link tag close. */
            'label'             => sprintf( esc_html__( 'For CPT heading and sub-heading, go %1$shere%2$s', 'chique-pro' ),
                '<a href="javascript:wp.customize.section( \'jetpack_testimonials\' ).focus();">',
                '</a>'
            ),
            'section'           => 'chique_testimonials',
            'type'              => 'description',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_testimonial_headline',
            'default'           => esc_html__( 'Testimonials', 'chique-pro' ),
            'sanitize_callback' => 'wp_kses_post',
            'label'             => esc_html__( 'Headline', 'chique-pro' ),
            'active_callback'   => 'chique_is_cpt_testimonial_inactive',
            'section'           => 'chique_testimonials',
            'type'              => 'text',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_testimonial_subheadline',
            'sanitize_callback' => 'wp_kses_post',
            'label'             => esc_html__( 'Sub headline', 'chique-pro' ),
            'active_callback'   => 'chique_is_cpt_testimonial_inactive',
            'section'           => 'chique_testimonials',
            'type'              => 'textarea',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_testimonial_number',
            'default'           => '5',
            'sanitize_callback' => 'chique_sanitize_number_range',
            'active_callback'   => 'chique_is_testimonial_active',
            'label'             => esc_html__( 'Number of items', 'chique-pro' ),
            'section'           => 'chique_testimonials',
            'type'              => 'number',
            'input_attrs'       => array(
                'style'             => 'width: 100px;',
                'min'               => 0,
            ),
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_testimonial_enable_title',
            'default'           => 1,
            'sanitize_callback' => 'chique_sanitize_checkbox',
            'active_callback'   => 'chique_is_testimonial_active',
            'label'             => esc_html__( 'Testimonial Title', 'chique-pro' ),
            'section'           => 'chique_testimonials',
            'custom_control'    => 'Chique_Toggle_Control',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_testimonial_show',
            'default'           => 'excerpt',
            'sanitize_callback' => 'chique_sanitize_select',
            'active_callback'   => 'chique_is_post_page_category_cpt_testimonial_active',
            'choices'           => chique_content_show(),
            'label'             => esc_html__( 'Display Content', 'chique-pro' ),
            'section'           => 'chique_testimonials',
            'type'              => 'select',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_testimonial_select_category',
            'sanitize_callback' => 'chique_sanitize_category_list',
            'active_callback'   => 'chique_is_category_testimonial_active',
            'custom_control'    => 'Chique_Multi_Cat',
            'label'             => esc_html__( 'Select Categories', 'chique-pro' ),
            'name'              => 'chique_testimonial_select_category',
            'section'           => 'chique_testimonials',
            'type'              => 'dropdown-categories',
        )
    );

    $number = get_theme_mod( 'chique_testimonial_number', 5 );

    for ( $i = 1; $i <= $number ; $i++ ) {
        //for featured post content
        chique_register_option( $wp_customize, array(
                'name'              => 'chique_testimonial_post_' . $i,
                'sanitize_callback' => 'chique_sanitize_post',
                'active_callback'   => 'chique_is_post_testimonial_active',
                'input_attrs'       => array(
                'style'             => 'width: 100px;'
                ),
                'label'             => esc_html__( 'Post', 'chique-pro' ) . ' ' . $i ,
                'section'           => 'chique_testimonials',
                'choices'           => chique_generate_post_array(),
                'type'              => 'select',
            )
        );

        //for CPT
        chique_register_option( $wp_customize, array(
                'name'              => 'chique_testimonial_cpt_' . $i,
                'sanitize_callback' => 'chique_sanitize_post',
                'active_callback'   => 'chique_is_jetpack_testimonial_active',
                'label'             => esc_html__( 'Testimonial', 'chique-pro' ) . ' ' . $i ,
                'section'           => 'chique_testimonials',
                'type'              => 'select',
                'choices'           => chique_generate_post_array( 'jetpack-testimonial' ),
            )
        );

        chique_register_option( $wp_customize, array(
                'name'              => 'chique_testimonial_page_' . $i,
                'sanitize_callback' => 'chique_sanitize_post',
                'active_callback'   => 'chique_is_page_testimonial_active',
                'label'             => esc_html__( 'Page', 'chique-pro' ) . ' ' . $i ,
                'section'           => 'chique_testimonials',
                'type'              => 'dropdown-pages',
            )
        );

        chique_register_option( $wp_customize, array(
                'name'              => 'chique_testimonial_note_' . $i,
                'sanitize_callback' => 'sanitize_text_field',
                'custom_control'    => 'Chique_Note_Control',
                'active_callback'   => 'chique_is_image_testimonial_active',
                'label'             => esc_html__( 'Testimonial #', 'chique-pro' ) .  $i,
                'section'           => 'chique_testimonials',
                'type'              => 'description',
            )
        );

        chique_register_option( $wp_customize, array(
                'name'              => 'chique_testimonial_image_' . $i,
                'custom_control'      => 'WP_Customize_Image_Control',
                'sanitize_callback' => 'chique_sanitize_image',
                'active_callback'   => 'chique_is_image_testimonial_active',
                'label'             => esc_html__( 'Image', 'chique-pro' ),
                'section'           => 'chique_testimonials',
            )
        );

        chique_register_option( $wp_customize, array(
                'name'              => 'chique_testimonial_link_' . $i,
                'sanitize_callback' => 'esc_url_raw',
                'active_callback'   => 'chique_is_image_testimonial_active',
                'label'             => esc_html__( 'Link', 'chique-pro' ),
                'section'           => 'chique_testimonials',
            )
        );

        chique_register_option( $wp_customize, array(
                'name'              => 'chique_testimonial_target_' . $i,
                'sanitize_callback' => 'chique_sanitize_checkbox',
                'active_callback'   => 'chique_is_image_testimonial_active',
                'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
                'section'           => 'chique_testimonials',
                'custom_control'    => 'Chique_Toggle_Control',
            )
        );

        chique_register_option( $wp_customize, array(
                'name'              => 'chique_testimonial_content_' . $i,
                'sanitize_callback' => 'sanitize_text_field',
                'active_callback'   => 'chique_is_image_testimonial_active',
                'label'             => esc_html__( 'Testimonial Text', 'chique-pro' ),
                'section'           => 'chique_testimonials',
                'type'              => 'textarea',
            )
        );

        chique_register_option( $wp_customize, array(
                'name'              => 'chique_testimonial_title_' . $i,
                'sanitize_callback' => 'sanitize_text_field',
                'active_callback'   => 'chique_is_image_testimonial_active',
                'label'             => esc_html__( 'Name', 'chique-pro' ),
                'section'           => 'chique_testimonials',
                'type'              => 'text',
            )
        );

        chique_register_option( $wp_customize, array(
                'name'              => 'chique_testimonial_position_' . $i,
                'sanitize_callback' => 'sanitize_text_field',
                'active_callback'   => 'chique_is_cpt_testimonial_inactive',
                'label'             => esc_html__( 'Position', 'chique-pro' ),
                'section'           => 'chique_testimonials',
                'type'              => 'text',
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
    * @since Chique Pro 1.0
    */
    function chique_is_testimonial_active( $control ) {
        $enable = $control->manager->get_setting( 'chique_testimonial_option' )->value();

        //return true only if previwed page on customizer matches the type of content option selected
        return chique_check_section( $enable );
    }
endif;

if ( ! function_exists( 'chique_is_post_testimonial_active' ) ) :
    /**
    * Return true if page content is active
    *
    * @since Chique Pro 1.0
    */
    function chique_is_post_testimonial_active( $control ) {
        $type = $control->manager->get_setting( 'chique_testimonial_type' )->value();

        //return true only if previwed page on customizer matches the type of option selected
        return ( chique_is_testimonial_active( $control ) && 'post' === $type );
    }
endif;

if ( ! function_exists( 'chique_is_jetpack_testimonial_active' ) ) :
    /**
    * Return true if jetpack testimonial is active
    *
    * @since Chique Pro 1.0
    */
    function chique_is_jetpack_testimonial_active( $control ) {
        $type = $control->manager->get_setting( 'chique_testimonial_type' )->value();

        //return true only if previwed page on customizer matches the type of option selected
        return ( chique_is_testimonial_active( $control ) && 'jetpack-testimonial' === $type );
    }
endif;

if ( ! function_exists( 'chique_is_page_testimonial_active' ) ) :
    /**
    * Return true if page content is active
    *
    * @since Chique Pro 1.0
    */
    function chique_is_page_testimonial_active( $control ) {
        $type = $control->manager->get_setting( 'chique_testimonial_type' )->value();

        //return true only if previwed page on customizer matches the type of option selected
        return ( chique_is_testimonial_active( $control ) && 'page' === $type );
    }
endif;

if ( ! function_exists( 'chique_is_category_testimonial_active' ) ) :
    /**
    * Return true if page content is active
    *
    * @since Chique Pro 1.0
    */
    function chique_is_category_testimonial_active( $control ) {
        $type = $control->manager->get_setting( 'chique_testimonial_type' )->value();

        //return true only if previwed page on customizer matches the type of option selected
        return ( chique_is_testimonial_active( $control ) && 'category' === $type );
    }
endif;

if ( ! function_exists( 'chique_is_post_page_category_cpt_testimonial_active' ) ) :
    /**
    * Return true if custom option is not active
    *
    * @since Chique Pro 1.0
    */
    function chique_is_post_page_category_cpt_testimonial_active( $control ) {
        $type = $control->manager->get_setting( 'chique_testimonial_type' )->value();

        //return true only if previwed page on customizer matches the type of option selected
        return ( chique_is_testimonial_active( $control ) && 'custom' !== $type );
    }
endif;

if ( ! function_exists( 'chique_is_cpt_testimonial_inactive' ) ) :
    /**
    * Return true if cpt option is not active
    *
    * @since Chique Pro 1.0
    */
    function chique_is_cpt_testimonial_inactive( $control ) {
        $type = $control->manager->get_setting( 'chique_testimonial_type' )->value();

        //return true only if previwed page on customizer matches the type of option selected
        return ( chique_is_testimonial_active( $control ) && 'jetpack-testimonial' !== $type );
    }
endif;

if ( ! function_exists( 'chique_is_image_testimonial_active' ) ) :
    /**
    * Return true if page content is active
    *
    * @since Chique Pro 1.0
    */
    function chique_is_image_testimonial_active( $control ) {
        $type = $control->manager->get_setting( 'chique_testimonial_type' )->value();

        //return true only if previwed page on customizer matches the type of option selected
        return ( chique_is_testimonial_active( $control ) && 'custom' === $type );
    }
endif;

if ( ! function_exists( 'chique_is_testimonial_slider_active' ) ) :
    /**
    * Return true testimonial slider is selected is selected
    *
    * @since Chique Pro 1.0
    */
    function chique_is_testimonial_slider_active( $control ) {
        $slider = $control->manager->get_setting( 'chique_testimonial_slider' )->value();

        //return true only if previwed page on customizer matches the type of testimonial option selected and is or is not selected type
        return ( chique_is_testimonial_active( $control ) && $slider );
    }
endif;
