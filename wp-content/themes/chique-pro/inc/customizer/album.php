<?php
/**
 * Album options
 *
 * @package Chique
 */

/**
 * Add album content options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_album_options( $wp_customize ) {

	$wp_customize->add_section( 'chique_album', array(
			'title' => esc_html__( 'Album', 'chique-pro' ),
			'panel' => 'chique_theme_options',
		)
	);

	// Add color scheme setting and control.
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_album_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique-pro' ),
			'section'           => 'chique_album',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_album_layout',
			'default'           => 'layout-three',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_album_active',
			'choices'           => array(
				'layout-two'   => esc_html__( '2 columns', 'chique-pro' ),
				'layout-three' => esc_html__( '3 columns', 'chique-pro' ),
				'layout-four'  => esc_html__( '4 columns', 'chique-pro' ),
			),
			'label'             => esc_html__( 'Select Featured Content Layout', 'chique-pro' ),
			'section'           => 'chique_album',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_album_title',
			'default'           => esc_html__( 'Our Album', 'chique-pro' ),
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_album_active',
			'label'             => esc_html__( 'Headline', 'chique-pro' ),
			'section'           => 'chique_album',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_album_sub_title',
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_album_active',
			'label'             => esc_html__( 'Sub headline', 'chique-pro' ),
			'section'           => 'chique_album',
			'type'              => 'textarea',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_album_type',
			'default'           => 'category',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_album_active',
			'choices'           => chique_section_type_options(),
			'label'             => esc_html__( 'Type', 'chique-pro' ),
			'section'           => 'chique_album',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_album_text_align',
			'default'           => 'text-aligned-left',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_album_active',
			'choices'           => array(
				'text-aligned-right'  => esc_html__( 'Right', 'chique-pro' ),
				'text-aligned-center' => esc_html__( 'Center', 'chique-pro' ),
				'text-aligned-left'   => esc_html__( 'Left', 'chique-pro' ),
			),
			'label'             => esc_html__( 'Text Alignment', 'chique-pro' ),
			'section'           => 'chique_album',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_album_number',
			'default'           => 3,
			'sanitize_callback' => 'chique_sanitize_number_range',
			'active_callback'   => 'chique_is_album_active',
			'description'       => esc_html__( 'Save and refresh the page if No. of Items is changed', 'chique-pro' ),
			'input_attrs'       => array(
				'style' => 'width: 100px;',
				'min'   => 0,
			),
			'label'             => esc_html__( 'No of Items', 'chique-pro' ),
			'section'           => 'chique_album',
			'type'              => 'number',
			'transport'         => 'postMessage',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_album_show',
			'default'           => 'hide-content',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_album_post_page_category_content_active',
			'choices'           => chique_content_show(),
			'label'             => esc_html__( 'Display Content', 'chique-pro' ),
			'section'           => 'chique_album',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_album_meta_show',
			'default'           => 'show-meta',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_album_post_page_category_content_active',
			'choices'           => chique_meta_show(),
			'label'             => esc_html__( 'Display Meta', 'chique-pro' ),
			'section'           => 'chique_album',
			'type'              => 'select',
		)
	);


	chique_register_option( $wp_customize, array(
			'name'              => 'chique_album_select_category',
			'sanitize_callback' => 'chique_sanitize_category_list',
			'custom_control'    => 'Chique_Multi_Cat',
			'active_callback'   => 'chique_is_album_category_content_active',
			'label'             => esc_html__( 'Select Categories', 'chique-pro' ),
			'name'              => 'chique_album_select_category',
			'section'           => 'chique_album',
			'type'              => 'dropdown-categories',
		)
	);

	$number = get_theme_mod( 'chique_album_number', 3 );

	//loop for album post content
	for ( $i = 1; $i <= $number ; $i++ ) {
		chique_register_option( $wp_customize, array(
				'name'              => 'chique_album_post_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'default'           => 0,
				'active_callback'   => 'chique_is_album_post_content_active',
				'input_attrs'       => array(
					'style'             => 'width: 40px;'
				),
				'label'             => esc_html__( 'Post', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_album',
				'choices'           => chique_generate_post_array(),
				'type'              => 'select'
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_album_page_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'active_callback'   => 'chique_is_album_page_content_active',
				'label'             => esc_html__( 'Album Page', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_album',
				'type'              => 'dropdown-pages',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_album_note_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'custom_control'    => 'chique_Note_Control',
				'active_callback'   => 'chique_is_album_image_content_active',
				'label'             => esc_html__( 'Album #', 'chique-pro' ) .  $i,
				'section'           => 'chique_album',
				'type'              => 'description',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_album_image_' . $i,
				'sanitize_callback' => 'chique_sanitize_image',
				'custom_control'    => 'WP_Customize_Image_Control',
				'active_callback'   => 'chique_is_album_image_content_active',
				'label'             => esc_html__( 'Image', 'chique-pro' ),
				'section'           => 'chique_album',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_album_link_' . $i,
				'sanitize_callback' => 'esc_url_raw',
				'active_callback'   => 'chique_is_album_image_content_active',
				'label'             => esc_html__( 'Link', 'chique-pro' ),
				'section'           => 'chique_album',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_album_target_' . $i,
				'sanitize_callback' => 'chique_sanitize_checkbox',
				'active_callback'   => 'chique_is_album_image_content_active',
				'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
				'section'           => 'chique_album',
				'custom_control'    => 'Chique_Toggle_Control',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_album_title_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'active_callback'   => 'chique_is_album_image_content_active',
				'label'             => esc_html__( 'Title', 'chique-pro' ),
				'section'           => 'chique_album',
				'type'              => 'text',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_album_position_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'active_callback'   => 'chique_is_album_image_content_active',
				'label'             => esc_html__( 'Position', 'chique-pro' ),
				'section'           => 'chique_album',
				'type'              => 'text',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_album_content_' . $i,
				'sanitize_callback' => 'wp_kses_post',
				'active_callback'   => 'chique_is_album_image_content_active',
				'label'             => esc_html__( 'Content', 'chique-pro' ),
				'section'           => 'chique_album',
				'type'              => 'textarea',
			)
		);
	} // End for().

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_album_text',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_album_active',
			'label'             => esc_html__( 'Button Text', 'chique-pro' ),
			'section'           => 'chique_album',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_album_link',
			'sanitize_callback' => 'esc_url_raw',
			'active_callback'   => 'chique_is_album_active',
			'label'             => esc_html__( 'Button Link', 'chique-pro' ),
			'section'           => 'chique_album',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_album_target',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_album_active',
			'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
			'section'           => 'chique_album',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);
}
add_action( 'customize_register', 'chique_album_options', 10 );

/** Active Callback Functions **/
if ( ! function_exists( 'chique_is_album_active' ) ) :
	/**
	* Return true if album content is active
	*
	* @since Solid Construction Pro 1.0
	*/
	function chique_is_album_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_album_option' )->value();

		//return true only if previewed page on customizer matches the type of content option selected
		return ( chique_check_section( $enable ) );
	}
endif;

if ( ! function_exists( 'chique_is_album_post_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Solid Construction Pro 1.0
	*/
	function chique_is_album_post_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_album_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_album_active( $control ) && ( 'post' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_album_page_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Solid Construction Pro 1.0
	*/
	function chique_is_album_page_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_album_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_album_active( $control ) && ( 'page' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_album_category_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Solid Construction Pro 1.0
	*/
	function chique_is_album_category_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_album_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_album_active( $control ) && ( 'category' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_album_post_page_category_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Solid Construction Pro 1.0
	*/
	function chique_is_album_post_page_category_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_album_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_album_active( $control ) && ( 'category' === $type || 'page' === $type || 'post' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_album_image_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Solid Construction Pro 1.0
	*/
	function chique_is_album_image_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_album_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_album_active( $control ) && ( 'custom' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_album_bg_active' ) ) :
    /**
    * Return true if background is set
    *
    * @since Chique Pro 1.0
    */
    function chique_is_album_bg_active( $control ) {
        $bg_image = $control->manager->get_setting( 'chique_album_bg_image' )->value();

        return ( chique_is_album_active( $control ) && '' !== $bg_image );
    }
endif;
