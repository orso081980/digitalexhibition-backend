<?php
/**
 * The template used for displaying promotion sale
 *
 * @package Chique
 */
?>
<?php
$content_pos = get_theme_mod( 'chique_promotion_sale_position', 'content-aligned-right' );
$text_align  = get_theme_mod( 'chique_promotion_sale_text_align', 'text-aligned-left' );

$classes[] = 'promotion-sale-wrapper';
$classes[] = 'section';
$classes[] = $content_pos;
$classes[] = $text_align;

?>
<div id="promotion-sale" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="wrapper">
		<div class="section-content-wrap">

			<article id="post-0" class="hentry">
				<?php
				$title       = get_theme_mod( 'chique_promotion_sale_title' );
				$title_image = get_theme_mod( 'chique_promotion_sale_title_image' );
				$subtitle    = get_theme_mod( 'chique_promotion_sale_subtitle' );
				$image    = get_theme_mod( 'chique_promotion_sale_image' );
				if ( $image ) :
					$link = get_theme_mod( 'chique_promotion_sale_link' );
					$target = get_theme_mod( 'chique_promotion_sale_target' ) ? '_blank' : '_self';
					?>
					<div class="featured-content-image" style="background-image: url( <?php echo $image; ?> );">
						<?php if( $link ) : ?>
						<a class="cover-link" href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( $target ); ?>"></a>
						<?php endif; ?>
					</div>
					<div class="entry-container">
				<?php
				else: ?>
				<div class="entry-container full-width">
				<?php endif; ?>

				<?php if ( $title_image || $title || $subtitle ) : ?>
					<header class="entry-header">
						<?php if ( $title_image ) : ?>
							<div class="entry-image"><img src="<?php echo esc_url( $title_image ); ?>" /></div>
						<?php endif; ?>
						<h2 class="entry-title ">
							<?php if ( $title ) : ?>
								<?php echo esc_html( $title ); ?>
							<?php endif; ?>

							<?php if ( $subtitle ) : ?>
								<span><?php echo esc_html( $subtitle ); ?></span>
							<?php endif; ?>
						</h2>
					</header><!-- .entry-header -->
				<?php endif; ?>

				<?php if ( $content = get_theme_mod( 'chique_promotion_sale_content' ) ) : ?>
					<div class="entry-content">
						<?php echo wp_kses_post( apply_filters( 'the_content', $content ) ); ?>

						<?php
						$more_text   = get_theme_mod( 'chique_promotion_sale_more_text' );
						$more_link   = get_theme_mod( 'chique_promotion_sale_more_link', '#' );
						$more_target = get_theme_mod( 'chique_promotion_sale_more_target' ) ? '_blank' : '_self' ;


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
</div> <!-- promotion-sale-wrapper -->

