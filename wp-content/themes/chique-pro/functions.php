<?php

/**
 * Add an HTML class to MediaElement.js container elements to aid styling.
 *
 * Extends the core _wpmejsSettings object to add a new feature via the
 * MediaElement.js plugin API.
 */
function chique_mejs_add_container_class() {
	if ( ! wp_script_is( 'mediaelement', 'done' ) ) {
		return;
	}
	?>
	<script>
	(function() {
		var settings = window._wpmejsSettings || {};

		settings.features = settings.features || mejs.MepDefaults.features;

		settings.features.push( 'chique_class' );

		MediaElementPlayer.prototype.buildchique_class = function(player, controls, layers, media) {
			if ( ! player.isVideo ) {
				var container = player.container[0] || player.container;

				container.style.height = '';
				container.style.width = '';
				player.options.setDimensions = false;
			}

			if ( jQuery( '#' + player.id ).parents('#sticky-playlist-section').length ) {
				player.container.addClass( 'chique-mejs-container chique-mejs-sticky-playlist-container' );

				jQuery( '#' + player.id ).parent().children('.wp-playlist-tracks').addClass('displaynone');

				var volume_slider = controls[0].children[5];

				if ( jQuery( '#' + player.id ).parent().children('.wp-playlist-tracks').length > 0) {
					var playlist_button =
					jQuery('<div class="mejs-button mejs-playlist-button mejs-toggle-playlist">' +
						'<button type="button" aria-controls="mep_0" title="Toggle Playlist"></button>' +
					'</div>')

					// append it to the toolbar
					.appendTo( jQuery( '#' + player.id ) )

					// add a click toggle event
					.click(function(e) {
						jQuery( '#' + player.id ).parent().children('.wp-playlist-tracks').slideToggle();
						jQuery( this ).toggleClass('is-open');
					});

					var play_button = controls[0].children[0];

					// Add next button after volume slider
					var next_button =
					jQuery('<div class="mejs-button mejs-next-button mejs-next">' +
						'<button type="button" aria-controls="' + player.id
						+ '" title="Next Track"></button>' +
					'</div>')

					// insert after volume slider
					.insertAfter(play_button)

					// add a click toggle event
					.click(function() {
						jQuery( '#' + player.id ).parent().find( '.wp-playlist-next').trigger('click');
					});

					// Add prev button after volume slider
					var previous_button =
					jQuery('<div class="mejs-button mejs-previous-button mejs-previous">' +
						'<button type="button" aria-controls="' + player.id
						+ '" title="Previous Track"></button>' +
					'</div>')

					// insert after volume slider
					.insertBefore( play_button )

					// add a click toggle event
					.click(function() {
						jQuery( '#' + player.id ).parent().find( '.wp-playlist-prev').trigger('click');
					});
				}
			} else {
				player.container.addClass( 'chique-mejs-container' );
				if ( jQuery( '#' + player.id ).parent().children('.wp-playlist-tracks').length > 0) {
					var play_button = controls[0].children[0];

					// Add next button after volume slider
					var next_button =
					jQuery('<div class="mejs-button mejs-next-button mejs-next">' +
						'<button type="button" aria-controls="' + player.id
						+ '" title="Next Track"></button>' +
					'</div>')

					// insert after volume slider
					.insertAfter(play_button)

					// add a click toggle event
					.click(function() {
						jQuery( '#' + player.id ).parent().find( '.wp-playlist-next').trigger('click');
					});

					// Add prev button after volume slider
					var previous_button =
					jQuery('<div class="mejs-button mejs-previous-button mejs-previous">' +
						'<button type="button" aria-controls="' + player.id
						+ '" title="Previous Track"></button>' +
					'</div>')

					// insert after volume slider
					.insertBefore( play_button )

					// add a click toggle event
					.click(function() {
						jQuery( '#' + player.id ).parent().find( '.wp-playlist-prev').trigger('click');
					});
				}
			}
		}
	})();
	</script>
	<?php
}
add_action( 'wp_print_footer_scripts', 'chique_mejs_add_container_class' );

if ( ! function_exists( 'chique_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function chique_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Chique Pro, use a find and replace
		 * to change 'chique-pro' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'chique-pro', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// Used in Recent Posts
		set_post_thumbnail_size( 940, 528, true ); // Ratio 16:9

		// Used in Grid Blog
		add_image_size( 'chique-grid-blog', 666, 999, true ); // Image Ratio 16:9

		// Used in Slider and Promotion
		add_image_size( 'chique-slider', 1650, 1080, true ); // Image Ratio 16:9

		// Used in Featured Content
		add_image_size( 'chique-featured', 666, 499, true ); // Image Ratio 4:3

		// Used in Custom Header for single and archive pages
		add_image_size( 'chique-header-inner', 1650, 480, true );

		//Used in Hero Content, Services, Team, Shop, Skills Section
		add_image_size( 'chique-hero-content', 825, 825, true ); // Image Ratio 1:1

		// Used in Logo Sections.
		add_image_size( 'chique-logo', 180, 120, true ); //  Image Ratio 3:2

		// Used in Why Choose Us and Testimonial Sections.
		add_image_size( 'chique-why-choose-us', 100, 100, true ); //  Image Ratio 16:8

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'menu-1'       		=> esc_html__( 'Primary', 'chique-pro' ),
			'social'     		=> esc_html__( 'Social on Header', 'chique-pro' ),
			'social-footer'		=> esc_html__( 'Social On Footer', 'chique-pro' ),
			'social-contact'	=> esc_html__( 'Social on Contact Info', 'chique-pro' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );

		// Add support for Block Styles.
		add_theme_support( 'wp-block-styles' );

		// Add support for full and wide align images.
		add_theme_support( 'align-wide' );

		// Add support for editor styles.
		add_theme_support( 'editor-styles' );

		// Add support for responsive embeds.
		add_theme_support( 'responsive-embeds' );

		// Add custom editor font sizes.
		add_theme_support(
			'editor-font-sizes',
			array(
				array(
					'name'      => esc_html__( 'Small', 'chique-pro' ),
					'shortName' => esc_html__( 'S', 'chique-pro' ),
					'size'      => 13,
					'slug'      => 'small',
				),
				array(
					'name'      => esc_html__( 'Normal', 'chique-pro' ),
					'shortName' => esc_html__( 'M', 'chique-pro' ),
					'size'      => 16,
					'slug'      => 'normal',
				),
				array(
					'name'      => esc_html__( 'Large', 'chique-pro' ),
					'shortName' => esc_html__( 'L', 'chique-pro' ),
					'size'      => 42,
					'slug'      => 'large',
				),
				array(
					'name'      => esc_html__( 'Huge', 'chique-pro' ),
					'shortName' => esc_html__( 'XL', 'chique-pro' ),
					'size'      => 54,
					'slug'      => 'huge',
				),
			)
		);

		// Add support for custom color scheme.
		add_theme_support( 'editor-color-palette', array(
			array(
				'name'  => esc_html__( 'White', 'chique-pro' ),
				'slug'  => 'white',
				'color' => '#ffffff',
			),
			array(
				'name'  => esc_html__( 'Black', 'chique-pro' ),
				'slug'  => 'black',
				'color' => '#000000',
			),
			array(
				'name'  => esc_html__( 'Medium Black', 'chique-pro' ),
				'slug'  => 'medium-black',
				'color' => '#222222',
			),
			array(
				'name'  => esc_html__( 'Gray', 'chique-pro' ),
				'slug'  => 'gray',
				'color' => '#999999',
			),
			array(
				'name'  => esc_html__( 'Light Gray', 'chique-pro' ),
				'slug'  => 'light-gray',
				'color' => '#f9f9f9',
			),
			array(
				'name'  => esc_html__( 'Deep Blush', 'chique-pro' ),
				'slug'  => 'deep-blush',
				'color' => '#e186a2',
			),
			array(
				'name'  => esc_html__( 'Tradewind', 'chique-pro' ),
				'slug'  => 'tradewind',
				'color' => '#6bbcba',
			),
		) );

		add_editor_style( array( 'assets/css/editor-style.css', chique_fonts_url() ) );

		// Support Alternate image for services, testimonials when using Essential Content Types Pro.
		if ( class_exists( 'Essential_Content_Types_Pro' ) ) {
			add_theme_support( 'ect-alt-featured-image-jetpack-testimonial' );
		}

		/**
		 * Add Support for Sticky Menu.
		 */
		add_theme_support( 'catch-sticky-menu', apply_filters( 'chique_sticky_menu_args', array(
			'sticky_desktop_menu_selector' => '#masthead',
			'sticky_mobile_menu_selector'  => '#masthead',
			'sticky_background_color'      => '#ffffff',
			'sticky_text_color'            => '#000000',
		) ) );

		/**
		 * Adds support for Catch Breadcrumb.
		 */
		add_theme_support( 'catch-breadcrumb', array(
			'content_selector'   => '.custom-header .entry-header',
			'breadcrumb_dynamic' => 'after',
		) );
	}
endif;
add_action( 'after_setup_theme', 'chique_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function chique_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'chique_content_width', 940 );
}
add_action( 'after_setup_theme', 'chique_content_width', 0 );

if ( ! function_exists( 'chique_template_redirect' ) ) :
	/**
	 * Set the content width in pixels, based on the theme's design and stylesheet for different value other than the default one
	 *
	 * @global int $content_width
	 */
	function chique_template_redirect() {
		$layout = chique_get_theme_layout();

		if ( 'no-sidebar-full-width' === $layout ) {
			$GLOBALS['content_width'] = 1320;
		}
	}
endif;
add_action( 'template_redirect', 'chique_template_redirect' );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function chique_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'chique-pro' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'chique-pro' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-wrap">',
		'after_widget'  => '</div></section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer 1', 'chique-pro' ),
		'id'            => 'sidebar-2',
		'description'   => esc_html__( 'Add widgets here to appear in your footer.', 'chique-pro' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-wrap">',
		'after_widget'  => '</div></section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer 2', 'chique-pro' ),
		'id'            => 'sidebar-3',
		'description'   => esc_html__( 'Add widgets here to appear in your footer.', 'chique-pro' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-wrap">',
		'after_widget'  => '</div></section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer 3', 'chique-pro' ),
		'id'            => 'sidebar-4',
		'description'   => esc_html__( 'Add widgets here to appear in your footer.', 'chique-pro' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-wrap">',
		'after_widget'  => '</div></section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer 4', 'chique-pro' ),
		'id'            => 'sidebar-5',
		'description'   => esc_html__( 'Add widgets here to appear in your footer.', 'chique-pro' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-wrap">',
		'after_widget'  => '</div></section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	if ( class_exists( 'WooCommerce' ) ) {
		//Optional Primary Sidebar for Shop
		register_sidebar( array(
			'name' 				=> esc_html__( 'WooCommerce Sidebar', 'chique-pro' ),
			'id' 				=> 'sidebar-6',
			'description'		=> esc_html__( 'This is Optional Sidebar for WooCommerce Pages', 'chique-pro' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-wrap">',
			'after_widget'  => '</div></section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
	}

	// Registering 404 Error Page Content
	register_sidebar( array(
		'name'					=> esc_html__( '404 Page Not Found Content', 'chique-pro' ),
		'id' 					=> 'sidebar-notfound',
		'description'			=> esc_html__( 'Replaces the default 404 Page Not Found Content', 'chique-pro' ),
		'before_widget'			=> '<section id="%1$s" class="widget %2$s"><div class="widget-wrap">',
		'after_widget'			=> '</div></section>',
		'before_title'			=> '<h2 class="widget-title">',
		'after_title'			=> '</h2>',
	) );

	//Optional Sidebar for Hompeage instead of main sidebar
	register_sidebar( array(
		'name' 				=> esc_html__( 'Optional Homepage Sidebar', 'chique-pro' ),
		'id' 				=> 'sidebar-optional-homepage',
		'description'		=> esc_html__( 'This is Optional Sidebar for Homepage', 'chique-pro' ),
		'before_widget' 	=> '<section id="%1$s" class="widget %2$s"><div class="widget-wrap">',
		'after_widget'  	=> '</div></section>',
		'before_title' 		=> '<h2 class="widget-title">',
		'after_title' 		=> '</h2>'
	) );

	//Optional Sidebar for Archive instead of main sidebar
	register_sidebar( array(
		'name' 				=> esc_html__( 'Optional Archive Sidebar', 'chique-pro' ),
		'id' 				=> 'sidebar-optional-archive',
		'description'		=> esc_html__( 'This is Optional Sidebar for Archive', 'chique-pro' ),
		'before_widget' 	=> '<section id="%1$s" class="widget %2$s"><div class="widget-wrap">',
		'after_widget'  	=> '</div></section>',
		'before_title' 		=> '<h2 class="widget-title">',
		'after_title' 		=> '</h2>'
	) );

	//Optional Sidebar for Page instead of main sidebar
	register_sidebar( array(
		'name' 				=> esc_html__( 'Optional Page Sidebar', 'chique-pro' ),
		'id' 				=> 'sidebar-optional-page',
		'description'		=> esc_html__( 'This is Optional Sidebar for Page', 'chique-pro' ),
		'before_widget' 	=> '<section id="%1$s" class="widget %2$s"><div class="widget-wrap">',
		'after_widget'  	=> '</div></section>',
		'before_title' 		=> '<h2 class="widget-title">',
		'after_title' 		=> '</h2>'
	) );

	//Optional Sidebar for Post instead of main sidebar
	register_sidebar( array(
		'name' 				=> esc_html__( 'Optional Post Sidebar', 'chique-pro' ),
		'id' 				=> 'sidebar-optional-post',
		'description'		=> esc_html__( 'This is Optional Sidebar for Post', 'chique-pro' ),
		'before_widget' 	=> '<section id="%1$s" class="widget %2$s"><div class="widget-wrap">',
		'after_widget'  	=> '</div></section>',
		'before_title' 		=> '<h2 class="widget-title">',
		'after_title' 		=> '</h2>'
	) );

	//Optional Sidebar one for page and post
	register_sidebar( array(
		'name' 				=> esc_html__( 'Optional Sidebar One', 'chique-pro' ),
		'id' 				=> 'sidebar-optional-one',
		'description'		=> esc_html__( 'This is Optional Sidebar One', 'chique-pro' ),
		'before_widget' 	=> '<section id="%1$s" class="widget %2$s"><div class="widget-wrap">',
		'after_widget'  	=> '</div></section>',
		'before_title' 		=> '<h2 class="widget-title">',
		'after_title' 		=> '</h2>'
	) );

	//Optional Sidebar two for page and post
	register_sidebar( array(
		'name' 				=> esc_html__( 'Optional Sidebar Two', 'chique-pro' ),
		'id' 				=> 'sidebar-optional-two',
		'description'		=> esc_html__( 'This is Optional Sidebar Two', 'chique-pro' ),
		'before_widget' 	=> '<section id="%1$s" class="widget %2$s"><div class="widget-wrap">',
		'after_widget'  	=> '</div></section>',
		'before_title' 		=> '<h2 class="widget-title">',
		'after_title' 		=> '</h2>'
	) );

	//Optional Sidebar Three for page and post
	register_sidebar( array(
		'name' 				=> esc_html__( 'Optional Sidebar Three', 'chique-pro' ),
		'id' 				=> 'sidebar-optional-three',
		'description'		=> esc_html__( 'This is Optional Sidebar Three', 'chique-pro' ),
		'before_widget' 	=> '<section id="%1$s" class="widget %2$s"><div class="widget-wrap">',
		'after_widget'  	=> '</div></section>',
		'before_title' 		=> '<h2 class="widget-title">',
		'after_title' 		=> '</h2>'
	) );

	//Optional Sidebar Five Footer Instagram
	if ( class_exists( 'Catch_Instagram_Feed_Gallery_Widget' ) ||  class_exists( 'Catch_Instagram_Feed_Gallery_Widget_Pro' ) ) {
		register_sidebar( array(
			'name'          => esc_html__( 'Instagram', 'chique-pro' ),
			'id'            => 'sidebar-instagram',
			'description'   => esc_html__( 'Appears above footer. This sidebar is only for Widget from plugin Catch Instagram Feed Gallery Widget and Catch Instagram Feed Gallery Widget Pro', 'chique-pro' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-wrap">',
			'after_widget'  => '</div></section>',
			'before_title'  => '<div class="section-title-wrapper"><h2 class="section-title">',
			'after_title'   => '</h2></div>',
		) );
	}

	//Optional Sidebar Six Footer Newsletter
	register_sidebar( array(
		'name'          => esc_html__( 'Newsletter', 'chique-pro' ),
		'id'            => 'sidebar-newsletter',
		'description'   => esc_html__( 'This is for Newsletter Template Widget Area.', 'chique-pro' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s"><div class="widget-wrap">',
		'after_widget'  => '</div></section>',
		'before_title'  => '<div class="section-title-wrapper"><h2 class="section-title">',
		'after_title'   => '</h2></div>'
	) );
}
add_action( 'widgets_init', 'chique_widgets_init' );

if ( ! function_exists( 'chique_fonts_url' ) ) :
	/**
	 * Register Google fonts for Verity Pro.
	 *
	 * Create your own chique_fonts_url() function to override in a child theme.
	 *
	 * @since Chique Pro 1.0
	 *
	 * @return string Google fonts URL for the theme.
	 */
	function chique_fonts_url() {
		$fonts_url = '';
		$fonts     = array();
		$subsets   = 'latin,latin-ext';

		$font_values   = array();
		$font_values[] = get_theme_mod( 'chique_body_font', 'merriweather' );
		$font_values[] = get_theme_mod( 'chique_site_title_font', 'roboto' );
		$font_values[] = get_theme_mod( 'chique_site_tagline_font', 'roboto-condensed' );
		$font_values[] = get_theme_mod( 'chique_menu_font', 'titillium-web' );
		$font_values[] = get_theme_mod( 'chique_title_font', 'roboto-condensed' );
		$font_values[] = get_theme_mod( 'chique_headings_font', 'roboto-condensed' );
		$font_values[] = get_theme_mod( 'chique_button_font', 'roboto-condensed' );
		$font_values[] = get_theme_mod( 'chique_slider_custom_header_font', 'titillium-web' );

		$web_fonts = array(
			'allan'             => 'Allan',
			'allerta'           => 'Allerta',
			'amaranth'          => 'Amaranth',
			'amatic-sc'         => 'Amatic SC',
			'arizonia'          => 'Arizonia',
			'bitter'            => 'Bitter',
			'cabin'             => 'Cabin',
			'cantarell'         => 'Cantarell',
			'cousine'			=> 'Cousine',
			'crimson-text'      => 'Crimson+Text',
			'cuprum'            => 'Cuprum',
			'dancing-script'    => 'Dancing Script',
			'droid-sans'        => 'Droid Sans',
			'droid-serif'       => 'Droid Serif',
			'exo'               => 'Exo',
			'exo-2'             => 'Exo 2',
			'great-vibes'       => 'Great Vibes',
			'istok-web'         => 'Istok Web',
			'josefin-sans'      => 'Josefin Sans',
			'lato'              => 'Lato',
			'lobster'           => 'Lobster',
			'lora'              => 'Lora',
			'montserrat'        => 'Montserrat',
			'merriweather'      => 'Merriweather',
			'nobile'            => 'Nobile',
			'noto-serif'        => 'Noto Serif',
			'neuton'            => 'Neuton',
			'open-sans'         => 'Open Sans',
			'oswald'            => 'Oswald',
			'patua-one'         => 'Patua One',
			'playfair-display'  => 'Playfair Display',
			'pt-sans'           => 'PT Sans',
			'pt-serif'          => 'PT Serif',
			'rubik'             => 'Rubik',
			'quattrocento-sans' => 'Quattrocento Sans',
			'roboto'            => 'Roboto',
			'roboto-condensed'  => 'Roboto Condensed',
			'roboto-slab'       => 'Roboto Slab',
			'source-sans-pro'   => 'Source Sans Pro',
			'titillium-web'   	=> 'Titillium Web',
			'ubuntu'            => 'Ubuntu',
			'varela'            => 'Varela',
			'yanone-kaffeesatz' => 'Yanone Kaffeesatz',
		);

		$font_values = array_unique( $font_values ); // Make the array of fonts unique so that same font is not loaded twice.

		$font_values = array_intersect( $font_values, array_keys( $web_fonts ) ); // Intersect selected fonts and webfonts to only recover fonts that need loading.

		foreach ( $font_values as $font_value ) {
			$fonts[] = $web_fonts[ $font_value ] . ':300,400,600,700';
		}

		if ( $fonts ) {
			$fonts_url = add_query_arg( array(
				'family' => urlencode( implode( '|', $fonts ) ),
				'subset' => urlencode( $subsets ),
			), 'https://fonts.googleapis.com/css' );
		}

		return esc_url( $fonts_url );
	}
endif;

/**
 * Add preconnect for Google Fonts.
 */
function chique_resource_hints( $urls, $relation_type ) {
	if ( wp_style_is( 'chique-fonts', 'queue' ) && 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'chique_resource_hints', 10, 2 );

/**
 * Enqueue scripts and styles.
 */
function chique_scripts() {
	$min  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$path = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'assets/js/source/' : 'assets/js/';

	wp_enqueue_style( 'chique-fonts', chique_fonts_url(), array(), null );

	wp_enqueue_style( 'font-awesome', trailingslashit( esc_url ( get_template_directory_uri() ) ) . 'assets/css/font-awesome/css/font-awesome.css', array(), '4.7.0', 'all' );

	// Theme stylesheet.
	wp_enqueue_style( 'chique-style', get_stylesheet_uri() );

	// Theme block stylesheet.
	wp_enqueue_style( 'chique-block-style', get_theme_file_uri( '/assets/css/blocks.css' ), array( 'chique-style' ), '1.0' );

	wp_enqueue_script( 'chique-navigation', trailingslashit( esc_url ( get_template_directory_uri() ) ) . $path . 'navigation' . $min . '.js', array(), '20171226', true );

	wp_enqueue_script( 'chique-skip-link-focus-fix', trailingslashit( esc_url ( get_template_directory_uri() ) ) . $path . 'skip-link-focus-fix' . $min . '.js', array(), '20171226', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	$deps[] = 'jquery';

	wp_register_script( 'jquery-match-height', trailingslashit( esc_url ( get_template_directory_uri() ) ) . $path . 'jquery.matchHeight' . $min . '.js', array( 'jquery' ), '20171226', true );

	$deps[] = 'jquery-match-height';

	// Countdown Scripts.
	$enable_countdown = chique_check_section( get_theme_mod( 'chique_countdown_option', 'disabled' ) );

	if ( $enable_countdown ) {
		wp_register_script( 'jquery-countdown', trailingslashit( esc_url ( get_template_directory_uri() ) ) . $path . 'jquery.countdown' . $min . '.js', array(), '2.2.0', true );

		$deps[] = 'jquery-countdown';
	}

	//Slider Scripts
		$enable_slider      = chique_check_section( get_theme_mod( 'chique_slider_option', 'disabled' ) );
		$enable_logo_slider   = chique_check_section( get_theme_mod( 'chique_logo_slider_option', 'disabled' ) );

		$enable_testimonial_slider      = chique_check_section( get_theme_mod( 'chique_testimonial_option', 'disabled' ) ) && get_theme_mod( 'chique_testimonial_slider', 1 );
		$enable_event_slider = chique_check_section( get_theme_mod( 'chique_events_option', 'disabled' ) );

		$slider_trans_in       = get_theme_mod( 'chique_slider_transition_in', 'default' );
		$slider_trans_out      = get_theme_mod( 'chique_slider_transition_out', 'default' );
		$testimonial_trans_in  = get_theme_mod( 'chique_testimonial_transition_in', 'default' );
		$testimonial_trans_out = get_theme_mod( 'chique_testimonial_transition_out', 'default' );
		$logo_slider_trans_in  = get_theme_mod( 'chique_logo_slider_transition_in', 'default' );
		$logo_slider_trans_out = get_theme_mod( 'chique_logo_slider_transition_out', 'default' );
		$logo_slider_design    = get_theme_mod( 'chique_logo_slider_design', 'static-logo');

		if ( $enable_slider || $enable_event_slider || $enable_testimonial_slider || ( $enable_logo_slider && 'scrollable-logo' === $logo_slider_design ) ) {
			// Enqueue owl carousel css. Must load CSS before JS.
			wp_enqueue_style( 'owl-carousel-core', get_theme_file_uri( 'assets/css/owl-carousel/owl.carousel.min.css' ), null, '2.3.4' );
			wp_enqueue_style( 'owl-carousel-default', get_theme_file_uri( 'assets/css/owl-carousel/owl.theme.default.min.css' ), null, '2.3.4' );

			if ( ( $enable_slider && ( 'default' !== $slider_trans_in || 'default' !== $slider_trans_out ) )
			|| ( $enable_event_slider )
			|| ( $enable_logo_slider && ( 'default' !== $logo_slider_trans_in || 'default' !== $logo_slider_trans_out ) )
			|| ( $enable_testimonial_slider && ( 'default' !== $testimonial_trans_in || 'default' !== $testimonial_trans_out ) ) ) {
				wp_enqueue_style( 'animate', get_theme_file_uri( 'assets/css/animate.css' ), null, '3.7.0' );
			}

			// Enqueue script
			wp_enqueue_script( 'owl-carousel', get_theme_file_uri( $path . 'owl.carousel' . $min . '.js' ), array( 'jquery' ), '2.3.4', true );

			$deps[] = 'owl-carousel';

		}

		// Add masonry to dependent scripts of main script.
		$deps[] = 'jquery-masonry';

		wp_enqueue_script( 'chique-script', get_theme_file_uri( $path . 'custom-scripts' . $min . '.js' ), $deps, '201800703', true );

		wp_localize_script( 'chique-script', 'chiqueOptions', array(
			'screenReaderText' => array(
				'expand'   => esc_html__( 'expand child menu', 'chique-pro' ),
				'collapse' => esc_html__( 'collapse child menu', 'chique-pro' ),
			),
			'sliderOptions' => array(
				'transitionIn'      => esc_js( $slider_trans_in ),
				'transitionOut'     => esc_js( $slider_trans_out ),
				'nav'               => esc_js( ! get_theme_mod( 'chique_slider_nav', 1 ) ),
				'autoplay'          => esc_js( ! get_theme_mod( 'chique_slider_autoplay', 1 ) ),
				'loop'              => esc_js( get_theme_mod( 'chique_slider_loop' ) ),
				'transitionTimeout' => esc_js( get_theme_mod( 'chique_slider_transition_timeout', 4 ) ),
				'layout'            => esc_js( get_theme_mod( 'chique_slider_layout', 1 ) ),
				'dots'              => esc_js( ! get_theme_mod( 'chique_slider_dots', 1 ) ),
			),
			'logoSliderOptions' => array(
				'transitionIn'      => esc_js( $logo_slider_trans_in ),
				'transitionOut'     => esc_js( $logo_slider_trans_out ),
				'nav'               => esc_js( get_theme_mod( 'chique_logo_slider_nav', 1 ) ),
				'autoplay'          => esc_js( get_theme_mod( 'chique_logo_slider_autoplay', 1 ) ),
				'loop'              => esc_js( get_theme_mod( 'chique_logo_slider_loop' ) ),
				'transitionTimeout' => esc_js( get_theme_mod( 'chique_logo_slider_transition_timeout', 4 ) ),
				'dots'              => esc_js( get_theme_mod( 'chique_logo_slider_dots', 1 ) ),
				'layout'            => esc_js( get_theme_mod( 'chique_logo_slider_layout', 3 ) ),
				'slider_design'     => esc_js( get_theme_mod( 'chique_logo_slider_design', 'static-logo' ) ),
			),
			'testimonialOptions' => array(
				'transitionIn'      => esc_js( $testimonial_trans_in ),
				'transitionOut'     => esc_js( $testimonial_trans_out ),
				'nav'               => esc_js( ! get_theme_mod( 'chique_testimonial_nav', 1 ) ),
				'autoplay'          => esc_js( ! get_theme_mod( 'chique_testimonial_autoplay', 0 ) ),
				'loop'              => esc_js( ! get_theme_mod( 'chique_testimonial_loop', 1 ) ),
				'transitionTimeout' => esc_js( get_theme_mod( 'chique_testimonial_transition_timeout', 4 ) ),
				'dots'              => esc_js( ! get_theme_mod( 'chique_testimonial_dots', 1 ) ),
			),
			'iconNavPrev'     => '<i class="fa fa-angle-left"></i>',

			'iconNavNext'     => '<i class="fa fa-angle-right"></i>',
		) );

		if ( $enable_countdown ) {
			$date = new DateTime();

			// Add 10 Days to current Date.
			$date->add( new DateInterval( 'P10D' ) );
			$default_date =  $date->format( 'Y-m-d h:m:s' );

			$end_date = get_theme_mod( 'chique_countdown_end_date', $default_date );

			wp_localize_script( 'chique-script', 'chiqueCountdownEndDate', $end_date );
		}

		// Remove Media CSS, we have ouw own CSS for this.
		wp_deregister_style('wp-mediaelement');
	}
	add_action( 'wp_enqueue_scripts', 'chique_scripts' );

/**
 * Enqueue editor styles for Gutenberg
 */
function chique_block_editor_styles() {
	// Block styles.
	wp_enqueue_style( 'chique-block-editor-style', trailingslashit( esc_url ( get_template_directory_uri() ) ) . 'assets/css/editor-blocks.css' );
	// Add custom fonts.
	wp_enqueue_style( 'chique-fonts', chique_fonts_url(), array(), null );
}
add_action( 'enqueue_block_editor_assets', 'chique_block_editor_styles' );

if ( ! function_exists( 'chique_excerpt_length' ) ) :
	/**
	 * Sets the post excerpt length to n words.
	 *
	 * function tied to the excerpt_length filter hook.
	 * @uses filter excerpt_length
	 *
	 * @since Chique Pro 1.0
	 */
	function chique_excerpt_length( $length ) {
		if ( is_admin() ) {
			return $length;
		}

		// Getting data from Customizer Options
		$length	= get_theme_mod( 'chique_excerpt_length', 35 );
		return absint( $length );
	}
endif; //chique_excerpt_length
add_filter( 'excerpt_length', 'chique_excerpt_length', 999 );

if ( ! function_exists( 'chique_excerpt_more' ) ) :
	/**
	 * Replaces "[...]" (appended to automatically generated excerpts) with ... and a option from customizer.
	 * @return string option from customizer prepended with an ellipsis.
	 */
	function chique_excerpt_more( $more ) {
		if ( is_admin() ) {
			return $more;
		}

		$more_tag_text	= get_theme_mod( 'chique_excerpt_more_text',  esc_html__( 'Continue reading', 'chique-pro' ) );

		$excerpt_icon = get_theme_mod( 'chique_excerpt_continue_reading_icon', 1 );

		if( $excerpt_icon ) {
			$link = sprintf( '<span class="more-button"><a href="%1$s" class="more-link">%2$s<i class="fa fa-angle-right" aria-hidden="true"></i></a></span>',
				esc_url( get_permalink( get_the_ID() ) ),
				/* translators: %s: Name of current post */
				wp_kses_data( $more_tag_text ). '<span class="screen-reader-text">' . get_the_title( get_the_ID() ) . '</span>'
				);
		}
		else {
			$link = sprintf( '<span class="more-button"><a href="%1$s" class="more-link">%2$s</a></span>',
				esc_url( get_permalink( get_the_ID() ) ),
				/* translators: %s: Name of current post */
				wp_kses_data( $more_tag_text ). '<span class="screen-reader-text">' . get_the_title( get_the_ID() ) . '</span>'
				);
		}

		return $link;
	}
endif;
add_filter( 'excerpt_more', 'chique_excerpt_more' );


if ( ! function_exists( 'chique_custom_excerpt' ) ) :
	/**
	 * Adds Continue reading link to more tag excerpts.
	 *
	 * function tied to the get_the_excerpt filter hook.
	 *
	 * @since Chique Pro 1.0
	 */
	function chique_custom_excerpt( $output ) {
		if ( has_excerpt() && ! is_attachment() ) {
			$more_tag_text = get_theme_mod( 'chique_excerpt_more_text', esc_html__( 'Continue reading', 'chique-pro' ) );

			$link = sprintf( '<span class="more-button"><a href="%1$s" class="more-link">%2$s</a></span>',
			esc_url( get_permalink( get_the_ID() ) ),
			/* translators: %s: Name of current post */
			wp_kses_data( $more_tag_text ). '<span class="screen-reader-text">' . get_the_title( get_the_ID() ) . '</span>'
			);

			$link = ' &hellip; ' . $link;

			$output .= $link;
		}

		return $output;
	}
endif; //chique_custom_excerpt
add_filter( 'get_the_excerpt', 'chique_custom_excerpt' );


if ( ! function_exists( 'chique_more_link' ) ) :
	/**
	 * Replacing Continue reading link to the_content more.
	 *
	 * function tied to the the_content_more_link filter hook.
	 *
	 * @since Chique Pro 1.0
	 */
	function chique_more_link( $more_link, $more_link_text ) {
		$more_tag_text = get_theme_mod( 'chique_excerpt_more_text', esc_html__( 'Continue reading', 'chique-pro' ) );

		return ' &hellip; ' . str_replace( $more_link_text, $more_tag_text, $more_link );
	}
endif; //chique_more_link
add_filter( 'the_content_more_link', 'chique_more_link', 10, 2 );

/**
 * Count the number of footer sidebars to enable dynamic classes for the footer
 *
 * @since Chique Pro 1.0
 */
function chique_footer_sidebar_class() {
	$count = 0;

	if ( is_active_sidebar( 'sidebar-2' ) ) {
		$count++;
	}

	if ( is_active_sidebar( 'sidebar-3' ) ) {
		$count++;
	}

	if ( is_active_sidebar( 'sidebar-4' ) ) {
		$count++;
	}

	if ( is_active_sidebar( 'sidebar-5' ) ) {
		$count++;
	}

	$class = '';

	switch ( $count ) {
		case '1':
			$class = 'one';
			break;
		case '2':
			$class = 'two';
			break;
		case '3':
			$class = 'three';
			break;
		case '4':
			$class = 'four';
			break;
	}

	if ( $class ) {
		echo 'class="widget-area footer-widget-area ' . $class . '"'; // WPCS: XSS OK.
	}
}

/**
 * Register the required plugins for this theme.
 *
 * In this example, we register five plugins:
 * - one included with the TGMPA library
 * - two from an external source, one from an arbitrary source, one from a GitHub repository
 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
 *
 * The variables passed to the `tgmpa()` function should be:
 * - an array of plugin arrays;
 * - optionally a configuration array.
 * If you are not changing anything in the configuration array, you can remove the array and remove the
 * variable from the function call: `tgmpa( $plugins );`.
 * In that case, the TGMPA default settings will be used.
 *
 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
 */
function chique_register_required_plugins() {
	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(
		// Catch Web Tools.
		array(
			'name' => 'Catch Web Tools', // Plugin Name, translation not required.
			'slug' => 'catch-web-tools',
		),
		// Catch IDs
		array(
			'name' => 'Catch IDs', // Plugin Name, translation not required.
			'slug' => 'catch-ids',
		),
		// To Top.
		array(
			'name' => 'To top', // Plugin Name, translation not required.
			'slug' => 'to-top',
		),
		// Catch Gallery.
		array(
			'name' => 'Catch Gallery', // Plugin Name, translation not required.
			'slug' => 'catch-gallery',
		),
	);

	if ( ! class_exists( 'Catch_Infinite_Scroll_Pro' ) ) {
		$plugins[] = array(
			'name' => 'Catch Infinite Scroll', // Plugin Name, translation not required.
			'slug' => 'catch-infinite-scroll',
		);
	}

	if ( ! class_exists( 'Essential_Content_Types_Pro' ) ) {
		$plugins[] = array(
			'name' => 'Essential Content Types', // Plugin Name, translation not required.
			'slug' => 'essential-content-types',
		);
	}

	if ( ! class_exists( 'Essential_Widgets_Pro' ) ) {
		$plugins[] = array(
			'name' => 'Essential Widgets', // Plugin Name, translation not required.
			'slug' => 'essential-widgets',
		);
	}

	if ( ! class_exists( 'Catch_Instagram_Feed_Gallery_Widget_Pro' ) ) {
		$plugins[] = array(
			'name' => 'Catch Instagram Feed Gallery & Widget', // Plugin Name, translation not required.
			'slug' => 'catch-instagram-feed-gallery-widget',
		);
	}

	/*
	 * Array of configuration settings. Amend each line as needed.
	 *
	 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
	 * strings available, please help us make TGMPA even better by giving us access to these translations or by
	 * sending in a pull-request with .po file(s) with the translations.
	 *
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(
		'id'           => 'chique-pro',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
	);

	tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'chique_register_required_plugins' );

/**
 * Checks if there are options already present in free version and adds it to the Pro version
 *
 * @since Chique Pro 1.0
 * @hook after_theme_switch
 */
function chique_setup_options( $old_theme_name ) {
	if ( $old_theme_name ) {
		$old_theme_slug = sanitize_title( $old_theme_name );

		$free_version_slug = 'chique';
		$pro_version_slug  = 'chique-pro';

		$free_options = get_option( 'theme_mods_' . $old_theme_slug );

		// Perform action only if options of free version exists and theme is being switched from free version
		if ( $free_version_slug === $old_theme_slug && $free_options && '1' !== get_theme_mod( 'free_pro_migration' ) ) {
			$new_options = wp_parse_args( get_theme_mods(), $free_options );

			if ( update_option( 'theme_mods_' . $pro_version_slug, $free_options ) ) {
				// Set Migration Parameter to true so that this script does not run multiple times.
				set_theme_mod( 'free_pro_migration', '1' );
			}
		}
	}
}
add_action( 'after_switch_theme', 'chique_setup_options', 100 );

/**
 * Implement the Custom Header feature
 */
require get_parent_theme_file_path( '/inc/custom-header.php' );

/**
 * Include Header Background Color Options
 */
require get_parent_theme_file_path( 'inc/header-background-color.php' );

/**
 * Custom template tags for this theme
 */
require get_parent_theme_file_path( '/inc/template-tags.php' );

/**
 * Add theme admin page.
 */
if ( is_admin() ) {
	require get_parent_theme_file_path( 'inc/about.php' );
}

/**
 * Functions which enhance the theme by hooking into WordPress
 */
require get_parent_theme_file_path( '/inc/template-functions.php' );

/**
 * Customizer additions
 */
require get_parent_theme_file_path( '/inc/customizer/customizer.php' );

/**
 * Metabox Options
 */
require get_parent_theme_file_path( '/inc/metabox/metabox.php' );

/**
 * WooCommerce Support
 */
require get_parent_theme_file_path( '/inc/woocommerce.php' );

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_parent_theme_file_path( '/inc/jetpack.php' );
}

/**
 * Load Social Widget
 */
require get_parent_theme_file_path( '/inc/widget-social-icons.php' );

/**
 * Include Timeline
 */
require get_parent_theme_file_path( '/inc/timeline.php' );

/**
 * Load TGMPA
 */
require get_parent_theme_file_path( '/inc/class-tgm-plugin-activation.php' );

/**
 * Add EDD SW Licensing support
 */
function chique_theme_updater() {
	require( get_template_directory() . '/inc/updater/theme-updater.php' );
}
add_action( 'after_setup_theme', 'chique_theme_updater' );
