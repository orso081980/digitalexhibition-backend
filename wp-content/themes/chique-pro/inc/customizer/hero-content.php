<?php
/**
 * Hero Content Options
 *
 * @package Chique
 */

/**
 * Add hero content options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_hero_content_options( $wp_customize ) {
	$wp_customize->add_section( 'chique_hero_content_options', array(
			'title' => esc_html__( 'Hero Content', 'chique-pro' ),
			'panel' => 'chique_theme_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_hero_content_visibility',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique-pro' ),
			'section'           => 'chique_hero_content_options',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_hero_content_layout',
			'default'           => 'fluid',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_hero_content_active',
			'label'             => esc_html__( 'Layout', 'chique-pro' ),
			'section'           => 'chique_hero_content_options',
			'type'              => 'radio',
			'choices'           => array(
				'boxed' => esc_html__( 'Boxed', 'chique-pro' ),
				'fluid' => esc_html__( 'Fluid', 'chique-pro' ),
			),
		)
	);

	/* Hero Background */
    chique_register_option( $wp_customize, array(
            'name'              => 'chique_hero_content_bg_image',
            'sanitize_callback' => 'esc_url_raw',
            'active_callback'   => 'chique_is_hero_content_active',
            'custom_control'    => 'WP_Customize_Image_Control',
            'label'             => esc_html__( 'Background Image', 'chique-pro' ),
            'section'           => 'chique_hero_content_options',
        )
    );

    $wp_customize->add_setting( 'chique_hero_content_bg_position_x', array(
        'sanitize_callback' => 'chique_sanitize_hero_content_bg_position',
    ) );

    $wp_customize->add_setting( 'chique_hero_content_bg_position_y', array(
        'sanitize_callback' => 'chique_sanitize_hero_content_bg_position',
    ) );

    $wp_customize->add_control( new WP_Customize_Background_Position_Control( $wp_customize, 'chique_hero_content_bg_position', array(
        'label'           => esc_html__( 'Background Image Position', 'chique-pro' ),
        'active_callback' => 'chique_is_hero_content_bg_active',
        'section'         => 'chique_hero_content_options',
        'settings'        => array(
            'x' => 'chique_hero_content_bg_position_x',
            'y' => 'chique_hero_content_bg_position_y',
        ),
    ) ) );

    chique_register_option( $wp_customize, array(
        'name'              => 'chique_hero_content_bg_size',
        'default'           => 'auto',
        'description'       => esc_html__( 'In mobiles, Background Size is always cover', 'chique-pro' ),
        'sanitize_callback' => 'chique_sanitize_select',
        'active_callback'   => 'chique_is_hero_content_bg_active',
        'label'             => esc_html__( 'Desktop Background Image Size', 'chique-pro' ),
        'section'           => 'chique_hero_content_options',
        'type'              => 'select',
        'choices' => array(
            'auto'    => esc_html__( 'Original', 'chique-pro' ),
            'contain' => esc_html__( 'Fit to Screen', 'chique-pro' ),
            'cover'   => esc_html__( 'Fill Screen', 'chique-pro' ),
        ),
    ) );

    chique_register_option( $wp_customize, array(
        'name'              => 'chique_hero_content_bg_repeat',
        'default'           => 'repeat',
        'sanitize_callback' => 'chique_sanitize_select',
        'active_callback'   => 'chique_is_hero_content_bg_active',
        'label'             => esc_html__( 'Repeat Background Image', 'chique-pro' ),
        'type'              => 'select',
        'section'           => 'chique_hero_content_options',
        'choices'           => array(
            'no-repeat' =>  esc_html__( 'No Repeat', 'chique-pro' ),
            'repeat'    =>  esc_html__( 'Repeat both vertically and horizontally (The last image will be clipped if it does not fit)', 'chique-pro' ),
            'repeat-x'  =>  esc_html__( 'Repeat only horizontally', 'chique-pro' ),
            'repeat-y'  =>  esc_html__( 'Repeat only vertically', 'chique-pro' ),
        ),
    ) );

    chique_register_option( $wp_customize, array(
        'name'              => 'chique_hero_content_bg_attachment',
        'default'           => 1,
        'sanitize_callback' => 'chique_sanitize_checkbox',
        'active_callback'   => 'chique_is_hero_content_bg_active',
        'label'             => esc_html__( 'Scroll with Page', 'chique-pro' ),
        'section'           => 'chique_hero_content_options',
        'custom_control'    => 'Chique_Toggle_Control',
    ) );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_hero_content_type',
			'default'           => 'page',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_hero_content_active',
			'choices'           => chique_section_type_options(),
			'label'             => esc_html__( 'Type', 'chique-pro' ),
			'section'           => 'chique_hero_content_options',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_hero_content',
			'default'           => '0',
			'sanitize_callback' => 'chique_sanitize_post',
			'active_callback'   => 'chique_is_hero_page_content_active',
			'label'             => esc_html__( 'Page', 'chique-pro' ),
			'section'           => 'chique_hero_content_options',
			'type'              => 'dropdown-pages',
			'allow_addition'    => true,
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_hero_content_post',
			'default'           => 0,
			'sanitize_callback' => 'chique_sanitize_post',
			'active_callback'   => 'chique_is_hero_post_content_active',
			'label'             => esc_html__( 'Post', 'chique-pro' ),
			'section'           => 'chique_hero_content_options',
			'choices'           => chique_generate_post_array(),
			'type'              => 'select',
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
			'name'              => 'chique_hero_content_category',
			'default'           => '0',
			'sanitize_callback' => 'absint',
			'active_callback'   => 'chique_is_hero_category_content_active',
			'label'             => esc_html__( 'Category', 'chique-pro' ),
			'type'              => 'select',
			'choices'           => $cats,
			'section'           => 'chique_hero_content_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_display_hero_content_title',
			'default'           => 1,
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_hero_post_page_category_content_active',
			'label'             => esc_html__( 'Display Title', 'chique-pro' ),
			'section'           => 'chique_hero_content_options',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_hero_content_show',
			'default'           => 'full-content',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_hero_post_page_category_content_active',
			'choices'           => chique_content_show(),
			'label'             => esc_html__( 'Display Content', 'chique-pro' ),
			'section'           => 'chique_hero_content_options',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_hero_content_frame',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_hero_content_fluid_active',
			'label'             => esc_html__( 'Add frame to content', 'chique-pro' ),
			'section'           => 'chique_hero_content_options',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_hero_content_position',
			'default'           => 'content-aligned-left',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_hero_content_active',
			'choices'           => array(
				'content-aligned-right'  => esc_html__( 'Right', 'chique-pro' ),
				'content-aligned-left'   => esc_html__( 'Left', 'chique-pro' ),
			),
			'label'             => esc_html__( 'Content Position', 'chique-pro' ),
			'section'           => 'chique_hero_content_options',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_hero_text_align',
			'default'           => 'text-aligned-right',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_hero_content_active',
			'choices'           => array(
				'text-aligned-right'  => esc_html__( 'Right', 'chique-pro' ),
				'text-aligned-center' => esc_html__( 'Center', 'chique-pro' ),
				'text-aligned-left'   => esc_html__( 'Left', 'chique-pro' ),
			),
			'label'             => esc_html__( 'Text Alignment', 'chique-pro' ),
			'section'           => 'chique_hero_content_options',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_hero_content_title',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_hero_custom_content_active',
			'label'             => esc_html__( 'Title', 'chique-pro' ),
			'section'           => 'chique_hero_content_options',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_hero_content_subtitle',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_hero_content_active',
			'label'             => esc_html__( 'Subtitle', 'chique-pro' ),
			'section'           => 'chique_hero_content_options',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_hero_content_content',
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_hero_custom_content_active',
			'label'             => esc_html__( 'Content', 'chique-pro' ),
			'section'           => 'chique_hero_content_options',
			'type'              => 'textarea',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_hero_content_image',
			'sanitize_callback' => 'chique_sanitize_image',
			'custom_control'    => 'WP_Customize_Image_Control',
			'active_callback'   => 'chique_is_hero_custom_content_active',
			'label'             => esc_html__( 'Image', 'chique-pro' ),
			'section'           => 'chique_hero_content_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_hero_content_link',
			'sanitize_callback' => 'esc_url_raw',
			'active_callback'   => 'chique_is_hero_custom_content_active',
			'label'             => esc_html__( 'Image Link', 'chique-pro' ),
			'section'           => 'chique_hero_content_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_hero_content_target',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_hero_custom_content_active',
			'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
			'section'           => 'chique_hero_content_options',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_hero_content_more_text',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_hero_custom_content_active',
			'label'             => esc_html__( 'Button Text', 'chique-pro' ),
			'section'           => 'chique_hero_content_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_hero_content_more_link',
			'sanitize_callback' => 'esc_url_raw',
			'active_callback'   => 'chique_is_hero_custom_content_active',
			'label'             => esc_html__( 'Button Link', 'chique-pro' ),
			'section'           => 'chique_hero_content_options',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_hero_content_more_target',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_hero_custom_content_active',
			'label'             => esc_html__( 'Open Button Link in New Window/Tab', 'chique-pro' ),
			'section'           => 'chique_hero_content_options',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);
}
add_action( 'customize_register', 'chique_hero_content_options' );

/** Active Callback Functions **/
if ( ! function_exists( 'chique_is_hero_content_active' ) ) :
	/**
	* Return true if hero content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_hero_content_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_hero_content_visibility' )->value();

		//return true only if previewed page on customizer matches the type of content option selected
		return ( chique_check_section( $enable ) );
	}
endif;

if ( ! function_exists( 'chique_is_hero_content_fluid_active' ) ) :
	/**
	* Return true if hero content with fluid layout is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_hero_content_fluid_active( $control ) {
		$layout = $control->manager->get_setting( 'chique_hero_content_layout' )->value();

		//return true only if previewed page on customizer matches the type of content option selected
		return ( chique_is_hero_content_active( $control ) && 'fluid' === $layout );
	}
endif;

if ( ! function_exists( 'chique_is_hero_post_content_active' ) ) :
	/**
	* Return true if post content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_hero_post_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_hero_content_type' )->value();

		return ( chique_is_hero_content_active( $control ) && 'post' == $type );
	}
endif;

if ( ! function_exists( 'chique_is_hero_page_content_active' ) ) :
	/**
	* Return true if hero page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_hero_page_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_hero_content_type' )->value();

		return ( chique_is_hero_content_active( $control ) && 'page' == $type );
	}
endif;

if ( ! function_exists( 'chique_is_hero_category_content_active' ) ) :
	/**
	* Return true if hero category content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_hero_category_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_hero_content_type' )->value();

		return ( chique_is_hero_content_active( $control ) && 'category' == $type );
	}
endif;

if ( ! function_exists( 'chique_is_hero_post_page_category_content_active' ) ) :
	/**
	* Return true if hero post/page/category content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_hero_post_page_category_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_hero_content_type' )->value();

		return ( chique_is_hero_content_active( $control ) && ( 'page' == $type || 'post' == $type || 'category' == $type )
			);
	}
endif;

if ( ! function_exists( 'chique_is_hero_custom_content_active' ) ) :
	/**
	* Return true if hero custom content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_hero_custom_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_hero_content_type' )->value();

		return ( chique_is_hero_content_active( $control ) && ( 'custom' == $type )
			);
	}
endif;

if ( ! function_exists( 'chique_is_hero_content_bg_active' ) ) :
    /**
    * Return true if background is set
    *
    * @since Chique Pro 1.0
    */
    function chique_is_hero_content_bg_active( $control ) {
        $bg_image = $control->manager->get_setting( 'chique_hero_content_bg_image' )->value();

        return ( chique_is_hero_content_active( $control ) && '' !== $bg_image );
    }
endif;
