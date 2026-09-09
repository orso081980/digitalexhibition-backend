<?php
/**
 * The template used for displaying promotion sale
 *
 * @package Chique
 */
?>

<?php
$enable_section = get_theme_mod( 'chique_promotion_sale_visibility', 'homepage' );

if ( ! chique_check_section( $enable_section ) ) {
	// Bail if promotion sale is not enabled
	return;
}

$type = get_theme_mod( 'chique_promotion_sale_type', 'page' );

if ( 'page' === $type || 'post' === $type || 'category' === $type ) :
	get_template_part( 'template-parts/promotion-sale/post-type', 'promotion-sale' );
else :
	get_template_part( 'template-parts/promotion-sale/custom', 'promotion-sale' );
endif;
