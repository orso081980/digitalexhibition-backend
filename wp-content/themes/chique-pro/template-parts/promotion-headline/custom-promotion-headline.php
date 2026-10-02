<?php
/**
 * The template used for displaying promotion_headline content
 *
 * @package Chique
 */
?>

<?php
$title = get_theme_mod( 'chique_promotion_headline_title' );
// Image added from Inline CSS

$content_align = get_theme_mod( 'chique_promotion_headline_align', 'content-aligned-right' );
$text_align    = get_theme_mod( 'chique_promotion_headline_text_align', 'text-aligned-center' );
$content_frame = get_theme_mod( 'chique_promotion_headline_content_frame', 0 );
$image = get_theme_mod( 'chique_promotion_headline_image' );

$classes[] = $content_align;
$classes[] = $text_align;
if( $content_frame ) {
	$classes[] = 'content-frame';
}
if( $image ) {
	$classes[] = 'has-background-image';
}

?>

<div id="promotion-headline" class="promotion-headline-wrapper section <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="wrapper">
		<div class="section-content-wrap">

			<article id="post-0" class="hentry">
				<?php
				$title = get_theme_mod( 'chique_promotion_headline_title' );
				$subtitle = get_theme_mod( 'chique_promotion_headline_subtitle' );
				?>
				<div class="entry-container">
				<?php if ( $title || $subtitle ) : ?>
					<header class="entry-header">
						<?php if ( $title ) : ?>
						<h2 class="section-title">
							<?php echo wp_kses_post( $title ); ?>
						</h2>
						<?php endif; ?>

						<?php if ( $subtitle ) : ?>
						<div class="section-description">
							<?php
							$subtitle = apply_filters( 'the_content', $subtitle );
							echo str_replace( ']]>', ']]&gt;', $subtitle );
							?>
						</div><!-- .section-description -->
						<?php endif; ?>
					</header><!-- .entry-header -->
				<?php endif; ?>

				<?php if ( $content = get_theme_mod( 'chique_promotion_headline' ) ) : ?>
					<div class="entry-content">
						<?php echo wp_kses_post( apply_filters( 'the_content', $content ) ); ?>

						<?php
						$more_text   = get_theme_mod( 'chique_promotion_headline_more_text' );
						$more_link   = get_theme_mod( 'chique_promotion_headline_more_link', '#' );
						$more_target = get_theme_mod( 'chique_promotion_headline_more_target' ) ? '_blank' : '_self' ;

						if ( $more_text ) : ?>
						<span class="more-button">
							<a class="more-link" href="<?php echo esc_url( $more_link ); ?>" target="<?php echo esc_attr( $more_target ); ?>"> <?php echo esc_html( $more_text ); ?> </a>
						</span>
						<?php endif; ?>
					</div><!-- .entry-content -->
				<?php endif; ?>
				</div><!-- .entry-container -->
			</article><!-- #post-## -->
		</div><!-- .section-content-wrap -->
	</div> <!-- Wrapper -->
</div> <!-- promotion_headline-wrapper -->

