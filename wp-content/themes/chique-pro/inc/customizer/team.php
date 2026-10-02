<?php
/**
 * Team options
 *
 * @package Chique
 */

/**
 * Add team content options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_team_options( $wp_customize ) {

	$wp_customize->add_section( 'chique_team', array(
			'title' => esc_html__( 'Team', 'chique-pro' ),
			'panel' => 'chique_theme_options',
		)
	);

	// Add color scheme setting and control.
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_team_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique-pro' ),
			'section'           => 'chique_team',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_team_layout',
			'default'           => 'layout-three',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_team_active',
			'choices'           => array(
				'layout-two'   => esc_html__( '2 columns', 'chique-pro' ),
				'layout-three' => esc_html__( '3 columns', 'chique-pro' ),
				'layout-four'  => esc_html__( '4 columns', 'chique-pro' ),
			),
			'label'             => esc_html__( 'Select Layout', 'chique-pro' ),
			'section'           => 'chique_team',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_team_style',
			'default'           => 'style-1',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_team_active',
			'choices'           => array(
				'style-1'   	=> esc_html__( 'Style 1', 'chique-pro' ),
				'style-2'  		=> esc_html__( 'Style 2', 'chique-pro' ),
			),
			'label'             => esc_html__( 'Style', 'chique-pro' ),
			'section'           => 'chique_team',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_team_title',
			'default'           => esc_html__( 'Our Team', 'chique-pro' ),
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_team_active',
			'label'             => esc_html__( 'Headline', 'chique-pro' ),
			'section'           => 'chique_team',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_team_sub_title',
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_team_active',
			'label'             => esc_html__( 'Sub headline', 'chique-pro' ),
			'section'           => 'chique_team',
			'type'              => 'textarea',
		)
	);

	$type = chique_section_type_options();

	$type['ect-team'] = esc_html__( 'Custom Post Type', 'chique-pro' );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_team_type',
			'default'           => 'category',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_team_active',
			'choices'           => $type,
			'label'             => esc_html__( 'Type', 'chique-pro' ),
			'section'           => 'chique_team',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_team_text_align',
			'default'           => 'text-aligned-center',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_team_active',
			'choices'           => array(
				'text-aligned-right'  => esc_html__( 'Right', 'chique-pro' ),
				'text-aligned-center' => esc_html__( 'Center', 'chique-pro' ),
				'text-aligned-left'   => esc_html__( 'Left', 'chique-pro' ),
			),
			'label'             => esc_html__( 'Text Alignment', 'chique-pro' ),
			'section'           => 'chique_team',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_team_number',
			'default'           => 5,
			'sanitize_callback' => 'chique_sanitize_number_range',
			'active_callback'   => 'chique_is_team_active',
			'description'       => esc_html__( 'Save and refresh the page if No. of Items is changed', 'chique-pro' ),
			'input_attrs'       => array(
				'style' => 'width: 100px;',
				'min'   => 0,
			),
			'label'             => esc_html__( 'No of Items', 'chique-pro' ),
			'section'           => 'chique_team',
			'type'              => 'number',
			'transport'         => 'postMessage',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_team_show',
			'default'           => 'hide-content',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_team_post_page_category_cpt_content_active',
			'choices'           => chique_content_show(),
			'label'             => esc_html__( 'Display Content', 'chique-pro' ),
			'section'           => 'chique_team',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_team_meta_show',
			'default'           => 'show-meta',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_team_post_page_category_cpt_content_active',
			'choices'           => chique_meta_show(),
			'label'             => esc_html__( 'Display Meta', 'chique-pro' ),
			'section'           => 'chique_team',
			'type'              => 'select',
		)
	);


	chique_register_option( $wp_customize, array(
			'name'              => 'chique_team_select_category',
			'sanitize_callback' => 'chique_sanitize_category_list',
			'custom_control'    => 'Chique_Multi_Cat',
			'active_callback'   => 'chique_is_team_category_content_active',
			'label'             => esc_html__( 'Select Categories', 'chique-pro' ),
			'name'              => 'chique_team_select_category',
			'section'           => 'chique_team',
			'type'              => 'dropdown-categories',
		)
	);

	$number = get_theme_mod( 'chique_team_number', 5 );

	//loop for team post content
	for ( $i = 1; $i <= $number ; $i++ ) {
		chique_register_option( $wp_customize, array(
				'name'              => 'chique_team_post_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'default'           => 0,
				'active_callback'   => 'chique_is_team_post_content_active',
				'input_attrs'       => array(
					'style'             => 'width: 40px;'
				),
				'label'             => esc_html__( 'Post', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_team',
				'choices'           => chique_generate_post_array(),
				'type'              => 'select'
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_team_page_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'active_callback'   => 'chique_is_team_page_content_active',
				'label'             => esc_html__( 'Team Page', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_team',
				'type'              => 'dropdown-pages',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_team_cpt_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'active_callback'   => 'chique_is_team_cpt_active',
				'label'             => esc_html__( 'Team', 'chique-pro' ) . ' ' . $i ,
				'section'           => 'chique_team',
				'type'              => 'select',
                'choices'           => chique_generate_post_array( 'ect-team' ),
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_team_note_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'custom_control'    => 'chique_Note_Control',
				'active_callback'   => 'chique_is_team_image_content_active',
				'label'             => esc_html__( 'Team #', 'chique-pro' ) .  $i,
				'section'           => 'chique_team',
				'type'              => 'description',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_team_image_' . $i,
				'sanitize_callback' => 'chique_sanitize_image',
				'custom_control'    => 'WP_Customize_Image_Control',
				'active_callback'   => 'chique_is_team_image_content_active',
				'label'             => esc_html__( 'Image', 'chique-pro' ),
				'section'           => 'chique_team',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_team_link_' . $i,
				'sanitize_callback' => 'esc_url_raw',
				'active_callback'   => 'chique_is_team_image_content_active',
				'label'             => esc_html__( 'Link', 'chique-pro' ),
				'section'           => 'chique_team',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_team_target_' . $i,
				'sanitize_callback' => 'chique_sanitize_checkbox',
				'active_callback'   => 'chique_is_team_image_content_active',
				'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
				'section'           => 'chique_team',
				'custom_control'    => 'Chique_Toggle_Control',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_team_title_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'active_callback'   => 'chique_is_team_image_content_active',
				'label'             => esc_html__( 'Title', 'chique-pro' ),
				'section'           => 'chique_team',
				'type'              => 'text',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_team_position_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'active_callback'   => 'chique_is_team_image_content_active',
				'label'             => esc_html__( 'Position', 'chique-pro' ),
				'section'           => 'chique_team',
				'type'              => 'text',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_team_content_' . $i,
				'sanitize_callback' => 'wp_kses_post',
				'active_callback'   => 'chique_is_team_image_content_active',
				'label'             => esc_html__( 'Content', 'chique-pro' ),
				'section'           => 'chique_team',
				'type'              => 'textarea',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_team_social_link_one_' . $i,
				'sanitize_callback' => 'esc_url_raw',
				'active_callback'   => 'chique_is_team_cpt_inactive',
				'label'             => esc_html__( 'Team #', 'chique-pro' ) .  $i . esc_html__( ': Social Link #1', 'chique-pro' ),
				'section'           => 'chique_team',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_team_social_link_two_' . $i,
				'sanitize_callback' => 'esc_url_raw',
				'active_callback'   => 'chique_is_team_cpt_inactive',
				'label'             => esc_html__( 'Team #', 'chique-pro' ) .  $i . esc_html__( ': Social Link #2', 'chique-pro' ),
				'section'           => 'chique_team',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_team_social_link_three_' . $i,
				'sanitize_callback' => 'esc_url_raw',
				'active_callback'   => 'chique_is_team_cpt_inactive',
				'label'             => esc_html__( 'Team #', 'chique-pro' ) .  $i . esc_html__( ': Social Link #3', 'chique-pro' ),
				'section'           => 'chique_team',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_team_social_link_four_' . $i,
				'sanitize_callback' => 'esc_url_raw',
				'active_callback'   => 'chique_is_team_cpt_inactive',
				'label'             => esc_html__( 'Team #', 'chique-pro' ) .  $i . esc_html__( ': Social Link #4', 'chique-pro' ),
				'section'           => 'chique_team',
			)
		);
	} // End for().

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_team_text',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_team_active',
			'label'             => esc_html__( 'Button Text', 'chique-pro' ),
			'section'           => 'chique_team',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_team_link',
			'sanitize_callback' => 'esc_url_raw',
			'active_callback'   => 'chique_is_team_active',
			'label'             => esc_html__( 'Button Link', 'chique-pro' ),
			'section'           => 'chique_team',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_team_target',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_team_active',
			'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique-pro' ),
			'section'           => 'chique_team',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);
}
add_action( 'customize_register', 'chique_team_options', 10 );

/** Active Callback Functions **/
if ( ! function_exists( 'chique_is_team_active' ) ) :
	/**
	* Return true if team content is active
	*
	* @since Solid Construction Pro 1.0
	*/
	function chique_is_team_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_team_option' )->value();

		//return true only if previewed page on customizer matches the type of content option selected
		return ( chique_check_section( $enable ) );
	}
endif;

if ( ! function_exists( 'chique_is_team_post_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Solid Construction Pro 1.0
	*/
	function chique_is_team_post_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_team_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_team_active( $control ) && ( 'post' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_team_page_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Solid Construction Pro 1.0
	*/
	function chique_is_team_page_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_team_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_team_active( $control ) && ( 'page' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_team_category_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Solid Construction Pro 1.0
	*/
	function chique_is_team_category_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_team_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_team_active( $control ) && ( 'category' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_team_post_page_category_cpt_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Solid Construction Pro 1.0
	*/
	function chique_is_team_post_page_category_cpt_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_team_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_team_active( $control ) && ( 'category' === $type || 'page' === $type || 'post' === $type || 'ect-team' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_team_image_content_active' ) ) :
	/**
	* Return true if page content is active
	*
	* @since Solid Construction Pro 1.0
	*/
	function chique_is_team_image_content_active( $control ) {
		$type = $control->manager->get_setting( 'chique_team_type' )->value();

		//return true only if previewed page on customizer matches the type of content option selected and is or is not selected type
		return ( chique_is_team_active( $control ) && ( 'custom' === $type ) );
	}
endif;

if ( ! function_exists( 'chique_is_team_bg_active' ) ) :
    /**
    * Return true if background is set
    *
    * @since Chique Pro 1.0
    */
    function chique_is_team_bg_active( $control ) {
        $bg_image = $control->manager->get_setting( 'chique_team_bg_image' )->value();

        return ( chique_is_team_active( $control ) && '' !== $bg_image );
    }
endif;

if ( ! function_exists( 'chique_is_team_cpt_active' ) ) :
	/**
	* Return true if cpt team is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_team_cpt_active( $control ) {
		$type = $control->manager->get_setting( 'chique_team_type' )->value();

		//return true only if previwed page on customizer matches the type of team option selected and is or is not selected type
		return ( chique_is_team_active( $control ) && 'ect-team' == $type );
	}
endif;

if ( ! function_exists( 'chique_is_team_cpt_inactive' ) ) :
	/**
	* Return true if cpt team is inactive
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_team_cpt_inactive( $control ) {
		$type = $control->manager->get_setting( 'chique_team_type' )->value();

		//return true only if previwed page on customizer matches the type of team option selected and is or is not selected type
		return ( chique_is_team_active( $control ) && 'ect-team' !== $type );
	}
endif;
