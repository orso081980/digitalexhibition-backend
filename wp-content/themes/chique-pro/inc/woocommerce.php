<?php
/**
 * Adding support for WooCommerce Plugin
 */

if ( ! class_exists( 'WooCommerce' ) ) {
    // Bail if WooCommerce is not installed
    return;
}


if ( ! function_exists( 'chique_woocommerce_setup' ) ) :
    /**
     * Sets up support for various WooCommerce features.
     */
    function chique_woocommerce_setup() {
        add_theme_support( 'woocommerce', array(
            'thumbnail_image_width' => 666,
        ) );

        if ( get_theme_mod( 'chique_product_gallery_zoom', 1 ) ) {
            add_theme_support('wc-product-gallery-zoom');
        }

        if ( get_theme_mod( 'chique_product_gallery_lightbox', 1 ) ) {
            add_theme_support('wc-product-gallery-lightbox');
        }

        if ( get_theme_mod( 'chique_product_gallery_slider', 1 ) ) {
            add_theme_support('wc-product-gallery-slider');
        }
    }
endif; //chique_woocommerce_setup
add_action( 'after_setup_theme', 'chique_woocommerce_setup' );


/**
 * Add WooCommerce Options to customizer
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_woocommerce_options( $wp_customize ) {
    chique_register_option( $wp_customize, array(
            'name'              => 'chique_woocommerce_layout',
            'default'           => 'no-sidebar-full-width',
            'sanitize_callback' => 'chique_sanitize_select',
            'description'       => esc_html__( 'Layout for WooCommerce Pages', 'chique-pro' ),
            'label'             => esc_html__( 'WooCommerce Layout', 'chique-pro' ),
            'section'           => 'chique_layout_options',
            'type'              => 'radio',
            'choices'           => array(
                'right-sidebar'         => esc_html__( 'Right Sidebar ( Content, Primary Sidebar )', 'chique-pro' ),
                'left-sidebar'          => esc_html__( 'Left Sidebar ( Primary Sidebar, Content )', 'chique-pro' ),
                'no-sidebar'            => esc_html__( 'No Sidebar', 'chique-pro' ),
                'no-sidebar-full-width' => esc_html__( 'No Sidebar: Full Width', 'chique-pro' ),
            ),
        )
    );

    // WooCommerce Options
    $wp_customize->add_section( 'chique_woocommerce_options', array(
        'title'       => esc_html__( 'WooCommerce Options', 'chique-pro' ),
        'panel'       => 'chique_theme_options',
        'description' => esc_html__( 'Since these options are added via theme support, you will need to save and refresh the customizer to view the full effect.', 'chique-pro' ),
    ) );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_shop_subtitle',
            'sanitize_callback' => 'wp_kses_post',
            'label'             => esc_html__( 'Shop Page Subtitle', 'chique-pro' ),
            'default'           => esc_html__( 'This is where you can add new products to your store.', 'chique-pro' ),
            'section'           => 'chique_woocommerce_options',
            'type'              => 'textarea',
        )
    );

    // WooCommerce Options
    chique_register_option( $wp_customize, array(
            'name'              => 'chique_product_gallery_zoom',
            'sanitize_callback' => 'chique_sanitize_checkbox',
            'default'           => 1,
            'label'             => esc_html__( 'Product Gallery Zoom', 'chique-pro' ),
            'section'           => 'chique_woocommerce_options',
            'custom_control'    => 'Chique_Toggle_Control',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_product_gallery_lightbox',
            'sanitize_callback' => 'chique_sanitize_checkbox',
            'default'           => 1,
            'label'             => esc_html__( 'Product Gallery Lightbox', 'chique-pro' ),
            'section'           => 'chique_woocommerce_options',
            'custom_control'    => 'Chique_Toggle_Control',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_product_gallery_slider',
            'sanitize_callback' => 'chique_sanitize_checkbox',
            'default'           => 1,
            'label'             => esc_html__( 'Product Gallery Slider', 'chique-pro' ),
            'section'           => 'chique_woocommerce_options',
            'custom_control'    => 'Chique_Toggle_Control',
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_shopping_cart',
            'sanitize_callback' => 'chique_sanitize_checkbox',
            'default'           => 0,
            'label'             => esc_html__( 'Header Shopping Cart', 'chique-pro' ),
            'section'           => 'chique_woocommerce_options',
            'custom_control'    => 'Chique_Toggle_Control',
        )
    );
    chique_register_option( $wp_customize, array(
            'name'              => 'chique_floating_shopping_cart',
            'sanitize_callback' => 'chique_sanitize_checkbox',
            'default'           => 0,
            'label'             => esc_html__( 'Floating Shopping Cart', 'chique-pro' ),
            'section'           => 'chique_woocommerce_options',
            'custom_control'    => 'Chique_Toggle_Control',
        )
    );
    chique_register_option( $wp_customize, array(
            'name'              => 'chique_full_width_layout',
            'sanitize_callback' => 'chique_sanitize_checkbox',
            'default'           => 1,
            'label'             => esc_html__( 'Full Width Layout', 'chique-pro' ),
            'section'           => 'chique_woocommerce_options',
            'custom_control'    => 'Chique_Toggle_Control',
        )
    );
}
add_action( 'customize_register', 'chique_woocommerce_options' );

function chique_woocommerce_hide_page_title() { 
    if ( is_shop() && chique_has_header_media_text() ) {
        return false;
    }

    return true;  
}
add_filter( 'woocommerce_show_page_title', 'chique_woocommerce_hide_page_title' );

/**
 * Make Shop Page Title dynamic
 */
function chique_woocommerce_shop_subtitle( $args ) {
    if ( is_shop() ) {
        return wp_kses_post( get_theme_mod( 'chique_shop_subtitle', esc_html__( 'This is where you can add new products to your store.', 'chique-pro' ) ) );
    }

    return $args;
}
add_filter( 'get_the_archive_description', 'chique_woocommerce_shop_subtitle', 20 ); 


/**
 * uses remove_action to remove the WooCommerce Wrapper and add_action to add Main Wrapper
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

if ( ! function_exists( 'chique_woocommerce_container_start' ) ) :
    function chique_woocommerce_container_start() {
        echo '<div class="singular-content-wrapper site-content-wrapper">';
    }
endif; //chique_woocommerce_start
add_action( 'woocommerce_before_main_content', 'chique_woocommerce_container_start', 10 );

if ( ! function_exists( 'chique_woocommerce_container_end' ) ) :
    function chique_woocommerce_container_end() {
        echo '</div><!-- .singular-content-wrapper -->';
    }
endif; //chique_woocommerce_end
add_action( 'woocommerce_sidebar', 'chique_woocommerce_container_end', 20 );


if ( ! function_exists( 'chique_woocommerce_start' ) ) :
    function chique_woocommerce_start() {
    	echo '<div id="primary" class="content-area"><main role="main" class="site-main woocommerce" id="main"><div class="woocommerce-posts-wrapper">';
    }
endif; //chique_woocommerce_start
add_action( 'woocommerce_before_main_content', 'chique_woocommerce_start', 20 );


if ( ! function_exists( 'chique_woocommerce_end' ) ) :
    function chique_woocommerce_end() {
    	echo '</div><!-- .woocommerce-posts-wrapper --></main><!-- #main --></div><!-- #primary -->';
    }
endif; //chique_woocommerce_end
add_action( 'woocommerce_after_main_content', 'chique_woocommerce_end', 20 );


function chique_woocommerce_shorting_start() {
	echo '<div class="woocommerce-shorting-wrapper">';
}
add_action( 'woocommerce_before_shop_loop', 'chique_woocommerce_shorting_start', 10 );


function chique_woocommerce_shorting_end() {
	echo '</div><!-- .woocommerce-shorting-wrapper -->';
}
add_action( 'woocommerce_before_shop_loop', 'chique_woocommerce_shorting_end', 40 );


function chique_woocommerce_product_container_start() {
	echo '<div class="product-container">';
}
add_action( 'woocommerce_before_shop_loop_item_title', 'chique_woocommerce_product_container_start', 20 );


function chique_woocommerce_product_container_end() {
	echo '</div><!-- .product-container -->';
}
add_action( 'woocommerce_after_shop_loop_item', 'chique_woocommerce_product_container_end', 20 );

/**
 * Remove breadcrumb from default position
 * Check template-parts/header/breadcrumb.php
 */
function chique_remove_wc_breadcrumbs() {
    remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
}
add_action( 'init', 'chique_remove_wc_breadcrumbs' );

if ( ! function_exists( 'chique_header_cart' ) ) {
    /**
     * Display Header Cart
     *
     * @since  1.0.0
     * @uses  chique_is_woocommerce_activated() check if WooCommerce is activated
     * @return void
     */
    function chique_header_cart() {
        if ( is_cart() ) {
            $class = 'current-menu-item';
        } else {
            $class = '';
        }
        ?>
        <div id="site-header-cart-wrapper" class="menu-wrapper">
            <ul id="site-header-cart" class="site-header-cart menu">
                <li class="<?php echo esc_attr( $class ); ?>">
                    <?php chique_cart_link(); ?>
                </li>
                <li>
                    <?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
                </li>
            </ul>
        </div>
        <?php
    }
}

if ( ! function_exists( 'chique_cart_link' ) ) {
    /**
     * Cart Link
     * Displayed a link to the cart including the number of items present and the cart total
     *
     * @return void
     * @since  1.0.0
     */
    function chique_cart_link() {
        ?>
            <a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'chique-pro' ); ?>"><span class="count"><?php echo wp_kses_data( sprintf( _n( '%d', '%d', WC()->cart->get_cart_contents_count(), 'chique-pro' ), WC()->cart->get_cart_contents_count() ) );?></span> <?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?></a>
        <?php
    }
}

/**
 * Disable the default WooCommerce stylesheet.
 *
 * Removing the default WooCommerce stylesheet and enqueing your own will
 * protect you during WooCommerce core updates.
 *
 * @link https://docs.woocommerce.com/document/disable-the-default-stylesheet/
 */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * WooCommerce specific scripts & stylesheets.
 *
 * @return void
 */
function chique_woocommerce_scripts() {
    $font_path   = WC()->plugin_url() . '/assets/fonts/';
    $inline_font = '@font-face {
            font-family: "star";
            src: url("' . $font_path . 'star.eot");
            src: url("' . $font_path . 'star.eot?#iefix") format("embedded-opentype"),
                url("' . $font_path . 'star.woff") format("woff"),
                url("' . $font_path . 'star.ttf") format("truetype"),
                url("' . $font_path . 'star.svg#star") format("svg");
            font-weight: normal;
            font-style: normal;
        }';

    wp_add_inline_style( 'chique-style', $inline_font );
}
add_action( 'wp_enqueue_scripts', 'chique_woocommerce_scripts' );

if ( ! function_exists( 'chique_woocommerce_product_columns_wrapper' ) ) {
    /**
     * Product columns wrapper.
     *
     * @return  void
     */
    function chique_woocommerce_product_columns_wrapper() {
        // Get option from Customizer=> WooCommerce=> Product Catlog=> Products per row.
        echo '<div class="columns-' . absint( get_option( 'woocommerce_catalog_columns', 3 ) ) . '">';
    }
}
add_action( 'woocommerce_before_shop_loop', 'chique_woocommerce_product_columns_wrapper', 40 );

if ( ! function_exists( 'chique_woocommerce_product_columns_wrapper_close' ) ) {
    /**
     * Product columns wrapper close.
     *
     * @return  void
     */
    function chique_woocommerce_product_columns_wrapper_close() {
        echo '</div>';
    }
}
add_action( 'woocommerce_after_shop_loop', 'chique_woocommerce_product_columns_wrapper_close', 40 );

/**
 * Include Woo Products Showcase
 */
require get_parent_theme_file_path( 'inc/customizer/woo-products-showcase.php' );

if ( ! function_exists( 'chique_header_mini_cart_refresh_number' ) ) {
    /**
     * Update Header Cart items number on add to cart
     */
    function chique_header_mini_cart_refresh_number( $fragments ){
        ob_start();
        ?>
        <span class="count"><?php echo wp_kses_data( sprintf( _n( '%d', '%d', WC()->cart->get_cart_contents_count(), 'chique-pro' ), WC()->cart->get_cart_contents_count() ) );?></span>
        <?php
            $fragments['.site-header-cart .count'] = ob_get_clean();
        return $fragments;
    }
}
add_filter( 'woocommerce_add_to_cart_fragments', 'chique_header_mini_cart_refresh_number' );

if ( ! function_exists( 'chique_header_mini_cart_refresh_amount' ) ) {
    /**
     * Update Header Cart amount on add to cart
     */
    function chique_header_mini_cart_refresh_amount( $fragments ){
        ob_start();
        ?>
        <span class="amount"><?php echo wp_kses_data( WC()->cart->get_cart_subtotal() ); ?></span>
        <?php
            $fragments['.site-header-cart .amount'] = ob_get_clean();
        return $fragments;
    }
}
add_filter( 'woocommerce_add_to_cart_fragments', 'chique_header_mini_cart_refresh_amount' );
