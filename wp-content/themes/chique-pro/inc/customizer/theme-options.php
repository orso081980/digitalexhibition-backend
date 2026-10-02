<?php
/**
 * Theme Options
 *
 * @package Chique
 */

/**
 * Add theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_theme_options( $wp_customize ) {
	$wp_customize->add_panel( 'chique_theme_options', array(
		'title'    => esc_html__( 'Theme Options', 'chique-pro' ),
		'priority' => 130,
	) );

	// Font Family Options.
	$wp_customize->add_section( 'chique_font_family', array(
		'panel' => 'chique_theme_options',
		'title' => esc_html__( 'Font Family Options', 'chique-pro' ),
	) );

	$avaliable_fonts = chique_avaliable_fonts();

	$choices = array();

	foreach ( $avaliable_fonts as $font ) {
		$choices[ $font['value'] ] = str_replace( '"', '', $font['label'] );
	}

	$font_family_options = chique_font_family_options();

	foreach ( $font_family_options as $key => $value ) {
		chique_register_option( $wp_customize, array(
				'name'              => $key,
				'default'           => $value['default'],
				'sanitize_callback' => 'chique_sanitize_select',
				'choices'           => $choices,
				'label'             => $value['label'],
				'section'           => 'chique_font_family',
				'type'              => 'select',
			)
		);
	}

	// Footer Editor Options.
	$wp_customize->add_section( 'chique_footer_editor_options', array(
		'title'       => esc_html__( 'Footer Editor Options', 'chique-pro' ),
		'description' => esc_html__( 'You can either add html or plain text or custom shortcodes, which will be automatically inserted into your theme. Some shorcodes: [the-year], [site-link] and [privacy-policy-link] for current year, site link and privacy policy link respectively.', 'chique-pro' ),
		'panel'       => 'chique_theme_options',
	) );

	$theme_data = wp_get_theme();

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_footer_content',
			'default'           => sprintf( _x( 'Copyright &copy; %1$s %2$s. All Rights Reserved. %3$s', '1: Year, 2: Site Title with home URL, 3: Privacy Policy Link', 'chique-pro' ), '[the-year]', '[site-link]', '[privacy-policy-link]' ) . ' &#124; ' . esc_html( $theme_data->get( 'Name') ) . '&nbsp;' . esc_html__( 'by', 'chique-pro' ). '&nbsp;<a target="_blank" href="'. esc_url( $theme_data->get( 'AuthorURI' ) ) .'">'. esc_html( $theme_data->get( 'Author' ) ) .'</a>',
			'sanitize_callback' => 'wp_kses_post',
			'label'             => esc_html__( 'Footer Content', 'chique-pro' ),
			'section'           => 'chique_footer_editor_options',
			'type'              => 'textarea',
		)
	);

	// Layout Options
	$wp_customize->add_section( 'chique_layout_options', array(
		'title' => esc_html__( 'Layout Options', 'chique-pro' ),
		'panel' => 'chique_theme_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_sidebar_width',
			'default'           => 300,
			'sanitize_callback' => 'chique_sanitize_number_range',
			'label'             => esc_html__( 'Header Sidebar Width ( 200px - 500px)', 'chique-pro' ),
			'section'           => 'chique_layout_options',
			'type'              => 'number',
			'input_attrs' => array(
				'min'   => 200,
				'max'   => 500,
				'style' => 'width: 65px',
			),
		)
	);

	/* Blog Style */
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_blog_style',
			'default'           => 0,
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'label'             => esc_html__( 'Blog Style Grid', 'chique-pro' ),
			'section'           => 'chique_layout_options',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_blog_style_boxed',
			'default'           => 1,
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_blog_style_grid_enabled',
			'label'             => esc_html__( 'Blog Style Grid - Fluid', 'chique-pro' ),
			'section'           => 'chique_layout_options',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	/* Default Layout */
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_default_layout',
			'default'           => 'right-sidebar',
			'sanitize_callback' => 'chique_sanitize_select',
			'label'             => esc_html__( 'Default Layout', 'chique-pro' ),
			'section'           => 'chique_layout_options',
			'type'              => 'select',
			'choices'           => array(
				'right-sidebar'         => esc_html__( 'Right Sidebar ( Content, Primary Sidebar )', 'chique-pro' ),
				'left-sidebar'          => esc_html__( 'Left Sidebar ( Primary Sidebar, Content )', 'chique-pro' ),
				'no-sidebar'            => esc_html__( 'No Sidebar', 'chique-pro' ),
				'no-sidebar-full-width' => esc_html__( 'No Sidebar: Full Width', 'chique-pro' ),
			),
		)
	);

	/* Homepage Layout */
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_homepage_layout',
			'default'           => 'no-sidebar',
			'sanitize_callback' => 'chique_sanitize_select',
			'label'             => esc_html__( 'Homepage Layout', 'chique-pro' ),
			'section'           => 'chique_layout_options',
			'type'              => 'select',
			'choices'           => array(
				'right-sidebar'         => esc_html__( 'Right Sidebar ( Content, Primary Sidebar )', 'chique-pro' ),
				'left-sidebar'          => esc_html__( 'Left Sidebar ( Primary Sidebar, Content )', 'chique-pro' ),
				'no-sidebar'            => esc_html__( 'No Sidebar', 'chique-pro' ),
				'no-sidebar-full-width' => esc_html__( 'No Sidebar: Full Width', 'chique-pro' ),
			),
		)
	);

	/* Blog/Archive Layout */
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_archive_layout',
			'default'           => 'right-sidebar',
			'sanitize_callback' => 'chique_sanitize_select',
			'label'             => esc_html__( 'Blog/Archive Layout', 'chique-pro' ),
			'section'           => 'chique_layout_options',
			'type'              => 'select',
			'choices'           => array(
				'right-sidebar'         => esc_html__( 'Right Sidebar ( Content, Primary Sidebar )', 'chique-pro' ),
				'left-sidebar'          => esc_html__( 'Left Sidebar ( Primary Sidebar, Content )', 'chique-pro' ),
				'no-sidebar'            => esc_html__( 'No Sidebar', 'chique-pro' ),
				'no-sidebar-full-width' => esc_html__( 'No Sidebar: Full Width', 'chique-pro' ),
			),
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_archive_content_show',
			'default'           => 'excerpt',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_content_show(),
			'label'             => esc_html__( 'Archive Content Layout', 'chique-pro' ),
			'section'           => 'chique_layout_options',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_archive_meta_note',
			'sanitize_callback' => 'sanitize_text_field',
			'custom_control'    => 'Chique_Note_Control',
			'label'             => esc_html__( 'Archive Meta', 'chique-pro' ),
			'section'           => 'chique_layout_options',
			'type'              => 'description',
        )
    );

    chique_register_option( $wp_customize, array(
    		'name'              => 'chique_display_date',
    		'default'			=> 1,
    		'sanitize_callback' => 'chique_sanitize_checkbox',
    		'label'             => esc_html__( 'Posted On', 'chique-pro' ),
    		'section'           => 'chique_layout_options',
    		'custom_control'    => 'Chique_Toggle_Control',
    	)
    );

    chique_register_option( $wp_customize, array(
    		'name'              => 'chique_display_author',
    		'default'			=> 1,
    		'sanitize_callback' => 'chique_sanitize_checkbox',
    		'label'             => esc_html__( 'Author', 'chique-pro' ),
    		'section'           => 'chique_layout_options',
    		'custom_control'    => 'Chique_Toggle_Control',
    	)
    );

    chique_register_option( $wp_customize, array(
    		'name'              => 'chique_display_tags',
    		'default'			=> 0,
    		'sanitize_callback' => 'chique_sanitize_checkbox',
    		'label'             => esc_html__( 'Tags', 'chique-pro' ),
    		'section'           => 'chique_layout_options',
    		'custom_control'    => 'Chique_Toggle_Control',
    	)
    );

    chique_register_option( $wp_customize, array(
    		'name'              => 'chique_display_categories',
    		'default'			=> 0,
    		'sanitize_callback' => 'chique_sanitize_checkbox',
    		'label'             => esc_html__( 'Categories', 'chique-pro' ),
    		'section'           => 'chique_layout_options',
    		'custom_control'    => 'Chique_Toggle_Control',
    	)
    );


	// Single Page/Post Image
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_single_layout',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'label'             => esc_html__( 'Single Page/Post Image', 'chique-pro' ),
			'section'           => 'chique_layout_options',
			'type'              => 'select',
			'choices'           => array(
				'disabled'            => esc_html__( 'Disabled', 'chique-pro' ),
				'post-thumbnail'      => esc_html__( 'Post Thumbnail (940x528)', 'chique-pro' ),
				'chique-featured' => esc_html__( 'Featured (666x499)', 'chique-pro' ),
				'full'                => esc_html__( 'Original Image Size', 'chique-pro' ),
			),
		)
	);

	// Excerpt Options.
	$wp_customize->add_section( 'chique_excerpt_options', array(
		'panel' => 'chique_theme_options',
		'title' => esc_html__( 'Excerpt Options', 'chique-pro' ),
	) );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_excerpt_length',
			'default'           => '35',
			'sanitize_callback' => 'absint',
			'description' => esc_html__( 'Excerpt length. Default is 55 words', 'chique-pro' ),
			'input_attrs' => array(
				'min'   => 10,
				'max'   => 200,
				'step'  => 5,
				'style' => 'width: 60px;',
			),
			'label'    => esc_html__( 'Excerpt Length (words)', 'chique-pro' ),
			'section'  => 'chique_excerpt_options',
			'type'     => 'number',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_excerpt_more_text',
			'default'           => esc_html__( 'Continue reading', 'chique-pro' ),
			'sanitize_callback' => 'sanitize_text_field',
			'label'             => esc_html__( 'Read More Text', 'chique-pro' ),
			'section'           => 'chique_excerpt_options',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_excerpt_continue_reading_icon',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'default'           => 1,
			'label'             => esc_html__( 'Read More Icon', 'chique-pro' ),
			'section'           => 'chique_excerpt_options',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	// Excerpt Options.
	$wp_customize->add_section( 'chique_search_options', array(
		'panel'     => 'chique_theme_options',
		'title'     => esc_html__( 'Search Options', 'chique-pro' ),
	) );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_search_text',
			'default'           => esc_html__( 'Search ...', 'chique-pro' ),
			'sanitize_callback' => 'wp_kses_data',
			'label'             => esc_html__( 'Search Text', 'chique-pro' ),
			'section'           => 'chique_search_options',
			'type'              => 'text',
		)
	);

	// Comment Option.
	$wp_customize->add_section( 'chique_comment_option', array(
		'description'   => esc_html__( 'Comments can also be disabled on a per post/page basis when creating/editing posts/pages.', 'chique-pro' ),
		'panel'         => 'chique_theme_options',
		'title'         => esc_html__( 'Comment Options', 'chique-pro' ),
	) );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_comment_option',
			'default'           => 'use-wordpress-setting',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_comment_options(),
			'label'             => esc_html__( 'Comment Option', 'chique-pro' ),
			'section'           => 'chique_comment_option',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_website_field',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'default'           => 1,
			'label'             => esc_html__( 'Display Website Field', 'chique-pro' ),
			'section'           => 'chique_comment_option',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	// Homepage / Frontpage Options.
	$wp_customize->add_section( 'chique_homepage_options', array(
		'description' => esc_html__( 'Only posts that belong to the categories selected here will be displayed on the front page', 'chique-pro' ),
		'panel'       => 'chique_theme_options',
		'title'       => esc_html__( 'Homepage / Frontpage Options', 'chique-pro' ),
	) );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_display_homepage_posts',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'default'           => 1,
			'label'             => esc_html__( 'Display Recent Posts/Content on homepage', 'chique-pro' ),
			'section'           => 'chique_homepage_options',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_recent_posts_heading',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => esc_html__( 'Blog', 'chique-pro' ),
			'label'             => esc_html__( 'Recent Posts Heading', 'chique-pro' ),
			'section'           => 'chique_homepage_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_recent_posts_subheading',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => esc_html__( 'Latest Updates', 'chique-pro' ),
			'label'             => esc_html__( 'Recent Posts Sub Heading', 'chique-pro' ),
			'section'           => 'chique_homepage_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_front_page_category',
			'sanitize_callback' => 'chique_sanitize_category_list',
			'custom_control'    => 'Chique_Multi_Cat',
			'active_callback'   => 'chique_is_homepage_posts_enabled',
			'label'             => esc_html__( 'Categories', 'chique-pro' ),
			'section'           => 'chique_homepage_options',
			'type'              => 'dropdown-categories',
		)
	);

	// Disable Recent post in static frontpage
    chique_register_option( $wp_customize, array(
		'name'              => 'chique_enable_static_page_posts',
		'sanitize_callback' => 'chique_sanitize_checkbox',
		'label'             => esc_html__( 'Enable Recent Posts on Static Page', 'chique-pro' ),
		'section'           => 'chique_homepage_options',
		'custom_control'    => 'Chique_Toggle_Control',
    ) );

	$wp_customize->add_section( 'chique_menu_options', array(
		'panel'       => 'chique_theme_options',
		'title'       => esc_html__( 'Menu Options', 'chique-pro' ),
	) );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_menu_style',
			'default'           => 'classic',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => array(
				'classic' => esc_html__( 'Classic', 'chique-pro' ),
				'modern'  => esc_html__( 'Modern', 'chique-pro' ),
			),
			'label'             => esc_html__( 'Menu Style', 'chique-pro' ),
			'section'           => 'chique_menu_options',
			'type'              => 'radio',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_primary_subtitle_popup_disable',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_classic_menu_enabled',
			'label'             => esc_html__( 'Show Submenu below Parent menu(Disable submenu hover popup)', 'chique-pro' ),
			'section'           => 'chique_menu_options',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_display_primary_search',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'default'           => 1,
			'label'             => esc_html__( 'Display Search On Primary', 'chique-pro' ),
			'section'           => 'chique_menu_options',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_primary_modern_menu_label',
			'default'           => esc_html__( 'Menu', 'chique-pro' ),
			'active_callback'   => 'chique_is_modern_menu_enabled',
			'sanitize_callback' => 'sanitize_text_field',
			'label'             => esc_html__( 'Modern Menu Label', 'chique-pro' ),
			'section'           => 'chique_menu_options',
			'type'              => 'text',
		)
	);


	// Pagination Options.
	$wp_customize->add_section( 'chique_pagination_options', array(
		'panel'       => 'chique_theme_options',
		'title'       => esc_html__( 'Pagination Options', 'chique-pro' ),
	) );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_pagination_type',
			'default'           => 'default',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_get_pagination_types(),
			'label'             => esc_html__( 'Pagination type', 'chique-pro' ),
			'section'           => 'chique_pagination_options',
			'type'              => 'select',
		)
	);
	// For WooCommerce layout: chique_woocommerce_layout, check woocommerce-options.php.
	/* Scrollup Options */
	$wp_customize->add_section( 'chique_scrollup', array(
		'panel'    => 'chique_theme_options',
		'title'    => esc_html__( 'Scrollup Options', 'chique-pro' ),
	) );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_display_scrollup',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'default'           => 1,
			'label'             => esc_html__( 'Display Scroll Up', 'chique-pro' ),
			'section'           => 'chique_scrollup',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	// Sections Sorter.
	$wp_customize->add_section( 'chique_sections_sort', array(
		'title'       => esc_html__( 'Sections Sorter', 'chique-pro' ),
		'description' => esc_html__( 'Drag and drop to sort your sections', 'chique-pro' ),
		'panel'       => 'chique_theme_options',
	) );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_sections_sort',
			'default'           => chique_get_default_sections_value(),
			'label'             => esc_html__( 'Sortable Sections', 'chique-pro' ),
			'custom_control'    => 'Chique_Sortable_Custom_Control',
			'sanitize_callback' => 'sanitize_text_field',
			'section'           => 'chique_sections_sort',
			'type'              => 'custom-sortable',
		)
	);
}
add_action( 'customize_register', 'chique_theme_options' );


/**
 * Returns an array of avaliable fonts registered for Foodie World
 *
 * @since Chique Pro 1.0
 */
function chique_avaliable_fonts() {
	$avaliable_fonts = array(
		'arial-black' => array(
			'value' => 'arial-black',
			'label' => '"Arial Black", Gadget, sans-serif',
		),
		'allan' => array(
			'value' => 'allan',
			'label' => '"Allan", sans-serif',
		),
		'allerta' => array(
			'value' => 'allerta',
			'label' => '"Allerta", sans-serif',
		),
		'amaranth' => array(
			'value' => 'amaranth',
			'label' => '"Amaranth", sans-serif',
		),
		'amatic-sc' => array(
			'value' => 'amatic-sc',
			'label' => '"Amatic SC", cursive',
		),
		'arial' => array(
			'value' => 'arial',
			'label' => 'Arial, Helvetica, sans-serif',
		),
		'arizonia' => array(
			'value' => 'arizonia',
			'label' => '"Arizonia", cursive',
		),
		'bitter' => array(
			'value' => 'bitter',
			'label' => '"Bitter", sans-serif',
		),
		'cabin' => array(
			'value' => 'cabin',
			'label' => '"Cabin", sans-serif',
		),
		'cantarell' => array(
			'value' => 'cantarell',
			'label' => '"Cantarell", sans-serif',
		),
		'cousine' => array(
			'value' => 'cousine',
			'label' => '"Cousine", monospace',
		),
		'century-gothic' => array(
			'value' => 'century-gothic',
			'label' => '"Century Gothic", sans-serif',
		),
		'courier-new' => array(
			'value' => 'courier-new',
			'label' => '"Courier New", Courier, monospace',
		),
		'crimson-text' => array(
			'value' => 'crimson-text',
			'label' => '"Crimson Text", sans-serif',
		),
		'cuprum' => array(
			'value' => 'cuprum',
			'label' => '"Cuprum", sans-serif',
		),
		'dancing-script' => array(
			'value' => 'dancing-script',
			'label' => '"Dancing Script", sans-serif',
		),
		'droid-sans' => array(
			'value' => 'droid-sans',
			'label' => '"Droid Sans", sans-serif',
		),
		'droid-serif' => array(
			'value' => 'droid-serif',
			'label' => '"Droid Serif", sans-serif',
		),
		'exo' => array(
			'value' => 'exo',
			'label' => '"Exo", sans-serif',
		),
		'exo-2' => array(
			'value' => 'exo-2',
			'label' => '"Exo 2", sans-serif',
		),
		'georgia' => array(
			'value' => 'georgia',
			'label' => 'Georgia, "Times New Roman", Times, serif',
		),
		'great-vibes' => array(
			'value' => 'great-vibes',
			'label' => '"Great Vibes", cursive',
		),
		'helvetica' => array(
			'value' => 'helvetica',
			'label' => 'Helvetica, "Helvetica Neue", Arial, sans-serif',
		),
		'helvetica-neue' => array(
			'value' => 'helvetica-neue',
			'label' => '"Helvetica Neue",Helvetica,Arial,sans-serif',
		),
		'istok-web' => array(
			'value' => 'istok-web',
			'label' => '"Istok Web", sans-serif',
		),
		'impact' => array(
			'value' => 'impact',
			'label' => 'Impact, Charcoal, sans-serif',
		),
		'josefin-sans' => array(
			'value' => 'josefin-sans',
			'label' => '"Josefin Sans", sans-serif',
		),
		'lato' => array(
			'value' => 'lato',
			'label' => '"Lato", sans-serif',
		),
		'lucida-sans-unicode' => array(
			'value' => 'lucida-sans-unicode',
			'label' => '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
		),
		'lucida-grande' => array(
			'value' => 'lucida-grande',
			'label' => '"Lucida Grande", "Lucida Sans Unicode", sans-serif',
		),
		'lobster' => array(
			'value' => 'lobster',
			'label' => '"Lobster", sans-serif',
		),
		'lora' => array(
			'value' => 'lora',
			'label' => '"Lora", serif',
		),
		'monaco' => array(
			'value' => 'monaco',
			'label' => 'Monaco, Consolas, "Lucida Console", monospace, sans-serif',
		),
		'montserrat' => array(
			'value' => 'montserrat',
			'label' => '"Montserrat", sans-serif',
		),
		'merriweather' => array(
			'value' => 'merriweather',
			'label' => '"Merriweather", serif',
		),
		'nobile' => array(
			'value' => 'nobile',
			'label' => '"Nobile", sans-serif',
		),
		'noto-serif' => array(
			'value' => 'noto-serif',
			'label' => '"Noto Serif", serif',
		),
		'neuton' => array(
			'value' => 'neuton',
			'label' => '"Neuton", serif',
		),
		'open-sans' => array(
			'value' => 'open-sans',
			'label' => '"Open Sans", sans-serif',
		),
		'oswald' => array(
			'value' => 'oswald',
			'label' => '"Oswald", sans-serif',
		),
		'palatino' => array(
			'value' => 'palatino',
			'label' => 'Palatino, "Palatino Linotype", "Book Antiqua", serif',
		),
		'patua-one' => array(
			'value' => 'patua-one',
			'label' => '"Patua One", sans-serif',
		),
		'playfair-display' => array(
			'value' => 'playfair-display',
			'label' => '"Playfair Display", sans-serif',
		),
		'pt-sans' => array(
			'value' => 'pt-sans',
			'label' => '"PT Sans", sans-serif',
		),
		'pt-serif' => array(
			'value' => 'pt-serif',
			'label' => '"PT Serif", serif',
		),
		'quattrocento-sans' => array(
			'value' => 'quattrocento-sans',
			'label' => '"Quattrocento Sans", sans-serif',
		),
		'roboto' => array(
			'value' => 'roboto',
			'label' => '"Roboto", sans-serif',
		),
		'roboto-condensed' => array(
			'value' => 'roboto-condensed',
			'label' => '"Roboto Condensed", sans-serif',
		),
		'roboto-slab' => array(
			'value' => 'roboto-slab',
			'label' => '"Roboto Slab", serif',
		),
		'rubik' => array(
			'value' => 'rubik',
			'label' => '"Rubik", serif',
		),
		'sans-serif' => array(
			'value' => 'sans-serif',
			'label' => 'Sans Serif, Arial',
		),
		'source-sans-pro' => array(
			'value' => 'source-sans-pro',
			'label' => '"Source Sans Pro", sans-serif',
		),
		'tahoma' => array(
			'value' => 'tahoma',
			'label' => 'Tahoma, Geneva, sans-serif',
		),
		'trebuchet-ms' => array(
			'value' => 'trebuchet-ms',
			'label' => '"Trebuchet MS", "Helvetica", sans-serif',
		),
		'times-new-roman' => array(
			'value' => 'times-new-roman',
			'label' => '"Times New Roman", Times, serif',
		),
		'titillium-web' => array(
			'value' => 'titillium-web',
			'label' => '"Titillium Web", sans-serif',
		),
		'ubuntu' => array(
			'value' => 'ubuntu',
			'label' => '"Ubuntu", sans-serif',
		),
		'varela' => array(
			'value' => 'varela',
			'label' => '"Varela", sans-serif',
		),
		'verdana' => array(
			'value' => 'verdana',
			'label' => 'Verdana, Geneva, sans-serif',
		),
		'yanone-kaffeesatz' => array(
			'value' => 'yanone-kaffeesatz',
			'label' => '"Yanone Kaffeesatz", sans-serif',
		),
	);

	return apply_filters( 'chique_avaliable_fonts', $avaliable_fonts );
}


/**
 * Returns an array of font family options
 *
 * @since Chique Pro 1.0
 */
function chique_font_family_options() {
	$options = array(
		'chique_body_font'         => array(
			'label'    => esc_html__( 'Default', 'chique-pro' ),
			'default'  => 'merriweather',
			'selector' => 'body, button, input, select, optgroup, textarea, .hero-content-wrapper .entry-title span, .promotion-sale-wrapper .entry-title span, .contact-section .entry-title span, #skill-section .entry-title span, #playlist-section .entry-title span, .reserve-content-wrapper .entry-title span',
		),
		'chique_site_title_font'        => array(
			'label'    => esc_html__( 'Site Title', 'chique-pro' ),
			'default'  => 'titillium-web',
			'selector' => '.site-title a',
		),
		'chique_site_tagline_font'      => array(
			'label'    => esc_html__( 'Site Tagline', 'chique-pro' ),
			'default'  => 'roboto-condensed',
			'selector' => '.site-description',
		),
		'chique_menu_font'      => array(
			'label'    => esc_html__( 'Menu', 'chique-pro' ),
			'default'  => 'titillium-web',
			'selector' => '.main-navigation a',
		),
		'chique_title_font' => array(
			'label'    => esc_html__( 'Section Title', 'chique-pro' ),
			'default'  => 'roboto-condensed',
			'selector' => 'h1, h2, h3, h4, h5, h6, .section-title, .entry-title, cite, .widget a, .edit-link, .entry-meta a, .sticky-label, .comment-metadata a, .post-navigation .nav-subtitle, .nav-title, .widget_categories ul li a, .widget_archive ul li a, .ew-archive ul li a, .ew-category ul li a, form label, .author-link, .entry-breadcrumbs a, .breadcrumb-current, .entry-breadcrumbs .sep, #team-content-section .position, .pagination .nav-links > span, #footer-newsletter .ewnewsletter .ew-newsletter-wrap .ew-newsletter-subbox, .contact-wrap > span, .pricing-section  package-price, .reservation-highlight-text span, .reserve-content-wrapper .contact-description  trong, .info, #gallery-content-section .gallery-item figcaption, .woocommerce .product-container .wc-forward, #event-slider-section .owl-dots li span' ),
		'chique_headings_font'     => array(
			'label' => esc_html__( 'Headings Tags from h1 to h6', 'chique-pro' ),
			'default' => 'roboto-condensed',
			'selector' => 'h1, h2, h3, h4, h5, h6',
		),
		'chique_button_font'      => array(
			'label'    => esc_html__( 'Button', 'chique-pro' ),
			'default'  => 'roboto-condensed',
			'selector' => '.more-link, button, input[type="button"], input[type="reset"], input[type="submit"], input[type="search"], .button, .site-main #infinite-handle span button, .posts-navigation a, .pagination a'),
		'chique_slider_custom_header_font'      => array(
			'label'    => esc_html__( 'Slider & Custom Header', 'chique-pro' ),
			'default'  => 'titillium-web',
			'selector' => '#feature-slider-section .entry-title, .custom-header .entry-title, .collapse-menu-label',
		),
	);

	return apply_filters( 'chique_font_family_options', $options );
}

/** Active Callback Functions */

if( ! function_exists( 'chique_is_homepage_posts_enabled' ) ) :
	/**
	* Return true if hommepage posts/content is enabled
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_homepage_posts_enabled( $control ) {
		return ( $control->manager->get_setting( 'chique_display_homepage_posts' )->value() );
	}
endif;

if( ! function_exists( 'chique_is_classic_menu_enabled' ) ) :
	/**
	* Return true if classic menu is enabled
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_classic_menu_enabled( $control ) {
		return ( 'classic' === $control->manager->get_setting( 'chique_menu_style' )->value() );
	}
endif;

if( ! function_exists( 'chique_is_modern_menu_enabled' ) ) :
	/**
	* Return true if modern menu is enabled
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_modern_menu_enabled( $control ) {
		return ( 'modern' === $control->manager->get_setting( 'chique_menu_style' )->value() );
	}
endif;

if ( ! function_exists( 'chique_blog_style_grid_enabled' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Foodoholic Pro 1.0
	*/
	function chique_blog_style_grid_enabled( $control ) {

		return ( $control->manager->get_setting( 'chique_blog_style' )->value() );
	}
endif;
