<?php
/**
 * Header Media Options
 *
 * @package Chique
 */

/**
 * Add Header Media options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function chique_header_media_options( $wp_customize ) {
	$wp_customize->get_section( 'header_image' )->description = esc_html__( 'If you add video, it will only show up on Homepage/FrontPage. Other Pages will use Header/Post/Page Image depending on your selection of option. Header Image will be used as a fallback while the video loads ', 'chique' );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_header_media_option',
			'default'           => 'entire-site',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => array(
				'homepage'               => esc_html__( 'Homepage / Frontpage', 'chique' ),
				'entire-site'            => esc_html__( 'Entire Site', 'chique' ),
				'disable'                => esc_html__( 'Disabled', 'chique' ),
			),
			'label'             => esc_html__( 'Enable on', 'chique' ),
			'section'           => 'header_image',
			'type'              => 'select',
			'priority'          => 1,
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_header_media_content_align',
			'default'           => 'content-aligned-center',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => array(
				'content-aligned-center' => esc_html__( 'Center', 'chique' ),
				'content-aligned-right'  => esc_html__( 'Right', 'chique' ),
				'content-aligned-left'   => esc_html__( 'Left', 'chique' ),
			),
			'label'             => esc_html__( 'Content Position', 'chique' ),
			'section'           => 'header_image',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_header_media_text_align',
			'default'           => 'text-aligned-center',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => array(
				'text-aligned-right'  => esc_html__( 'Right', 'chique' ),
				'text-aligned-center' => esc_html__( 'Center', 'chique' ),
				'text-aligned-left'   => esc_html__( 'Left', 'chique' ),
			),
			'label'             => esc_html__( 'Text Alignment', 'chique' ),
			'section'           => 'header_image',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_header_media_homepage_opacity',
			'default'           => 0,
			'sanitize_callback' => 'chique_sanitize_number_range',
			'label'             => esc_html__( 'Header Media Overlay', 'chique' ),
			'section'           => 'header_image',
			'type'              => 'number',
			'input_attrs'       => array(
				'style' => 'width: 60px;',
				'min'   => 0,
				'max'   => 100,
			),
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_header_media_except_homepage_opacity',
			'default'           => 50,
			'sanitize_callback' => 'chique_sanitize_number_range',
			'label'             => esc_html__( 'Header Media Overlay (Except Homepage)', 'chique' ),
			'section'           => 'header_image',
			'type'              => 'number',
			'input_attrs'       => array(
				'style' => 'width: 60px;',
				'min'   => 0,
				'max'   => 100,
			),
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_header_media_scroll_down',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'default'           => 1,
			'label'             => esc_html__( 'Scroll Down Button', 'chique' ),
			'section'           => 'header_image',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_header_media_title',
			'sanitize_callback' => 'wp_kses_post',
			'label'             => esc_html__( 'Header Media Title', 'chique' ),
			'section'           => 'header_image',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_header_media_image',
			'sanitize_callback' => 'chique_sanitize_image',
			'custom_control'    => 'WP_Customize_Image_Control',
			'label'             => esc_html__( 'Header Media Logo', 'chique' ),
			'section'           => 'header_image',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_header_media_subtitle',
			'sanitize_callback' => 'wp_kses_post',
			'label'             => esc_html__( 'Header Media Sub Title', 'chique' ),
			'section'           => 'header_image',
			'type'              => 'text',
		)
	);

    chique_register_option( $wp_customize, array(
			'name'              => 'chique_header_media_text',
			'sanitize_callback' => 'wp_kses_post',
			'label'             => esc_html__( 'Header Media Text', 'chique' ),
			'section'           => 'header_image',
			'type'              => 'textarea',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_header_media_url',
			'sanitize_callback' => 'esc_url_raw',
			'label'             => esc_html__( 'Header Media Url', 'chique' ),
			'section'           => 'header_image',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_header_media_url_text',
			'sanitize_callback' => 'sanitize_text_field',
			'label'             => esc_html__( 'Header Media Url Text', 'chique' ),
			'section'           => 'header_image',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_header_url_target',
			'sanitize_callback' => 'chique_sanitize_checkbox',
			'label'             => esc_html__( 'Open Link in New Window/Tab', 'chique' ),
			'section'           => 'header_image',
			'custom_control'    => 'Chique_Toggle_Control',
		)
	);
}
add_action( 'customize_register', 'chique_header_media_options' );
