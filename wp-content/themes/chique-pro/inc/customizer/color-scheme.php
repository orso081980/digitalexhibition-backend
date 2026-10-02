<?php
/**
 * Theme Customizer
 *
 * @package Chique
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_color_scheme_register( $wp_customize ) {
	//Color Scheme
	$color_scheme = chique_get_color_scheme();

	// Add color scheme setting and control.
	chique_register_option( $wp_customize, array(
			'name'              => 'color_scheme',
			'default'           => 'default',
			'sanitize_callback' => 'chique_sanitize_select',
			'transport'         => 'postMessage',
			'label'             => esc_html__( 'Base Color Scheme', 'chique-pro' ),
			'section'           => 'colors',
			'type'              => 'select',
			'choices'           => chique_get_color_scheme_choices(),
			'priority'          => 1,
		)
	);

	$color_options = chique_color_options();

	$i = 30;
	foreach ( $color_options as $key => $value ) {
		chique_register_option( $wp_customize, array(
				'name'              => $key,
				'default'           => $value['default'],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
				'custom_control'    => 'WP_Customize_Color_Control',
				'label'             => $value['label'],
				'section'           => 'colors',
				'priority'          => $i,
			)
		);

		$i = $i + 10;
	}

	$wp_customize->get_control( 'secondary_background_color' )->priority = 40;
	$wp_customize->get_control( 'main_text_color' )->priority = 50;
	$wp_customize->get_control( 'slider_color' )->priority = 41;
	$wp_customize->get_control( 'slider_hover_color' )->priority = 42;
	$wp_customize->get_control( 'slider_content_color' )->priority = 43;
	$wp_customize->get_control( 'secondary_link_color' )->priority = 80;
	$wp_customize->get_control( 'button_background_color' )->priority = 90;
	$wp_customize->get_control( 'secondary_link_hover_color' )->priority = 81;
}
add_action( 'customize_register', 'chique_color_scheme_register' );
