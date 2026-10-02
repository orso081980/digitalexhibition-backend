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


				$type = get_theme_mod( 'chique_testimonial_type', 'category' ); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="hentry-inner">

						<?php
						$show_content = get_theme_mod( 'chique_testimonial_show', 'excerpt' ); ?>
						<div class="entry-container">
							<?php if ( 'excerpt' === $show_content  ) : ?>
								<div class="entry-content">
									<?php the_excerpt(); ?>
								</div>
							<?php elseif ( 'full-content' === $show_content ) : ?>
								<div class="entry-content">
									<?php the_content(); ?>
								</div>
							<?php endif; ?>

							<?php 
							if ( 'jetpack-testimonial' == $type ) {
								$position = get_post_meta( get_the_id(), 'ect_testimonial_position', true ); 			
							} else {
								$position = get_theme_mod('chique_testimonial_position_' . ( absint ( $loop->current_post ) + 1 ) ); 
							} 

							if ( get_theme_mod( 'chique_testimonial_enable_title', 1 ) || $position ) : ?>
									<header class="entry-header">
										<?php 
											if ( get_theme_mod( 'chique_testimonial_enable_title', 1 ) ) { ?>
												<h2 class="entry-title"><a href=<?php the_permalink(); ?>><?php the_title(); ?></a></h2> <?php
											}
										?>
										<?php if ( $position ) : ?>
											<p class="entry-meta"><span class="position">
												<?php echo esc_html( $position ); ?></span>
											</p>
										<?php endif; ?>
									</header>
							<?php endif;?>
						</div><!-- .entry-container -->	
					</div><!-- .hentry-inner -->
				</article> 
			<?php endwhile;
			wp_reset_postdata();
		endif;
	?>
</div><!-- .section-content-wrapper -->

<?php if( get_theme_mod( 'chique_testimonial_dots', 1) ) : ?>
	<ul id='testimonial-dots' class='owl-dots'>
		<?php
			foreach ( $thumbnails as $thumb ) {
				echo '<li class="owl-dot"><img src="' . esc_url( $thumb ) . '"/> </li> ';
			}
		?>
	</ul>
<?php endif; ?>

<ul id='testimonial-nav' class='owl-nav'>
</ul>
