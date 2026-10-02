<?php
/**
 * Featured Content options
 *
 * @package Chique
 */

/**
 * Add featured content options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_featured_content_options( $wp_customize ) {
	// Add note to Jetpack Testimonial Section
    chique_register_option( $wp_customize, array(
            'name'              => 'chique_featured_content_jetpack_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Chique_Note_Control',
            'label'             => sprintf( esc_html__( 'For all Featured Content Options for Chique Theme, go %1$shere%2$s', 'chique-pro' ),
                '<a href="javascript:wp.customize.section( \'chique_featured_content\' ).focus();">',
                 '</a>'
            ),
           'section'            => 'featured_content',
            'type'              => 'description',
            'priority'          => 1,
        )
    );

    $wp_customize->add_section( 'chique_featured_content', array(
			'title' => esc_html__( 'Featured Content', 'chique-pro' ),
			'panel' => 'chique_theme_options',
		)
	);

	// Add color scheme setting and control.
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_featured_content_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique-pro' ),
			'section'           => 'chique_featured_content',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_featured_content_layout',
			'default'           => 'layout-three',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_featured_content_active',
			'choices'           => chique_sections_layout_options(),
			'label'             => esc_html__( 'Select Featured Content Layout', 'chique-pro' ),
			'section'           => 'chique_featured_content',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_featured_content_archive_title',
			'default'           => esc_html__( 'Featured', 'chique-pro' ),
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_featured_content_active',
			'label'             => esc_html__( 'Headline', 'chique-pro' ),
			'section'           => 'chique_featured_content',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_featured_content_sub_title',
			'default'           => esc_html__( 'My Featured Articles ', 'chique-pro' ),
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_featured_content_active',
			'label'             => esc_html__( 'Sub headline', 'chique-pro' ),
			'section'           => 'chique_featured_content',
			'type'              => 'textarea',
		)
	);

	$type = chique_section_type_options();

	$type['featured-content'] = esc_html__( 'Custom Post Type', 'chique-pro' );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_featured_content_type',
			'default'           => 'category',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_featured_content_active',
			/* translators: 1: plugin <a>/link tag start, 2: plugin </a>/link tag close. */
			'description'       => sprintf( esc_html__( 'For Custom Post Type Content, install %1$sEssential Content Types%2$s Plugin with Featured Content Type Enabled', 'chique-pro' ),
                '<a target="_blank" href="https://wordpress.org/plugins/essential-content-types/">',
                '</a>'
            ),
            'choices'           => $type,
			'label'             => esc_html__( 'Type', 'chique-pro' ),
			'section'           => 'chique_featured_content',
			'type'              => 'select',
		)
	);

    chique_register_option( $wp_customize, array(
            'name'              => 'chique_featured_content_cpt_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Chique_Note_Control',
            'active_callback'   => 'chique_is_featured_cpt_content_active',
            /* translators: 1: <a>/link tag start, 2: </a>/link tag close. */
			'label'             => sprintf( esc_html__( 'For CPT heading and sub-heading, go %1$shere%2$s', 'chique-pro' ),
                 '<a href="javascript:wp.customize.control( \'featured_content_title\' ).focus();">',
                 '</a>'
            ),
            'section'           => 'chique_featured_content',
            'type'              => 'description',
        )
    );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_featured_content_number',
			'default'           => 3,
			'sanitize_callback' => 'chique_sanitize_number_range',
			'active_callback'   => 'chique_is_featured_content_active',
			'description'       => esc_html__( 'Save and refresh the page if No. of Featured Content is changed (Max no of Featured Content is 20)', 'chique-pro' ),
			'input_attrs'       => array(
				'style' => 'width: 100px;',
				'min'   => 0,
			),
			'label'             => esc_html__( 'No of items', 'chique-pro' ),
			'section'           => 'chique_featured_content',
			'type'              => 'number',
			'transport'         => 'postMessage',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_featured_content_text_align',
			'default'           => 'text-aligned-center',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_featured_content_active',
			'choices'           => array(
				'text-aligned-right'  => esc_html__( 'Right', 'chique-pro' ),
				'text-aligned-center' => esc_html__( 'Center', 'chique-pro' ),
				'text-aligned-left'   => esc_html__( 'Left', 'chique-pro' ),
			),
			'label'             => esc_html__( 'Text Alignment', 'chique-pro' ),
			'section'           => 'chique_featured_content',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_featured_content_show',
			'default'           => 'excerpt',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_featured_post_page_category_cpt_content_active',
			'choices'           => chique_content_show(),
			'label'             => esc_html__( 'Display Content', 'chique-pro' ),
			'section'           => 'chique_featured_content',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_featured_meta_show',
			'default'           => 'hide-meta',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_featured_post_category_cpt_content_active',
			'choices'           => chique_meta_show(),
			'label'             => esc_html__( 'Display Meta', 'chique-pro' ),
			'section'           => 'chique_featured_content',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_featured_category_show',
			'default'           => 'hide-cat',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_featured_post_category_cpt_content_active',
			'choices'           => chique_category_show(),
			'label'             => esc_html__( 'Display Category', 'chique-pro' ),
			'section'           => 'chique_featured_content',
			'type'              => 'select',
		)
	);


	chique_register_option( $wp_customize, array(
			'name'              => 'chique_featured_content_select_category',
			'sanitize_callback' => 'chique_sanitize_category_list',
			'custom_control'    => 'Chique_Multi_Cat',
			'active_callback'   => 'chique_is_featured_category_content_active',
			'label'             => esc_html__( 'Select Categories', 'chique-pro' ),
			'name'              => 'chique_featured_content_select_category',
			'section'           => 'chique_featured_content',
			'type'              => 'dropdown-categories',
		)
	);

	$number = get_theme_mod( 'chique_featured_content_number', 3 );

	//loop for featured post content
	for ( $i = 1; $i <= $number ; $i++ ) {
		chique_register_option( $wp_customize, array(
				'name'              => 'chique_featured_content_post_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'default'           => 0,
				'active_callback'   => 'chique_is_featured_post_content_active',
				'input_attrs'       => array(
					'style'             => 'width: 40px;'
				),
				'label'             => esc_html__( 'Post', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_featured_content',
				'choices'           => chique_generate_post_array(),
				'type'              => 'select'
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_featured_content_page_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'active_callback'   => 'chique_is_featured_page_content_active',
				'label'             => esc_html__( 'Featured Page', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_featured_content',
				'type'              => 'dropdown-pages',
				'allow_addition'    => true,
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_featured_content_cpt_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'active_callback'   => 'chique_is_featured_cpt_content_active',
				'label'             => esc_html__( 'Featured Content', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_featured_content',
				'type'              => 'select',
                'choices'           => chique_generate_post_array( 'featured-content' ),
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_featured_content_note_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'custom_control'    => 'Chique_Note_Control',
				'active_callback'   => 'chique_is_featured_image_content_active',
				'label'             => esc_html__( 'Featured Content #', 'chique-pro' ) .  $i,
				'section'           => 'chique_featured_content',
				'type'              => 'description',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_featured_content_image_' . $i,
				'sanitize_callback' => 'chique_sanitize_image',
				'custom_control'    => 'WP_Customize_Image_Control',
				'active_callback'   => 'chique_is_featured_image_content_active',
				'label'             => esc_html__( 'Image', 'chique-pro' ),
				'section'           => 'chique_featured_content',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_featured_content_link_' . $i,
				'sanitize_callback' => 'esc_url_raw',
				'active_callback'   => 'chique_is_featured_image_content_active',
				'label'             => esc_html__( 'Link', 'chique-pro' ),
				'section'           => 'chique_featured_content',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_featured_content_target_' . $i,
				'sanitize_callback' => 'chique_sanitize_checkbox',
				'active_callback'   => 'chique_is_featured_image_content_active',
				'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
				'section'           => 'chique_featured_content',
				'custom_control'    => 'Chique_Toggle_Control',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_featured_content_title_' . $i,
				'sanitize_callback' => 'wp_kses_post',
				'active_callback'   => 'chique_is_featured_image_content_active',
				'label'             => esc_html__( 'Title', 'chique-pro' ),
				'section'           => 'chique_featured_content',
				'type'              => 'text',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_featured_content_content_' . $i,
				'sanitize_callback' => 'wp_kses_post',
				'active_callback'   => 'chique_is_featured_image_content_active',
				'label'             => esc_html__( 'Content', 'chique-pro' ),
				'section'           => 'chique_featured_content',
				'type'              => 'textarea',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_featured_content_more_button_text_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'active_callback'   => 'chique_is_featured_image_content_active',
				'label'             => esc_html__( 'More Button Text', 'chique-pro' ),
				'section'           => 'chique_featured_content',
				'type'              => 'text',
			)
		);
	} // End for().

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_featured_content_text',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_featured_content_active',
			'label'             => esc_html__( 'Button Text', 'chique-pro' ),
			'section'           => 'chique_featured_content',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_featured_content_link',
			'sanitize_callback' => 'esc_url_raw',
			'active_callback'   => 'chique_is_featured_content_active',
			'label'             => esc_html__( 'Button Link', 'chique-pro' ),
			'section'           => 'chique_featured_content',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_featured_content_target',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_featured_content_active',
			'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
			'section'           => 'chique_featured_content',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);
}
add_action( 'customize_register', 'chique_featured_content_options', 10 );

/** Active Callback Functions **/
if ( ! function_exists( 'chique_is_featured_content_active' ) ) :
	/**
	* Return true if featured content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_featured_content_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_featured_content_option' )->value();

		//return true only if previewed page on customizer matches the type of content option selected
		return ( chique_check_section( $enable ) );
	}
endif;

if ( ! function_exists( 'chique_is_featured_post_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_featured_post_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_featured_content_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_featured_content_active( $control ) && ( 'post' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_featured_page_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_featured_page_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_featured_content_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_featured_content_active( $control ) && ( 'page' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_featured_cpt_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_featured_cpt_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_featured_content_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_featured_content_active( $control ) && ( 'featured-content' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_featured_category_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_featured_category_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_featured_content_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_featured_content_active( $control ) && ( 'category' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_featured_post_page_category_cpt_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_featured_post_page_category_cpt_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_featured_content_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_featured_content_active( $control ) && ( 'category' === $type || 'featured-content' === $type || 'page' === $type || 'post' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_featured_post_category_cpt_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_featured_post_category_cpt_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_featured_content_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_featured_content_active( $control ) && ( 'category' === $type || 'featured-content' === $type || 'post' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_featured_image_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_featured_image_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_featured_content_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_featured_content_active( $control ) && ( 'custom' === $type ) );
	}
endif;
