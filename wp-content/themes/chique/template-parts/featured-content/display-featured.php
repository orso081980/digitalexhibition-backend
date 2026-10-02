<?php
/**
 * The template for displaying featured content
 *
 * @package Chique
 */
?>

<?php
$enable_content = get_theme_mod( 'chique_featured_content_option', 'disabled' );

if ( ! chique_check_section( $enable_content ) ) {
	// Bail if featured content is disabled.
	return;
}

$featured_posts = chique_get_featured_posts();

if ( empty( $featured_posts ) ) {
	return;
}

$title    = get_option( 'featured_content_title', esc_html__( 'Contents', 'chique' ) );
$subtitle = get_option( 'featured_content_content' );

$classes[] = 'featured-content-section';
$classes[] = 'section';
$classes[] = 'text-aligned-center';

if ( ! $title && ! $subtitle ) {
	$classes[] = 'no-section-heading';
}

?>

<div id="featured-content" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="wrapper">
		<?php if ( '' !== $title || $subtitle ) : ?>
			<div class="section-heading-wrapper">
				<?php if ( '' !== $title ) : ?>
					<div class="section-title-wrapper">
						<h2 class="section-title"><?php echo wp_kses_post( $title ); ?></h2>
					</div><!-- .page-title-wrapper -->
				<?php endif; ?>

				<?php if ( $subtitle ) : ?>
					<div class="section-description">
						<?php
						$subtitle = apply_filters( 'the_content', $subtitle );
						echo wp_kses_post( str_replace( ']]>', ']]&gt;', $subtitle ) );
						?>
					</div><!-- .section-description -->
				<?php endif; ?>
			</div><!-- .section-heading-wrapper -->
		<?php endif; ?>

		<div class="section-content-wrapper layout-three">

			<?php
				// Include the featured content template.
				get_template_part( 'template-parts/featured-content/content', 'featured' );
			?>
		</div><!-- .featured-content-wrapper -->
	</div><!-- .wrapper -->
</div><!-- #featured-content-section -->
