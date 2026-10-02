<?php
/**
 * Stats options
 *
 * @package Chique
 */

/**
 * Add stats content options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_stats_options( $wp_customize ) {

    $wp_customize->add_section( 'chique_stats', array(
			'title' => esc_html__( 'Stats', 'chique-pro' ),
			'panel' => 'chique_theme_options',
		)
	);

	// Add color scheme setting and control.
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_stats_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique-pro' ),
			'section'           => 'chique_stats',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_stats_layout',
			'default'           => 'layout-four',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_stats_active',
			'choices'           => chique_sections_layout_options(),
			'label'             => esc_html__( 'Layout', 'chique-pro' ),
			'section'           => 'chique_stats',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_stats_archive_title',
			'default'           => esc_html__( 'Stats', 'chique-pro' ),
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_cpt_stats_inactive',
			'label'             => esc_html__( 'Headline', 'chique-pro' ),
			'section'           => 'chique_stats',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_stats_sub_title',
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_cpt_stats_inactive',
			'label'             => esc_html__( 'Sub headline', 'chique-pro' ),
			'section'           => 'chique_stats',
			'type'              => 'textarea',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_stats_type',
			'default'           => 'category',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_stats_active',
            'choices'           => chique_section_type_options(),
			'label'             => esc_html__( 'Type', 'chique-pro' ),
			'section'           => 'chique_stats',
			'type'              => 'select',
		)
	);

    chique_register_option( $wp_customize, array(
			'name'              => 'chique_stats_number',
			'default'           => 4,
			'sanitize_callback' => 'chique_sanitize_number_range',
			'active_callback'   => 'chique_is_stats_active',
			'description'       => esc_html__( 'Save and refresh the page if No. of Items is changed', 'chique-pro' ),
			'input_attrs'       => array(
				'style' => 'width: 100px;',
				'min'   => 0,
			),
			'label'             => esc_html__( 'No of Items', 'chique-pro' ),
			'section'           => 'chique_stats',
			'type'              => 'number',
			'transport'         => 'postMessage',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_stats_show',
			'default'           => 'excerpt',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_stats_post_page_category_cpt_content_active',
			'choices'           => chique_content_show(),
			'label'             => esc_html__( 'Display Content', 'chique-pro' ),
			'section'           => 'chique_stats',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_stats_select_category',
			'sanitize_callback' => 'chique_sanitize_category_list',
			'custom_control'    => 'Chique_Multi_Cat',
			'active_callback'   => 'chique_is_stats_category_content_active',
			'label'             => esc_html__( 'Select Categories', 'chique-pro' ),
			'name'              => 'chique_stats_select_category',
			'section'           => 'chique_stats',
			'type'              => 'dropdown-categories',
		)
	);

	$number = get_theme_mod( 'chique_stats_number', 4 );

	//loop for stats post content
	for ( $i = 1; $i <= $number ; $i++ ) {
		chique_register_option( $wp_customize, array(
				'name'              => 'chique_stats_post_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'default'           => 0,
				'active_callback'   => 'chique_is_stats_post_content_active',
				'input_attrs'       => array(
					'style'             => 'width: 40px;'
				),
				'label'             => esc_html__( 'Post', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_stats',
				'choices'           => chique_generate_post_array(),
				'type'              => 'select'
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_stats_page_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'active_callback'   => 'chique_is_stats_page_content_active',
				'label'             => esc_html__( 'Stats Page', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_stats',
				'type'              => 'dropdown-pages',
			)
		);


		chique_register_option( $wp_customize, array(
				'name'              => 'chique_stats_note_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'custom_control'    => 'Chique_Note_Control',
				'active_callback'   => 'chique_is_stats_image_content_active',
				'label'             => esc_html__( 'Stats #', 'chique-pro' ) .  $i,
				'section'           => 'chique_stats',
				'type'              => 'description',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_stats_image_' . $i,
				'sanitize_callback' => 'chique_sanitize_image',
				'custom_control'    => 'WP_Customize_Image_Control',
				'active_callback'   => 'chique_is_stats_image_content_active',
				'label'             => esc_html__( 'Image', 'chique-pro' ),
				'section'           => 'chique_stats',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_stats_link_' . $i,
				'sanitize_callback' => 'esc_url_raw',
				'active_callback'   => 'chique_is_stats_image_content_active',
				'label'             => esc_html__( 'Link', 'chique-pro' ),
				'section'           => 'chique_stats',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_stats_target_' . $i,
				'sanitize_callback' => 'chique_sanitize_checkbox',
				'active_callback'   => 'chique_is_stats_image_content_active',
				'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
				'section'           => 'chique_stats',
				'custom_control'    => 'Chique_Toggle_Control',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_stats_title_' . $i,
				'sanitize_callback' => 'wp_kses_post',
				'active_callback'   => 'chique_is_stats_image_content_active',
				'label'             => esc_html__( 'Title', 'chique-pro' ),
				'section'           => 'chique_stats',
				'type'              => 'text',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_stats_content_' . $i,
				'sanitize_callback' => 'wp_kses_post',
				'active_callback'   => 'chique_is_stats_image_content_active',
				'label'             => esc_html__( 'Content', 'chique-pro' ),
				'section'           => 'chique_stats',
				'type'              => 'textarea',
			)
		);
	} // End for().

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_stats_text',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_stats_active',
			'label'             => esc_html__( 'Button Text', 'chique-pro' ),
			'section'           => 'chique_stats',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_stats_link',
			'sanitize_callback' => 'esc_url_raw',
			'active_callback'   => 'chique_is_stats_active',
			'label'             => esc_html__( 'Button Link', 'chique-pro' ),
			'section'           => 'chique_stats',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_stats_target',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_stats_active',
			'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
			'section'           => 'chique_stats',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);
}
add_action( 'customize_register', 'chique_stats_options', 10 );

/** Active Callback Functions **/
if ( ! function_exists( 'chique_is_stats_active' ) ) :
	/**
	* Return true if stats content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_stats_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_stats_option' )->value();

		//return true only if previewed page on customizer matches the type of content option selected
		return ( chique_check_section( $enable ) );
	}
endif;

if ( ! function_exists( 'chique_is_cpt_stats_inactive' ) ) :
	/**
	* Return true if CPT stats content is inactive
	*
	* @since Chique Pro 1.0
	*/

	function chique_is_cpt_stats_inactive( $control ) {
		$type = $control->manager->get_setting( 'chique_stats_type' )->value();

		return ( chique_is_stats_active( $control ) && 'ect-service' !== $type );
	}
endif;

if ( ! function_exists( 'chique_is_stats_post_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_stats_post_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_stats_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_stats_active( $control ) && ( 'post' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_stats_page_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_stats_page_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_stats_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_stats_active( $control ) && ( 'page' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_stats_cpt_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_stats_cpt_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_stats_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_stats_active( $control ) && ( 'ect-service' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_stats_category_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_stats_category_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_stats_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_stats_active( $control ) && ( 'category' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_stats_post_page_category_cpt_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_stats_post_page_category_cpt_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_stats_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_stats_active( $control ) && ( 'category' === $type || 'page' === $type || 'post' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_stats_image_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_stats_image_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_stats_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_stats_active( $control ) && ( 'custom' === $type ) );
	}
endif;
