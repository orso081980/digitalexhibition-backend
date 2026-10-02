<?php
/**
 * The template for displaying stats content
 *
 * @package Chique
 */
?>

<?php

$enable_content = get_theme_mod( 'chique_stats_option', 'disabled' );

if ( ! chique_check_section( $enable_content ) ) {
	// Bail if stats content is disabled.
	return;
}

$type = get_theme_mod( 'chique_stats_type', 'category' );

if ( 'custom' !== $type ) {
	$stats_posts = chique_get_stats_posts();

	if ( empty( $stats_posts ) ) {
		return;
	}

}

$title     = get_theme_mod( 'chique_stats_archive_title', esc_html__( 'Stats', 'chique-pro' ) );
$sub_title = get_theme_mod( 'chique_stats_sub_title' );

$layout = get_theme_mod( 'chique_stats_layout', 'layout-four' );

$classes[] = 'stats-section';
$classes[] = 'section';

if ( ! $title && ! $sub_title ) {
	$classes[] = 'no-section-heading';
}

?>

<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="wrapper">

			<?php if ( $title || $sub_title ) : ?>
				<div class="section-heading-wrapper">
					<?php if ( $title ) : ?>
						<div class="section-title-wrapper">
							<h2 class="section-title"><?php echo wp_kses_post( $title ); ?></h2>
						</div><!-- .page-title-wrapper -->
					<?php endif; ?>

					<?php if ( $sub_title ) : ?>
						<div class="section-description">
							<?php echo wp_kses_post( $sub_title ); ?>
						</div><!-- .section-description -->
					<?php endif; ?>
				</div><!-- .section-heading-wrapper -->
			<?php endif; ?>

			<div class="section-content-wrapper <?php echo esc_attr( $layout ); ?>">

				<?php
				if ( 'custom' === $type ) {
					get_template_part( 'template-parts/stats/content', 'custom' );
				} else {
					get_template_part( 'template-parts/stats/post-types-stats' );
				}
				?>

				<?php
					$target = get_theme_mod( 'chique_stats_target' ) ? '_blank': '_self';
					$link   = get_theme_mod( 'chique_stats_link', '#' );
					$text   = get_theme_mod( 'chique_stats_text' );

					if ( $text ) :
				?>
				<p class="view-all-button">
					<span class="more-button"><a class="more-link" target="<?php echo $target; ?>" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $text ); ?></a></span>
				</p>
				<?php endif; ?>

			</div><!-- .section-content-wrapper -->
	</div><!-- .wrapper -->
</div><!-- #stats-section -->
