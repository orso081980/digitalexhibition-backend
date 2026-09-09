<?php
/**
 * Template part for displaying Recent Posts in the front page template
 *
 * @package Chique
 */

if ( ! ( 'page' == get_option( 'show_on_front' ) && is_front_page() && get_theme_mod( 'chique_enable_static_page_posts' ) ) )  {
	return;
}

$grid_style = get_theme_mod( 'chique_blog_style' );
$recent_post_heading = get_theme_mod( 'chique_recent_posts_heading', esc_html__( 'Blog', 'chique-pro' ) );
$recent_post_sub_heading = get_theme_mod( 'chique_recent_posts_subheading', esc_html__( 'Latest Updates', 'chique-pro' ) );

$class[] = '';

if( $grid_style && ! $recent_post_heading && ! $recent_post_sub_heading ) {
	$class[] = 'no-section-heading';
}
?>

<div class="recent-blog-content-wrapper section<?php echo esc_attr( implode( ' ', $class ) ); ?>">
	<div class="wrapper">
		<div class="recent-blog-container">
			<div class="archive-content-wrap">
			<?php
			$post_title = get_theme_mod( 'chique_recent_posts_heading', esc_html__( 'Blog', 'chique-pro' ) );
			$post_subtitle = get_theme_mod( 'chique_recent_posts_subheading', esc_html__( 'Latest Updates', 'chique-pro' ) );

			if ( '' !== $post_title || '' !== $post_subtitle ) :
			?>
				<div class="section-heading-wrap">
					<?php if ( '' !== $post_title ) : ?>
						<div class="section-title-wrapper">
							<h2 class="section-title"><?php echo esc_html( $post_title ); ?></h2>
						</div> <!-- .section-title-wrapper -->
					<?php endif; ?>

					<?php if ( '' !== $post_subtitle ) : ?>
						<div class="section-description">
							<?php
			                $post_subtitle = apply_filters( 'the_content', $post_subtitle );
			                echo str_replace( ']]>', ']]&gt;', $post_subtitle );
			                ?>
						</div><!-- .section-description -->
					<?php endif; ?>


				</div><!-- .section-heading-wrap -->
			<?php
			endif;
			?>
			<?php
			$grid_style = get_theme_mod( 'chique_blog_style', 0 );

			if ( $grid_style ) {
				$classes[] = 'layout-three';
			}

			$classes[] = 'section-content-wrapper';

			?>

			<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
				<?php

					if ( $grid_style ) : ?>

					<div class="grid">

					<?php endif; ?>


				<?php
				$recent_posts = new WP_Query( array(
					'ignore_sticky_posts' => true,
				) );

				/* Start the Loop */
				while ( $recent_posts->have_posts() ) :
					$recent_posts->the_post();

					if ( $grid_style ) : ?>
						<article id="post-<?php the_ID(); ?>" <?php post_class('grid-item'); ?>>
					<?php else : ?>
						<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php endif; ?>
						<div class="hentry-inner">

							<?php if ( has_post_thumbnail() ) : ?>
								<div class="post-thumbnail">
									<a href="<?php the_permalink(); ?>" rel="bookmark">
										<?php
										$thumbnail = 'post-thumbnail';

										if ( $grid_style ) {
											$thumbnail = 'chique-grid-blog';
										}

										$layout  = chique_get_theme_layout();

										if ( 'no-sidebar-full-width' === $layout &&  ! $grid_style ) {
											$thumbnail = 'chique-slider';
										}

										the_post_thumbnail( $thumbnail );
										?>
									</a>
								</div>
							<?php endif; ?>

							<div class="entry-container">
								<?php if ( is_sticky() ) { ?>
									<span class="sticky-label"><?php esc_html_e( 'Featured', 'chique-pro' ); ?></span>
								<?php } ?>
								<header class="entry-header">
									<?php
									$show_content = get_theme_mod( 'chique_archive_content_show', 'excerpt' );
									$show_meta    = get_theme_mod( 'chique_archive_meta_show', 'show-meta' );

									the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
									<div class="entry-meta">
										<?php chique_blog_entry_meta(); ?>
									</div><!-- .entry_category -->
								</header><!-- .entry-header -->

								<?php
									if ( 'excerpt' === $show_content ) {
										echo '<div class="entry-summary"><p>' . get_the_excerpt() . '</p></div><!-- .entry-summary -->';
									} elseif ( 'full-content' === $show_content ) {
										$content = apply_filters( 'the_content', get_the_content() );
										$content = str_replace( ']]>', ']]&gt;', $content );
										echo '<div class="entry-content"' . wp_kses_post( $content ) . '</div><!-- .entry-content -->';
									}?>
							</div> <!-- .entry-container -->
						</div><!-- .hentry-inner-->
					</article><!-- #post -->
					<?php
				endwhile;

				wp_reset_postdata();
				?>
			</div><!-- .section-content-wrap -->
			<p class="view-all-button"><span class="more-button more-recent-posts">
					<a class="more-link" href="<?php the_permalink( get_option( 'page_for_posts' ) ); ?>"><?php esc_html_e( 'More Posts', 'chique-pro' ); ?></a>
				<span>
			</p>

			<?php if ( $grid_style ) : ?>
				</div> <!-- grid -->
			<?php endif; ?>
			</div> <!-- .archive-content-wrap -->
		</div> <!-- .recent-blog-container -->
	</div> <!-- .wrapper -->
</div> <!-- .recent-blog-content-wrapper -->
