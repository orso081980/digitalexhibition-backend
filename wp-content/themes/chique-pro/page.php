<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Chique
 */

get_header();

$enable_homepage_posts = chique_enable_homepage_posts();
if ( $enable_homepage_posts ) : ?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main">
			<div class="singular-content-wrap">
				<?php
				while ( have_posts() ) : the_post();

					get_template_part( 'template-parts/content/content', 'page' );

					get_template_part( 'template-parts/content/content', 'comment' );

				endwhile; // End of the loop.
				?>
				</div> <!-- singular-content-wrap -->
		</main><!-- #main -->
	</div><!-- #primary -->

	<?php
	get_sidebar();
endif; // $enable_homepage_posts
get_footer();
