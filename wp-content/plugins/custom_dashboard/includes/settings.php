<?php
/**
 * custom_dashboard/includes/settings.php
 * Impostazioni via Customizer per il plugin.
 */

// [2026-09-03] Customizer: ID post da escludere dalla pagina projects (senza toccarli altrove nel sito).
add_action( 'customize_register', function ( $wp_customize ) {
    $wp_customize->add_section( 'cd_projects_visibility', array(
        'title'    => 'Projects - Post nascosti',
        'priority' => 160,
    ) );

    $wp_customize->add_setting( 'cd_hidden_project_ids', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( 'cd_hidden_project_ids', array(
        'label'       => 'ID post da nascondere dalla pagina Projects',
        'description' => 'ID separati da virgola, es: 123,456. Il post resta pubblicato e raggiungibile via URL/ricerca, sparisce solo dai listati projects.',
        'section'     => 'cd_projects_visibility',
        'type'        => 'text',
    ) );
} );

function cd_get_hidden_project_ids() {
    $raw = get_theme_mod( 'cd_hidden_project_ids', '' );
    $ids = array_map( 'intval', array_filter( array_map( 'trim', explode( ',', $raw ) ) ) );
    return array_values( array_filter( $ids ) );
}
