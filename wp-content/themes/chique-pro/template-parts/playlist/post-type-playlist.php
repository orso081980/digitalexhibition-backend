<?php
/**
 * The template used for displaying playlist
 *
 * @package Chique
 */
?>

<?php

$type = get_theme_mod( 'chique_playlist_type', 'page' );

if ( 'page' === $type && $id = get_theme_mod( 'chique_playlist' ) ) {
	$args['page_id'] = absint( $id );
} elseif ( 'post' === $type && $id = get_theme_mod( 'chique_playlist_post' ) ) {
	$args['p'] = absint( $id );
} elseif ( 'category' === $type && $cat = get_theme_mod( 'chique_playlist_category' ) ) {
	$args['cat']            = absint( $cat );
	$args['posts_per_page'] = 1;
}

$img_alig      = get_theme_mod( 'chique_playlist_image_alignment', 'content-align-right' );
$section_sub_title = get_theme_mod( 'chique_playlist_section_sub_title' );

// If $args is empty return false
if ( empty( $args ) ) {
	return;
}

// Create a new WP_Query using the argument previously created
$playlist_query = new WP_Query( $args );
if ( $playlist_query->have_posts() ) :
	while ( $playlist_query->have_posts() ) :
		$playlist_query->the_post();

		$thumb = get_the_post_thumbnail_url( get_the_ID(), 'post-thumbnail' );
		?>
		<div id="playlist-section" class="section <?php echo esc_attr( $img_alig ); ?>">
			<div class="wrapper">

				<div class="section-content-wrapper playlist-content-wrapper layout-one <?php echo esc_attr( $img_alig ); ?>">
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<div class="hentry-inner">
							<?php $post_thumbnail = chique_post_thumbnail( 'chique-gallery', 'html-with-bg', false ); // chique_post_thumbnail( $image_size, $type = 'html', $echo = true, $no_thumb = false ).

							if ( $post_thumbnail ) :
								echo $post_thumbnail;
								?>
								<div class="entry-container">
							<?php else : ?>
								<div class="entry-container full-width">
							<?php endif; ?>
								<?php if ( get_theme_mod( 'chique_disable_playlist_title', 1 ) ) : ?>
								<header class="entry-header">
									<h2 class="entry-title">
										<?php the_title(); ?>
										<span> <?php echo esc_html( $section_sub_title ); ?> </span>
									</h2>
								</header><!-- .entry-header -->
								<?php endif; ?>

								<div class="entry-content">
									<?php the_content(); ?>
								</div><!-- .entry-content -->

								<?php if ( get_edit_post_link() ) : ?>
									<footer class="entry-footer">
										<div class="entry-meta">
											<?php
												edit_post_link(
													sprintf(
														/* translators: %s: Name of current post */
														esc_html__( 'Edit %s', 'chique-pro' ),
														the_title( '<span class="screen-reader-text">"', '"</span>', false )
													),
													'<span class="edit-link">',
													'</span>'
												);
											?>
										</div>
									</footer><!-- .entry-footer -->
								<?php endif; ?>

								<?php 
								$target = get_theme_mod( 'chique_playlist_button_link_target' ) ? '_blank': '_self';
								$link   = get_theme_mod( 'chique_playlist_button_link', '#' );
								$text   = get_theme_mod( 'chique_playlist_button_text', 'View Album' );

								if ( $text ) :
								?>

								<p class="view-all-button">
									<span class="more-button">
										<a class="more-link" target="<?php echo $target; ?>" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $text ); ?></a>
									</span>
								</p>
								<?php endif; ?>
								
							</div><!-- .entry-container -->
						</div><!-- .hentry-inner -->
					</article><!-- #post-## -->
				</div><!-- .wrapper -->
			</div><!-- .section-content -->
		</div><!-- #playlist-section -->
	<?php
	endwhile;

	wp_reset_postdata();
endif;
