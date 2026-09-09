<?php
/**
 * Adding support for WooCommerce Products Showcase Option
 */

/**
 * Add WooCommerce Product Showcase Options to customizer
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_woo_products_showcase( $wp_customize ) {
   $wp_customize->add_section( 'chique_woo_products_showcase', array(
        'title' => esc_html__( 'WooCommerce Products Showcase', 'chique-pro' ),
        'panel' => 'chique_theme_options',
    ) );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_woo_products_showcase_option',
            'default'           => 'disabled',
            'sanitize_callback' => 'chique_sanitize_select',
            'choices'           => chique_section_visibility_options(),
            'label'             => esc_html__( 'Enable on', 'chique-pro' ),
            'section'           => 'chique_woo_products_showcase',
            'type'              => 'select',
        )
    );

    /* Woo Background */
    chique_register_option( $wp_customize, array(
            'name'              => 'chique_woo_products_showcase_bg_image',
            'sanitize_callback' => 'esc_url_raw',
            'active_callback'   => 'chique_is_woo_products_showcase_active',
            'custom_control'    => 'WP_Customize_Image_Control',
            'label'             => esc_html__( 'Background Image', 'chique-pro' ),
            'section'           => 'chique_woo_products_showcase',
        )
    );

    $wp_customize->add_setting( 'chique_woo_products_showcase_bg_position_x', array(
        'sanitize_callback' => 'chique_sanitize_woo_products_showcase_bg_position',
    ) );

    $wp_customize->add_setting( 'chique_woo_products_showcase_bg_position_y', array(
        'sanitize_callback' => 'chique_sanitize_woo_products_showcase_bg_position',
    ) );

    $wp_customize->add_control( new WP_Customize_Background_Position_Control( $wp_customize, 'chique_woo_products_showcase_bg_position', array(
        'label'           => esc_html__( 'Background Image Position', 'chique-pro' ),
        'active_callback' => 'chique_is_woo_products_showcase_bg_active',
        'section'         => 'chique_woo_products_showcase',
        'settings'        => array(
            'x' => 'chique_woo_products_showcase_bg_position_x',
            'y' => 'chique_woo_products_showcase_bg_position_y',
        ),
    ) ) );

    chique_register_option( $wp_customize, array(
        'name'              => 'chique_woo_products_showcase_bg_size',
        'default'           => 'auto',
        'description'       => esc_html__( 'In mobiles, Background Size is always cover', 'chique-pro' ),
        'sanitize_callback' => 'chique_sanitize_select',
        'active_callback'   => 'chique_is_woo_products_showcase_bg_active',
        'label'             => esc_html__( 'Desktop Background Image Size', 'chique-pro' ),
        'section'           => 'chique_woo_products_showcase',
        'type'              => 'select',
        'choices' => array(
            'auto'    => esc_html__( 'Original', 'chique-pro' ),
            'contain' => esc_html__( 'Fit to Screen', 'chique-pro' ),
            'cover'   => esc_html__( 'Fill Screen', 'chique-pro' ),
        ),
    ) );

    chique_register_option( $wp_customize, array(
        'name'              => 'chique_woo_products_showcase_bg_repeat',
        'default'           => 'repeat',
        'sanitize_callback' => 'chique_sanitize_select',
        'active_callback'   => 'chique_is_woo_products_showcase_bg_active',
        'label'             => esc_html__( 'Repeat Background Image', 'chique-pro' ),
        'type'              => 'select',
        'section'           => 'chique_woo_products_showcase',
        'choices'           => array(
            'no-repeat' =>  esc_html__( 'No Repeat', 'chique-pro' ),
            'repeat'    =>  esc_html__( 'Repeat both vertically and horizontally (The last image will be clipped if it does not fit)', 'chique-pro' ),
            'repeat-x'  =>  esc_html__( 'Repeat only horizontally', 'chique-pro' ),
            'repeat-y'  =>  esc_html__( 'Repeat only vertically', 'chique-pro' ),
        ),
    ) );

    chique_register_option( $wp_customize, array(
        'name'              => 'chique_woo_products_showcase_bg_attachment',
        'default'           => 1,
        'sanitize_callback' => 'chique_sanitize_checkbox',
        'active_callback'   => 'chique_is_woo_products_showcase_bg_active',
        'label'             => esc_html__( 'Scroll with Page', 'chique-pro' ),
        'section'           => 'chique_woo_products_showcase',
        'custom_control'    => 'chique_Toggle_Control',
    ) );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_woo_products_showcase_headline',
            'default'           => esc_html__( 'Our Store', 'chique-pro' ),
            'sanitize_callback' => 'wp_kses_post',
            'label'             => esc_html__( 'Headline', 'chique-pro' ),
            'active_callback'   => 'chique_is_woo_products_showcase_active',
            'section'           => 'chique_woo_products_showcase',
            'type'              => 'text',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_woo_products_showcase_subheadline',
            'default'           => esc_html__( 'Order Online', 'chique-pro' ),
            'sanitize_callback' => 'wp_kses_post',
            'label'             => esc_html__( 'Sub headline', 'chique-pro' ),
            'active_callback'   => 'chique_is_woo_products_showcase_active',
            'section'           => 'chique_woo_products_showcase',
            'type'              => 'textarea',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_woo_products_showcase_number',
            'default'           => 3,
            'sanitize_callback' => 'chique_sanitize_number_range',
            'active_callback'   => 'chique_is_woo_products_showcase_active',
            'description'       => esc_html__( 'Save and refresh the page if No. of Products is changed. Set -1 to display all', 'chique-pro' ),
            'input_attrs'       => array(
                'style' => 'width: 50px;',
                'min'   => -1,
            ),
            'label'             => esc_html__( 'No of Products', 'chique-pro' ),
            'section'           => 'chique_woo_products_showcase',
            'type'              => 'number',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'               => 'chique_woo_products_showcase_columns',
            'default'            => 3,
            'sanitize_callback'  => 'chique_sanitize_number_range',
            'active_callback'    => 'chique_is_woo_products_showcase_active',
            'description'        => esc_html__( 'Theme supports up to 6 columns', 'chique-pro' ),
            'label'              => esc_html__( 'No of Columns', 'chique-pro' ),
            'section'            => 'chique_woo_products_showcase',
            'type'               => 'number',
            'input_attrs'       => array(
                'style' => 'width: 50px;',
                'min'   => 1,
                'max'   => 6,
            ),
        )
    );

    chique_register_option( $wp_customize, array(
            'name'               => 'chique_woo_products_showcase_paginate',
            'default'            => 'false',
            'sanitize_callback'  => 'chique_sanitize_select',
            'active_callback'    => 'chique_is_woo_products_showcase_active',
            'label'              => esc_html__( 'Paginate', 'chique-pro' ),
            'section'            => 'chique_woo_products_showcase',
            'type'               => 'radio',
            'choices'            => array(
                'false' => esc_html__( 'No', 'chique-pro' ),
                'true' => esc_html__( 'Yes', 'chique-pro' ),
            ),
        )
    );

    chique_register_option( $wp_customize, array(
            'name'               => 'chique_woo_products_showcase_orderby',
            'default'            => 'title',
            'sanitize_callback'  => 'chique_sanitize_select',
            'active_callback'    => 'chique_is_woo_products_showcase_active',
            'label'              => esc_html__( 'Order By', 'chique-pro' ),
            'section'            => 'chique_woo_products_showcase',
            'type'               => 'select',
            'choices'            => array(
                'date'       => esc_html__( 'Date - The date the product was published', 'chique-pro' ),
                'id'         => esc_html__( 'ID - The post ID of the product', 'chique-pro' ),
                'menu_order' => esc_html__( 'Menu Order - The Menu Order, if set (lower numbers display first)', 'chique-pro' ),
                'popularity' => esc_html__( 'Popularity - The number of purchases', 'chique-pro' ),
                'rand'       => esc_html__( 'Random', 'chique-pro' ),
                'rating'     => esc_html__( 'Rating - The average product rating', 'chique-pro' ),
                'title'      => esc_html__( 'Title - The product title', 'chique-pro' ),
            ),
        )
    );

    chique_register_option( $wp_customize, array(
            'name'               => 'chique_woo_products_showcase_products_filter',
            'default'            => 'none',
            'sanitize_callback'  => 'chique_sanitize_select',
            'active_callback'    => 'chique_is_woo_products_showcase_active',
            'label'              => esc_html__( 'Products Filter', 'chique-pro' ),
            'section'            => 'chique_woo_products_showcase',
            'type'               => 'radio',
            'choices'            => array(
                'none'         => esc_html__( 'None', 'chique-pro' ),
                'on_sale'      => esc_html__( 'Retrieve on sale products', 'chique-pro' ),
                'best_selling' => esc_html__( 'Retrieve best selling products', 'chique-pro' ),
                'top_rated'    => esc_html__( 'Retrieve top rated products', 'chique-pro' ),
            ),
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_round_product_thumbnail',
            'sanitize_callback' => 'chique_sanitize_checkbox',
            'active_callback'   => 'chique_is_woo_products_showcase_active',
            'label'             => esc_html__( 'Make product image round', 'chique-pro' ),
            'section'           => 'chique_woo_products_showcase',
            'custom_control'    => 'chique_Toggle_Control',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_woo_products_showcase_featured',
            'sanitize_callback' => 'chique_sanitize_checkbox',
            'active_callback'   => 'chique_is_woo_products_showcase_active',
            'label'             => esc_html__( 'Show only Products that are marked as Featured Products', 'chique-pro' ),
            'section'           => 'chique_woo_products_showcase',
            'custom_control'    => 'chique_Toggle_Control',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'               => 'chique_woo_products_showcase_order',
            'default'            => 'ASC',
            'sanitize_callback'  => 'chique_sanitize_select',
            'active_callback'    => 'chique_is_woo_products_showcase_active',
            'label'              => esc_html__( 'Order', 'chique-pro' ),
            'section'            => 'chique_woo_products_showcase',
            'type'               => 'radio',
            'choices'            => array(
                'ASC'  => esc_html__( 'Ascending', 'chique-pro' ),
                'DESC' => esc_html__( 'Descending', 'chique-pro' ),
            ),
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_woo_products_showcase_skus',
            'description'       => esc_html__( 'Comma separated list of product SKUs', 'chique-pro' ),
            'sanitize_callback' => 'sanitize_text_field',
            'active_callback'   => 'chique_is_woo_products_showcase_active',
            'label'             => esc_html__( 'SKUs', 'chique-pro' ),
            'section'           => 'chique_woo_products_showcase',
            'type'              => 'text',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_woo_products_showcase_category',
            'description'       => esc_html__( 'Comma separated list of category slugs', 'chique-pro' ),
            'sanitize_callback' => 'sanitize_text_field',
            'active_callback'   => 'chique_is_woo_products_showcase_active',
            'label'             => esc_html__( 'Category', 'chique-pro' ),
            'section'           => 'chique_woo_products_showcase',
            'type'              => 'textarea',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_woo_products_showcase_text',
            'default'           => esc_html__( 'Go to Shop Page', 'chique-pro' ),
            'sanitize_callback' => 'sanitize_text_field',
            'active_callback'   => 'chique_is_woo_products_showcase_active',
            'label'             => esc_html__( 'Button Text', 'chique-pro' ),
            'section'           => 'chique_woo_products_showcase',
            'type'              => 'text',
        )
    );

    $shop_page_url = get_permalink( wc_get_page_id( 'shop' ) );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_woo_products_showcase_link',
            'default'           =>  esc_url( $shop_page_url ),
            'sanitize_callback' => 'esc_url_raw',
            'active_callback'   => 'chique_is_woo_products_showcase_active',
            'label'             => esc_html__( 'Button Link', 'chique-pro' ),
            'section'           => 'chique_woo_products_showcase',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_woo_products_showcase_target',
            'sanitize_callback' => 'chique_sanitize_checkbox',
            'active_callback'   => 'chique_is_woo_products_showcase_active',
            'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
            'section'           => 'chique_woo_products_showcase',
            'custom_control'    => 'chique_Toggle_Control',
        )
    );
}
add_action( 'customize_register', 'chique_woo_products_showcase', 10 );

/** Active Callback Functions **/
if( ! function_exists( 'chique_is_woo_products_showcase_active' ) ) :
    /**
    * Return true if featured content is active
    *
    * @since Chique Pro 1.0
    */
    function chique_is_woo_products_showcase_active( $control ) {
        $enable = $control->manager->get_setting( 'chique_woo_products_showcase_option' )->value();

        return ( chique_check_section( $enable ) );
    }
endif;

if ( ! function_exists( 'chique_is_woo_products_showcase_bg_active' ) ) :
    /**
    * Return true if background is set
    *
    * @since Chique Pro 1.0
    */
    function chique_is_woo_products_showcase_bg_active( $control ) {
        $bg_image = $control->manager->get_setting( 'chique_woo_products_showcase_bg_image' )->value();

        return ( chique_is_woo_products_showcase_active( $control ) && '' !== $bg_image );
    }
endif;
