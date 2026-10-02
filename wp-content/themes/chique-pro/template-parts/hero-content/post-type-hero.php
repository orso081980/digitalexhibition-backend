<?php
/**
 * The template used for displaying hero content
 *
 * @package Chique
 */
?>

<?php

$type = get_theme_mod( 'chique_hero_content_type', 'page' );

if ( 'page' === $type && $id = get_theme_mod( 'chique_hero_content' ) ) {
	$args['page_id'] = absint( $id );
} elseif ( 'post' === $type && $id = get_theme_mod( 'chique_hero_content_post' ) ) {
	$args['p'] = absint( $id );
} elseif ( 'category' === $type && $cat = get_theme_mod( 'chique_hero_content_category' ) ) {
	$args['cat']            = absint( $cat );
	$args['posts_per_page'] = 1;
}

// If $args is empty return false
if ( empty( $args ) ) {
	return;
}

// Create a new WP_Query using the argument previously created
$hero_query = new WP_Query( $args );
if ( $hero_query->have_posts() ) :
	while ( $hero_query->have_posts() ) :
		$hero_query->the_post();
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
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="featured-content-image" style="background-image: url( <?php the_post_thumbnail_url( 'chique-hero-content' ); ?> );">
								<a class="cover-link" href="<?php the_permalink(); ?>"></a>
							</div>
							<div class="entry-container">
						<?php else : ?>
							<div class="entry-container full-width">
						<?php endif; ?>

							<?php
								$title = '';

								if ( get_theme_mod( 'chique_display_hero_content_title', 1 ) ) {
									$title = get_the_title();
								}

								$subtitle = get_theme_mod( 'chique_hero_content_subtitle' );
							?>

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

							<div class="entry-content">
								<?php
									$show_content = get_theme_mod( 'chique_hero_content_show', 'full-content' );

									if ( 'full-content' === $show_content ) {
										the_content();
									} elseif ( 'excerpt' === $show_content ) {
										echo '<p>' . get_the_excerpt() . '</p>';
									}

									wp_link_pages( array(
										'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'chique-pro' ) . '</span>',
										'after'       => '</div>',
										'link_before' => '<span class="page-number">',
										'link_after'  => '</span>',
										'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'chique-pro' ) . ' </span>%',
										'separator'   => '<span class="screen-reader-text">, </span>',
									) );
								?>
							</div><!-- .entry-content -->

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
		</div> <!-- hero-content-wrapper -->
	<?php
	endwhile;

	wp_reset_postdata();
endif;
