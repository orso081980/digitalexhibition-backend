<?php
/**
 * The template for displaying testimonial items
 *
 * @package Chique
 */
?>

<?php
$number = get_theme_mod( 'chique_testimonial_number', 5 );

if ( ! $number ) {
	// If number is 0, then this section is disabled
	return;
}

$disable_slider = get_theme_mod( 'chique_testimonial_slider', 1 );

$content_classes = 'section-content-wrapper testimonial-content-wrapper';

if ( $disable_slider ) {
	$content_classes .= ' testimonial-slider owl-carousel';
} else {
	$content_classes .= ' slider-disabled';
}
?>

<div class="<?php echo esc_attr( $content_classes ); ?>">
<?php
	$thumbnails = array();

	for ( $i = 1; $i <= $number; $i++ ) {
		$content  = get_theme_mod( 'chique_testimonial_content_' . $i );
		$target   = get_theme_mod( 'chique_testimonial_target_' . $i ) ? '_blank': '_self';
		$link     = get_theme_mod( 'chique_testimonial_link_' . $i, '#' );
		$title    = get_theme_mod( 'chique_testimonial_title_' . $i );
		$image    = $thumbnails[] = get_theme_mod( 'chique_testimonial_image_' . $i ) ? get_theme_mod( 'chique_testimonial_image_' . $i ) : trailingslashit( esc_url( get_template_directory_uri() ) ) . 'assets/images/no-thumb-100x100.jpg';
		$position = get_theme_mod( 'chique_testimonial_position_' . $i );

		if ( function_exists( 'qtrans_convertURL' ) ) {
			$link = qtrans_convertURL( $link );
		}

		?>

		<article id="post-<?php echo esc_attr( $i ) ?>" class="post hentry post-image has-post-thumbnail">
			<div class="hentry-inner">
				<div class="entry-container">
					<?php if ( $content ) : ?>
						<div class="entry-content">
							<?php echo wp_kses_post( apply_filters( 'the_content', $content ) ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $title || $position ) : ?>
						<header class="entry-header">
							<?php if ( $title ): ?>
							<h2 class="entry-title"><a target="<?php echo esc_attr( $target ); ?>" href="<?php echo esc_url( $link ); ?>"><?php echo wp_kses_post( $title ); ?></a></h2>
							<?php endif; ?>

							<?php if ( $position ): ?>
							<p class="entry-meta"><span class="position"><?php echo esc_html( $position ); ?></span></p>
							<?php endif; ?>
						</header>
					<?php endif; ?>
				</div><!-- .entry-container -->
			</div><!-- .hentry-inner -->
		</article>
		<?php
	}
?>
</div><!-- .section-content-wrapper -->

<ul id='testimonial-dots' class='owl-dots'>
	<?php
		foreach ( $thumbnails as $thumb ) {
			echo '<li class="owl-dot"><img src="' . esc_url( $thumb ) . '"/> </li> ';
		}
	?>
</ul>
