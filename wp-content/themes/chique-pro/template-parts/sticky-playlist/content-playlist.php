<?php
/**
 * The template used for displaying playlist
 *
 * @package Chique
 */
?>

<?php
$enable_section = get_theme_mod( 'chique_sticky_playlist_visibility', 'disabled' );

if ( ! chique_check_section( $enable_section ) ) {
	// Bail if playlist is not enabled
	return;
}

get_template_part( 'template-parts/sticky-playlist/post-type', 'playlist' );
