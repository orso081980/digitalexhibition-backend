<?php
/**
 * The template for displaying featured posts on the front page
 *
 * @package Chique
 */

$number        = get_theme_mod( 'chique_featured_content_number', 3 );
$post_list     = array();
$no_of_post    = 0;

$args = array(
	'post_type'           => 'post',
	'ignore_sticky_posts' => 1, // ignore sticky posts.
);

// Get valid number of posts.
$args['post_type'] = 'featured-content';

for ( $i = 1; $i <= $number; $i++ ) {
	$chique_post_id = '';

	$chique_post_id = get_theme_mod( 'chique_featured_content_cpt_' . $i );

	if ( $chique_post_id && '' !== $chique_post_id ) {
		$post_list = array_merge( $post_list, array( $chique_post_id ) );

		$no_of_post++;
	}
}

$args['post__in'] = $post_list;
$args['orderby']  = 'post__in';

if ( ! $no_of_post ) {
	return;
}

$args['posts_per_page'] = $no_of_post;

$loop = new WP_Query( $args );

while ( $loop->have_posts() ) :

	$loop->the_post();

	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="hentry-inner">
			<div class="post-thumbnail">
				<a href="<?php the_permalink(); ?>">
					<?php
					if ( has_post_thumbnail() ) {
						the_post_thumbnail('chique-featured');
					}
					else {
						$image = '<img src="' . trailingslashit( esc_url( get_template_directory_uri() ) ) . 'assets/images/no-thumb-666x499.jpg"/>';

						// Get the first image in page, returns false if there is no image.
						$first_image = chique_get_first_image( $post->ID, 'post-thumbnail', '' );

						// Set value of image as first image if there is an image present in the page.
						if ( $first_image ) {
							$image = $first_image;
						}

						echo $image;
					}
					?>
				</a>
			</div>

			<div class="entry-container">
				<header class="entry-header">
					<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">','</a></h2>' ); ?>
				</header>

				<?php
					$excerpt = get_the_excerpt();
					echo '<div class="entry-summary"><p>' . $excerpt . '</p></div><!-- .entry-summary -->';
				?>
			</div><!-- .entry-container -->
		</div> <!-- .hentry-inner -->
	</article>
<?php
endwhile;

wp_reset_postdata();



