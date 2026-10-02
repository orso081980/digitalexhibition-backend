<?php
/**
 * The template used for displaying promotion_headline content
 *
 * @package Chique
 */
?>

<?php
$enable_section = get_theme_mod( 'chique_promotion_headline_visibility', 'homepage' );

if ( ! chique_check_section( $enable_section ) ) {
	// Bail if promotion_headline content is not enabled
	return;
}

$type = get_theme_mod( 'chique_promotion_headline_type', 'page' );

if ( 'page' === $type || 'post' === $type || 'category' === $type ) :
	get_template_part( 'template-parts/promotion-headline/post-type', 'promotion-headline' );
else :
	get_template_part( 'template-parts/promotion-headline/custom', 'promotion-headline' );
endif;
