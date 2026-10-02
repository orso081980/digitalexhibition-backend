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
		'title'    => esc_html__( 'Theme Options', 'chique' ),
		'priority' => 130,
	) );

	// Layout Options
	$wp_customize->add_section( 'chique_layout_options', array(
		'title' => esc_html__( 'Layout Options', 'chique' ),
		'panel' => 'chique_theme_options',
		)
	);

	/* Default Layout */
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_default_layout',
			'default'           => 'right-sidebar',
			'sanitize_callback' => 'chique_sanitize_select',
			'label'             => esc_html__( 'Default Layout', 'chique' ),
			'section'           => 'chique_layout_options',
			'type'              => 'select',
			'choices'           => array(
				'right-sidebar'         => esc_html__( 'Right Sidebar ( Content, Primary Sidebar )', 'chique' ),
				'no-sidebar'            => esc_html__( 'No Sidebar', 'chique' ),
			),
		)
	);

	/* Homepage Layout */
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_homepage_layout',
			'default'           => 'no-sidebar',
			'sanitize_callback' => 'chique_sanitize_select',
			'label'             => esc_html__( 'Homepage Layout', 'chique' ),
			'section'           => 'chique_layout_options',
			'type'              => 'select',
			'choices'           => array(
				'right-sidebar'         => esc_html__( 'Right Sidebar ( Content, Primary Sidebar )', 'chique' ),
				'no-sidebar'            => esc_html__( 'No Sidebar', 'chique' ),
			),
		)
	);

	/* Blog/Archive Layout */
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_archive_layout',
			'default'           => 'right-sidebar',
			'sanitize_callback' => 'chique_sanitize_select',
			'label'             => esc_html__( 'Blog/Archive Layout', 'chique' ),
			'section'           => 'chique_layout_options',
			'type'              => 'select',
			'choices'           => array(
				'right-sidebar'         => esc_html__( 'Right Sidebar ( Content, Primary Sidebar )', 'chique' ),
				'no-sidebar'            => esc_html__( 'No Sidebar', 'chique' ),
			),
		)
	);
	
	// Excerpt Options.
	$wp_customize->add_section( 'chique_excerpt_options', array(
		'panel' => 'chique_theme_options',
		'title' => esc_html__( 'Excerpt Options', 'chique' ),
	) );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_excerpt_length',
			'default'           => '35',
			'sanitize_callback' => 'absint',
			'description' => esc_html__( 'Excerpt length. Default is 55 words', 'chique' ),
			'input_attrs' => array(
				'min'   => 10,
				'max'   => 200,
				'step'  => 5,
				'style' => 'width: 60px;',
			),
			'label'    => esc_html__( 'Excerpt Length (words)', 'chique' ),
			'section'  => 'chique_excerpt_options',
			'type'     => 'number',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_excerpt_more_text',
			'default'           => esc_html__( 'Continue reading', 'chique' ),
			'sanitize_callback' => 'sanitize_text_field',
			'label'             => esc_html__( 'Read More Text', 'chique' ),
			'section'           => 'chique_excerpt_options',
			'type'              => 'text',
		)
	);

	// Excerpt Options.
	$wp_customize->add_section( 'chique_search_options', array(
		'panel'     => 'chique_theme_options',
		'title'     => esc_html__( 'Search Options', 'chique' ),
	) );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_search_text',
			'default'           => esc_html__( 'Search ...', 'chique' ),
			'sanitize_callback' => 'wp_kses_data',
			'label'             => esc_html__( 'Search Text', 'chique' ),
			'section'           => 'chique_search_options',
			'type'              => 'text',
		)
	);

	// Homepage / Frontpage Options.
	$wp_customize->add_section( 'chique_homepage_options', array(
		'description' => esc_html__( 'Only posts that belong to the categories selected here will be displayed on the front page', 'chique' ),
		'panel'       => 'chique_theme_options',
		'title'       => esc_html__( 'Homepage / Frontpage Options', 'chique' ),
	) );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_recent_posts_heading',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => esc_html__( 'Blog', 'chique' ),
			'label'             => esc_html__( 'Recent Posts Heading', 'chique' ),
			'section'           => 'chique_homepage_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_front_page_category',
			'sanitize_callback' => 'chique_sanitize_category_list',
			'custom_control'    => 'Chique_Multi_Cat',
			'label'             => esc_html__( 'Categories', 'chique' ),
			'section'           => 'chique_homepage_options',
			'type'              => 'dropdown-categories',
		)
	);

	// Pagination Options.
	$wp_customize->add_section( 'chique_pagination_options', array(
		'panel'       => 'chique_theme_options',
		'title'       => esc_html__( 'Pagination Options', 'chique' ),
	) );

		chique_register_option( $wp_customize, array(
			'name'              => 'chique_pagination_type',
			'default'           => 'default',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_get_pagination_types(),
			'label'             => esc_html__( 'Pagination type', 'chique' ),
			'section'           => 'chique_pagination_options',
			'type'              => 'select',
		)
	);

	/* Scrollup Options */
	$wp_customize->add_section( 'chique_scrollup', array(
		'panel'    => 'chique_theme_options',
		'title'    => esc_html__( 'Scrollup Options', 'chique' ),
	) );

	$action = 'install-plugin';
	$slug   = 'to-top';

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

	// Add note to Scroll up Section
    chique_register_option( $wp_customize, array(
            'name'              => 'chique_to_top_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Chique_Note_Control',
            'active_callback'   => 'chique_is_to_top_inactive',
            /* translators: 1: <a>/link tag start, 2: </a>/link tag close. */
            'label'             => sprintf( esc_html__( 'For Scroll Up, install %1$sTo Top%2$s Plugin', 'chique' ),
                '<a target="_blank" href="' . esc_url( $install_url ) . '">',
                '</a>'

            ),
           'section'            => 'chique_scrollup',
            'type'              => 'description',
            'priority'          => 1,
        )
    );

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_to_top_option_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Chique_Note_Control',
            'active_callback'   => 'chique_is_to_top_active',
            /* translators: 1: <a>/link tag start, 2: </a>/link tag close. */
			'label'             => sprintf( esc_html__( 'For Scroll Up Options, go %1$shere%2$s', 'chique'  ),
                 '<a href="javascript:wp.customize.panel( \'to_top_panel\' ).focus();">',
                 '</a>'
            ),
            'section'           => 'chique_scrollup',
            'type'              => 'description',
        )
    );
}
add_action( 'customize_register', 'chique_theme_options' );

if ( ! function_exists( 'chique_is_to_top_inactive' ) ) :
    /**
    * Return true if featured_content is active
    *
    * @since Simclick 0.1
    */
    function chique_is_to_top_inactive( $control ) {
        return ! ( class_exists( 'To_Top' ) );
    }
endif;

if ( ! function_exists( 'chique_is_to_top_active' ) ) :
    /**
    * Return true if featured_content is active
    *
    * @since Simclick 0.1
    */
    function chique_is_to_top_active( $control ) {
        return ( class_exists( 'To_Top' ) );
    }
endif;
