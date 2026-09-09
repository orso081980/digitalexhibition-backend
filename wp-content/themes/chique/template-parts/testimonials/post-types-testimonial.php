<?php
/**
 * The template for displaying testimonial items
 *
 * @package Chique
 */

$number = get_theme_mod( 'chique_testimonial_number', 5 );

if ( ! $number ) {
	// If number is 0, then this section is disabled
	return;
}

$content_classes = 'section-content-wrapper testimonial-content-wrapper';

$content_classes .= ' testimonial-slider owl-carousel';
?>

<div class="<?php echo esc_attr( $content_classes ); ?>">
	<?php
		$loop = new WP_Query( chique_testimonial_posts_args() );

		$thumbnails = array();

		if ( $loop -> have_posts() ) :
			while ( $loop -> have_posts() ) :
				$loop -> the_post();

				if( has_post_thumbnail() ) {
					$thumbnails[] = get_the_post_thumbnail_url( null, 'chique-why-choose-us' );
				} else {
					$thumbnails[] = trailingslashit( esc_url( get_template_directory_uri() ) ) . 'assets/images/no-thumb-100x100.jpg';
				}


				get_template_part( 'template-parts/testimonials/content', 'testimonial' );
			endwhile;
			wp_reset_postdata();
		endif;
	?>
</div><!-- .section-content-wrapper -->

<ul id='testimonial-dots' class='owl-dots'>
	<?php
		foreach ( $thumbnails as $thumb ) {
			echo '<li class="owl-dot"><img src="' . esc_url( $thumb ) . '"/> </li> ';
		}
	?>
</ul>

<ul id='testimonial-nav' class='owl-nav'>
</ul>
