<?php
/**
 * The template used for displaying hero content
 *
 * @package Chique
 */
?>
<?php
$content_pos = get_theme_mod( 'chique_hero_content_position', 'content-aligned-left' );
$layout      = get_theme_mod( 'chique_hero_content_layout', 'fluid' );
$text_align  = get_theme_mod( 'chique_hero_text_align', 'text-aligned-right' );

$background = get_theme_mod( 'chique_hero_content_bg_image' );

$classes[] = 'hero-content-wrapper';
$classes[] = 'section';
$classes[] = $content_pos;
$classes[] = $layout;
$classes[] = $text_align;

if ( $background ) {
	$classes[] = 'has-background-image';
}

if ( 'fluid' === $layout && get_theme_mod( 'chique_hero_content_frame' ) ) {
	$classes[] = 'has-content-frame';
}

?>
<div id="hero-content" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="wrapper">
		<div class="section-content-wrap">

			<article id="post-0" class="hentry hero-image-content">
				<?php
				$title    = get_theme_mod( 'chique_hero_content_title' );
				$subtitle = get_theme_mod( 'chique_hero_content_subtitle' );
				$image    = get_theme_mod( 'chique_hero_content_image' );
				if ( $image ) :
					$link = get_theme_mod( 'chique_hero_content_link' );
					$target = get_theme_mod( 'chique_hero_content_target' ) ? '_blank' : '_self';
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

				<?php if ( $title || $subtitle ) : ?>
					<header class="entry-header">
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

				<?php if ( $content = get_theme_mod( 'chique_hero_content_content' ) ) : ?>
					<div class="entry-content">
						<?php echo wp_kses_post( apply_filters( 'the_content', $content ) ); ?>

						<?php
						$more_text   = get_theme_mod( 'chique_hero_content_more_text' );
						$more_link   = get_theme_mod( 'chique_hero_content_more_link', '#' );
						$more_target = get_theme_mod( 'chique_hero_content_more_target' ) ? '_blank' : '_self' ;


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
</div> <!-- hero-content-wrapper -->

