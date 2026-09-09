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

$type = get_theme_mod( 'chique_portfolio_type', 'category' );

if ( 'jetpack-portfolio' === $type ) {
	$headline   = get_option( 'jetpack_portfolio_title', esc_html__( 'Portfolio', 'chique-pro' ) );
	$subheadline = get_option( 'jetpack_portfolio_content' );
} else {
	$headline = get_theme_mod( 'chique_portfolio_headline', esc_html__( 'Portfolio', 'chique-pro' ) );

	$subheadline = get_theme_mod( 'chique_portfolio_subheadline' );
}

$classes[] = 'section';

$classes[] = get_theme_mod( 'chique_portfolio_content_layout', 'layout-three' );

if ( ! $headline && ! $subheadline ) {
	$classes[] = 'no-section-heading';
}

?>

<div id="portfolio-content-section" class="portfolio-section <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
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

		<div class="section-content-wrapper portfolio-content-wrapper <?php echo esc_attr( get_theme_mod( 'chique_portfolio_content_layout', 'layout-three' ) ); ?>">
			<div class="grid">
				<?php
				if( 'post' === $type || 'jetpack-portfolio' === $type || 'page' === $type || 'category' === $type  ) {
					get_template_part( 'template-parts/portfolio/post-types', 'portfolio' );
				} elseif ( 'custom' === $type ) {
					get_template_part( 'template-parts/portfolio/custom', 'portfolio' );
				}
				?>
			</div>
		</div><!-- .portfolio-wrapper -->
	</div><!-- .wrapper -->
</div><!-- #portfolio-content-section -->
