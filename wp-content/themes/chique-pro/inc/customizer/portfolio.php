<?php
/**
 * Add Portfolio Settings in Customizer
 *
 * @package Chique
 */

/**
 * Add portfolio options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_portfolio_options( $wp_customize ) {
    // Add note to Jetpack Portfolio Section
    chique_register_option( $wp_customize, array(
            'name'              => 'chique_jetpack_portfolio_cpt_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Chique_Note_Control',
            'label'             => sprintf( esc_html__( 'For Portfolio Options for Chique Theme, go %1$shere%2$s', 'chique-pro' ),
                 '<a href="javascript:wp.customize.section( \'chique_portfolio\' ).focus();">',
                 '</a>'
            ),
            'section'           => 'jetpack_portfolio',
            'type'              => 'description',
            'priority'          => 1,
        )
    );

	$wp_customize->add_section( 'chique_portfolio', array(
            'panel'    => 'chique_theme_options',
            'title'    => esc_html__( 'Portfolio', 'chique-pro' ),
        )
    );

    chique_register_option( $wp_customize, array(
			'name'              => 'chique_portfolio_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique-pro' ),
			'section'           => 'chique_portfolio',
			'type'              => 'select',
		)
	);

    chique_register_option( $wp_customize, array(
			'name'              => 'chique_portfolio_content_layout',
			'default'           => 'layout-three',
			'sanitize_callback' => 'chique_sanitize_select',
            'active_callback'   => 'chique_is_portfolio_active',
			'choices'           => chique_sections_layout_options(),
			'label'             => esc_html__( 'Select Portfolio Layout', 'chique-pro' ),
			'section'           => 'chique_portfolio',
			'type'              => 'select',
		)
	);

    $type = chique_section_type_options();

    $type['jetpack-portfolio'] = esc_html__( 'Custom Post Type', 'chique-pro' );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_portfolio_type',
            'default'           => 'category',
            'sanitize_callback' => 'chique_sanitize_select',
            'active_callback'   => 'chique_is_portfolio_active',
            'choices'           => $type,
            'description'       => sprintf( esc_html__( 'For Custom Post Type Content, install %1$sEssential Content Types%2$s Plugin with Portfolio Content Type Enabled', 'chique-pro' ),
                '<a target="_blank" href="https://wordpress.org/plugins/essential-content-types/">',
                '</a>'
            ),
            'label'             => esc_html__( 'Select Type', 'chique-pro' ),
            'section'           => 'chique_portfolio',
            'type'              => 'select',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_portfolio_cpt_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Chique_Note_Control',
            'active_callback'   => 'chique_is_jetpack_portfolio_active',
            /* translators: 1: <a>/link tag start, 2: </a>/link tag close. */
			'label'             => sprintf( esc_html__( 'For CPT heading and sub-heading, go %1$shere%2$s', 'chique-pro' ),
                 '<a href="javascript:wp.customize.control( \'jetpack_portfolio_title\' ).focus();">',
                 '</a>'
            ),
            'section'           => 'chique_portfolio',
            'type'              => 'description',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_portfolio_headline',
            'default'           => esc_html__( 'Portfolio', 'chique-pro' ),
            'sanitize_callback' => 'wp_kses_post',
            'label'             => esc_html__( 'Headline', 'chique-pro' ),
            'active_callback'   => 'chique_is_portfolio_active',
            'section'           => 'chique_portfolio',
            'type'              => 'text',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_portfolio_subheadline',
            'sanitize_callback' => 'wp_kses_post',
            'label'             => esc_html__( 'Sub headline', 'chique-pro' ),
            'active_callback'   => 'chique_is_portfolio_active',
            'section'           => 'chique_portfolio',
            'type'              => 'text',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_portfolio_meta_show',
            'default'           => 'show-meta',
            'sanitize_callback' => 'chique_sanitize_select',
            'active_callback'   => 'chique_is_post_category_cpt_active',
            'choices'           => chique_meta_show(),
            'label'             => esc_html__( 'Display Meta', 'chique-pro' ),
            'section'           => 'chique_portfolio',
            'type'              => 'select',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_portfolio_number',
            'default'           => '6',
            'sanitize_callback' => 'chique_sanitize_number_range',
            'active_callback'   => 'chique_is_portfolio_active',
            'label'             => esc_html__( 'Number of items to show', 'chique-pro' ),
            'section'           => 'chique_portfolio',
            'type'              => 'number',
            'input_attrs'       => array(
                'style'             => 'width: 100px;',
                'min'               => 0,
            ),
        )
    );

    $type = chique_content_show();

    unset( $type['full-content'] );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_portfolio_select_category',
            'sanitize_callback' => 'chique_sanitize_category_list',
            'active_callback'   => 'chique_is_category_portfolio_active',
            'custom_control'    => 'Chique_Multi_Cat',
            'label'             => esc_html__( 'Select Categories', 'chique-pro' ),
            'name'              => 'chique_portfolio_select_category',
            'section'           => 'chique_portfolio',
            'type'              => 'dropdown-categories',
        )
    );

    $number = get_theme_mod( 'chique_portfolio_number', 6 );

    for ( $i = 1; $i <= $number ; $i++ ) {
        //for featured post content
        chique_register_option( $wp_customize, array(
                'name'              => 'chique_portfolio_post_' . $i,
                'sanitize_callback' => 'chique_sanitize_post',
                'active_callback'   => 'chique_is_post_portfolio_active',
                'input_attrs'       => array(
                'style'             => 'width: 100px;'
                ),
                'label'             => esc_html__( 'Post', 'chique-pro' ) . ' ' . $i ,
                'section'           => 'chique_portfolio',
                'choices'           => chique_generate_post_array(),
                'type'              => 'select',
            )
        );

        //for CPT
        chique_register_option( $wp_customize, array(
                'name'              => 'chique_portfolio_cpt_' . $i,
                'sanitize_callback' => 'chique_sanitize_post',
                'active_callback'   => 'chique_is_jetpack_portfolio_active',
                'label'             => esc_html__( 'Portfolio', 'chique-pro' ) . ' ' . $i ,
                'section'           => 'chique_portfolio',
                'type'              => 'select',
                'choices'           => chique_generate_post_array( 'jetpack-portfolio' ),
            )
        );

        chique_register_option( $wp_customize, array(
                'name'              => 'chique_portfolio_page_' . $i,
                'sanitize_callback' => 'chique_sanitize_post',
                'active_callback'   => 'chique_is_page_portfolio_active',
                'label'             => esc_html__( 'Page', 'chique-pro' ) . ' ' . $i ,
                'section'           => 'chique_portfolio',
                'type'              => 'dropdown-pages',
            )
        );

        chique_register_option( $wp_customize, array(
                'name'              => 'chique_portfolio_note_' . $i,
                'sanitize_callback' => 'sanitize_text_field',
                'custom_control'    => 'Chique_Note_Control',
                'active_callback'   => 'chique_is_image_portfolio_active',
                'label'             => esc_html__( 'Portfolio #', 'chique-pro' ) .  $i,
                'section'           => 'chique_portfolio',
                'type'              => 'description',
            )
        );

        chique_register_option( $wp_customize, array(
                'name'              => 'chique_portfolio_image_' . $i,
                'custom_control'      => 'WP_Customize_Image_Control',
                'sanitize_callback' => 'chique_sanitize_image',
                'active_callback'   => 'chique_is_image_portfolio_active',
                'label'             => esc_html__( 'Image', 'chique-pro' ),
                'section'           => 'chique_portfolio',
            )
        );

        chique_register_option( $wp_customize, array(
                'name'              => 'chique_portfolio_link_' . $i,
                'sanitize_callback' => 'esc_url_raw',
                'active_callback'   => 'chique_is_image_portfolio_active',
                'label'             => esc_html__( 'Link', 'chique-pro' ),
                'section'           => 'chique_portfolio',
            )
        );

        chique_register_option( $wp_customize, array(
                'name'              => 'chique_portfolio_target_' . $i,
                'sanitize_callback' => 'chique_sanitize_checkbox',
                'active_callback'   => 'chique_is_image_portfolio_active',
                'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
                'section'           => 'chique_portfolio',
                'custom_control'    => 'Chique_Toggle_Control',
            )
        );

        chique_register_option( $wp_customize, array(
                'name'              => 'chique_portfolio_title_' . $i,
                'sanitize_callback' => 'sanitize_text_field',
                'active_callback'   => 'chique_is_image_portfolio_active',
                'label'             => esc_html__( 'Title', 'chique-pro' ),
                'section'           => 'chique_portfolio',
                'type'              => 'text',
            )
        );
    } // End for().
}
add_action( 'customize_register', 'chique_portfolio_options' );

/**
 * Active Callback Functions
 */
if ( ! function_exists( 'chique_is_portfolio_active' ) ) :
    /**
    * Return true if portfolio is active
    *
    * @since Chique Pro 1.0
    */
    function chique_is_portfolio_active( $control ) {
        $enable = $control->manager->get_setting( 'chique_portfolio_option' )->value();

        //return true only if previwed page on customizer matches the type of content option selected
        return ( chique_check_section( $enable ) );
    }
endif;

if ( ! function_exists( 'chique_is_post_portfolio_active' ) ) :
    /**
    * Return true if page content is active
    *
    * @since Chique Pro 1.0
    */
    function chique_is_post_portfolio_active( $control ) {
        $type = $control->manager->get_setting( 'chique_portfolio_type' )->value();

        //return true only if previwed page on customizer matches the type of slider option selected
        return ( chique_is_portfolio_active( $control ) && 'post' === $type );
    }
endif;

if ( ! function_exists( 'chique_is_jetpack_portfolio_active' ) ) :
    /**
    * Return true if jetpack portfolio is active
    *
    * @since Chique Pro 1.0
    */
    function chique_is_jetpack_portfolio_active( $control ) {
        $type = $control->manager->get_setting( 'chique_portfolio_type' )->value();

        //return true only if previwed page on customizer matches the type of slider option selected
        return ( chique_is_portfolio_active( $control ) && 'jetpack-portfolio' === $type );
    }
endif;

if ( ! function_exists( 'chique_is_page_portfolio_active' ) ) :
    /**
    * Return true if page content is active
    *
    * @since Chique Pro 1.0
    */
    function chique_is_page_portfolio_active( $control ) {
        $type = $control->manager->get_setting( 'chique_portfolio_type' )->value();

        //return true only if previwed page on customizer matches the type of slider option selected
        return ( chique_is_portfolio_active( $control ) && 'page' === $type );
    }
endif;

if ( ! function_exists( 'chique_is_category_portfolio_active' ) ) :
    /**
    * Return true if page content is active
    *
    * @since Chique Pro 1.0
    */
    function chique_is_category_portfolio_active( $control ) {
        $type = $control->manager->get_setting( 'chique_portfolio_type' )->value();

        //return true only if previwed page on customizer matches the type of slider option selected
        return ( chique_is_portfolio_active( $control ) && 'category' === $type );
    }
endif;

if ( ! function_exists( 'chique_is_image_portfolio_active' ) ) :
    /**
    * Return true if page content is active
    *
    * @since Chique Pro 1.0
    */
    function chique_is_image_portfolio_active( $control ) {
        $type = $control->manager->get_setting( 'chique_portfolio_type' )->value();

        //return true only if previwed page on customizer matches the type of slider option selected
        return ( chique_is_portfolio_active( $control ) && 'custom' === $type );
    }
endif;

if ( ! function_exists( 'chique_is_post_category_cpt_active' ) ) :
    /**
    * Return true if page content is active
    *
    * @since Chique Pro 1.0
    */
    function chique_is_post_category_cpt_active( $control ) {
        $type = $control->manager->get_setting( 'chique_portfolio_type' )->value();

        //return true only if previwed page on customizer matches the type of slider option selected
        return ( chique_is_portfolio_active( $control ) && ( 'post' === $type || 'jetpack-portfolio' === $type || 'category' === $type ) );
    }
endif;
