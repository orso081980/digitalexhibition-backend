<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Chique
 */

get_header();

$enable_homepage_posts = chique_enable_homepage_posts();
$enable = get_theme_mod( 'chique_header_media_option', 'entire-site' );

if ( $enable_homepage_posts ) : ?>
		<div id="primary" class="content-area">
			<main id="main" class="site-main">
				<div class="archive-content-wrap">
					<?php
					$post_title = get_theme_mod( 'chique_recent_posts_heading', esc_html__( 'Blog', 'chique-pro' ) );
					$post_subtitle = get_theme_mod( 'chique_recent_posts_subheading', esc_html__( 'Latest Updates', 'chique-pro' ) );

					if( ( is_home() && is_front_page() ) || ( 'homepage' === $enable && is_home() ) ) :
						if ( '' !== $post_title || '' !== $post_subtitle ) :
						?>
							<div class="section-heading-wrapper">
								<?php if ( '' !== $post_title ) : ?>
									<div class="section-title-wrapper">
										<h2 class="section-title"><?php echo esc_html( $post_title ); ?></h2>
									</div>
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
						<?php endif; ?>
					<?php endif; ?>

					<?php
					if ( have_posts() ) :

						if ( is_home() && ! is_front_page() ) : ?>
							<header>
								<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
							</header>

						<?php endif; ?>

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
									/* Start the Loop */
									while ( have_posts() ) : the_post();

										get_template_part( 'template-parts/content/content', get_post_format() );

									endwhile;
									?>
									<?php
								if ( $grid_style ) : ?>
								</div> <!-- grid -->
								<?php endif; ?>
						</div> <!-- .section-content-wrapper -->


						<?php
						chique_content_nav();

					else :

						get_template_part( 'template-parts/content/content', 'none' );

					endif; ?>
				</div> <!-- .archive-content-wrap -->
			</main><!-- #main -->
		</div><!-- #primary -->
	<?php get_sidebar(); ?>
<?php endif; // $enable_homepage_posts
get_footer();
