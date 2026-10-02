<?php
/**
 * The template used for displaying promotion_headline content
 *
 * @package Chique
 */
?>

<?php

$type = get_theme_mod( 'chique_promotion_headline_type', 'page' );

if ( 'page' === $type && $id = get_theme_mod( 'chique_promotion_headline_page' ) ) {
	$args['page_id'] = absint( $id );
} elseif ( 'post' === $type && $id = get_theme_mod( 'chique_promotion_headline_post' ) ) {
	$args['p'] = absint( $id );
} elseif ( 'category' === $type && $cat = get_theme_mod( 'chique_promotion_headline_category' ) ) {
	$args['cat']            = absint( $cat );
	$args['posts_per_page'] = 1;
}

// If $args is empty return false
if ( empty( $args ) ) {
	return;
}

// Create a new WP_Query using the argument previously created
$promotion_headline_query = new WP_Query( $args );
if ( $promotion_headline_query->have_posts() ) :
	while ( $promotion_headline_query->have_posts() ) :
		$promotion_headline_query->the_post();

		// Bg image added from function chique_promo_headline_bg_css()

		$content_align = get_theme_mod( 'chique_promotion_headline_align', 'content-aligned-right' );
		$text_align    = get_theme_mod( 'chique_promotion_headline_text_align', 'text-aligned-center' );
		$content_frame = get_theme_mod( 'chique_promotion_headline_content_frame', 0 );
		$classes[] = $content_align;
		$classes[] = $text_align;
		if( $content_frame ) {
			$classes[] = 'content-frame';
		}
		if ( has_post_thumbnail( $id ) ) {
			$classes[] = 'has-background-image'; 
		}
		?>
		<div id="promotion-headline" class="promotion-headline-wrapper section <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<div class="wrapper">
				<div class="section-content-wrap">
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<div class="entry-container">
							<?php
							$subtitle = get_theme_mod( 'chique_promotion_headline_subtitle' );

							if ( get_theme_mod( 'chique_display_promotion_headline_title', 1 ) || $subtitle ) : ?>
								<header class="entry-header">
									<?php if ( get_theme_mod( 'chique_display_promotion_headline_title', 1 ) ) : ?>
										<?php the_title( '<h2 class="section-title">', '</h2>' ); ?>
									<?php endif; ?>

									<?php if ( $subtitle ) : ?>
									<div class="section-description">
										<?php
										$subtitle = apply_filters( 'the_content', $subtitle );
										echo wp_kses_post( str_replace( ']]>', ']]&gt;', $subtitle ) );
										?>
									</div><!-- .section-description -->
									<?php endif; ?>
								</header><!-- .entry-header -->
							<?php endif; ?>

							<?php
								$show_content = get_theme_mod( 'chique_promotion_headline_show', 'full-content' );

								if ( 'full-content' === $show_content ) {
									echo '<div class="entry-content">';
									the_content();
									echo '</div>';
								} elseif ( 'excerpt' === $show_content ) {
									echo '<div class="entry-summary">';
									echo '<p>' . get_the_excerpt() . '</p>';
									echo '</div>';
								}
							?>

							<?php if ( get_edit_post_link() ) : ?>
								<footer class="entry-footer">
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
								</footer><!-- .entry-footer -->
							<?php endif; ?>
						</div><!-- .entry-container -->
					</article><!-- #post-## -->
				</div><!-- .section-content-wrap -->
			</div> <!-- Wrapper -->
		</div> <!-- promotion_headline-wrapper -->
	<?php
	endwhile;

	wp_reset_postdata();
endif;
