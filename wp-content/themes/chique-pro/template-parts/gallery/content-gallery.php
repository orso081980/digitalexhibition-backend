<?php
/**
 * The template used for displaying gallery content
 *
 * @package Chique
 */

$enable_section = get_theme_mod( 'chique_gallery_visibility', 'disabled' );

if ( ! chique_check_section( $enable_section ) ) {
	// Bail if hero content is not enabled
	return;
}

// Set Content width to 1920px for Gallery Section only
$temp_content_width = $GLOBALS['content_width'];
$GLOBALS['content_width'] = 1920;

$type = get_theme_mod( 'chique_gallery_type', 'page' );

get_template_part( 'template-parts/gallery/post-type', 'gallery' );

// Set Content width back to theme's original content width
$GLOBALS['content_width'] = $temp_content_width;
