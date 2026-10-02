<?php
/**
 * The template for displaying featured content
 *
 * @package Chique
 */
?>

<?php
$enable_content = get_theme_mod( 'chique_countdown_option', 'disabled' );

if ( ! chique_check_section( $enable_content ) ) {
	// Bail if featured content is disabled.
	return;
}

$title            = get_theme_mod( 'chique_countdown_title', esc_html__( 'Countdown', 'chique-pro' ) );
$sub_title        = get_theme_mod( 'chique_countdown_sub_title' );
?>

<div id="countdown-section" class="section countdown">
	<div class="wrapper">
		<?php if ( '' !== $title || $sub_title ) : ?>
			<div class="section-heading-wrapper">
				<?php if ( '' !== $title ) : ?>
					<div class="section-title-wrapper">
						<h2 class="section-title"><?php echo wp_kses_post( $title ); ?></h2>
					</div><!-- .page-title-wrapper -->
				<?php endif; ?>

				<?php if ( $sub_title ) : ?>
					<div class="section-description">
						<?php
						$sub_title = apply_filters( 'the_content', $sub_title );
						echo wp_kses_post( str_replace( ']]>', ']]&gt;', $sub_title ) );
						?>
					</div><!-- .section-description -->
				<?php endif; ?>
			</div><!-- .section-heading-wrapper -->
		<?php endif; ?>

		<div class="section-content-wrapper">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="hentry-inner">
					<div class="entry-container">
						<div class="entry-content">
							<div id="clock"></div>
						</div><!-- .entry-content -->
					</div><!-- .entry-container -->
				</div><!-- .hentry-inner -->
			</article><!-- #post-## -->
		</div><!-- .section-content-wrapper -->
	</div><!-- .wrapper -->
</div><!-- .section -->
