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

// [2026-09-27] Customizer: Admin UI Image, replaces the site icon shown next
// to the site name in the wp-admin toolbar (top-left of the admin bar).
add_action( 'customize_register', function ( $wp_customize ) {
    $wp_customize->add_section( 'cd_admin_ui', array(
        'title'    => 'Admin UI',
        'priority' => 161,
    ) );

    $wp_customize->add_setting( 'cd_admin_ui_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ) );

    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'cd_admin_ui_image', array(
        'label'       => 'Admin UI Image',
        'description' => 'Shown instead of the Site Icon next to the site name in the wp-admin toolbar (top bar).',
        'section'     => 'cd_admin_ui',
    ) ) );
} );

// Swap the toolbar's site icon for the custom Admin UI Image, if one is set.
// Runs after wp_admin_bar_site_menu() (priority 30) so the "site-name" node
// already exists — we just rewrite its icon, we don't touch WP core.
add_action( 'admin_bar_menu', function ( $wp_admin_bar ) {
    $image_url = get_theme_mod( 'cd_admin_ui_image', '' );
    if ( empty( $image_url ) ) {
        return;
    }

    $node = $wp_admin_bar->get_node( 'site-name' );
    if ( ! $node ) {
        return;
    }

    $title = preg_replace( '#^<img[^>]*class="site-icon"[^>]*/?>#', '', $node->title );
    $icon  = sprintf(
        '<img class="site-icon" src="%s" alt="" width="20" height="20" />',
        esc_url( $image_url )
    );

    $node          = (array) $node;
    $node['title'] = $icon . $title;
    $node['meta']['class'] = trim( ( $node['meta']['class'] ?? '' ) . ' has-site-icon' );

    $wp_admin_bar->add_node( $node );
}, 31 );
