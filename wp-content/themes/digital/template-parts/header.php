<?php
/**
 * The template for displaying header.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$site_name = get_bloginfo( 'name' );
$tagline   = get_bloginfo( 'description', 'display' );
$header_nav_menu = wp_nav_menu( [
	'theme_location' => 'menu-1',
	'fallback_cb' => false,
	'container' => false,
	'echo' => false,
] );
?>

<header id="masthead" class="site-header">
<div class="wrapper">	<div class="site-header-main">

<div class="site-branding">
	<?php if ( has_custom_logo() ) : ?>
		<?php the_custom_logo(); ?>
	<?php else : ?>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link" rel="home"><?php echo esc_html( $site_name ); ?></a>
	<?php endif; ?>
	</div>


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
						<span class="menu-label">Menu</span>
					</button>
				</div><!-- .menu-toggle-wrapper -->

			</div>



			<div class="menu-inside-wrapper">

<?php if ( has_nav_menu( 'menu-1' ) ) : ?>
				<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'chique-pro' ); ?>">
					<?php
						wp_nav_menu( array(
								'theme_location' => 'menu-1',
								'menu_id'        => 'primary-menu',
								'menu_class'     => 'menu nav-menu',
							)
						);
					?>
			<?php else : ?>

				<nav id="site-navigation" class="main-navigation default-page-menu" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'chique-pro' ); ?>" aria-expanded="false">
					<?php wp_page_menu(
						array(
							'menu_class' => 'primary-menu-container',
							'before'     => '<ul id="menu-primary-items" class="menu nav-menu">',
							'after'      => '</ul>',
						)
					); ?>

			<?php endif; ?>

				<div class="search-social-container">

				<div class="custom-search-wrapper">
				<div id="primary-search-wrapper-custom">
					<div class="custom-search-form-container">
						<?php
						$unique_id = uniqid( 'search-form-' );
						?>
						<form role="search" method="get" class="custom-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
							<label for="<?php echo $unique_id; ?>">
								<span class="screen-reader-text"><?php echo _x( 'Search for:', 'label', 'tuo-tema' ); ?></span>
								<input type="search" id="<?php echo $unique_id; ?>" class="custom-search-field-input" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'tuo-tema' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'tuo-tema' ); ?>">
							</label>

							<!-- TIPO BOTTONE MODIFICATO IN "button" -->
							<button type="button" class="custom-search-submit-button fa fa-search">
								<span class="screen-reader-text"><?php echo esc_html_x( 'Search', 'submit button', 'tuo-tema' ); ?></span>
							</button>
						</form>

					</div>
				</div></div>

				</div>

				</nav><!-- .main-navigation -->


			</div><!-- .menu-inside-wrapper -->
		</div><!-- #primary-menu-wrapper.menu-wrapper -->

	</div>





<footer>
<div class="box_platform">
	A platform from
	<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logo_tue.png " alt="Logo TUE" />
</div>


<div class="last_box">
	<span> &copy; <?php echo date('Y'); ?> TU/e </span> <a href="/privacy-policy">  Privacy Policy</a>
</div>

</footer>

</div></div>
</header>
