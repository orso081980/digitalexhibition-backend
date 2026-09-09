<?php
/**
 * The template used for displaying playlist
 *
 * @package Chique
 */
?>

<?php
$enable_section = get_theme_mod( 'chique_playlist_visibility', 'disabled' );

if ( ! chique_check_section( $enable_section ) ) {
	// Bail if playlist is not enabled
	return;
}

get_template_part( 'template-parts/playlist/post-type', 'playlist' );
