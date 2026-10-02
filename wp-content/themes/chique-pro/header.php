<?php
/**
* The header for our theme
*
* This is the template that displays all of the <head> section and everything up until <div id="content">
*
* @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
*
* @package Chique
*/

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php do_action( 'wp_body_open' ); ?>

	<div id="page" class="site">
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'chique-pro' ); ?></a>

		<header id="masthead" class="site-header">
			<div class="wrapper">
				<div class="site-header-main">
					<?php get_template_part( 'template-parts/header/site', 'branding' ); ?>

					<?php get_template_part( 'template-parts/header/site', 'navigation' ); ?>


				</div> <!-- .site-header-main -->

			</div> <!-- .wrapper -->
		</header><!-- #masthead -->

		<div class="below-site-header">

			<?php

				if( function_exists( 'chique_header_cart' ) ) {
					chique_header_cart();
				}
			?>

			<div class="site-overlay"><span class="screen-reader-text"><?php esc_html_e( 'Site Overlay', 'chique-pro' ); ?></span></div>

			<?php chique_sections(); ?>

			<?php
			$enable_homepage_posts = chique_enable_homepage_posts();

			if ( $enable_homepage_posts ) :

			$recent_post_heading = get_theme_mod( 'chique_recent_posts_heading', esc_html__( 'Blog', 'chique-pro' ) );
			$recent_post_sub_heading = get_theme_mod( 'chique_recent_posts_subheading', esc_html__( 'Latest Updates', 'chique-pro' ) );

			$classes[] = '';

			if( ! $recent_post_heading && ! $recent_post_sub_heading ) {
				$classes[] = 'no-section-heading';
			}
			?>
			<div id="content" class="site-content<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
				<div class="wrapper">
			<?php endif; ?>
