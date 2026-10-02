<?php
/**
 * Primary Menu Template
 *
 * @package Chique
 */

$menu_label = get_theme_mod( 'chique_primary_modern_menu_label', esc_html__( 'Menu', 'chique-pro' ) );
?>
<div id="site-header-menu" class="site-header-menu">
	<div id="primary-menu-wrapper" class="menu-wrapper">

		<div class="header-overlay"></div>

		<div class="menu-cart-wrap">
			<div class="menu-toggle-wrapper">
				<button id="menu-toggle" class="menu-toggle" aria-controls="top-menu" aria-expanded="false">
					<div class="menu-bars">
						<div class="bars bar1"></div>
		  				<div class="bars bar2"></div>
		  				<div class="bars bar3"></div>
	  				</div>
					<span class="menu-label"><?php echo esc_html( $menu_label ); ?></span>
				</button>
			</div><!-- .menu-toggle-wrapper -->

			<?php
				if( function_exists( 'chique_header_cart' ) ) {
					chique_header_cart();
				}
			?>
		</div> <!-- .menu-cart-wrap -->


		<div class="menu-inside-wrapper">

			<?php get_template_part( 'template-parts/header/header', 'navigation' ); ?>

			<div class="mobile-social-search">
				<?php if ( get_theme_mod( 'chique_display_primary_search', 1 ) ) : ?>
				<div class="search-container">
					<?php get_search_form(); ?>
				</div>
				<?php endif; ?>


			<nav class="social-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Social Links Menu', 'chique-pro' ); ?>">
				<?php
					wp_nav_menu( array(
						'theme_location' => 'social',
						'container'       => 'div',
						'container_class' => 'menu-social-container',
						'link_before'    => '<span class="screen-reader-text">',
						'link_after'     => '</span>',
						'depth'          => 1,
					) );
				?>
			</nav><!-- .social-navigation -->


			</div><!-- .mobile-social-search -->
		</div><!-- .menu-inside-wrapper -->
	</div><!-- #primary-menu-wrapper.menu-wrapper -->

</div><!-- .site-header-menu -->

<div class="search-social-container">
	<?php if ( get_theme_mod( 'chique_display_primary_search', 1 ) ) : ?>
	<div id="primary-search-wrapper">
			<div class="search-container">
				<?php get_Search_form(); ?>
			</div>
	</div><!-- #primary-search-wrapper -->
	<?php endif; ?>

<?php get_template_part( 'template-parts/header/social', 'header' ); ?>
</div> <!-- .search-social-container -->
