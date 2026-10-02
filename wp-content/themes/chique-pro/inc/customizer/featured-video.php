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
function chique_featured_video_options( $wp_customize ) {

    $wp_customize->add_section( 'chique_featured_video', array(
			'title' => esc_html__( 'Featured Video', 'chique-pro' ),
			'panel' => 'chique_theme_options',
		)
	);

	// Add color scheme setting and control.
	chique_register_option( $wp_customize, array(
			'name'              => 'chique_featured_video_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'chique_sanitize_select',
			'choices'           => chique_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique-pro' ),
			'section'           => 'chique_featured_video',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
            'name'              => 'chique_featured_video_main_image',
            'sanitize_callback' => 'chique_sanitize_image',
            'active_callback'   => 'chique_is_featured_video_active',
            'custom_control'    => 'WP_Customize_Image_Control',
            'label'             => esc_html__( 'Section Background Image', 'chique-pro' ),
            'section'           => 'chique_featured_video',
            'mime_type'         => 'image',
        )
    );

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_featured_video_layout',
			'default'           => 'layout-one',
			'sanitize_callback' => 'chique_sanitize_select',
			'active_callback'   => 'chique_is_featured_video_active',
			'choices'           => chique_sections_layout_options(),
			'label'             => esc_html__( 'Select Layout', 'chique-pro' ),
			'section'           => 'chique_featured_video',
			'type'              => 'select',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_featured_video_archive_title',
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_featured_video_active',
			'label'             => esc_html__( 'Featured Video Title', 'chique-pro' ),
			'section'           => 'chique_featured_video',
			'type'              => 'text',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_featured_video_sub_title',
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'chique_is_featured_video_active',
			'label'             => esc_html__( 'Featured Content Archive Content', 'chique-pro' ),
			'section'           => 'chique_featured_video',
			'type'              => 'textarea',
		)
	);

	chique_register_option( $wp_customize, array(
			'name'              => 'chique_featured_video_number',
			'default'           => 1,
			'sanitize_callback' => 'chique_sanitize_number_range',
			'active_callback'   => 'chique_is_featured_video_active',
			'description'       => esc_html__( 'Save and refresh the page if No. of Featured Video is changed (Max no of Featured Video is 20)', 'chique-pro' ),
			'input_attrs'       => array(
				'style' => 'width: 100px;',
				'min'   => 0,
			),
			'label'             => esc_html__( 'No of Featured Video', 'chique-pro' ),
			'section'           => 'chique_featured_video',
			'type'              => 'number',
			'transport'         => 'postMessage',
		)
	);

	$number = get_theme_mod( 'chique_featured_video_number', 1 );

	for ( $i = 1; $i <= $number ; $i++ ) {
	    chique_register_option( $wp_customize, array(
	            'name'              => 'chique_featured_video_link_' . $i,
	            'sanitize_callback' => 'esc_url_raw',
	            'active_callback'   => 'chique_is_featured_video_active',
	            'label'             => esc_html__( 'Video Url', 'chique-pro' ) . ' ' . $i ,
	            'section'           => 'chique_featured_video',
	        )
	    );
	}
}
add_action( 'customize_register', 'chique_featured_video_options', 10 );

/** Active Callback Functions **/
if( ! function_exists( 'chique_is_featured_video_active' ) ) :
	/**
	* Return true if featured content is active
	*
	* @since Chique Pro Pro 1.0
	*/
	function chique_is_featured_video_active( $control ) {
		$enable = $control->manager->get_setting( 'chique_featured_video_option' )->value();

		return ( chique_check_section( $enable ) );
	}
endif;
