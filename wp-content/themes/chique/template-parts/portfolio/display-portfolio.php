<?php
/**
 * The template for displaying portfolio items
 *
 * @package Chique
 */
?>

<?php
$enable = get_theme_mod( 'chique_portfolio_option', 'disabled' );

if ( ! chique_check_section( $enable ) ) {
	// Bail if portfolio section is disabled.
	return;
}

$headline   = get_option( 'jetpack_portfolio_title', esc_html__( 'Portfolio', 'chique' ) );
$subheadline = get_option( 'jetpack_portfolio_content' );

$classes = array();

if ( ! $headline && ! $subheadline ) {
	$classes[] = 'no-section-heading';
}

?>

<div id="portfolio-content-section" class="portfolio-section section layout-three<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="wrapper">
		<?php if ( $headline || $subheadline ) : ?>
			<div class="section-heading-wrapper">
			<?php if ( $headline ) : ?>
				<div class="section-title-wrapper">
					<h2 class="section-title"><?php echo wp_kses_post( $headline ); ?></h2>
				</div><!-- .section-title-wrapper -->
			<?php endif; ?>

			<?php if ( $subheadline ) : ?>
				<div class="section-description">
					<?php
	                $subheadline = apply_filters( 'the_content', $subheadline );
	                echo str_replace( ']]>', ']]&gt;', $subheadline );
	                ?>
				</div><!-- .section-description -->
			<?php endif; ?>
			</div><!-- .section-heading-wrapper -->
		<?php endif; ?>

		<div class="section-content-wrapper portfolio-content-wrapper layout-three">
			<div class="grid">
				<?php
					get_template_part( 'template-parts/portfolio/post-types', 'portfolio' );
				?>
			</div>
		</div><!-- .portfolio-wrapper -->
	</div><!-- .wrapper -->
</div><!-- #portfolio-content-section -->
