<?php
/**
 * Featured Slider Options
 *
 * @package Chique
 */

/**
 * Add hero content options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_slider_options( $wp_customize ) {
	$wp_customize->add_section( 'chique_featured_slider', array(
			'panel' => 'chique_theme_options',
			'title' => esc_html__( 'Featured Slider', 'chique' ),
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_slider_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique' ),
			'section'           => 'chique_featured_slider',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_slider_number',
			'default'           => '4',
			'sanitize_callback' => 'chique_sanitize_number_range',

			'active_callback'   => 'chique_is_slider_active',
			'description'       => esc_html__( 'Save and refresh the page if No. of Slides is changed (Max no of slides is 20)', 'chique' ),
			'input_attrs'       => array(
				'style' => 'width: 100px;',
				'min'   => 0,
				'max'   => 20,
				'step'  => 1,
			),
			'label'             => esc_html__( 'No of Slides', 'chique' ),
			'section'           => 'chique_featured_slider',
			'type'              => 'number',
		)
	);

	$slider_number = get_theme_mod( 'chique_slider_number', 4 );

	for ( $i = 1; $i <= $slider_number ; $i++ ) {

		// Page Sliders
		chique_register_option( $wp_customize, array(
				'name'              => 'chique_slider_page_' . $i,
				'sanitize_callback' => 'chique_sanitize_post',
				'active_callback'   => 'chique_is_slider_active',
				'label'             => esc_html__( 'Page', 'chique' ) . ' # ' . $i,
				'section'           => 'chique_featured_slider',
				'type'              => 'dropdown-pages',
			)
		);
	} // End for().
}
add_action( 'customize_register', 'chique_slider_options' );

/** Active Callback Functions */

if ( ! function_exists( 'chique_is_slider_active' ) ) :
	/**
	* Return true if slider is active
	*
	* @since Chique Pro 1.0
	*/
	function chique_is_slider_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_slider_option' )->value();

		//return true only if previwed page on customizer matches the type option selected
		return chique_check_section( $enable );
	}
endif;
