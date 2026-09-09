<?php
/**
 * Stats options
 *
 * @package Chique
 */

/**
 * Add skills options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_skills_options( $wp_customize ) {
	$wp_customize->add_section( 'chique_skills', array(
			'title' => esc_html__( 'Skills', 'chique-pro' ),
			'panel' => 'chique_theme_options',
		)
	);

	// Add color scheme setting and control.
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_skills_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique-pro' ),
			'section'           => 'chique_skills',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_skills_title',
			'default'           => esc_html__( 'My Skills', 'chique-pro' ),
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_skills_active',
			'label'             => esc_html__( 'Title', 'chique-pro' ),
			'section'           => 'chique_skills',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_skills_sub_title',
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_skills_active',
			'label'             => esc_html__( 'Sub Title', 'chique-pro' ),
			'section'           => 'chique_skills',
			'type'              => 'textarea',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_skills_number',
			'default'           => 4,
			'sanitize_callback' => 'chique_sanitize_number_range',
			'active_callback'   => 'chique_is_skills_active',
			'description'       => esc_html__( 'Save and refresh the page if No of items is changed', 'chique-pro' ),
			'input_attrs'       => array(
				'style' => 'width: 100px;',
				'min'   => 0,
			),
			'label'             => esc_html__( 'No of Items', 'chique-pro' ),
			'section'           => 'chique_skills',
			'type'              => 'number',
			'transport'         => 'postMessage',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_skills_image',
			'sanitize_callback' => 'chique_sanitize_image',
			'active_callback'   => 'chique_is_skills_active',
			'custom_control'    => 'WP_Customize_Image_Control',
			'label'             => esc_html__( 'Image', 'chique-pro' ),
			'section'           => 'chique_skills',
		)
	);

	$number = get_theme_mod( 'chique_skills_number', 4 );

	for ( $i = 1; $i <= $number ; $i++ ) {
		chique_register_option( $wp_customize, array(
				'name'              => 'chique_skills_note_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'custom_control'    => 'Chique_Note_Control',
				'active_callback'   => 'chique_is_skills_active',
				'label'             => esc_html__( 'Item #', 'chique-pro' ) .  $i,
				'section'           => 'chique_skills',
				'type'              => 'description',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_skills_title_' . $i,
				'sanitize_callback' => 'sanitize_text_field',
				'active_callback'   => 'chique_is_skills_active',
				'label'             => esc_html__( 'Title', 'chique-pro' ),
				'section'           => 'chique_skills',
				'type'              => 'text',
			)
		);

		chique_register_option( $wp_customize, array(
				'name'              => 'chique_skills_percentage_' . $i,
				'sanitize_callback' => 'chique_sanitize_number_range',
				'active_callback'   => 'chique_is_skills_active',
				'label'             => esc_html__( 'Percentage', 'chique-pro' ),
				'section'           => 'chique_skills',
				'type'              => 'number',
					'input_attrs'       => array(
					'style' => 'width: 50px;',
					'min'   => 1,
					'max'   => 100,
				),
			)
		);
	} // End for().

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_skills_more_text',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'chique_is_skills_active',
			'label'             => esc_html__( 'Button Text', 'chique-pro' ),
			'section'           => 'chique_skills',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_skills_more_link',
			'sanitize_callback' => 'esc_url_raw',
			'active_callback'   => 'chique_is_skills_active',
			'label'             => esc_html__( 'Button Link', 'chique-pro' ),
			'section'           => 'chique_skills',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_skills_more_target',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'active_callback'   => 'chique_is_skills_active',
			'label'             => esc_html__( 'Open Button Link in New Window/Tab', 'chique-pro' ),
			'section'           => 'chique_skills',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);
}
add_action( 'customize_register', 'chique_skills_options', 10 );

/** Active Callback Functions **/
if( ! function_exists( 'chique_is_skills_active' ) ) :
	/**
	* Return true if stat is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_skills_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_skills_option' )->value();

		return ( chique_check_section( $enable ) );
	}
endif;
