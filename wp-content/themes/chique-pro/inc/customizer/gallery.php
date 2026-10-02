<?php
/**
 * Gallery Options
 *
 * @package  Chique
 */

/**
 * Add gallery options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_gallery_options( $wp_customize ) {
	// Add note to Gallery Colors Section.
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_gallery_colors_note',
			'sanitize_callback' => 'sanitize_text_field',
			'custom_control'    => 'chique_Note_Control',
			'label'             => sprintf( esc_html__( 'For all Gallery Options, go %1$shere%2$s', 'chique-pro' ),
				'<a href="javascript:wp.customize.section( \'chique_gallery_options\' ).focus();">',
				 '</a>'
			),
			'section'           => 'chique_colors_gallery',
			'type'              => 'description',
			'priority'          => 1,
		)
	);

	// Add note to Gallery Section so that user can move to colors section quickly.

	$wp_customize->add_section( 'chique_gallery_options', array(
			'title' => esc_html__( 'Gallery', 'chique-pro' ),
			'panel' => 'chique_theme_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_gallery_visibility',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique-pro' ),
			'section'           => 'chique_gallery_options',
			'type'              => 'select',
		)
	);

	/* Gallery Background */
    chique_register_option( $wp_customize, array(
            'name'              => 'chique_gallery_bg_image',
            'sanitize_callback' => 'esc_url_raw',
            'active_callback'   => 'chique_is_gallery_active',
            'custom_control'    => 'WP_Customize_Image_Control',
            'label'             => esc_html__( 'Background Image', 'chique-pro' ),
            'section'           => 'chique_gallery_options',
        )
    );

    $wp_customize->add_setting( 'chique_gallery_bg_position_x', array(
        'sanitize_callback' => 'chique_sanitize_gallery_bg_position',
    ) );

    $wp_customize->add_setting( 'chique_gallery_bg_position_y', array(
        'sanitize_callback' => 'chique_sanitize_gallery_bg_position',
    ) );

    $wp_customize->add_control( new WP_Customize_Background_Position_Control( $wp_customize, 'chique_gallery_bg_position', array(
        'label'           => esc_html__( 'Background Image Position', 'chique-pro' ),
        'active_callback' => 'chique_is_gallery_bg_active',
        'section'         => 'chique_gallery_options',
        'settings'        => array(
            'x' => 'chique_gallery_bg_position_x',
            'y' => 'chique_gallery_bg_position_y',
        ),
    ) ) );

    chique_register_option( $wp_customize, array(
        'name'              => 'chique_gallery_bg_size',
        'default'           => 'auto',
        'description'       => esc_html__( 'In mobiles, Background Size is always cover', 'chique-pro' ),
        'sanitize_callback' => 'chique_sanitize_select',
        'active_callback'   => 'chique_is_gallery_bg_active',
        'label'             => esc_html__( 'Desktop Background Image Size', 'chique-pro' ),
        'section'           => 'chique_gallery_options',
        'type'              => 'select',
        'choices' => array(
            'auto'    => esc_html__( 'Original', 'chique-pro' ),
            'contain' => esc_html__( 'Fit to Screen', 'chique-pro' ),
            'cover'   => esc_html__( 'Fill Screen', 'chique-pro' ),
        ),
    ) );

    chique_register_option( $wp_customize, array(
        'name'              => 'chique_gallery_bg_repeat',
        'default'           => 'repeat',
        'sanitize_callback' => 'chique_sanitize_select',
        'active_callback'   => 'chique_is_gallery_bg_active',
        'label'             => esc_html__( 'Repeat Background Image', 'chique-pro' ),
        'type'              => 'select',
        'section'           => 'chique_gallery_options',
        'choices'           => array(
            'no-repeat' =>  esc_html__( 'No Repeat', 'chique-pro' ),
            'repeat'    =>  esc_html__( 'Repeat both vertically and horizontally (The last image will be clipped if it does not fit)', 'chique-pro' ),
            'repeat-x'  =>  esc_html__( 'Repeat only horizontally', 'chique-pro' ),
            'repeat-y'  =>  esc_html__( 'Repeat only vertically', 'chique-pro' ),
        ),
    ) );

    chique_register_option( $wp_customize, array(
        'name'              => 'chique_gallery_bg_attachment',
        'default'           => 1,
        'sanitize_callback' => 'chique_sanitize_checkbox',
        'active_callback'   => 'chique_is_gallery_bg_active',
        'label'             => esc_html__( 'Scroll with Page', 'chique-pro' ),
        'section'           => 'chique_gallery_options',
        'custom_control'    => 'Chique_Toggle_Control',
    ) );

	$types = chique_section_type_options();

	// Unset image as gallery content has no image
	unset( $types['custom'] );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_gallery_type',
			'default'           => 'page',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_gallery_active',
			'choices'           => $types,
			'label'             => esc_html__( 'Type', 'chique-pro' ),
			'section'           => 'chique_gallery_options',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_gallery',
			'default'           => 0,
			'sanitize_callback' => 'chique_sanitize_post',
			'active_callback'   => 'chique_is_gallery_page_content_active',
			'label'             => esc_html__( 'Page', 'chique-pro' ),
			'section'           => 'chique_gallery_options',
			'type'              => 'dropdown-pages',
			'allow_addition'    => true,
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_gallery_post',
			'default'           => 0,
			'sanitize_callback' => 'chique_sanitize_post',
			'active_callback'   => 'chique_is_gallery_post_content_active',
			'label'             => esc_html__( 'Post', 'chique-pro' ),
			'section'           => 'chique_gallery_options',
			'choices'           => chique_generate_post_array(),
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_gallery_subtitle',
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_gallery_active',
			'label'             => esc_html__( 'Sub headline', 'chique-pro' ),
			'section'           => 'chique_gallery_options',
			'type'              => 'text',
		)
	);

	// create an empty array.
	$cats = array();

	$cats['0'] = esc_html__( '-- Select --', 'chique-pro' );

	// we loop over the categories and set the names and
	// labels we need.
	foreach ( get_categories() as $categories => $category ) {
		$cats[ $category->term_id ] = $category->name;
	}

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_gallery_category',
			'default'           => '0',
			'sanitize_callback' => 'absint',
			'active_callback'   => 'chique_is_gallery_category_content_active',
			'label'             => esc_html__( 'Category', 'chique-pro' ),
			'type'              => 'select',
			'choices'           => $cats,
			'section'           => 'chique_gallery_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_display_gallery_title',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'default'           => 1,
			'active_callback'   => 'chique_is_gallery_post_page_category_content_active',
			'label'             => esc_html__( 'Display Title', 'chique-pro' ),
			'section'           => 'chique_gallery_options',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);
}
add_action( 'customize_register', 'chique_gallery_options' );

/** Active Callback Functions **/
if ( ! function_exists( 'chique_is_gallery_active' ) ) :
	/**
	* Return true if gallery content is active
	*
	* @since Pop Rock Pro 1.2.2
	*/
	function chique_is_gallery_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_gallery_visibility' )->value();

		return ( chique_check_section( $enable ) );
	}
endif;

if ( ! function_exists( 'chique_is_gallery_post_content_active' ) ) :
	/**
	* Return true if post content is active
	*
	* @since Pop Rock Pro 1.2.2
	*/
	function chique_is_gallery_post_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_gallery_type' )->value();

		return ( chique_is_gallery_active( $control ) && 'post' === $type );
	}
endif;

if ( ! function_exists( 'chique_is_gallery_page_content_active' ) ) :
	/**
	* Return true if gallery page content is active
	*
	* @since Pop Rock Pro 1.2.2
	*/
	function chique_is_gallery_page_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_gallery_type' )->value();

		return ( chique_is_gallery_active( $control ) && 'page' === $type );
	}
endif;

if ( ! function_exists( 'chique_is_gallery_category_content_active' ) ) :
	/**
	* Return true if gallery category content is active
	*
	* @since Pop Rock Pro 1.2.2
	*/
	function chique_is_gallery_category_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_gallery_type' )->value();

		return ( chique_is_gallery_active( $control ) && 'category' === $type );
	}
endif;

if ( ! function_exists( 'chique_is_gallery_post_page_category_content_active' ) ) :
	/**
	* Return true if gallery post/page/category content is active
	*
	* @since Pop Rock Pro 1.2.2
	*/
	function chique_is_gallery_post_page_category_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_gallery_type' )->value();

		return ( chique_is_gallery_active( $control ) && ( 'page' === $type || 'post' === $type || 'category' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_gallery_bg_active' ) ) :
    /**
    * Return true if background is set
    *
    * @since Chique Pro 1.0
    */
    function chique_is_gallery_bg_active( $control ) {
        $bg_image = $control->manager->get_setting( 'chique_gallery_bg_image' )->value();

        return ( chique_is_gallery_active( $control ) && '' !== $bg_image );
    }
endif;
