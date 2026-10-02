<?php
/**
 * The template for displaying featured posts on the front page
 *
 * @package Chique
 */
?>
<?php
$quantity = get_theme_mod( 'chique_featured_video_number', 1);

for ( $i = 1; $i <= $quantity; $i++ ) {  ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="hentry-inner">
			<div class="entry-container">
				<?php
					$link = get_theme_mod( 'chique_featured_video_link_' . $i ) ? get_theme_mod( 'chique_featured_video_link_' . $i ) : '#';

					$embed_code = wp_oembed_get( esc_url( $link ) );

					echo '<div class="entry-content">
							' . $embed_code . '
						</div><!-- .entry-content -->';					
				?>
			</div><!-- .entry-container -->
		</div><!-- .hentry-inner -->
	</article>
<?php } // End for(). ?>
