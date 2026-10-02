<?php
/**
 * Customizer functionality
 *
 * @package Chique
 */

/**
 * Sets up the WordPress core custom header and custom background features.
 *
 * @since Chique Pro 1.0
 *
 * @see chique_header_style()
 */
function chique_custom_header_and_background() {
	$color_scheme             = chique_get_color_scheme();
	$default_background_color = trim( $color_scheme[0], '#' );
	$default_text_color       = trim( $color_scheme[1], '#' );

	/**
	 * Filter the arguments used when adding 'custom-background' support in Foodie World.
	 *
	 * @since Chique Pro 1.0
	 *
	 * @param array $args {
	 *     An array of custom-background support arguments.
	 *
	 *     @type string $default-color Default color of the background.
	 * }
	 */
	add_theme_support( 'custom-background', apply_filters( 'chique_custom_background_args', array(
		'default-color'    => $default_background_color,
	) ) );

	/**
	 * Filter the arguments used when adding 'custom-header' support in Foodie World.
	 *
	 * @since Chique Pro 1.0
	 *
	 * @param array $args {
	 *     An array of custom-header support arguments.
	 *
	 *     @type string $default-text-color Default color of the header text.
	 *     @type int      $width            Width in pixels of the custom header image. Default 1200.
	 *     @type int      $height           Height in pixels of the custom header image. Default 280.
	 *     @type bool     $flex-height      Whether to allow flexible-height header images. Default true.
	 *     @type callable $wp-head-callback Callback function used to style the header image and text
	 *                                      displayed on the blog.
	 * }
	 */
	add_theme_support( 'custom-header', apply_filters( 'chique_custom_header_args', array(
		'default-image'      	 => get_parent_theme_file_uri( '/assets/images/header.jpg' ),
		'default-text-color'     => $default_text_color,
		'width'                  => 1920,
		'height'                 => 540,
		'flex-height'            => true,
		'flex-height'            => true,
		'wp-head-callback'       => 'chique_header_style',
		'video'                  => true,
	) ) );

	$default_headers_args = array(
		'main' => array(
			'thumbnail_url' => get_stylesheet_directory_uri() . '/assets/images/header-thumb-275x77.jpg',
			'url'           => get_stylesheet_directory_uri() . '/assets/images/header.jpg',
		),
	);

	register_default_headers( $default_headers_args );
}
add_action( 'after_setup_theme', 'chique_custom_header_and_background' );

function chique_color_options() {
	//Color Scheme
	$default_color = chique_get_color_scheme();

	// We do not add Background Color and Header Text Color here as if comes from WordPress Core
	return apply_filters( 'chique_color_options', array(
			'page_background_color'           => array(
				'label'   => esc_html__( 'Page Background Color', 'chique-pro' ),
				'default' => $default_color[2],
			),
			'secondary_background_color' => array(
				'label'   => esc_html__( 'Secondary Background Color', 'chique-pro' ),
				'default' => $default_color[3],
			),
			'main_text_color'                 => array(
				'label'   => esc_html__( 'Main Text Color', 'chique-pro' ),
				'default' => $default_color[4],
			),
			'heading_text_color'              => array(
				'label'   => esc_html__( 'Heading Text Color', 'chique-pro' ),
				'default' => $default_color[5],
			),
			'link_color'                      => array(
				'label'   => esc_html__( 'Link Color', 'chique-pro' ),
				'default' => $default_color[6],
			),
			'link_hover_color'                => array(
				'label'   => esc_html__( 'Link Hover Color', 'chique-pro' ),
				'default' => $default_color[7],
			),
			'secondary_link_color'            => array(
				'label'   => esc_html__( 'Secondary Link Color', 'chique-pro' ),
				'default' => $default_color[8],
			),
			'button_background_color'         => array(
				'label'   => esc_html__( 'Button Background Color', 'chique-pro' ),
				'default' => $default_color[9],
			),
			'button_text_color'               => array(
				'label'   => esc_html__( 'Button Text Color', 'chique-pro' ),
				'default' => $default_color[10],
			),
			'button_hover_background_color'   => array(
				'label'   => esc_html__( 'Button Hover Background Color', 'chique-pro' ),
				'default' => $default_color[11],
			),
			'button_hover_text_color'         => array(
				'label'   => esc_html__( 'Button Hover Text Color', 'chique-pro' ),
				'default' => $default_color[12],
			),
			'border_color'                    => array(
				'label'   => esc_html__( 'Border Color', 'chique-pro' ),
				'default' => $default_color[13],
			),
			'tertiary_background_color'                    => array(
				'label'   => esc_html__( 'Tertiary Background Color', 'chique-pro' ),
				'default' => $default_color[14],
			),
			'text_color_with_background'                    => array(
				'label'   => esc_html__( 'Text Color with Background', 'chique-pro' ),
				'default' => $default_color[15],
			),
			'slider_color'                    => array(
				'label'   => esc_html__( 'Slider/Custom Header Text Color', 'chique-pro' ),
				'default' => $default_color[16],
			),
			'slider_hover_color'                    => array(
				'label'   => esc_html__( 'Slider/Custom Header Hover Color', 'chique-pro' ),
				'default' => $default_color[17],
			),
			'slider_content_color'                    => array(
				'label'   => esc_html__( 'Slider/Custom Header Content Color', 'chique-pro' ),
				'default' => $default_color[18],
			),
			'secondary_link_hover_color'            => array(
				'label'   => esc_html__( 'Secondary Link Hover Color', 'chique-pro' ),
				'default' => $default_color[19],
			),
			'gradient_background_color_first'            => array(
				'label'   => esc_html__( 'Gradient Background Color First', 'chique-pro' ),
				'default' => $default_color[20],
			),
			'gradient_background_color_second'            => array(
				'label'   => esc_html__( 'Gradient Background Color Second', 'chique-pro' ),
				'default' => $default_color[21],
			),
		)
	);
}

/**
 * Registers color schemes for Foodie World.
 *
 * Can be filtered with {@see 'chique_color_schemes'}.
 *
 * 0. Background Color
 * 1. Header Text Color
 * 2. Page Background Color
 * 3. Secondary Background Color
 * 4. Main Text Color
 * 5. Heading Text Color
 * 6. Link Color
 * 7. Link Hover Color
 * 8. Secondary Link Color
 * 9. Button Background Color
 * 10. Button Text Color
 * 11. Button Hover Background Color
 * 12. Button Hover Text Color
 * 13. Border Color
 * 14. Tertiary Background Color
 * 15. Text Color with background
 * 16. Slider/Header Media Color
 * 17. Slider/Header Media Hover Color
 * 18. Slider/Header Media Content Color
 * 19. Secondary Link Hover Color
 * 20. Gradient Background Color First
 * 21. Gradient Background Color Second

 *
 * @since Chique Pro 1.0
 *
 * @return array An associative array of color scheme options.
 */
function chique_get_color_schemes() {
	/**
	 * Filter the color schemes registered for use with Foodie World.
	 *
	 * The default schemes include 'default', dark', 'gray' and 'yellow'.
	 *
	 * @since Chique Pro 1.0
	 *
	 * @param array $schemes {
	 *     Associative array of color schemes data.
	 *
	 *     @type array $slug {
	 *         Associative array of information for setting up the color scheme.
	 *
	 *         @type string $label  Color scheme label.
	 *         @type array  $colors HEX codes for default colors prepended with a hash symbol ('#').
	 *                              Colors are defined in the following order: Main background, page
	 *                              background, link, main text, secondary text.
	 *     }
	 * }
	 */
	return apply_filters( 'chique_color_schemes', array(
		'default' => array(
			'label'  => esc_html__( 'Default', 'chique-pro' ),
			'colors' => array(
				'#ffffff', /* Main Background Color */
				'#000000', /* Header Text Color */
				'#ffffff', /* Page Background Color */
				'#f9f9f9', /* Secondary Background Color */
				'#666666', /* Main Text Color */
				'#000000', /* Heading Text Color */
				'#6bbcba', /* Link Color */
				'#000000', /* Link Hover Color */
				'#000000', /* Secondary Link Color */
				'#e186a2', /* Button Background Color */
				'#ffffff', /* Button Text Color */
				'#222222', /* Button Hover Background Color */
				'#ffffff', /* Button Hover Text Color */
				'#ebebeb', /* Border Color */
				'#f2f5f6', /* Tertiary Background Color */
				'#ffffff', /* Text Color With Background */
				'#ffffff', /* Slider/Header Media Color*/
				'#6bbcba', /* Slider/Header Media Hover Color */
				'#ffffff', /* Slider/Header Media Content Color */
				'#6bbcba', /* Secondary Link Hover Color */
				'#dbfbf8', /* Gradient Background Color First */
				'#f3f9e4', /* Gradient Background Color Second */
			),
		),
		'dark' => array(
			'label'  => esc_html__( 'Dark', 'chique-pro' ),
			'colors' => array(
				'#000000', /* Main Background Color */
				'#ffffff', /* Header Text Color */
				'#000000', /* Page Background Color */
				'#2d2d2d', /* Secondary Background Color */
				'#999999', /* Main Text Color */
				'#ffffff', /* Heading Text Color */
				'#fbc439', /* Link Color */
				'#ffffff', /* Link Hover Color */
				'#ffffff', /* Secondary Link Color */
				'#e186a2', /* Button Background Color */
				'#ffffff', /* Button Text Color */
				'#6a6a6a', /* Button Hover Background Color */
				'#ffffff', /* Button Hover Text Color */
				'#282828', /* Border Color */
				'#1d1d1d', /* Tertiary Background Color */
				'#ffffff', /* Text Color With Background */
				'#ffffff', /* Slider/Header Media Color*/
				'#fbc439', /* Slider/Header Media Hover Color */
				'#ffffff', /* Slider/Header Media Content Color */
				'#fbc439', /* Secondary Link Hover Color */
				'#2d2d2d', /* Gradient Background Color First */
				'#2d2d2d', /* Gradient Background Color Second */
			),
		),
		'photography' => array(
			'label'  => esc_html__( 'Photography', 'chique-pro' ),
			'colors' => array(
				'#ffffff', /* Main Background Color */
				'#000000', /* Header Text Color */
				'#ffffff', /* Page Background Color */
				'#f9f9f9', /* Secondary Background Color */
				'#666666', /* Main Text Color */
				'#000000', /* Heading Text Color */
				'#434343', /* Link Color */
				'#000000', /* Link Hover Color */
				'#000000', /* Secondary Link Color */
				'#111111', /* Button Background Color */
				'#ffffff', /* Button Text Color */
				'#222222', /* Button Hover Background Color */
				'#ffffff', /* Button Hover Text Color */
				'#ebebeb', /* Border Color */
				'#f2f5f6', /* Tertiary Background Color */
				'#ffffff', /* Text Color With Background */
				'#ffffff', /* Slider/Header Media Color*/
				'#434343', /* Slider/Header Media Hover Color */
				'#ffffff', /* Slider/Header Media Content Color */
				'#434343', /* Secondary Link Hover Color */
				'#fafafa', /* Gradient Background Color First */
				'#fafafa', /* Gradient Background Color Second */
			),
		),
		'corporate' => array(
			'label'  => esc_html__( 'Corporate', 'chique-pro' ),
			'colors' => array(
				'#ffffff', /* Main Background Color */
				'#000000', /* Header Text Color */
				'#ffffff', /* Page Background Color */
				'#f9f9f9', /* Secondary Background Color */
				'#666666', /* Main Text Color */
				'#000000', /* Heading Text Color */
				'#143ec2', /* Link Color */
				'#000000', /* Link Hover Color */
				'#000000', /* Secondary Link Color */
				'#143ec2', /* Button Background Color */
				'#ffffff', /* Button Text Color */
				'#222222', /* Button Hover Background Color */
				'#ffffff', /* Button Hover Text Color */
				'#ebebeb', /* Border Color */
				'#f2f5f6', /* Tertiary Background Color */
				'#ffffff', /* Text Color With Background */
				'#ffffff', /* Slider/Header Media Color*/
				'#143ec2', /* Slider/Header Media Hover Color */
				'#ffffff', /* Slider/Header Media Content Color */
				'#143ec2', /* Secondary Link Hover Color */
				'#f9f9f9', /* Gradient Background Color First */
				'#f9f9f9', /* Gradient Background Color Second */
			),
		),
		'ecommerce' => array(
			'label'  => esc_html__( 'eCommerce', 'chique-pro' ),
			'colors' => array(
				'#ffffff', /* Main Background Color */
				'#000000', /* Header Text Color */
				'#ffffff', /* Page Background Color */
				'#f9f9f9', /* Secondary Background Color */
				'#666666', /* Main Text Color */
				'#000000', /* Heading Text Color */
				'#ca2728', /* Link Color */
				'#000000', /* Link Hover Color */
				'#000000', /* Secondary Link Color */
				'#ca2728', /* Button Background Color */
				'#ffffff', /* Button Text Color */
				'#222222', /* Button Hover Background Color */
				'#ffffff', /* Button Hover Text Color */
				'#ebebeb', /* Border Color */
				'#f2f5f6', /* Tertiary Background Color */
				'#ffffff', /* Text Color With Background */
				'#ffffff', /* Slider/Header Media Color*/
				'#ca2728', /* Slider/Header Media Hover Color */
				'#ffffff', /* Slider/Header Media Content Color */
				'#ca2728', /* Secondary Link Hover Color */
				'#fafafa', /* Gradient Background Color First */
				'#fafafa', /* Gradient Background Color Second */
			),
		),
		'music' => array(
			'label'  => esc_html__( 'Music', 'chique-pro' ),
			'colors' => array(
				'#000000', /* Main Background Color */
				'#ffffff', /* Header Text Color */
				'#000000', /* Page Background Color */
				'#2d2d2d', /* Secondary Background Color */
				'#999999', /* Main Text Color */
				'#ffffff', /* Heading Text Color */
				'#f2b104', /* Link Color */
				'#ffffff', /* Link Hover Color */
				'#ffffff', /* Secondary Link Color */
				'#f2b104', /* Button Background Color */
				'#020202', /* Button Text Color */
				'#6a6a6a', /* Button Hover Background Color */
				'#ffffff', /* Button Hover Text Color */
				'#1d1d1d', /* Border Color */
				'#1d1d1d', /* Tertiary Background Color */
				'#ffffff', /* Text Color With Background */
				'#ffffff', /* Slider/Header Media Color*/
				'#f2b104', /* Slider/Header Media Hover Color */
				'#ffffff', /* Slider/Header Media Content Color */
				'#f2b104', /* Secondary Link Hover Color */
				'#2d2d2d', /* Gradient Background Color First */
				'#2d2d2d', /* Gradient Background Color Second */
			),
		),
		'wedding' => array(
			'label'  => esc_html__( 'Wedding', 'chique-pro' ),
			'colors' => array(
				'#ffffff', /* Main Background Color */
				'#000000', /* Header Text Color */
				'#ffffff', /* Page Background Color */
				'#f9f9f9', /* Secondary Background Color */
				'#666666', /* Main Text Color */
				'#000000', /* Heading Text Color */
				'#f25f30', /* Link Color */
				'#000000', /* Link Hover Color */
				'#000000', /* Secondary Link Color */
				'#f25f30', /* Button Background Color */
				'#ffffff', /* Button Text Color */
				'#222222', /* Button Hover Background Color */
				'#ffffff', /* Button Hover Text Color */
				'#ededed', /* Border Color */
				'#f2f5f6', /* Tertiary Background Color */
				'#ffffff', /* Text Color With Background */
				'#ffffff', /* Slider/Header Media Color*/
				'#f25f30', /* Slider/Header Media Hover Color */
				'#ffffff', /* Slider/Header Media Content Color */
				'#f25f30', /* Secondary Link Hover Color */
				'#f4f8f9', /* Gradient Background Color First */
				'#f4f8f9', /* Gradient Background Color Second */
			),
		),
		'blog' => array(
			'label'  => esc_html__( 'Blog', 'chique-pro' ),
			'colors' => array(
				'#ffffff', /* Main Background Color */
				'#000000', /* Header Text Color */
				'#ffffff', /* Page Background Color */
				'#f9f9f9', /* Secondary Background Color */
				'#666666', /* Main Text Color */
				'#000000', /* Heading Text Color */
				'#6bbcba', /* Link Color */
				'#000000', /* Link Hover Color */
				'#000000', /* Secondary Link Color */
				'#e186a2', /* Button Background Color */
				'#ffffff', /* Button Text Color */
				'#222222', /* Button Hover Background Color */
				'#ffffff', /* Button Hover Text Color */
				'#ebebeb', /* Border Color */
				'#f2f5f6', /* Tertiary Background Color */
				'#ffffff', /* Text Color With Background */
				'#ffffff', /* Slider/Header Media Color*/
				'#6bbcba', /* Slider/Header Media Hover Color */
				'#ffffff', /* Slider/Header Media Content Color */
				'#6bbcba', /* Secondary Link Hover Color */
				'#dbfbf8', /* Gradient Background Color First */
				'#f3f9e4', /* Gradient Background Color Second */
			),
		),
		'yellow' => array(
			'label'  => esc_html__( 'Yellow', 'chique-pro' ),
			'colors' => array(
				'#ccc200', /* Main Background Color */
				'#ffffff', /* Header Text Color */
				'#dbc300', /* Page Background Color */
				'#bda800', /* Secondary Background Color */
				'#666666', /* Main Text Color */
				'#ffffff', /* Heading Text Color */
				'#ffffff', /* Link Color */
				'#e87785', /* Link Hover Color */
				'#ffffff', /* Secondary Link Color */
				'#ffffff', /* Button Background Color */
				'#000000', /* Button Text Color */
				'#e87785', /* Button Hover Background Color */
				'#ffffff', /* Button Hover Text Color */
				'#d8ce2b', /* Border Color */
				'#c4bd00', /* Tertiary Background Color */
				'#ffffff', /* Text Color With Background */
				'#ffffff', /* Slider/Header Media Color*/
				'#e87785', /* Slider/Header Media Hover Color */
				'#ffffff', /* Slider/Header Media Content Color */
				'#ffffff', /* Secondary Link Hover Color */
				'#bda500', /* Gradient Background Color First */
				'#dcc41f', /* Gradient Background Color Second */
			),
		),
		'gray' => array(
			'label'  => esc_html__( 'Gray', 'chique-pro' ),
			'colors' => array(
				'#565656', /* Main Background Color */
				'#ffffff', /* Header Text Color */
				'#4b4b4b', /* Page Background Color */
				'#4f4f4f', /* Secondary Background Color */
				'#999999', /* Main Text Color */
				'#ffffff', /* Heading Text Color */
				'#ffffff', /* Link Color */
				'#e87785', /* Link Hover Color */
				'#999999', /* Secondary Link Color */
				'#ffffff', /* Button Background Color */
				'#000000', /* Button Text Color */
				'#e87785', /* Button Hover Background Color */
				'#ffffff', /* Button Hover Text Color */
				'#626262', /* Border Color */
				'#5C5C5C', /* Tertiary Background Color */
				'#ffffff', /* Text Color With Background */
				'#ffffff', /* Slider/Header Media Color*/
				'#e87785', /* Slider/Header Media Hover Color */
				'#ffffff', /* Slider/Header Media Content Color */
				'#ffffff', /* Secondary Link Hover Color */
				'#4f4f4f', /* Gradient Background Color First */
				'#424242', /* Gradient Background Color Second */
			),
		),
	) );
}

if ( ! function_exists( 'chique_get_color_scheme' ) ) :
/**
 * Retrieves the current Foodie World color scheme.
 *
 * Create your own chique_get_color_scheme() function to override in a child theme.
 *
 * @since Chique Pro 1.0
 *
 * @return array An associative array of either the current or default color scheme HEX values.
 */
function chique_get_color_scheme() {
	$color_scheme_option = get_theme_mod( 'color_scheme', 'default' );
	$color_schemes       = chique_get_color_schemes();

	if ( array_key_exists( $color_scheme_option, $color_schemes ) ) {
		return $color_schemes[ $color_scheme_option ]['colors'];
	}

	return $color_schemes['default']['colors'];
}
endif; // chique_get_color_scheme

if ( ! function_exists( 'chique_get_color_scheme_choices' ) ) :
/**
 * Retrieves an array of color scheme choices registered for Foodie World.
 *
 * Create your own chique_get_color_scheme_choices() function to override
 * in a child theme.
 *
 * @since Chique Pro 1.0
 *
 * @return array Array of color schemes.
 */
function chique_get_color_scheme_choices() {
	$color_schemes                = chique_get_color_schemes();
	$color_scheme_control_options = array();

	foreach ( $color_schemes as $color_scheme => $value ) {
		$color_scheme_control_options[ $color_scheme ] = $value['label'];
	}

	return $color_scheme_control_options;
}
endif; // chique_get_color_scheme_choices

/**
 * Enqueues front-end CSS for color scheme.
 *
 * @since Chique Pro 1.0
 *
 * @see wp_add_inline_style()
 */
function chique_color_scheme_css() {
	$color_scheme_option = get_theme_mod( 'color_scheme', 'default' );

	// Don't do anything if the default color scheme is selected.
	if ( 'default' === $color_scheme_option ) {
		return;
	}

	$color_scheme = chique_get_color_scheme();

	// Convert Secondary Background Color With Background hex color to rgba.
	$secondary_background_color_rgb = chique_hex2rgb( $color_scheme[3] );

	// Convert Button Background Color hex color to rgba.
	$button_hover_background_color = chique_hex2rgb( $color_scheme[11] );

	// Convert Secondary Link Color With Background hex color to rgba.
	$secondary_link_color_rgb = chique_hex2rgb( $color_scheme[8] );

	// If the rgba values are empty return early.
	if ( empty( $button_hover_background_color )  && empty( $secondary_background_color_rgb ) && empty( $secondary_link_color_rgb ) ) {
		return;
	}

	// If we get this far, we have a custom color scheme.
	$colors = array(
		'background_color'                     		=> $color_scheme[0],
		'header_textcolor'                     		=> $color_scheme[1],
		'page_background_color'                		=> $color_scheme[2],
		'secondary_background_color'           		=> $color_scheme[3],
		'secondary_ninety_six_background_color'   	=> vsprintf( 'rgba( %1$s, %2$s, %3$s, 0.96)', $secondary_background_color_rgb ),
		'main_text_color'                      		=> $color_scheme[4],
		'heading_text_color'                   		=> $color_scheme[5],
		'link_color'                           		=> $color_scheme[6],
		'link_hover_color'                     		=> $color_scheme[7],
		'secondary_link_color'                 		=> $color_scheme[8],
		'secondary_sixty_link_color'   				=> vsprintf( 'rgba( %1$s, %2$s, %3$s, 0.6)', $secondary_link_color_rgb ),
		'button_background_color'              		=> $color_scheme[9],
		'button_text_color'                    		=> $color_scheme[10],
		'button_hover_background_color'        		=> $color_scheme[11],
		'button_hover_five_background_color'        => vsprintf( 'rgba( %1$s, %2$s, %3$s, 0.05)', $button_hover_background_color ),
		'button_hover_text_color'              		=> $color_scheme[12],
		'border_color'                         		=> $color_scheme[13],
		'tertiary_background_color'            		=> $color_scheme[14],
		'text_color_with_background'           		=> $color_scheme[15],
		'slider_color'                    			=> $color_scheme[16],
		'slider_hover_color'                   		=> $color_scheme[17],
		'slider_content_color'            	   		=> $color_scheme[18],
		'secondary_link_hover_color'                => $color_scheme[19],
		'gradient_background_color_first'           => $color_scheme[20],
		'gradient_background_color_second'          => $color_scheme[21],
	);

	$color_scheme_css = chique_get_color_scheme_css( $colors );

	wp_add_inline_style( 'chique-block-style', $color_scheme_css );
}
add_action( 'wp_enqueue_scripts', 'chique_color_scheme_css' );

/**
 * Binds the JS listener to make Customizer color_scheme control.
 *
 * Passes color scheme data as colorScheme global.
 *
 * @since Chique Pro 1.0
 */
function chique_customize_control_js() {
	$min  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$path = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'assets/js/source/' : 'assets/js/';

	wp_enqueue_script( 'chique-color-scheme-control', trailingslashit( esc_url ( get_template_directory_uri() ) ) . $path . 'color-scheme-control' . $min . '.js', array( 'customize-controls', 'iris', 'underscore', 'wp-util' ), '20170816', true );

	$colors['colorScheme'] = chique_get_color_schemes();

	$color_options = chique_color_options();

	// Add background color and header text color index values
	$color_options = array_merge( array( 'background_color', 'header_textcolor' ), array_keys( $color_options ) );

	$colors['colorOptions'] = $color_options;

	wp_localize_script( 'chique-color-scheme-control', 'chiqueColorMain', $colors );

	wp_enqueue_script( 'chique-custom-controls', trailingslashit( esc_url( get_template_directory_uri() ) ) . $path . 'customize-custom-controls' . $min . '.js', array( 'jquery-ui-sortable' ), '20180802', true );

	wp_enqueue_style( 'chique-custom-controls-css', trailingslashit( esc_url( get_template_directory_uri() ) ) . 'assets/css/customizer.css' );
}
add_action( 'customize_controls_enqueue_scripts', 'chique_customize_control_js' );

/**
 * Returns CSS for the color schemes.
 *
 * @since Chique Pro 1.0
 *
 * @param array $colors Color scheme colors.
 * @return string Color scheme CSS.
 */
function chique_get_color_scheme_css( $colors ) {
	$colors = wp_parse_args( $colors, array(
		'background_color'                     		=> '',
		'header_textcolor'                     		=> '',
		'page_background_color'                		=> '',
		'secondary_background_color'           		=> '',
		'secondary_ninety_six_background_color'     => '',
		'main_text_color'                      		=> '',
		'heading_text_color'                   		=> '',
		'link_color'                           		=> '',
		'link_hover_color'                     		=> '',
		'secondary_link_color'                 		=> '',
		'secondary_sixty_link_color'                => '',
		'button_background_color'              		=> '',
		'button_text_color'                    		=> '',
		'button_hover_background_color'        		=> '',
		'button_hover_five_background_color'        => '',
		'button_hover_text_color'              		=> '',
		'border_color'                         		=> '',
		'tertiary_background_color'            		=> '',
		'text_color_with_background'           		=> '',
		'slider_color'                    			=> '',
		'slider_hover_color'                   		=> '',
		'slider_content_color'            	   		=> '',
		'secondary_link_hover_color'                => '',
		'gradient_background_color_first'           => '',
		'gradient_background_color_second'          => '',
	) );

	return <<<CSS
	/* Color Scheme */

	/* Background Color */
	body  {
		background-color: {$colors['background_color']};
	}

	/* Header Text Color */
	.site-title a,
	.site-description {
	    color: {$colors['header_textcolor']};
	}

	/* Page Background Color */
	#masthead,
	.menu-inside-wrapper,
	.navigation-default .header-overlay,
	.widget input[type="search"],
	.sticky-label:before,
	.sticky-label:after,
	.ewnewsletter .ew-newsletter-wrap .ew-newsletter-subbox,
	.ewtabbedrecentpopular .ui-tabs .ui-tabs-panel,
	.ewtabbedrecentpopular .ui-state-active,
	.ewtabbedrecentpopular .ui-widget-content .ui-state-active,
	.ewtabbedrecentpopular .ui-widget-header .ui-state-active,
	.ewtabbedrecentpopular .ui-state-default,
	.ewtabbedrecentpopular .ui-widget-content .ui-state-default,
	.ewtabbedrecentpopular .ui-widget-header .ui-state-default,
	.reserve-content-wrapper .contact-description {
		background-color: {$colors['page_background_color']};
	}

	/* Secondary Background Color */
	input[type="text"],
	input[type="email"],
	input[type="url"],
	input[type="password"],
	input[type="search"],
	input[type="number"],
	input[type="tel"],
	input[type="range"],
	input[type="date"],
	input[type="month"],
	input[type="week"],
	input[type="time"],
	input[type="datetime"],
	input[type="datetime-local"],
	input[type="color"],
	textarea,
	select,
	.sidebar .widget-wrap,
	.author-info,
	.pricing-section .hentry-inner,
	.site-header input[type="search"],
	.woocommerce-account .woocommerce-MyAccount-navigation a,
	.woocommerce-pagination ul li span.current,
	.woocommerce-pagination ul li a:hover,
	.hero-content-wrapper.boxed .entry-container,
	.hero-content-wrapper.fluid.has-content-frame .entry-container,
	#footer-instagram .widget-area .menu-social-container,
	.services-section .section-content-wrapper.layout-two .hentry-inner,
	.promotion-headline-wrapper.content-frame .hentry .entry-container,
	.reserve-content-wrapper .reservation-form {
		background-color: {$colors['secondary_background_color']};
	}

	.menu-toggle-wrapper:after {
		color: {$colors['secondary_background_color']};
	}

	@media screen and (min-width: 64em) {
		.main-navigation ul ul,
		.navigation-classic:not(.primary-subtitle-popup-disable) .main-navigation ul ul,
		.navigation-default .menu-inside-wrapper,
		.site-header-main #site-header-cart-wrapper,
		.below-site-header .site-header-cart .cart-contents,
		.site-header-cart .widget_shopping_cart {
			background-color: {$colors['secondary_background_color']};
		}
	}

	/* 96% of Secondary Background Color */
	.reserve-content-wrapper input[type="text"],
	.reserve-content-wrapper input[type="email"],
	.reserve-content-wrapper input[type="url"],
	.reserve-content-wrapper input[type="password"],
	.reserve-content-wrapper input[type="search"],
	.reserve-content-wrapper input[type="number"],
	.reserve-content-wrapper input[type="tel"],
	.reserve-content-wrapper input[type="range"],
	.reserve-content-wrapper input[type="date"],
	.reserve-content-wrapper input[type="month"],
	.reserve-content-wrapper input[type="week"],
	.reserve-content-wrapper input[type="time"],
	.reserve-content-wrapper input[type="datetime"],
	.reserve-content-wrapper input[type="datetime-local"],
	.reserve-content-wrapper input[type="color"],
	.reserve-content-wrapper textarea,
	.reserve-content-wrapper select,
	hr,
	pre,
	.custom-header:before,
	.page-numbers,
	.page-links a,
	table thead,
	mark,
	ins,
	.woocommerce-tabs .panel,
	.woocommerce-tabs ul.tabs li.active a,
	.custom-header {
		background-color: {$colors['secondary_ninety_six_background_color']};
	}

	@media screen and (min-width: 41.6875em) {
		table.shop_table .cart-subtotal th,
		table.shop_table .order-total th {
			background-color: {$colors['secondary_ninety_six_background_color']};
		}
	}

	/* Main Text Color */
	body,
	button,
	input,
	select,
	optgroup,
	textarea {
		color: {$colors['main_text_color']};
	}

	.hero-content-wrapper .entry-title span,
	#skill-section .entry-title span,
	.promotion-sale-wrapper .entry-title span,
	.reserve-content-wrapper .entry-title span,
	#playlist-section .entry-title span,
	.site-footer,
	.promotion-headline-wrapper.content-frame .section-description {
		color: {$colors['main_text_color']};
	}

	/* Heading Text Color */
	h1,
	h2,
	h3,
	h4,
	h5,
	h6,
	.section-title,
	.promotion-headline-wrapper.content-frame .section-title,
	.contact-section .entry-title,
	.no-header-media-image .custom-header .entry-summary,
	.no-header-media-image .custom-header .entry-content,
	.drop-cap:first-letter,
	.sidebar .widget-title,
	.sidebar .widgettitle,
	.contact-details li a:hover .contact-label,
	.contact-details li a:focus .contact-label,
	.reserve-content-wrapper .contact-description strong,
	.reserve-content-wrapper .contact-description .entry-title,
	.info {
		color: {$colors['heading_text_color']};
	}

	/* Link Color */
	a,
	.post-navigation .nav-links a:hover .nav-title,
	.post-navigation .nav-links a:focus .nav-title,
	.logged-in-as a:hover,
	.logged-in-as a:focus {
		color: {$colors['link_color']};
	}

	/* Link Hover Color */
	a:hover,
	a:focus,
	.post-navigation .nav-links .nav-title,
	.logged-in-as a {
		color: {$colors['link_hover_color']};
	}

	/* Secondary Link Color */
	.main-navigation a,
	.dropdown-toggle,
	.widget_categories ul li,
	.widget_archive ul li,
	.ew-archive ul li,
	.ew-category ul li,
	.contact-details a,
	.social-navigation a,
	.cart-contents,
	.contact-label,
	.entry-title a,
	.widget a,
	.author a,
	.no-header-media-image .custom-header .entry-title,
	.more-link,
	.woocommerce-tabs .panel h2:first-of-type,
	 ul.products li.product .woocommerce-loop-product__title,
	.product-category.product a h2,
	span.price ins,
	p.price ins,
	.reservation-highlight-text span,
	input[type="search"]:focus,
	input[type="text"]:focus,
	input[type="email"]:focus,
	input[type="url"]:focus,
	input[type="password"]:focus,
	input[type="number"]:focus,
	input[type="tel"]:focus,
	input[type="range"]:focus,
	input[type="date"]:focus,
	input[type="month"]:focus,
	input[type="week"]:focus,
	input[type="time"]:focus,
	input[type="datetime"]:focus,
	input[type="datetime-local"]:focus,
	input[type="color"]:focus,
	textarea:focus,
	.ewtabbedrecentpopular .ui-state-active a,
	.ewtabbedrecentpopular .ui-state-active a:link,
	.ewtabbedrecentpopular .ui-state-active a:visited,
	.author-name a,
	.comment-reply-link,
	#cancel-comment-reply-link:before,
	table a,
	.menu-toggle .menu-label,
	#logo-slider-section .owl-nav button,
	#testimonial-content-section .owl-nav button,
	.below-site-header .site-header-cart .cart-contents,
	.wp-playlist-item .wp-playlist-caption {
		color: {$colors['secondary_link_color']};
	}

	@media screen and (min-width: 64em) {
		.navigation-classic:not(.primary-subtitle-popup-disable) .main-navigation ul ul a {
			color: {$colors['secondary_link_color']};
		}
	}

	.bars {
		background-color: {$colors['secondary_link_color']};
	}

	/* 60% of Secondary Link Color */
	.main-navigation ul ul a,
	.search-submit,
	.site-info a,
	figcaption,
	.wp-caption .wp-caption-text,
	.ui-tabs-anchor,
	.date-label,
	.post-navigation .nav-subtitle,
	input[type="text"],
	input[type="email"],
	input[type="url"],
	input[type="password"],
	input[type="search"],
	input[type="number"],
	input[type="tel"],
	input[type="range"],
	input[type="date"],
	input[type="month"],
	input[type="week"],
	input[type="time"],
	input[type="datetime"],
	input[type="datetime-local"],
	input[type="color"],
	textarea,
	select,
	.reserve-content-wrapper .contact-description .entry-content,
	.reserve-content-wrapper .contact-description .entry-summary,
	table.shop_table_responsive tr td a:hover,
	table.shop_table_responsive tr td a:focus,
	.product-container a.added_to_cart:hover,
	.product-container a.added_to_cart:focus,
	.single-product .product_meta a:hover,
	.single-product .product_meta a:focus,
	.single-product div.product .woocommerce-product-rating .woocommerce-review-link:hover,
	.single-product div.product .woocommerce-product-rating .woocommerce-review-link:focus,
	.woocommerce-info a:hover,
	.woocommerce-info a:focus,
	.variations .reset_variations:hover,
	.variations .reset_variations:focus,
	.woocommerce-tabs ul.tabs li a,
	.woocommerce-pagination ul li a,
	p.stars a:before,
	p.stars a:hover ~ a:before,
	p.stars.selected a.active ~ a:before,
	.reservation-highlight-text,
	.has-background-image .position,
	.team-section.has-background-image .entry-content,
	.team-section.has-background-image .entry-summary,
	.hero-content-wrapper.has-background-image.fluid:not(.has-content-frame) .entry-content,
	.hero-content-wrapper.has-background-image.fluid:not(.has-content-frame) .entry-summary,
	.comment-metadata .edit-link a,
	.comment-metadata .edit-link:before,
	.comment-permalink,
	#team-content-section .position,
	.entry-meta a,
	.entry-meta a:before,
	p.stars:hover a:before,
	p.stars.selected a.active:before,
	p.stars.selected a:not(.active):before,
	.pagination .page-numbers.current,
	.page-links > span,
	 table.shop_table_responsive tr td a,
	.product-container a.added_to_cart,
	.single-product .product_meta a,
	.woocommerce-info a,
	.variations .reset_variations,
	.star-rating span:before,
	.single-product div.product .woocommerce-product-rating .woocommerce-review-link,
	.contact-details li .fa,
	.catch-breadcrumb a:after,
	.pagination a,
	.page-links .page-links-title,
	.page-links a,
	.job-label,
	.wp-playlist-item-artist {
		color: {$colors['secondary_sixty_link_color']};
	}

	input[type="text"]:focus,
	input[type="email"]:focus,
	input[type="url"]:focus,
	input[type="password"]:focus,
	input[type="search"]:focus,
	input[type="number"]:focus,
	input[type="tel"]:focus,
	input[type="range"]:focus,
	input[type="date"]:focus,
	input[type="month"]:focus,
	input[type="week"]:focus,
	input[type="time"]:focus,
	input[type="datetime"]:focus,
	input[type="datetime-local"]:focus,
	input[type="color"]:focus,
	textarea:focus,
	select:focus,
	#product-content-section.has-background-image .woocommerce .product-container .button,
	.site-header input[type="search"]:focus {
		border-color: {$colors['secondary_sixty_link_color']};
	}

	.bars,
	.skillbar-content,
	#logo-slider-section .owl-dots button span,
	#testimonial-content-section .owl-dots button span {
		  background-color: {$colors['secondary_sixty_link_color']};
	}

	.comment-respond input[type="date"]:focus,
	.comment-respond input[type="time"]:focus,
	.comment-respond input[type="datetime-local"]:focus,
	.comment-respond input[type="week"]:focus,
	.comment-respond input[type="month"]:focus,
	.comment-respond input[type="text"]:focus,
	.comment-respond input[type="email"]:focus,
	.comment-respond input[type="url"]:focus,
	.comment-respond input[type="password"]:focus,
	.comment-respond input[type="search"]:focus,
	.comment-respond input[type="tel"]:focus,
	.comment-respond input[type="number"]:focus,
	.comment-respond textarea:focus,
	.wpcf7 input[type="date"]:focus,
	.wpcf7 input[type="time"]:focus,
	.wpcf7 input[type="datetime-local"]:focus,
	.wpcf7 input[type="week"]:focus,
	.wpcf7 input[type="month"]:focus,
	.wpcf7 input[type="text"]:focus,
	.wpcf7 input[type="email"]:focus,
	.wpcf7 input[type="url"]:focus,
	.wpcf7 input[type="password"]:focus,
	.wpcf7 input[type="search"]:focus,
	.wpcf7 input[type="tel"]:focus,
	.wpcf7 input[type="number"]:focus,
	.wpcf7 textarea:focus {
		border-bottom-color: {$colors['secondary_sixty_link_color']};
	}

	/* Button Background Color */
	button,
	input[type="button"],
	input[type="reset"],
	input[type="submit"],
	.button,
	.posts-navigation .nav-links a,
	.pagination .prev,
	.pagination .next,
	.site-main #infinite-handle span button,
	#scrollup,
	.hero-content-wrapper .more-link,
	.recent-blog-content-wrapper .more-recent-posts .more-link,
	.instagram-button .button,
	.view-all-button .more-link,
	.woocommerce div.product form.cart .button,
	.woocommerce a.button,
	.woocommerce a.button.alt,
	.woocommerce button.button,
	.woocommerce button.button.alt,
	.woocommerce #respond input#submit,
	.woocommerce #respond input#submit.alt,
	.woocommerce input.button,
	.woocommerce input.button.alt,
	.woocommerce .widget_price_filter .ui-slider .ui-slider-range,
	.woocommerce .widget_price_filter .ui-slider .ui-slider-handle,
	.woocommerce nav.woocommerce-pagination ul li a,
	.woocommerce nav.woocommerce-pagination ul li span,
	.onsale,
	.widget_price_filter .ui-slider .ui-slider-handle,
	.widget_price_filter .ui-slider .ui-slider-range,
	.woocommerce #respond input#submit,
	.woocommerce .product-container .wc-forward,
	.demo_store .woocommerce-store-notice__dismiss-link,
	.woocommerce span.onsale,
	.woocommerce .product-container .added,
	.demo_store .woocommerce-store-notice__dismiss-link:hover,
	.demo_store .woocommerce-store-notice__dismiss-link:focus,
	#product-content-section.has-background-image .woocommerce .product-container .button:hover,
	#product-content-section.has-background-image .woocommerce .product-container .button:focus,
	#footer-newsletter .ewnewsletter .ew-newsletter-wrap #subbutton,
	.pricing-section .hentry .more-link:hover,
	.pricing-section .hentry .more-link:focus,
	.team-social-profile .menu-social-container a:hover,
	.team-social-profile .menu-social-container a:focus,
	.widget-area .menu-social-container a:hover,
	.widget-area .menu-social-container a:focus,
	.portfolio-section .entry-title a:hover:after,
	.portfolio-section .entry-title a:focus:after,
	.widget .tagcloud a:hover,
	.widget .tagcloud a:focus,
	.sticky-label,
	.custom-header .more-link,
	#feature-slider-section .more-link,
	.promotion-headline-wrapper .hentry .more-link,
	.promotion-sale-wrapper .hentry .more-link,
	#logo-slider-section .owl-prev:hover,
	#logo-slider-section .owl-prev:focus,
	#logo-slider-section .owl-next:hover,
	#logo-slider-section .owl-next:focus,
	#testimonial-content-section .owl-prev:hover,
	#testimonial-content-section .owl-prev:focus,
	#testimonial-content-section .owl-next:hover,
	#testimonial-content-section .owl-next:focus,
	.below-site-header .site-header-cart .cart-contents .count,
	.skillbar-bar,
	#sticky-playlist-section .playlist-hide,
	#gallery-content-section .gallery-icon a:after,
	#gallery-content-section .tiled-gallery-item a:after,
	.wp-block-file .wp-block-file__button,
	.wp-block-button .wp-block-button__link {
		background-color: {$colors['button_background_color']};
	}

	#logo-slider-section .owl-prev:hover,
	#logo-slider-section .owl-prev:focus,
	#logo-slider-section .owl-next:hover,
	#logo-slider-section .owl-next:focus,
	#testimonial-content-section .owl-prev:hover,
	#testimonial-content-section .owl-prev:focus,
	#testimonial-content-section .owl-next:hover,
	#testimonial-content-section .owl-next:focus {
		border-color: {$colors['button_background_color']};
	}

	.sticky-label:after {
		border-left-color: {$colors['button_background_color']};
	}

	/* Button Text Color */
	button,
	input[type="button"],
	input[type="reset"],
	input[type="submit"],
	.button,
	.posts-navigation .nav-links a,
	.pagination .prev,
	.pagination .next,
	.site-main #infinite-handle span button,
	#scrollup,
	.hero-content-wrapper .more-link,
	.recent-blog-content-wrapper .more-recent-posts .more-link,
	.instagram-button .button,
	.view-all-button .more-link,
	.wp-custom-header-video-button,
	.woocommerce div.product form.cart .button,
	.woocommerce a.button,
	.woocommerce a.button.alt,
	.woocommerce button.button,
	.woocommerce button.button.alt,
	.woocommerce #respond input#submit,
	.woocommerce #respond input#submit.alt,
	.woocommerce input.button,
	.woocommerce input.button.alt,
	.woocommerce .product-container .wc-forward,
	.woocommerce nav.woocommerce-pagination ul li a,
	.woocommerce nav.woocommerce-pagination ul li span,
	.woocommerce nav.woocommerce-pagination ul li span.current,
	.onsale,
	#product-content-section.has-background-image .woocommerce .product-container .button:hover,
	#product-content-section.has-background-image .woocommerce .product-container .button:focus,
	#footer-newsletter .ewnewsletter .ew-newsletter-wrap #subbutton,
	.pricing-section .hentry .more-link:hover,
	.pricing-section .hentry .more-link:focus,
	.team-social-profile .menu-social-container a:hover,
	.team-social-profile .menu-social-container a:focus,
	.widget-area .menu-social-container a:hover,
	.widget-area .menu-social-container a:focus,
	.portfolio-section .entry-title a:hover:after,
	.portfolio-section .entry-title a:focus:after,
	.widget .tagcloud a:hover,
	.widget .tagcloud a:focus,
	.sticky-label,
	.custom-header .more-link,
	#feature-slider-section .more-link,
	.promotion-headline-wrapper .hentry .more-link,
	.promotion-sale-wrapper .hentry .more-link,
	.below-site-header .site-header-cart .cart-contents .count,
	#sticky-playlist-section .playlist-hide,
	#gallery-content-section .gallery-icon a:after,
	#gallery-content-section .tiled-gallery-item a:after,
	.wp-block-file .wp-block-file__button,
	.wp-block-button .wp-block-button__link {
		color: {$colors['button_text_color']};
	}

	/* Button Hover Background Color */
	button:hover,
	button:focus,
	input[type="button"]:hover,
	input[type="button"]:focus,
	input[type="reset"]:hover,
	input[type="reset"]:focus,
	input[type="submit"]:hover,
	input[type="submit"]:focus,
	.button:hover,
	.button:focus,
	.posts-navigation .nav-links a:hover,
	.posts-navigation .nav-links a:focus,
	#footer-newsletter .ewnewsletter .ew-newsletter-wrap #subbutton:hover,
	#footer-newsletter .ewnewsletter .ew-newsletter-wrap #subbutton:focus,
	.site-main #infinite-handle span button:hover,
	.site-main #infinite-handle span button:focus,
	.pagination .prev:hover,
	.pagination .prev:focus,
	.pagination .next:hover,
	.pagination .next:focus,
	.custom-header .more-link:hover,
	.custom-header .more-link:focus,
	#feature-slider-section .more-link:hover,
	#feature-slider-section .more-link:focus,
	.promotion-sale-wrapper .hentry .more-link:hover,
	.promotion-sale-wrapper .hentry .more-link:focus,
	.promotion-headline-wrapper .hentry .more-link:hover,
	.promotion-headline-wrapper .hentry .more-link:focus,
	#scrollup:hover,
	#scrollup:focus,
	.hero-content-wrapper .more-link:hover,
	.hero-content-wrapper .more-link:focus,
	.recent-blog-content-wrapper .more-recent-posts .more-link:hover,
	.recent-blog-content-wrapper .more-recent-posts .more-link:focus,
	.footer-instagram .instagram-button .button:hover,
	.footer-instagram .instagram-button .button:focus,
	.pagination .nav-links > a:hover,
	.pagination .nav-links > a:focus,
	.page-links a:hover,
	.page-links a:focus,
	.view-all-button .more-link:hover,
	.view-all-button .more-link:focus,
	.wp-custom-header-video-button:hover,
	.wp-custom-header-video-button:focus,
	.woocommerce div.product form.cart .button:hover,
	.woocommerce div.product form.cart .button:focus,
	.woocommerce a.button:hover,
	.woocommerce a.button:focus,
	.woocommerce a.button.alt:hover,
	.woocommerce a.button.alt:focus,
	.woocommerce button.button:hover,
	.woocommerce button.button:focus,
	.woocommerce button.button.alt:hover,
	.woocommerce button.button.alt:focus,
	.woocommerce #respond input#submit:hover,
	.woocommerce #respond input#submit:focus,
	.woocommerce #respond input#submit.alt:hover,
	.woocommerce #respond input#submit.alt:focus,
	.woocommerce input.button:hover,
	.woocommerce input.button:focus,
	.woocommerce input.button.alt:focus,
	.woocommerce input.button.alt:hover,
	.woocommerce .product-container .wc-forward:hover,
	.woocommerce .product-container .wc-forward:focus,
	.woocommerce nav.woocommerce-pagination ul li a:hover,
	.woocommerce nav.woocommerce-pagination ul li a:focus,
	.woocommerce nav.woocommerce-pagination ul li span.current,
	.contact-details li a:hover .fa,
	.contact-details li a:focus .fa,
	.pricing-section .hentry .more-link,
	.team-social-profile .menu-social-container a,
	.widget-area .menu-social-container a,
	.portfolio-section .entry-title a:after,
	.wp-block-file .wp-block-file__button:hover,
	.wp-block-file .wp-block-file__button:focus,
	.wp-block-button .wp-block-button__link:hover,
	.wp-block-button .wp-block-button__link:focus {
		background-color: {$colors['button_hover_background_color']};
	}

	blockquote,
	.widget .tagcloud a {
		color: {$colors['button_hover_background_color']};
	}

	.widget .tagcloud a {
		background-color: {$colors['button_hover_five_background_color']};
	}

	/* Button Hover Text Color */
	button:hover,
	button:focus,
	input[type="button"]:hover,
	input[type="button"]:focus,
	input[type="reset"]:hover,
	input[type="reset"]:focus,
	input[type="submit"]:hover,
	input[type="submit"]:focus,
	.button:hover,
	.button:focus,
	.prev.page-numbers:hover,
	.prev.page-numbers:focus,
	.next.page-numbers:hover,
	.next.page-numbers:focus,
	.custom-header .more-link:hover,
	.custom-header .more-link:focus,
	.hero-content-wrapper .more-link:hover,
	.hero-content-wrapper .more-link:focus,
	.recent-blog-content-wrapper .more-recent-posts .more-link:hover,
	.recent-blog-content-wrapper .more-recent-posts .more-link:focus,
	.pagination .nav-links > a:hover,
	.pagination .nav-links > a:focus,
	.page-links a:hover,
	.page-links a:focus,
	#scrollup:hover,
	#scrollup:focus,
	.view-all-button .more-link:hover,
	.view-all-button .more-link:focus,
	.wp-custom-header-video-button:hover,
	.wp-custom-header-video-button:focus,
	.woocommerce div.product form.cart .button:hover,
	.woocommerce div.product form.cart .button:focus,
	.woocommerce a.button:hover,
	.woocommerce a.button:focus,
	.woocommerce a.button.alt:hover,
	.woocommerce a.button.alt:focus,
	.woocommerce button.button:hover,
	.woocommerce button.button:focus,
	.woocommerce button.button.alt:hover,
	.woocommerce button.button.alt:focus,
	.woocommerce #respond input#submit:hover,
	.woocommerce #respond input#submit:focus,
	.woocommerce #respond input#submit.alt:hover,
	.woocommerce #respond input#submit.alt:focus,
	.woocommerce input.button:hover,
	.woocommerce input.button:focus,
	.woocommerce input.button.alt:focus,
	.woocommerce input.button.alt:hover,
	.woocommerce .product-container .wc-forward:hover,
	.woocommerce .product-container .wc-forward:focus,
	.woocommerce nav.woocommerce-pagination ul li a:hover,
	.woocommerce nav.woocommerce-pagination ul li a:focus,
	.woocommerce nav.woocommerce-pagination ul li span.current,
	.site-main #infinite-handle span button:hover,
	.site-main #infinite-handle span button:focus,
	.contact-details li a:hover .fa,
	.contact-details li a:focus .fa,
	.posts-navigation .nav-links a:hover,
	.posts-navigation .nav-links a:focus,
	#footer-newsletter .ewnewsletter .ew-newsletter-wrap #subbutton:hover,
	#footer-newsletter .ewnewsletter .ew-newsletter-wrap #subbutton:focus,
	.pricing-section .hentry .more-link,
	.team-social-profile .menu-social-container a,
	.widget-area .menu-social-container a,
	.portfolio-section .entry-title a:after,
	.custom-header .more-link:hover,
	.custom-header .more-link:focus,
	#feature-slider-section .more-link:hover,
	#feature-slider-section .more-link:focus,
	.promotion-headline-wrapper .hentry .more-link:hover,
	.promotion-headline-wrapper .hentry .more-link:focus,
	.promotion-sale-wrapper .hentry .more-link:hover,
	.promotion-sale-wrapper .hentry .more-link:focus,
	.social-contact .menu-social-container li a:hover,
	.social-contact .menu-social-container li a:focus,
	.wp-block-file .wp-block-file__button:hover,
	.wp-block-file .wp-block-file__button:focus,
	.wp-block-button .wp-block-button__link:hover,
	.wp-block-button .wp-block-button__link:focus {
		color: {$colors['button_hover_text_color']};
	}

	/* Border Color */
	input[type="text"],
	input[type="email"],
	input[type="url"],
	input[type="password"],
	input[type="search"],
	input[type="number"],
	input[type="tel"],
	input[type="range"],
	input[type="date"],
	input[type="month"],
	input[type="week"],
	input[type="time"],
	input[type="datetime"],
	input[type="datetime-local"],
	input[type="color"],
	textarea,
	select,
	.tag-cloud-link,
	.two-columns-layout .singular-content-wrap,
	table,
	th,
	td,
	fieldset,
	abbr,
	acronym,
	.is-open .social-navigation-wrapper,
	.comment-list article,
	.comment-list .pingback,
	.comment-list .trackback,
	.site-content + .recent-blog-content-wrapper,
	.site-content,
	.pagination .nav-links > span,
	.pagination .nav-links > a,
	.page-links a,
	.page-links > span,
	#footer-instagram,
	.single-product .product_meta,
	.woocommerce-account .woocommerce-MyAccount-navigation,
	.woocommerce-account .woocommerce-MyAccount-navigation li,
	.woocommerce nav.woocommerce-pagination ul,
	.post-navigation .nav-links,
	.is-open .menu-inside-wrapper,
	.comment-respond,
	.single-blog .archive-content-wrap .hentry-inner,
	.why-choose-us-section.modern-style .hentry,
	.team-section .entry-container,
	.ewtabbedrecentpopular .ui-tabs .ui-tabs-panel,
	.ewtabbedrecentpopular .ui-state-active,
	.ewtabbedrecentpopular .ui-widget-content .ui-state-active,
	.ewtabbedrecentpopular .ui-widget-header .ui-state-active,
	.essential-widgets .hentry,
	.contact-section .entry-header:after,
	.reserve-content-wrapper .contact-description:before,
	#logo-slider-section .static-logo.layout-two .hentry,
	#logo-slider-section .static-logo.layout-four .hentry,
	#logo-slider-section .static-logo.layout-three .hentry,
	#logo-slider-section .static-logo.layout-four .hentry:nth-child(2n),
	#logo-slider-section .static-logo.layout-five .hentry,
	#logo-slider-section .static-logo.layout-five .hentry:nth-child(2n),
	.site-header-cart .cart-contents,
	.no-header-media-image.no-featured-slider.grid-blog .site-overlay + .recent-blog-content-wrapper,
	.wp-playlist-item,
	.countdown #clock .count-wrap,
	.venue .hentry-inner,
	.wp-block-table,
	.wp-block-table th,
	.wp-block-table td,
	.wp-block-latest-comments__comment {
		border-color: {$colors['border_color']};
	}

	@media screen and (min-width: 35.5em) {
		.featured-content-section .layout-two .entry-container,
		.featured-content-section .layout-four .entry-container,
		.featured-content-section .layout-four .hentry:nth-child(2n) .entry-container {
			border-color: {$colors['border_color']};
		}
	}

	@media screen and (min-width: 41.6875em) {
		.featured-content-section .layout-three .entry-container {
			border-color: {$colors['border_color']};
		}
	}

	.comment-respond input[type="date"],
	.comment-respond input[type="time"],
	.comment-respond input[type="datetime-local"],
	.comment-respond input[type="week"],
	.comment-respond input[type="month"],
	.comment-respond input[type="text"],
	.comment-respond input[type="email"],
	.comment-respond input[type="url"],
	.comment-respond input[type="password"],
	.comment-respond input[type="search"],
	.comment-respond input[type="tel"],
	.comment-respond input[type="number"],
	.comment-respond textarea,
	.wpcf7 input[type="date"],
	.wpcf7 input[type="time"],
	.wpcf7 input[type="datetime-local"],
	.wpcf7 input[type="week"],
	.wpcf7 input[type="month"],
	.wpcf7 input[type="text"],
	.wpcf7 input[type="email"],
	.wpcf7 input[type="url"],
	.wpcf7 input[type="password"],
	.wpcf7 input[type="search"],
	.wpcf7 input[type="tel"],
	.wpcf7 input[type="number"],
	.wpcf7 textarea {
		border-bottom-color: {$colors['border_color']};
	}

	.woocommerce nav.woocommerce-pagination,
	.woocommerce-account .woocommerce-MyAccount-navigation a:hover,
	.woocommerce-account .woocommerce-MyAccount-navigation a:focus,
	.woocommerce-account .woocommerce-MyAccount-navigation .is-active a,
	.woocommerce-message,
	.woocommerce-info,
	.woocommerce-error,
	.woocommerce-noreviews,
	.demo_store,
	p.no-comments,
	ul.wc_payment_methods .payment_box,
	.widget_price_filter .price_slider_wrapper .ui-widget-content,
	.woocommerce-tabs .panel input[type="text"],
	.woocommerce-tabs .panel input[type="email"],
	.woocommerce-tabs .panel textarea {
		background-color: {$colors['border_color']};
	}

	/* Tertiary Background Color */
	.site-footer,
	.blog.grid-blog .archive-content-wrap .hentry .entry-container,
	.archive.grid-blog .archive-content-wrap .hentry .entry-container,
	.grid-blog .recent-blog-content-wrapper .archive-content-wrap .hentry .entry-container,
	.grid-blog .sticky-label:before,
	.grid-blog .sticky-label:after,
	#timeline-section .hentry:before,
	#timeline-section .section-content-wrapper:before {
		background-color: {$colors['tertiary_background_color']};
	}

	/* Text Color With Background */
	.archive .custom-header,
	.search .custom-header,
	.error404 .custom-header,
	.woocommerce .product-container .added,
	.woocommerce .product-container .button.added,
	.demo_store .woocommerce-store-notice__dismiss-link,
	.demo_store .woocommerce-store-notice__dismiss-link:hover,
	.demo_store .woocommerce-store-notice__dismiss-link:focus,
	.gallery-section figcaption,
	.scroll-down,
	.has-background-image .section-title,
	.has-background-image .section-description,
	.has-background-image .before-text,
	.has-background-image .after-text,
	.promotion-headline-wrapper.has-background-image:not(.content-frame) .section-title,
	.promotion-headline-wrapper.has-background-image:not(.content-frame) .section-description,
	.promotion-headline-wrapper.has-background-image:not(.content-frame) .entry-content,
	.promotion-headline-wrapper.has-background-image:not(.content-frame) .entry-summary,
	.widget.has-background-image .widget-title,
	.team-section.has-background-image .entry-title a,
	.team-section.has-background-image .hentry .more-link,
	#product-content-section.has-background-image ul.products li.product .woocommerce-loop-product__title,
	#product-content-section.has-background-image .woocommerce-Price-amount,
	#product-content-section.has-background-image .woocommerce .product-container .button,
	.hero-content-wrapper.has-background-image.fluid:not(.has-content-frame) .entry-title span,
	.stats-section .entry-title a,
	.stats-section .hentry .more-link,
	.stats-section .section-title,
	.stats-section .section-description,
	.stats-section .entry-content,
	.stats-section .entry-summary,
	.pricing-section .entry-title,
	.pricing-section .entry-title a,
	.pricing-section .package-price,
	.pricing-section .package-month,
	.portfolio-content-wrapper .entry-title,
	.portfolio-content-wrapper .entry-title a,
	.portfolio-content-wrapper .entry-meta a,
	.portfolio-content-wrapper .entry-meta a:before,
	.catch-breadcrumb,
	.catch-breadcrumb a,
	.events-section .entry-title a,
	.events-section .entry-meta a,
	.events-section .hentry .more-link,
	.events-section .entry-content,
	.events-section .entry-summary,
	#events-section .sep {
		color: {$colors['text_color_with_background']};
	}

	.wp-custom-header-video-button,
	.stats-section .more-link .fa {
		border-color: {$colors['text_color_with_background']};
	}

	/* Slider/Header Media Color */
	#feature-slider-section .owl-dot,
	#feature-slider-section .owl-prev,
	#feature-slider-section .owl-next,
	#feature-slider-section .entry-title a,
	#feature-slider-section .entry-title,
	.custom-header .entry-title {
		color: {$colors['slider_color']};
	}

	/* Slider/Header Media Hover Color */
	#feature-slider-section .owl-dot:hover,
	#feature-slider-section .owl-dot:focus,
	#feature-slider-section .owl-prev:hover,
	#feature-slider-section .owl-prev:focus,
	#feature-slider-section .owl-next:hover,
	#feature-slider-section .owl-next:focus,
	#feature-slider-section .entry-title a:hover,
	#feature-slider-section .entry-title a:focus {
		color: {$colors['slider_hover_color']};
	}

	/* Slider/Header Media Content Color */
	#feature-slider-section .entry-content,
	#feature-slider-section .entry-summary,
	.custom-header .entry-content,
	.custom-header .entry-summary  {
		color: {$colors['slider_content_color']};
	}

	/* Secondary Link Hover Color */
	.main-navigation a:hover,
	.main-navigation a:focus,
	.main-navigation .menu > .current-menu-item > a,
	.main-navigation .menu > .current-menu-ancestor > a,
	.hero-content-wrapper.has-background-image.fluid:not(.has-content-frame) .entry-title,
	.navigation-default .main-navigation ul ul a:hover,
	.navigation-default .main-navigation ul ul a:focus,
	.dropdown-toggle:hover,
	.dropdown-toggle:focus,
	.search-submit:hover,
	.search-submit:focus,
	.more-link:hover,
	.more-link:focus,
	.entry-title a:hover,
	.entry-title a:focus,
	.widget a:hover,
	.widget a:focus,
	.page-numbers:hover,
	.page-numbers:focus,
	.woocommerce .catch-breadcrumb .woocommerce-breadcrumb a:hover,
	.woocommerce .catch-breadcrumb .woocommerce-breadcrumb a:focus,
	.catch-breadcrumb a:hover,
	.catch-breadcrumb a:focus,
	.site-title a:hover,
	.site-title a:focus,
	.site-footer a:hover,
	.site-footer a:focus,
	 ul.products li.product .woocommerce-loop-product__title:hover,
	 ul.products li.product .woocommerce-loop-product__title:focus,
	.portfolio-section .entry-title a:hover,
	.portfolio-section .entry-title a:focus,
	.scroll-down:hover,
	.scroll-down:focus,
	.team-section.has-background-image .entry-title a:hover,
	.team-section.has-background-image .entry-title a:focus,
	.team-section.has-background-image .hentry .more-link:hover,
	.team-section.has-background-image .hentry .more-link:focus,
	#product-content-section.has-background-image ul.products li.product .woocommerce-loop-product__title:hover,
	#product-content-section.has-background-image ul.products li.product .woocommerce-loop-product__title:focus,
	#product-content-section.has-background-image .woocommerce-Price-amount:hover,
	#product-content-section.has-background-image .woocommerce-Price-amount:focus,
	.stats-section .entry-title a:hover,
	.stats-section .entry-title a:focus,
	.stats-section .hentry .more-link:hover,
	.stats-section .hentry .more-link:focus,
	.site-info a:hover,
	.site-info a:focus,
	.site-footer .entry-title a:hover,
	.site-footer .entry-title a:focus,
	.contact-details a:hover,
	.contact-details a:hover,
	.cart-contents:hover,
	.cart-contents:focus,
	.social-navigation a:hover,
	.social-navigation a:focus,
	.entry-meta a:hover,
	.entry-meta a:focus,
	 .woocommerce-tabs ul.tabs li a:hover,
	.woocommerce-tabs ul.tabs li a:focus,
	.woocommerce-pagination ul li span.current,
	.woocommerce-tabs ul.tabs li.active a,
	.woocommerce-pagination ul li a:hover,
	.portfolio-content-wrapper .entry-title a:hover,
	.portfolio-content-wrapper .entry-title a:focus,
	.portfolio-content-wrapper .entry-meta a:hover,
	.portfolio-content-wrapper .entry-meta a:focus,
	.portfolio-content-wrapper .entry-meta a:hover::before,
	.portfolio-content-wrapper .entry-meta a:focus::before,
	.entry-meta a:hover::before,
	.entry-meta a:focus::before,
	.author-name a:hover,
	.author-name a:focus,
	.comment-reply-link:hover,
	.comment-reply-link:focus,
	#cancel-comment-reply-link:hover::before,
	#cancel-comment-reply-link:focus::before,
	table a:hover,
	table a:focus,
	.comment-permalink:hover,
	.comment-permalink:focus,
	.menu-toggle:hover .menu-label,
	.menu-toggle:focus .menu-label,
	.pricing-section .entry-title a:hover,
	.pricing-section .entry-title a:focus,
	.product-category.product a:hover h2,
	.product-category.product a:focus h2,
	.product-category.product a:hover h2 mark,
	.product-category.product a:focus h2 mark,
	.pricing-section .entry-content ul li:before,
	.pricing-section .entry-summary ul li:before,
	.chique-mejs-container.mejs-container button:hover,
	.chique-mejs-container.mejs-container button:focus,
	#sticky-playlist-section .chique-mejs-container.mejs-container .mejs-controls .mejs-playpause-button.mejs-button button:hover,
	#sticky-playlist-section .chique-mejs-container.mejs-container .mejs-controls .mejs-playpause-button.mejs-button button:focus,
	.wp-playlist .wp-playlist-caption:hover .wp-playlist-item-title,
	.wp-playlist .wp-playlist-caption:focus .wp-playlist-item-title,
	.wp-playlist .wp-playlist-playing .wp-playlist-caption .wp-playlist-item-title,
	.wp-playlist .wp-playlist-playing .wp-playlist-caption .wp-playlist-item-title:after,
	.wp-playlist-playing .wp-playlist-item-length,
	#sticky-playlist-section .wp-playlist .wp-playlist-playing .wp-playlist-item-length,
	.events-section .entry-title a:hover,
	.events-section .entry-title a:focus,
	.events-section .entry-meta a:hover,
	.events-section .entry-meta a:focus,
	.events-section .hentry .more-link:hover,
	.events-section .hentry .more-link:focus {
		color: {$colors['secondary_link_hover_color']};
	}

	.more-link .fa,
	.woocommerce-info,
	.woocommerce-message,
	blockquote,
	.wp-custom-header-video-button:hover,
	.wp-custom-header-video-button:focus,
	#logo-slider-section .owl-prev,
	#logo-slider-section .owl-next,
	#testimonial-content-section .owl-prev,
	#testimonial-content-section .owl-next,
	.social-contact .menu-social-container li a,
	.wp-playlist .wp-playlist-playing .wp-playlist-caption .wp-playlist-item-title:after,
	.mejs-time-handle,
	.mejs-time-handle-content,
	.wp-block-pullquote,
	.wp-block-quote:not(.is-large):not(.is-style-large) {
		border-color: {$colors['secondary_link_hover_color']};
	}

	@media screen and (min-width: 64em) {
		.navigation-default .main-navigation .menu > .current-menu-item > a,
		.navigation-default .main-navigation .menu > .current-menu-ancestor > a,
		.navigation-classic .main-navigation ul ul li a:hover,
		.navigation-classic .main-navigation ul ul li a:focus {
			color: {$colors['secondary_link_hover_color']};
		}
	}

	.menu-toggle:hover .bars,
	.menu-toggle:focus .bars,
	.slide-progress,
	#logo-slider-section .owl-dots button span:hover,
	#logo-slider-section .owl-dots button span:focus,
	#testimonial-content-section .owl-dots button span:hover,
	#testimonial-content-section .owl-dots button span:focus,
	#logo-slider-section .owl-dots button.active span,
	#testimonial-content-section .owl-dots button.active span,
	.stats-section,
	.pricing-section .entry-header-wrap,
	.social-contact .menu-social-container li a:hover,
	.social-contact .menu-social-container li a:focus,
	.mejs-time-handle,
	.mejs-time-handle-content,
	.chique-mejs-container.mejs-container .mejs-controls .mejs-time-rail .mejs-time-current,
	.mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-current,
	.mejs-controls .mejs-time-rail .mejs-time-loaded {
		background-color: {$colors['secondary_link_hover_color']};
	}

	.testimonial-content-section,
	.logo-slider-section,
	#footer-newsletter,
	ul.products li.product {
		background-image: -webkit-gradient(linear, left top, left bottom, from({$colors['gradient_background_color_first']}), to({$colors['gradient_background_color_first']}));
	    background-image: -o-linear-gradient(top, {$colors['gradient_background_color_first']}, {$colors['gradient_background_color_second']});
	    background-image: linear-gradient(to bottom, {$colors['gradient_background_color_first']}, {$colors['gradient_background_color_second']});
	}
CSS;
}


/**
 * Outputs an Underscore template for generating CSS for the color scheme.
 *
 * The template generates the css dynamically for instant display in the
 * Customizer preview.
 *
 * @since Chique Pro 1.0
 */
function chique_color_scheme_css_template() {
	$color_options = chique_color_options();

	// Add background color ahd header text color index values
	$color_options = array_keys( $color_options );

	foreach ( $color_options as $color ) {
		$colors[ $color ] = '{{ data.' . $color . '}}';
	}

	$colors['button_hover_five_background_color']   		= '{{ data.button_hover_five_background_color}}';
	$colors['secondary_ninety_six_background_color']      	= '{{ data.secondary_ninety_six_background_color}}';
	$colors['secondary_sixty_link_color']      				= '{{ data.secondary_sixty_link_color}}';

	?>
	<script type="text/html" id="tmpl-chique-color-scheme">
		<?php echo chique_get_color_scheme_css( $colors ); ?>
	</script>
	<?php
}
add_action( 'customize_controls_print_footer_scripts', 'chique_color_scheme_css_template' );

/**
 * Enqueues front-end CSS for the page background color.
 *
 * @since Chique Pro 1.0
 *
 * @see wp_add_inline_style()
 */
function chique_page_background_color_css() {
	$color_scheme          = chique_get_color_scheme();
	$default_color         = $color_scheme[2];
	$page_background_color = get_theme_mod( 'page_background_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $page_background_color === $default_color ) {
		return;
	}

	$css = '
		/* Page Background Color */
		#masthead,
		.menu-inside-wrapper,
		.navigation-default .header-overlay,
		.widget input[type="search"],
		.sticky-label:before,
		.sticky-label:after,
		.ewnewsletter .ew-newsletter-wrap .ew-newsletter-subbox,
		.ewtabbedrecentpopular .ui-tabs .ui-tabs-panel,
		.ewtabbedrecentpopular .ui-state-active,
		.ewtabbedrecentpopular .ui-widget-content .ui-state-active,
		.ewtabbedrecentpopular .ui-widget-header .ui-state-active,
		.ewtabbedrecentpopular .ui-state-default,
		.ewtabbedrecentpopular .ui-widget-content .ui-state-default,
		.ewtabbedrecentpopular .ui-widget-header .ui-state-default,
		.reserve-content-wrapper .contact-description {
			background-color: %1$s;
		}

	';

	wp_add_inline_style( 'chique-block-style', sprintf( $css, $page_background_color ) );
}
add_action( 'wp_enqueue_scripts', 'chique_page_background_color_css', 11 );

/**
 * Enqueues front-end CSS for the secondary background color.
 *
 * @since Chique Pro 1.0
 *
 * @see wp_add_inline_style()
 */
function chique_secondary_background_color_css() {
	$color_scheme          				= chique_get_color_scheme();
	$default_color         				= $color_scheme[3];
	$secondary_background_color 	= get_theme_mod( 'secondary_background_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $secondary_background_color === $default_color ) {
		return;
	}

	// Convert gradient text hex color to rgba.
	$secondary_background_color_rgb = chique_hex2rgb( $secondary_background_color );

	// If the rgba values are empty return early.
	if ( empty( $secondary_background_color_rgb ) ) {
		return;
	}

	$secondary_ninety_six_background_color = vsprintf( 'rgba( %1$s, %2$s, %3$s, 0.96)', $secondary_background_color_rgb );

	$css = '
		/* Secondary Background Color */
		input[type="text"],
		input[type="email"],
		input[type="url"],
		input[type="password"],
		input[type="search"],
		input[type="number"],
		input[type="tel"],
		input[type="range"],
		input[type="date"],
		input[type="month"],
		input[type="week"],
		input[type="time"],
		input[type="datetime"],
		input[type="datetime-local"],
		input[type="color"],
		textarea,
		select,
		.sidebar .widget-wrap,
		.author-info,
		.pricing-section .hentry-inner,
		.site-header input[type="search"],
		.woocommerce-account .woocommerce-MyAccount-navigation a,
		.woocommerce-pagination ul li span.current,
		.woocommerce-pagination ul li a:hover,
		.hero-content-wrapper.boxed .entry-container,
		.hero-content-wrapper.fluid.has-content-frame .entry-container,
		#footer-instagram .widget-area .menu-social-container,
		.services-section .section-content-wrapper.layout-two .hentry-inner,
		.promotion-headline-wrapper.content-frame .hentry .entry-container,
		.reserve-content-wrapper .reservation-form {
			background-color: %1$s;
		}

		.menu-toggle-wrapper:after {
			color: %1$s;
		}

		@media screen and (min-width: 64em) {
			.main-navigation ul ul,
			.navigation-classic:not(.primary-subtitle-popup-disable) .main-navigation ul ul,
			.navigation-default .menu-inside-wrapper,
			.site-header-main #site-header-cart-wrapper,
			.below-site-header .site-header-cart .cart-contents,
			.site-header-cart .widget_shopping_cart {
				background-color: %1$s;
			}
		}

		/* 96% of Secondary Background Color */
		.reserve-content-wrapper input[type="text"],
		.reserve-content-wrapper input[type="email"],
		.reserve-content-wrapper input[type="url"],
		.reserve-content-wrapper input[type="password"],
		.reserve-content-wrapper input[type="search"],
		.reserve-content-wrapper input[type="number"],
		.reserve-content-wrapper input[type="tel"],
		.reserve-content-wrapper input[type="range"],
		.reserve-content-wrapper input[type="date"],
		.reserve-content-wrapper input[type="month"],
		.reserve-content-wrapper input[type="week"],
		.reserve-content-wrapper input[type="time"],
		.reserve-content-wrapper input[type="datetime"],
		.reserve-content-wrapper input[type="datetime-local"],
		.reserve-content-wrapper input[type="color"],
		.reserve-content-wrapper textarea,
		.reserve-content-wrapper select,
		hr,
		pre,
		.custom-header:before,
		.page-numbers,
		.page-links a,
		table thead,
		mark,
		ins,
		.woocommerce-tabs .panel,
		.woocommerce-tabs ul.tabs li.active a,
		.custom-header {
			background-color: %2$s;
		}

		@media screen and (min-width: 41.6875em) {
			table.shop_table .cart-subtotal th,
			table.shop_table .order-total th {
				background-color: %2$s;
			}
		}
	';

	wp_add_inline_style( 'chique-block-style', sprintf( $css, $secondary_background_color, $secondary_ninety_six_background_color ) );
}
add_action( 'wp_enqueue_scripts', 'chique_secondary_background_color_css', 11 );

/**
 * Enqueues front-end CSS for the main text color.
 *
 * @since Chique Pro 1.0
 *
 * @see wp_add_inline_style()
 */
function chique_main_text_color_css() {
	$color_scheme          				= chique_get_color_scheme();
	$default_color         				= $color_scheme[4];
	$main_text_color 	= get_theme_mod( 'main_text_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $main_text_color === $default_color ) {
		return;
	}

	$css = '
		/* Main Text Color */
		body,
		button,
		input,
		select,
		optgroup,
		textarea {
			color: %1$s;
		}

		.hero-content-wrapper .entry-title span,
		#skill-section .entry-title span,
		.promotion-sale-wrapper .entry-title span,
		.reserve-content-wrapper .entry-title span,
		#playlist-section .entry-title span,
		.site-footer,
		.promotion-headline-wrapper.content-frame .section-description {
			color: %1$s;
		}
	';

	wp_add_inline_style( 'chique-block-style', sprintf( $css, $main_text_color ) );
}
add_action( 'wp_enqueue_scripts', 'chique_main_text_color_css', 11 );

/**
 * Enqueues front-end CSS for the heading text color.
 *
 * @since Chique Pro 1.0
 *
 * @see wp_add_inline_style()
 */
function chique_heading_text_color_css() {
	$color_scheme          				= chique_get_color_scheme();
	$default_color         				= $color_scheme[5];
	$heading_text_color 	= get_theme_mod( 'heading_text_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $heading_text_color === $default_color ) {
		return;
	}

	$css = '
		/* Heading Text Color */
		h1,
		h2,
		h3,
		h4,
		h5,
		h6,
		.section-title,
		.promotion-headline-wrapper.content-frame .section-title,
		.contact-section .entry-title,
		.no-header-media-image .custom-header .entry-summary,
		.no-header-media-image .custom-header .entry-content,
		.drop-cap:first-letter,
		.sidebar .widget-title,
		.sidebar .widgettitle,
		.contact-details li a:hover .contact-label,
		.contact-details li a:focus .contact-label,
		.reserve-content-wrapper .contact-description strong,
		.reserve-content-wrapper .contact-description .entry-title,
		.info {
			color: %1$s;
		}
	';

	wp_add_inline_style( 'chique-block-style', sprintf( $css, $heading_text_color ) );
}
add_action( 'wp_enqueue_scripts', 'chique_heading_text_color_css', 11 );

/**
 * Enqueues front-end CSS for the link color.
 *
 * @since Chique Pro 1.0
 *
 * @see wp_add_inline_style()
 */
function chique_link_color_css() {
	$color_scheme  = chique_get_color_scheme();
	$default_color = $color_scheme[6];
	$link_color    = get_theme_mod( 'link_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $link_color === $default_color ) {
		return;
	}

	$css = '
		/* Link Color */
		a,
		.post-navigation .nav-links a:hover .nav-title,
		.post-navigation .nav-links a:focus .nav-title,
		.logged-in-as a:hover,
		.logged-in-as a:focus {
			color: %1$s;
		}
	';

	wp_add_inline_style( 'chique-block-style', sprintf( $css, $link_color ) );
}
add_action( 'wp_enqueue_scripts', 'chique_link_color_css', 11 );

/**
 * Enqueues front-end CSS for the link hover color.
 *
 * @since Chique Pro 1.0
 *
 * @see wp_add_inline_style()
 */
function chique_link_hover_color_css() {
	$color_scheme          = chique_get_color_scheme();
	$default_color         = $color_scheme[7];
	$link_hover_color = get_theme_mod( 'link_hover_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $link_hover_color === $default_color ) {
		return;
	}

	$css = '
		/* Link Hover Color */
		a:hover,
		a:focus,
		.post-navigation .nav-links .nav-title,
		.logged-in-as a {
			color: %1$s;
		}
	';

	wp_add_inline_style( 'chique-block-style', sprintf( $css, $link_hover_color ) );
}
add_action( 'wp_enqueue_scripts', 'chique_link_hover_color_css', 11 );

/**
 * Enqueues front-end CSS for the secondary link color.
 *
 * @since Chique Pro 1.0
 *
 * @see wp_add_inline_style()
 */
function chique_secondary_link_color_css() {
	$color_scheme          				= chique_get_color_scheme();
	$default_color         				= $color_scheme[8];
	$secondary_link_color 	= get_theme_mod( 'secondary_link_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $secondary_link_color === $default_color ) {
		return;
	}

	// Convert gradient text hex color to rgba.
	$secondary_link_color_rgb = chique_hex2rgb( $secondary_link_color );

	// If the rgba values are empty return early.
	if ( empty( $secondary_link_color_rgb ) ) {
		return;
	}

	$secondary_sixty_link_color = vsprintf( 'rgba( %1$s, %2$s, %3$s, 0.6)', $secondary_link_color_rgb );

	$css = '
		/* Secondary Link Color */
		.main-navigation a,
		.dropdown-toggle,
		.widget_categories ul li,
		.widget_archive ul li,
		.ew-archive ul li,
		.ew-category ul li,
		.contact-details a,
		.social-navigation a,
		.cart-contents,
		.contact-label,
		.entry-title a,
		.widget a,
		.author a,
		.no-header-media-image .custom-header .entry-title,
		.more-link,
		.woocommerce-tabs .panel h2:first-of-type,
		 ul.products li.product .woocommerce-loop-product__title,
		.product-category.product a h2,
		span.price ins,
		p.price ins,
		.reservation-highlight-text span,
		input[type="search"]:focus,
		input[type="text"]:focus,
		input[type="email"]:focus,
		input[type="url"]:focus,
		input[type="password"]:focus,
		input[type="number"]:focus,
		input[type="tel"]:focus,
		input[type="range"]:focus,
		input[type="date"]:focus,
		input[type="month"]:focus,
		input[type="week"]:focus,
		input[type="time"]:focus,
		input[type="datetime"]:focus,
		input[type="datetime-local"]:focus,
		input[type="color"]:focus,
		textarea:focus,
		.ewtabbedrecentpopular .ui-state-active a,
		.ewtabbedrecentpopular .ui-state-active a:link,
		.ewtabbedrecentpopular .ui-state-active a:visited,
		.author-name a,
		.comment-reply-link,
		#cancel-comment-reply-link:before,
		table a,
		.menu-toggle .menu-label,
		#logo-slider-section .owl-nav button,
		#testimonial-content-section .owl-nav button,
		.below-site-header .site-header-cart .cart-contents,
		.wp-playlist-item .wp-playlist-caption {
			color: %1$s;
		}

		@media screen and (min-width: 64em) {
			.navigation-classic:not(.primary-subtitle-popup-disable) .main-navigation ul ul a {
				color: %1$s;
			}
		}

		.bars {
			background-color: %1$s;
		}

		/* 60% of Secondary Link Color */
		.main-navigation ul ul a,
		.search-submit,
		.site-info a,
		figcaption,
		.wp-caption .wp-caption-text,
		.ui-tabs-anchor,
		.date-label,
		.post-navigation .nav-subtitle,
		input[type="text"],
		input[type="email"],
		input[type="url"],
		input[type="password"],
		input[type="search"],
		input[type="number"],
		input[type="tel"],
		input[type="range"],
		input[type="date"],
		input[type="month"],
		input[type="week"],
		input[type="time"],
		input[type="datetime"],
		input[type="datetime-local"],
		input[type="color"],
		textarea,
		select,
		.reserve-content-wrapper .contact-description .entry-content,
		.reserve-content-wrapper .contact-description .entry-summary,
		table.shop_table_responsive tr td a:hover,
		table.shop_table_responsive tr td a:focus,
		.product-container a.added_to_cart:hover,
		.product-container a.added_to_cart:focus,
		.single-product .product_meta a:hover,
		.single-product .product_meta a:focus,
		.single-product div.product .woocommerce-product-rating .woocommerce-review-link:hover,
		.single-product div.product .woocommerce-product-rating .woocommerce-review-link:focus,
		.woocommerce-info a:hover,
		.woocommerce-info a:focus,
		.variations .reset_variations:hover,
		.variations .reset_variations:focus,
		.woocommerce-tabs ul.tabs li a,
		.woocommerce-pagination ul li a,
		p.stars a:before,
		p.stars a:hover ~ a:before,
		p.stars.selected a.active ~ a:before,
		.reservation-highlight-text,
		.has-background-image .position,
		.team-section.has-background-image .entry-content,
		.team-section.has-background-image .entry-summary,
		.hero-content-wrapper.has-background-image.fluid:not(.has-content-frame) .entry-content,
		.hero-content-wrapper.has-background-image.fluid:not(.has-content-frame) .entry-summary,
		.comment-metadata .edit-link a,
		.comment-metadata .edit-link:before,
		.comment-permalink,
		#team-content-section .position,
		.entry-meta a,
		.entry-meta a:before,
		p.stars:hover a:before,
		p.stars.selected a.active:before,
		p.stars.selected a:not(.active):before,
		.pagination .page-numbers.current,
		.page-links > span,
		 table.shop_table_responsive tr td a,
		.product-container a.added_to_cart,
		.single-product .product_meta a,
		.woocommerce-info a,
		.variations .reset_variations,
		.star-rating span:before,
		.single-product div.product .woocommerce-product-rating .woocommerce-review-link,
		.contact-details li .fa,
		.catch-breadcrumb a:after,
		.pagination a,
		.page-links .page-links-title,
		.page-links a,
		.job-label,
		.wp-playlist-item-artist {
			color: %2$s;
		}

		input[type="text"]:focus,
		input[type="email"]:focus,
		input[type="url"]:focus,
		input[type="password"]:focus,
		input[type="search"]:focus,
		input[type="number"]:focus,
		input[type="tel"]:focus,
		input[type="range"]:focus,
		input[type="date"]:focus,
		input[type="month"]:focus,
		input[type="week"]:focus,
		input[type="time"]:focus,
		input[type="datetime"]:focus,
		input[type="datetime-local"]:focus,
		input[type="color"]:focus,
		textarea:focus,
		select:focus,
		#product-content-section.has-background-image .woocommerce .product-container .button,
		.site-header input[type="search"]:focus {
			border-color: %2$s;
		}

		.bars,
		.skillbar-content,
		#logo-slider-section .owl-dots button span,
		#testimonial-content-section .owl-dots button span {
			  background-color: %2$s;
		}

		.comment-respond input[type="date"]:focus,
		.comment-respond input[type="time"]:focus,
		.comment-respond input[type="datetime-local"]:focus,
		.comment-respond input[type="week"]:focus,
		.comment-respond input[type="month"]:focus,
		.comment-respond input[type="text"]:focus,
		.comment-respond input[type="email"]:focus,
		.comment-respond input[type="url"]:focus,
		.comment-respond input[type="password"]:focus,
		.comment-respond input[type="search"]:focus,
		.comment-respond input[type="tel"]:focus,
		.comment-respond input[type="number"]:focus,
		.comment-respond textarea:focus,
		.wpcf7 input[type="date"]:focus,
		.wpcf7 input[type="time"]:focus,
		.wpcf7 input[type="datetime-local"]:focus,
		.wpcf7 input[type="week"]:focus,
		.wpcf7 input[type="month"]:focus,
		.wpcf7 input[type="text"]:focus,
		.wpcf7 input[type="email"]:focus,
		.wpcf7 input[type="url"]:focus,
		.wpcf7 input[type="password"]:focus,
		.wpcf7 input[type="search"]:focus,
		.wpcf7 input[type="tel"]:focus,
		.wpcf7 input[type="number"]:focus,
		.wpcf7 textarea:focus {
			border-bottom-color: %2$s;
		}
	';

	wp_add_inline_style( 'chique-block-style', sprintf( $css, $secondary_link_color, $secondary_sixty_link_color ) );
}
add_action( 'wp_enqueue_scripts', 'chique_secondary_link_color_css', 11 );

/**
 * Enqueues front-end CSS for the button background color.
 *
 * @since Chique Pro 1.0
 *
 * @see wp_add_inline_style()
 */
function chique_button_background_color_css() {
	$color_scheme          				= chique_get_color_scheme();
	$default_color         				= $color_scheme[9];
	$button_background_color 	= get_theme_mod( 'button_background_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $button_background_color === $default_color ) {
		return;
	}

	$css = '
		/* Button Background Color */
		button,
		input[type="button"],
		input[type="reset"],
		input[type="submit"],
		.button,
		.posts-navigation .nav-links a,
		.pagination .prev,
		.pagination .next,
		.site-main #infinite-handle span button,
		#scrollup,
		.hero-content-wrapper .more-link,
		.recent-blog-content-wrapper .more-recent-posts .more-link,
		.instagram-button .button,
		.view-all-button .more-link,
		.woocommerce div.product form.cart .button,
		.woocommerce a.button,
		.woocommerce a.button.alt,
		.woocommerce button.button,
		.woocommerce button.button.alt,
		.woocommerce #respond input#submit,
		.woocommerce #respond input#submit.alt,
		.woocommerce input.button,
		.woocommerce input.button.alt,
		.woocommerce .product-container .wc-forward,
		.woocommerce .widget_price_filter .ui-slider .ui-slider-range,
		.woocommerce .widget_price_filter .ui-slider .ui-slider-handle,
		.woocommerce nav.woocommerce-pagination ul li a,
		.woocommerce nav.woocommerce-pagination ul li span,
		.onsale,
		.widget_price_filter .ui-slider .ui-slider-handle,
		.widget_price_filter .ui-slider .ui-slider-range,
		.demo_store .woocommerce-store-notice__dismiss-link,
		.woocommerce span.onsale,
		.woocommerce .product-container .added,
		.demo_store .woocommerce-store-notice__dismiss-link:hover,
		.demo_store .woocommerce-store-notice__dismiss-link:focus,
		#product-content-section.has-background-image .woocommerce .product-container .button:hover,
		#product-content-section.has-background-image .woocommerce .product-container .button:focus,
		#footer-newsletter .ewnewsletter .ew-newsletter-wrap #subbutton,
		.pricing-section .hentry .more-link:hover,
		.pricing-section .hentry .more-link:focus,
		.team-social-profile .menu-social-container a:hover,
		.team-social-profile .menu-social-container a:focus,
		.widget-area .menu-social-container a:hover,
		.widget-area .menu-social-container a:focus,
		.portfolio-section .entry-title a:hover:after,
		.portfolio-section .entry-title a:focus:after,
		.widget .tagcloud a:hover,
		.widget .tagcloud a:focus,
		.sticky-label,
		.custom-header .more-link,
		#feature-slider-section .more-link,
		.promotion-headline-wrapper .hentry .more-link,
		.promotion-sale-wrapper .hentry .more-link,
		#logo-slider-section .owl-prev:hover,
		#logo-slider-section .owl-prev:focus,
		#logo-slider-section .owl-next:hover,
		#logo-slider-section .owl-next:focus,
		#testimonial-content-section .owl-prev:hover,
		#testimonial-content-section .owl-prev:focus,
		#testimonial-content-section .owl-next:hover,
		#testimonial-content-section .owl-next:focus,
		.below-site-header .site-header-cart .cart-contents .count,
		.skillbar-bar,
		#sticky-playlist-section .playlist-hide,
		.wp-block-file .wp-block-file__button,
		.wp-block-button .wp-block-button__link {
			background-color: %1$s;
		}

		#logo-slider-section .owl-prev:hover,
		#logo-slider-section .owl-prev:focus,
		#logo-slider-section .owl-next:hover,
		#logo-slider-section .owl-next:focus,
		#testimonial-content-section .owl-prev:hover,
		#testimonial-content-section .owl-prev:focus,
		#testimonial-content-section .owl-next:hover,
		#testimonial-content-section .owl-next:focus {
			border-color: %1$s;
		}


		.sticky-label:after {
			border-left-color: %1$s;
		}
	';

	wp_add_inline_style( 'chique-block-style', sprintf( $css, $button_background_color ) );
}
add_action( 'wp_enqueue_scripts', 'chique_button_background_color_css', 11 );

/**
 * Enqueues front-end CSS for the button text color.
 *
 * @since Chique Pro 1.0
 *
 * @see wp_add_inline_style()
 */
function chique_button_text_color_css() {
	$color_scheme          				= chique_get_color_scheme();
	$default_color         				= $color_scheme[10];
	$button_text_color 	= get_theme_mod( 'button_text_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $button_text_color === $default_color ) {
		return;
	}

	$css = '
		/* Button Text Color */
		button,
		input[type="button"],
		input[type="reset"],
		input[type="submit"],
		.button,
		.posts-navigation .nav-links a,
		.pagination .prev,
		.pagination .next,
		.site-main #infinite-handle span button,
		#scrollup,
		.hero-content-wrapper .more-link,
		.recent-blog-content-wrapper .more-recent-posts .more-link,
		.instagram-button .button,
		.view-all-button .more-link,
		.wp-custom-header-video-button,
		.woocommerce div.product form.cart .button,
		.woocommerce a.button,
		.woocommerce a.button.alt,
		.woocommerce button.button,
		.woocommerce button.button.alt,
		.woocommerce #respond input#submit,
		.woocommerce #respond input#submit.alt,
		.woocommerce input.button,
		.woocommerce input.button.alt,
		.woocommerce .product-container .wc-forward,
		.woocommerce nav.woocommerce-pagination ul li a,
		.woocommerce nav.woocommerce-pagination ul li span,
		.woocommerce nav.woocommerce-pagination ul li span.current,
		.onsale,
		#product-content-section.has-background-image .woocommerce .product-container .button:hover,
		#product-content-section.has-background-image .woocommerce .product-container .button:focus,
		#footer-newsletter .ewnewsletter .ew-newsletter-wrap #subbutton,
		.pricing-section .hentry .more-link:hover,
		.pricing-section .hentry .more-link:focus,
		.team-social-profile .menu-social-container a:hover,
		.team-social-profile .menu-social-container a:focus,
		.widget-area .menu-social-container a:hover,
		.widget-area .menu-social-container a:focus,
		.portfolio-section .entry-title a:hover:after,
		.portfolio-section .entry-title a:focus:after,
		.widget .tagcloud a:hover,
		.widget .tagcloud a:focus,
		.sticky-label,
		.custom-header .more-link,
		#feature-slider-section .more-link,
		.promotion-headline-wrapper .hentry .more-link,
		.promotion-sale-wrapper .hentry .more-link,
		.below-site-header .site-header-cart .cart-contents .count,
		#sticky-playlist-section .playlist-hide,
		.wp-block-file .wp-block-file__button,
		.wp-block-button .wp-block-button__link {
			color: %1$s;
		}
	';

	wp_add_inline_style( 'chique-block-style', sprintf( $css, $button_text_color ) );
}
add_action( 'wp_enqueue_scripts', 'chique_button_text_color_css', 11 );

/**
 * Enqueues front-end CSS for the button hover background color.
 *
 * @since Chique Pro 1.0
 *
 * @see wp_add_inline_style()
 */
function chique_button_hover_background_color_css() {
	$color_scheme          				= chique_get_color_scheme();
	$default_color         				= $color_scheme[11];
	$button_hover_background_color 	= get_theme_mod( 'button_hover_background_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $button_hover_background_color === $default_color ) {
		return;
	}

	// Convert gradient text hex color to rgba.
	$button_hover_background_color_rgb = chique_hex2rgb( $button_hover_background_color );

	// If the rgba values are empty return early.
	if ( empty( $button_hover_background_color_rgb ) ) {
		return;
	}

	$button_hover_five_background_color = vsprintf( 'rgba( %1$s, %2$s, %3$s, 0.96)', $button_hover_background_color_rgb );

	$css = '
		/* Button Hover Background Color */
		button:hover,
		button:focus,
		input[type="button"]:hover,
		input[type="button"]:focus,
		input[type="reset"]:hover,
		input[type="reset"]:focus,
		input[type="submit"]:hover,
		input[type="submit"]:focus,
		.button:hover,
		.button:focus,
		.posts-navigation .nav-links a:hover,
		.posts-navigation .nav-links a:focus,
		.site-main #infinite-handle span button:hover,
		.site-main #infinite-handle span button:focus,
		.pagination .prev:hover,
		.pagination .prev:focus,
		.pagination .next:hover,
		.pagination .next:focus,
		.custom-header .more-link:hover,
		.custom-header .more-link:focus,
		#feature-slider-section .more-link:hover,
		#feature-slider-section .more-link:focus,
		.promotion-sale-wrapper .hentry .more-link:hover,
		.promotion-sale-wrapper .hentry .more-link:focus,
		.promotion-headline-wrapper .hentry .more-link:hover,
		.promotion-headline-wrapper .hentry .more-link:focus,
		#scrollup:hover,
		#scrollup:focus,
		.hero-content-wrapper .more-link:hover,
		.hero-content-wrapper .more-link:focus,
		.recent-blog-content-wrapper .more-recent-posts .more-link:hover,
		.recent-blog-content-wrapper .more-recent-posts .more-link:focus,
		.footer-instagram .instagram-button .button:hover,
		.footer-instagram .instagram-button .button:focus,
		.pagination .nav-links > a:hover,
		.pagination .nav-links > a:focus,
		.page-links a:hover,
		.page-links a:focus,
		.view-all-button .more-link:hover,
		.view-all-button .more-link:focus,
		.wp-custom-header-video-button:hover,
		.wp-custom-header-video-button:focus,
		.woocommerce div.product form.cart .button:hover,
		.woocommerce div.product form.cart .button:focus,
		.woocommerce a.button:hover,
		.woocommerce a.button:focus,
		.woocommerce a.button.alt:hover,
		.woocommerce a.button.alt:focus,
		.woocommerce button.button:hover,
		.woocommerce button.button:focus,
		.woocommerce button.button.alt:hover,
		.woocommerce button.button.alt:focus,
		.woocommerce #respond input#submit:hover,
		.woocommerce #respond input#submit:focus,
		.woocommerce #respond input#submit.alt:hover,
		.woocommerce #respond input#submit.alt:focus,
		.woocommerce input.button:hover,
		.woocommerce input.button:focus,
		.woocommerce input.button.alt:focus,
		.woocommerce input.button.alt:hover,
		.woocommerce .product-container .wc-forward:hover,
		.woocommerce .product-container .wc-forward:focus,
		.woocommerce nav.woocommerce-pagination ul li a:hover,
		.woocommerce nav.woocommerce-pagination ul li a:focus,
		.woocommerce nav.woocommerce-pagination ul li span.current,
		.contact-details li a:hover .fa,
		.contact-details li a:focus .fa,
		#footer-newsletter .ewnewsletter .ew-newsletter-wrap #subbutton:hover,
		#footer-newsletter .ewnewsletter .ew-newsletter-wrap #subbutton:focus,
		.pricing-section .hentry .more-link,
		.team-social-profile .menu-social-container a,
		.widget-area .menu-social-container a,
		.portfolio-section .entry-title a:after,
		.wp-block-file .wp-block-file__button:hover,
		.wp-block-file .wp-block-file__button:focus,
		.wp-block-button .wp-block-button__link:hover,
		.wp-block-button .wp-block-button__link:focus {
			background-color: %1$s;
		}

		blockquote,
		.widget .tagcloud a {
			color: %1$s;
		}

		.widget .tagcloud a {
			background-color: %2$s;
		}
	';

	wp_add_inline_style( 'chique-block-style', sprintf( $css, $button_hover_background_color, $button_hover_five_background_color ) );
}
add_action( 'wp_enqueue_scripts', 'chique_button_hover_background_color_css', 11 );

/**
 * Enqueues front-end CSS for the button hover text color.
 *
 * @since Chique Pro 1.0
 *
 * @see wp_add_inline_style()
 */
function chique_button_hover_text_color_css() {
	$color_scheme          				= chique_get_color_scheme();
	$default_color         				= $color_scheme[12];
	$button_hover_text_color 	= get_theme_mod( 'button_hover_text_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $button_hover_text_color === $default_color ) {
		return;
	}

	$css = '
		/* Button Hover Text Color */
		button:hover,
		button:focus,
		input[type="button"]:hover,
		input[type="button"]:focus,
		input[type="reset"]:hover,
		input[type="reset"]:focus,
		input[type="submit"]:hover,
		input[type="submit"]:focus,
		.button:hover,
		.button:focus,
		.prev.page-numbers:hover,
		.prev.page-numbers:focus,
		.next.page-numbers:hover,
		.next.page-numbers:focus,
		.recent-blog-content-wrapper .more-recent-posts .more-link:hover,
		.recent-blog-content-wrapper .more-recent-posts .more-link:focus,
		.pagination .nav-links > a:hover,
		.pagination .nav-links > a:focus,
		.page-links a:hover,
		.page-links a:focus,
		#scrollup:hover,
		#scrollup:focus,
		.view-all-button .more-link:hover,
		.view-all-button .more-link:focus,
		.wp-custom-header-video-button:hover,
		.wp-custom-header-video-button:focus,
		.woocommerce div.product form.cart .button:hover,
		.woocommerce div.product form.cart .button:focus,
		.woocommerce a.button:hover,
		.woocommerce a.button:focus,
		.woocommerce a.button.alt:hover,
		.woocommerce a.button.alt:focus,
		.woocommerce button.button:hover,
		.woocommerce button.button:focus,
		.woocommerce button.button.alt:hover,
		.woocommerce button.button.alt:focus,
		.woocommerce #respond input#submit:hover,
		.woocommerce #respond input#submit:focus,
		.woocommerce #respond input#submit.alt:hover,
		.woocommerce #respond input#submit.alt:focus,
		.woocommerce input.button:hover,
		.woocommerce input.button:focus,
		.woocommerce input.button.alt:focus,
		.woocommerce input.button.alt:hover,
		.woocommerce .product-container .wc-forward:hover,
		.woocommerce .product-container .wc-forward:focus,
		.woocommerce nav.woocommerce-pagination ul li a:hover,
		.woocommerce nav.woocommerce-pagination ul li a:focus,
		.woocommerce nav.woocommerce-pagination ul li span.current,
		.site-main #infinite-handle span button:hover,
		.site-main #infinite-handle span button:focus,
		.contact-details li a:hover .fa,
		.contact-details li a:focus .fa,
		.posts-navigation .nav-links a:hover,
		.posts-navigation .nav-links a:focus,
		#footer-newsletter .ewnewsletter .ew-newsletter-wrap #subbutton:hover,
		#footer-newsletter .ewnewsletter .ew-newsletter-wrap #subbutton:focus,
		.pricing-section .hentry .more-link,
		.team-social-profile .menu-social-container a,
		.widget-area .menu-social-container a,
		.portfolio-section .entry-title a:after,
		.custom-header .more-link:hover,
		.custom-header .more-link:focus,
		#feature-slider-section .more-link:hover,
		#feature-slider-section .more-link:focus,
		.hero-content-wrapper .more-link:hover,
		.promotion-sale-wrapper .hentry .more-link:hover,
		.hero-content-wrapper .more-link:focus,
		.promotion-sale-wrapper .hentry .more-link:focus,
		.promotion-headline-wrapper .hentry .more-link:hover,
		.promotion-headline-wrapper .hentry .more-link:focus,
		.wp-block-file .wp-block-file__button:hover,
		.wp-block-file .wp-block-file__button:focus,
		.wp-block-button .wp-block-button__link:hover,
		.wp-block-button .wp-block-button__link:focus,
		.social-contact .menu-social-container li a:hover,
		.social-contact .menu-social-container li a:focus {
			color: %1$s;
		}
	';

	wp_add_inline_style( 'chique-block-style', sprintf( $css, $button_hover_text_color ) );
}
add_action( 'wp_enqueue_scripts', 'chique_button_hover_text_color_css', 11 );

/**
 * Enqueues front-end CSS for the border color.
 *
 * @since Chique Pro 1.0
 *
 * @see wp_add_inline_style()
 */
function chique_border_color_css() {
	$color_scheme  = chique_get_color_scheme();
	$default_color = $color_scheme[13];
	$border_color  = get_theme_mod( 'border_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $border_color === $default_color ) {
		return;
	}

	$css = '
		/* Border Color */
		input[type="text"],
		input[type="email"],
		input[type="url"],
		input[type="password"],
		input[type="search"],
		input[type="number"],
		input[type="tel"],
		input[type="range"],
		input[type="date"],
		input[type="month"],
		input[type="week"],
		input[type="time"],
		input[type="datetime"],
		input[type="datetime-local"],
		input[type="color"],
		textarea,
		select,
		.tag-cloud-link,
		.two-columns-layout .singular-content-wrap,
		table,
		th,
		td,
		fieldset,
		abbr,
		acronym,
		.is-open .social-navigation-wrapper,
		.comment-list article,
		.comment-list .pingback,
		.comment-list .trackback,
		.site-content + .recent-blog-content-wrapper,
		.site-content,
		.pagination .nav-links > span,
		.pagination .nav-links > a,
		.page-links a,
		.page-links > span,
		#footer-instagram,
		.single-product .product_meta,
		.woocommerce-account .woocommerce-MyAccount-navigation,
		.woocommerce-account .woocommerce-MyAccount-navigation li,
		.woocommerce nav.woocommerce-pagination ul,
		.post-navigation .nav-links,
		.is-open .menu-inside-wrapper,
		.comment-respond,
		.single-blog .archive-content-wrap .hentry-inner,
		.why-choose-us-section.modern-style .hentry,
		.team-section .entry-container,
		.ewtabbedrecentpopular .ui-tabs .ui-tabs-panel,
		.ewtabbedrecentpopular .ui-state-active,
		.ewtabbedrecentpopular .ui-widget-content .ui-state-active,
		.ewtabbedrecentpopular .ui-widget-header .ui-state-active,
		.essential-widgets .hentry,
		.contact-section .entry-header:after,
		.reserve-content-wrapper .contact-description:before,
		#logo-slider-section .static-logo.layout-two .hentry,
		#logo-slider-section .static-logo.layout-four .hentry,
		#logo-slider-section .static-logo.layout-three .hentry,
		#logo-slider-section .static-logo.layout-four .hentry:nth-child(2n),
		#logo-slider-section .static-logo.layout-five .hentry,
		#logo-slider-section .static-logo.layout-five .hentry:nth-child(2n),
		.site-header-cart .cart-contents,
		.no-header-media-image.no-featured-slider.grid-blog .site-overlay + .recent-blog-content-wrapper,
		.wp-playlist-item,
		.countdown #clock .count-wrap,
		.venue .hentry-inner,
		.wp-block-table,
		.wp-block-table th,
		.wp-block-table td,
		.wp-block-latest-comments__comment {
			border-color: %1$s;
		}

		@media screen and (min-width: 35.5em) {
			.featured-content-section .layout-two .entry-container,
			.featured-content-section .layout-four .entry-container,
			.featured-content-section .layout-four .hentry:nth-child(2n) .entry-container {
				border-color: %1$s;
			}
		}

		@media screen and (min-width: 41.6875em) {
			.featured-content-section .layout-three .entry-container {
				border-color: #%1$s;
			}
		}

		.comment-respond input[type="date"],
		.comment-respond input[type="time"],
		.comment-respond input[type="datetime-local"],
		.comment-respond input[type="week"],
		.comment-respond input[type="month"],
		.comment-respond input[type="text"],
		.comment-respond input[type="email"],
		.comment-respond input[type="url"],
		.comment-respond input[type="password"],
		.comment-respond input[type="search"],
		.comment-respond input[type="tel"],
		.comment-respond input[type="number"],
		.comment-respond textarea,
		.wpcf7 input[type="date"],
		.wpcf7 input[type="time"],
		.wpcf7 input[type="datetime-local"],
		.wpcf7 input[type="week"],
		.wpcf7 input[type="month"],
		.wpcf7 input[type="text"],
		.wpcf7 input[type="email"],
		.wpcf7 input[type="url"],
		.wpcf7 input[type="password"],
		.wpcf7 input[type="search"],
		.wpcf7 input[type="tel"],
		.wpcf7 input[type="number"],
		.wpcf7 textarea {
			border-bottom-color: %1$s;
		}

		.woocommerce nav.woocommerce-pagination,
		.woocommerce-account .woocommerce-MyAccount-navigation a:hover,
		.woocommerce-account .woocommerce-MyAccount-navigation a:focus,
		.woocommerce-account .woocommerce-MyAccount-navigation .is-active a,
		.woocommerce-message,
		.woocommerce-info,
		.woocommerce-error,
		.woocommerce-noreviews,
		.demo_store,
		p.no-comments,
		ul.wc_payment_methods .payment_box,
		.widget_price_filter .price_slider_wrapper .ui-widget-content,
		.woocommerce-tabs .panel input[type="text"],
		.woocommerce-tabs .panel input[type="email"],
		.woocommerce-tabs .panel textarea {
			background-color: %1$s;
		}
	';

	wp_add_inline_style( 'chique-block-style', sprintf( $css, $border_color ) );
}
add_action( 'wp_enqueue_scripts', 'chique_border_color_css', 11 );

/**
 * Enqueues front-end CSS for tertiary background color
 *
 * @since Chique Pro 1.0
 *
 * @see wp_add_inline_style()
 */
function chique_tertiary_background_color_css() {
	$color_scheme          				= chique_get_color_scheme();
	$default_color         				= $color_scheme[14];
	$tertiary_background_color 	= get_theme_mod( 'tertiary_background_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $tertiary_background_color === $default_color ) {
		return;
	}

	$css = '
		/* Tertiary Background Color */
		.site-footer,
		.blog.grid-blog .archive-content-wrap .hentry .entry-container,
		.archive.grid-blog .archive-content-wrap .hentry .entry-container,
		.grid-blog .recent-blog-content-wrapper .archive-content-wrap .hentry .entry-container,
		.grid-blog .sticky-label:before,
		.grid-blog .sticky-label:after,
		#timeline-section .hentry:before,
		#timeline-section .section-content-wrapper:before {
			background-color: %1$s;
		}
	';

	wp_add_inline_style( 'chique-block-style', sprintf( $css, $tertiary_background_color ) );
}
add_action( 'wp_enqueue_scripts', 'chique_tertiary_background_color_css', 11 );

/**
 * Enqueues front-end CSS for Text Color with Background
 *
 * @since Chique Pro 1.0
 *
 * @see wp_add_inline_style()
 */
function chique_text_color_with_background_css() {
	$color_scheme          				= chique_get_color_scheme();
	$default_color         				= $color_scheme[15];
	$text_color_with_background 	= get_theme_mod( 'text_color_with_background', $default_color );

	// Don't do anything if the current color is the default.
	if ( $text_color_with_background === $default_color ) {
		return;
	}

	$css = '

		/* Text Color With Background */
		.archive .custom-header,
		.search .custom-header,
		.error404 .custom-header,
		.woocommerce .product-container .added,
		.woocommerce .product-container .button.added,
		.demo_store .woocommerce-store-notice__dismiss-link,
		.demo_store .woocommerce-store-notice__dismiss-link:hover,
		.demo_store .woocommerce-store-notice__dismiss-link:focus,
		.gallery-section figcaption,
		.scroll-down,
		.has-background-image .section-title,
		.has-background-image .section-description,
		.has-background-image .before-text,
		.has-background-image .after-text,
		.promotion-headline-wrapper.has-background-image:not(.content-frame) .section-title,
		.promotion-headline-wrapper.has-background-image:not(.content-frame) .section-description,
		.promotion-headline-wrapper.has-background-image:not(.content-frame) .entry-content,
		.promotion-headline-wrapper.has-background-image:not(.content-frame) .entry-summary,
		.widget.has-background-image .widget-title,
		.team-section.has-background-image .entry-title a,
		.team-section.has-background-image .hentry .more-link,
		#product-content-section.has-background-image ul.products li.product .woocommerce-loop-product__title,
		#product-content-section.has-background-image .woocommerce-Price-amount,
		#product-content-section.has-background-image .woocommerce .product-container .button,
		.hero-content-wrapper.has-background-image.fluid:not(.has-content-frame) .entry-title span,
		.stats-section .entry-title a,
		.stats-section .hentry .more-link,
		.stats-section .section-title,
		.stats-section .section-description,
		.stats-section .entry-content,
		.stats-section .entry-summary,
		.pricing-section .entry-title,
		.pricing-section .entry-title a,
		.pricing-section .package-price,
		.pricing-section .package-month,
		.portfolio-content-wrapper .entry-title,
		.portfolio-content-wrapper .entry-title a,
		.portfolio-content-wrapper .entry-meta a,
		.portfolio-content-wrapper .entry-meta a:before,
		.catch-breadcrumb,
		.catch-breadcrumb a,
		.events-section .entry-title a,
		.events-section .entry-meta a,
		.events-section .hentry .more-link,
		.events-section .entry-content,
		.events-section .entry-summary {
			color: %1$s;
		}

		.wp-custom-header-video-button,
		.stats-section .more-link .fa {
			border-color: %1$s;
		}
	';

	wp_add_inline_style( 'chique-block-style', sprintf( $css, $text_color_with_background ) );
}
add_action( 'wp_enqueue_scripts', 'chique_text_color_with_background_css', 11 );

/**
 * Enqueues front-end CSS for Slider/Header Media Color
 *
 * @since Chique Pro 1.0
 *
 * @see wp_add_inline_style()
 */
function chique_slider_color_css() {
	$color_scheme          				= chique_get_color_scheme();
	$default_color         				= $color_scheme[16];
	$slider_color 	= get_theme_mod( 'slider_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $slider_color === $default_color ) {
		return;
	}

	$css = '
		/* Slider Text Color */
		#feature-slider-section .owl-dot,
		#feature-slider-section .owl-prev,
		#feature-slider-section .owl-next,
		#feature-slider-section .entry-title a,
		#feature-slider-section .entry-title,
		.custom-header .entry-title {
			color: %1$s;
		}
	';

	wp_add_inline_style( 'chique-block-style', sprintf( $css, $slider_color ) );
}
add_action( 'wp_enqueue_scripts', 'chique_slider_color_css', 11 );


/**
 * Enqueues front-end CSS for Slider/Header Media Hover Color
 *
 * @since Chique Pro 1.0
 *
 * @see wp_add_inline_style()
 */
function chique_slider_hover_color_css() {
	$color_scheme          				= chique_get_color_scheme();
	$default_color         				= $color_scheme[17];
	$slider_hover_color 	= get_theme_mod( 'slider_hover_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $slider_hover_color === $default_color ) {
		return;
	}

	$css = '
		/* Slider Hover Color */
		#feature-slider-section .owl-dot:hover,
		#feature-slider-section .owl-dot:focus,
		#feature-slider-section .owl-prev:hover,
		#feature-slider-section .owl-prev:focus,
		#feature-slider-section .owl-next:hover,
		#feature-slider-section .owl-next:focus,
		#feature-slider-section .entry-title a:hover,
		#feature-slider-section .entry-title a:focus {
			color: %1$s;
		}
	';

	wp_add_inline_style( 'chique-block-style', sprintf( $css, $slider_hover_color ) );
}
add_action( 'wp_enqueue_scripts', 'chique_slider_hover_color_css', 11 );

/**
 * Enqueues front-end CSS for Slider/Header Media Content Color
 *
 * @since Chique Pro 1.0
 *
 * @see wp_add_inline_style()
 */
function chique_slider_content_color_css() {
	$color_scheme          				= chique_get_color_scheme();
	$default_color         				= $color_scheme[18];
	$slider_content_color 	= get_theme_mod( 'slider_content_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $slider_content_color === $default_color ) {
		return;
	}

	$css = '
		/* Slider Content Color */
		#feature-slider-section .entry-content,
		#feature-slider-section .entry-summary,
		.custom-header .entry-content,
		.custom-header .entry-summary {
			color: %1$s;
		}
	';

	wp_add_inline_style( 'chique-block-style', sprintf( $css, $slider_content_color ) );
}
add_action( 'wp_enqueue_scripts', 'chique_slider_content_color_css', 11 );

/**
 * Enqueues front-end CSS for Secondary Link Hover Color
 *
 * @since Chique Pro 1.0
 *
 * @see wp_add_inline_style()
 */
function chique_secondary_hover_link_color_css() {
	$color_scheme          				= chique_get_color_scheme();
	$default_color         				= $color_scheme[19];
	$secondary_link_hover_color 	= get_theme_mod( 'secondary_link_hover_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $secondary_link_hover_color === $default_color ) {
		return;
	}

	$css = '
		/* Secondary Link Hover Color */
		.main-navigation a:hover,
		.main-navigation a:focus,
		.main-navigation .menu > .current-menu-item > a,
		.main-navigation .menu > .current-menu-ancestor > a,
		.hero-content-wrapper.has-background-image.fluid:not(.has-content-frame) .entry-title,
		.dropdown-toggle:hover,
		.dropdown-toggle:focus,
		.search-submit:hover,
		.search-submit:focus,
		.more-link:hover,
		.more-link:focus,
		.entry-title a:hover,
		.entry-title a:focus,
		.widget a:hover,
		.widget a:focus,
		.page-numbers:hover,
		.page-numbers:focus,
		.woocommerce .catch-breadcrumb .woocommerce-breadcrumb a:hover,
		.woocommerce .catch-breadcrumb .woocommerce-breadcrumb a:focus,
		.catch-breadcrumb a:hover,
		.catch-breadcrumb a:focus,
		.site-title a:hover,
		.site-title a:focus,
		.site-footer a:hover,
		.site-footer a:focus,
		 ul.products li.product .woocommerce-loop-product__title:hover,
		 ul.products li.product .woocommerce-loop-product__title:focus,
		.portfolio-section .entry-title a:hover,
		.portfolio-section .entry-title a:focus,
		.scroll-down:hover,
		.scroll-down:focus,
		.team-section.has-background-image .entry-title a:hover,
		.team-section.has-background-image .entry-title a:focus,
		.team-section.has-background-image .hentry .more-link:hover,
		.team-section.has-background-image .hentry .more-link:focus,
		#product-content-section.has-background-image ul.products li.product .woocommerce-loop-product__title:hover,
		#product-content-section.has-background-image ul.products li.product .woocommerce-loop-product__title:focus,
		#product-content-section.has-background-image .woocommerce-Price-amount:hover,
		#product-content-section.has-background-image .woocommerce-Price-amount:focus,
		.stats-section .entry-title a:hover,
		.stats-section .entry-title a:focus,
		.stats-section .hentry .more-link:hover,
		.stats-section .hentry .more-link:focus,
		.site-info a:hover,
		.site-info a:focus,
		.site-footer .entry-title a:hover,
		.site-footer .entry-title a:focus,
		.contact-details a:hover,
		.contact-details a:hover,
		.cart-contents:hover,
		.cart-contents:focus,
		.social-navigation a:hover,
		.social-navigation a:focus,
		.entry-meta a:hover,
		.entry-meta a:focus,
		 .woocommerce-tabs ul.tabs li a:hover,
		.woocommerce-tabs ul.tabs li a:focus,
		.woocommerce-pagination ul li span.current,
		.woocommerce-tabs ul.tabs li.active a,
		.woocommerce-pagination ul li a:hover,
		.portfolio-content-wrapper .entry-title a:hover,
		.portfolio-content-wrapper .entry-title a:focus,
		.portfolio-content-wrapper .entry-meta a:hover,
		.portfolio-content-wrapper .entry-meta a:focus,
		.portfolio-content-wrapper .entry-meta a:hover::before,
		.portfolio-content-wrapper .entry-meta a:focus::before,
		.entry-meta a:hover::before,
		.entry-meta a:focus::before,
		.author-name a:hover,
		.author-name a:focus,
		.comment-reply-link:hover,
		.comment-reply-link:focus,
		#cancel-comment-reply-link:hover::before,
		#cancel-comment-reply-link:focus::before,
		table a:hover,
		table a:focus,
		.comment-permalink:hover,
		.comment-permalink:focus,
		.menu-toggle:hover .menu-label,
		.menu-toggle:focus .menu-label,
		.pricing-section .entry-title a:hover,
		.pricing-section .entry-title a:focus,
		.product-category.product a:hover h2,
		.product-category.product a:focus h2,
		.product-category.product a:hover h2 mark,
		.product-category.product a:focus h2 mark,
		.pricing-section .entry-content ul li:before,
		.pricing-section .entry-summary ul li:before,
		.chique-mejs-container.mejs-container button:hover,
		.chique-mejs-container.mejs-container button:focus,
		#sticky-playlist-section .chique-mejs-container.mejs-container .mejs-controls .mejs-playpause-button.mejs-button button:hover,
		#sticky-playlist-section .chique-mejs-container.mejs-container .mejs-controls .mejs-playpause-button.mejs-button button:focus,
		.wp-playlist .wp-playlist-caption:hover .wp-playlist-item-title,
		.wp-playlist .wp-playlist-caption:focus .wp-playlist-item-title,
		.wp-playlist .wp-playlist-playing .wp-playlist-caption .wp-playlist-item-title,
		.wp-playlist .wp-playlist-playing .wp-playlist-caption .wp-playlist-item-title:after,
		.wp-playlist-playing .wp-playlist-item-length,
		#sticky-playlist-section .wp-playlist .wp-playlist-playing .wp-playlist-item-length,
		.events-section .entry-title a:hover,
		.events-section .entry-title a:focus,
		.events-section .entry-meta a:hover,
		.events-section .entry-meta a:focus,
		.events-section .hentry .more-link:hover,
		.events-section .hentry .more-link:focus {
			color: %1$s;
		}

		.more-link .fa,
		.woocommerce-info,
		.woocommerce-message,
		blockquote,
		.wp-custom-header-video-button:hover,
		.wp-custom-header-video-button:focus,
		#logo-slider-section .owl-prev,
		#logo-slider-section .owl-next,
		#testimonial-content-section .owl-prev,
		#testimonial-content-section .owl-next,
		.social-contact .menu-social-container li a,
		.wp-playlist .wp-playlist-playing .wp-playlist-caption .wp-playlist-item-title:after,
		.mejs-time-handle,
		.mejs-time-handle-content,
		.wp-block-pullquote,
		.wp-block-quote:not(.is-large):not(.is-style-large) {
			border-color: %1$s;
		}

		@media screen and (min-width: 64em) {
			.navigation-default .main-navigation .menu > .current-menu-item > a,
			.navigation-default .main-navigation .menu > .current-menu-ancestor > a,
			.navigation-classic .main-navigation ul ul li a:hover,
			.navigation-classic .main-navigation ul ul li a:focus {
				color: %1$s;
			}
		}

		.menu-toggle:hover .bars,
		.menu-toggle:focus .bars,
		.slide-progress,
		#logo-slider-section .owl-dots button span:hover,
		#logo-slider-section .owl-dots button span:focus,
		#testimonial-content-section .owl-dots button span:hover,
		#testimonial-content-section .owl-dots button span:focus,
		#logo-slider-section .owl-dots button.active span,
		#testimonial-content-section .owl-dots button.active span,
		.stats-section,
		.pricing-section .entry-header-wrap,
		.social-contact .menu-social-container li a:hover,
		.social-contact .menu-social-container li a:focus,
		.mejs-time-handle,
		.mejs-time-handle-content,
		.chique-mejs-container.mejs-container .mejs-controls .mejs-time-rail .mejs-time-current,
		.mejs-controls .mejs-horizontal-volume-slider .mejs-horizontal-volume-current,
		.mejs-controls .mejs-time-rail .mejs-time-loaded {
			background-color: %1$s;
		}
	';

	wp_add_inline_style( 'chique-block-style', sprintf( $css, $secondary_link_hover_color ) );
}
add_action( 'wp_enqueue_scripts', 'chique_secondary_hover_link_color_css', 11 );

/**
 * Enqueues front-end CSS for gradient background color
 *
 * @since Intuitive Pro 1.0
 *
 * @see wp_add_inline_style()
 */
function chique_gradient_background_color_css() {
	$color_scheme                   = chique_get_color_scheme();
	$gradient_background_color_first  = get_theme_mod( 'gradient_background_color_first', $color_scheme[20] );
	$gradient_background_color_second = get_theme_mod( 'gradient_background_color_second', $color_scheme[21] );

	// Don't do anything if the current color is the default.
	if ( $gradient_background_color_first === $color_scheme[20] && $gradient_background_color_second === $color_scheme[21] ) {
		return;
	}

	$css = '
		/* Gradient Background Color */
		.testimonial-content-section,
		.logo-slider-section,
		#footer-newsletter,
		ul.products li.product {
			background-image: -webkit-gradient(linear, left top, left bottom, from(%1$s), to(%2$s));
		    background-image: -o-linear-gradient(top, %1$s, %2$s);
		    background-image: linear-gradient(to bottom, %1$s, %2$s);
		}
	';

	wp_add_inline_style( 'chique-block-style', sprintf( $css, $gradient_background_color_first, $gradient_background_color_second ) );
}
add_action( 'wp_enqueue_scripts', 'chique_gradient_background_color_css', 11 );


/**
 * Converts a HEX value to RGB.
 *
 * @since Chique Pro 1.0
 *
 * @param string $color The original color, in 3- or 6-digit hexadecimal form.
 * @return array Array containing RGB (red, green, and blue) values for the given
 *               HEX code, empty array otherwise.
 */
function chique_hex2rgb( $color ) {
	$color = trim( $color, '#' );

	if ( strlen( $color ) === 3 ) {
		$r = hexdec( substr( $color, 0, 1 ).substr( $color, 0, 1 ) );
		$g = hexdec( substr( $color, 1, 1 ).substr( $color, 1, 1 ) );
		$b = hexdec( substr( $color, 2, 1 ).substr( $color, 2, 1 ) );
	} else if ( strlen( $color ) === 6 ) {
		$r = hexdec( substr( $color, 0, 2 ) );
		$g = hexdec( substr( $color, 2, 2 ) );
		$b = hexdec( substr( $color, 4, 2 ) );
	} else {
		return array();
	}

	return array( 'red' => $r, 'green' => $g, 'blue' => $b );
}
