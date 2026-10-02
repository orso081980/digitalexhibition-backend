<?php
/**
 * Promotion Contact Options
 *
 * @package Photo_Journal
 */

/**
 * Add promotion contact options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function photo_journal_promo_contact_options( $wp_customize ) {
	$wp_customize->add_section( 'photo_journal_promotion_contact', array(
			'title' => esc_html__( 'Promotion Contact', 'chique' ),
			'panel' => 'photo_journal_theme_options',
		)
	);

	photo_journal_register_option( $wp_customize, array(
			'name'              => 'photo_journal_promo_contact_visibility',
			'default'           => 'disabled',
			'sanitize_callback' => 'photo_journal_sanitize_select',
			'choices'           => photo_journal_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'chique' ),
			'section'           => 'photo_journal_promotion_contact',
			'type'              => 'select',
		)
	);

	photo_journal_register_option( $wp_customize, array(
			'name'              => 'photo_journal_promo_contact_image',
			'sanitize_callback' => 'photo_journal_sanitize_image',
			'custom_control'    => 'WP_Customize_Image_Control',
			'active_callback'   => 'photo_journal_is_promotion_contact_active',
			'label'             => esc_html__( 'Background Image', 'chique' ),
			'section'           => 'photo_journal_promotion_contact',
		)
	);

	/*Overlay Option for Promotion Headline Background Image */
	photo_journal_register_option( $wp_customize, array(
			'name'              => 'photo_journal_promo_contact_background_image_opacity',
			'default'           => '75',
			'sanitize_callback' => 'photo_journal_sanitize_number_range',
			'active_callback'   => 'photo_journal_is_promotion_contact_active',
			'label'             => esc_html__( 'Background Image Overlay', 'chique' ),
			'section'           => 'photo_journal_promotion_contact',
			'type'              => 'number',
			'input_attrs'       => array(
				'style' => 'width: 60px;',
				'min'   => 0,
				'max'   => 100,
			),
		)
	);

	photo_journal_register_option( $wp_customize, array(
			'name'              => 'photo_journal_promo_contact_title',
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'photo_journal_is_promotion_contact_active',
			'label'             => esc_html__( 'Headline', 'chique' ),
			'section'           => 'photo_journal_promotion_contact',
			'type'              => 'text',
		)
	);

	photo_journal_register_option( $wp_customize, array(
			'name'              => 'photo_journal_promo_contact_description',
			'sanitize_callback' => 'wp_kses_post',
			'label'             => esc_html__( 'Subheadline', 'chique' ),
			'active_callback'   => 'photo_journal_is_promotion_contact_active',
			'section'           => 'photo_journal_promotion_contact',
			'type'              => 'textarea',
		)
	);

	photo_journal_register_option( $wp_customize, array(
			'name'              => 'photo_journal_promo_contact_content',
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'photo_journal_is_promotion_contact_active',
			'label'             => esc_html__( 'Content', 'chique' ),
			'section'           => 'photo_journal_promotion_contact',
			'type'              => 'textarea',
		)
	);

	photo_journal_register_option( $wp_customize, array(
			'name'              => 'photo_journal_promo_contact_title_color',
			'default'           => '#fff',
			'sanitize_callback' => 'sanitize_hex_color',
			'active_callback'   => 'photo_journal_is_promotion_contact_active',
			'custom_control'    => 'WP_Customize_Color_Control',
			'label'             => esc_html__( 'Title Color', 'chique' ),
			'section'           => 'photo_journal_promotion_contact',
		)
	);

	photo_journal_register_option( $wp_customize, array(
			'name'              => 'photo_journal_promo_contact_content_color',
			'default'           => '#fff',
			'sanitize_callback' => 'sanitize_hex_color',
			'active_callback'   => 'photo_journal_is_promotion_contact_active',
			'custom_control'    => 'WP_Customize_Color_Control',
			'label'             => esc_html__( 'Content Color', 'chique' ),
			'section'           => 'photo_journal_promotion_contact',
		)
	);

	photo_journal_register_option( $wp_customize, array(
			'name'              => 'photo_journal_display_promotion_contact_title',
			'default'           => '1',
			'sanitize_callback' => 'photo_journal_sanitize_checkbox',
			'active_callback'   => 'photo_journal_is_promotion_contact_active',
			'label'             => esc_html__( 'Display title', 'chique' ),
			'section'           => 'photo_journal_promotion_contact',
			'custom_control'    => 'Photo_Journal_Toggle_Control',
		)
	);

	photo_journal_register_option( $wp_customize, array(
			'name'              => 'photo_journal_promo_contact_more_text',
			'sanitize_callback' => 'sanitize_text_field',
			'active_callback'   => 'photo_journal_is_promotion_contact_active',
			'label'             => esc_html__( 'More Button Text', 'chique' ),
			'section'           => 'photo_journal_promotion_contact',
		)
	);

	photo_journal_register_option( $wp_customize, array(
			'name'              => 'photo_journal_promo_contact_more_link',
			'sanitize_callback' => 'esc_url_raw',
			'active_callback'   => 'photo_journal_is_promotion_contact_active',
			'label'             => esc_html__( 'More Button Link', 'chique' ),
			'section'           => 'photo_journal_promotion_contact',
		)
	);

	photo_journal_register_option( $wp_customize, array(
			'name'              => 'photo_journal_promo_contact_more_target',
			'sanitize_callback' => 'photo_journal_sanitize_checkbox',
			'active_callback'   => 'photo_journal_is_promotion_contact_active',
			'label'             => esc_html__( 'Open Button Link in New Tab', 'chique' ),
			'section'           => 'photo_journal_promotion_contact',
			'custom_control'    => 'Photo_Journal_Toggle_Control',
		)
	);
}
add_action( 'customize_register', 'photo_journal_promo_contact_options' );

/** Active Callback Functions **/
if ( ! function_exists( 'photo_journal_is_promotion_contact_active' ) ) :
	/**
	* Return true if promotion contact is active
	*
	* @since 1.0
	*/
	function photo_journal_is_promotion_contact_active( $control ) {
		$enable = $control->manager->get_setting( 'photo_journal_promo_contact_visibility' )->value();

		return photo_journal_check_section( $enable );
	}
endif;
