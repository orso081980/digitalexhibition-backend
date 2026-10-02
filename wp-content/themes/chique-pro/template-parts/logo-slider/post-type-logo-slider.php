<?php
/**
 * The template used for displaying logo_slider
 *
 * @package Chique
 */
$quantity     = get_theme_mod( 'chique_logo_slider_number', 6);
$no_of_post   = 0; // for number of posts
$post_list    = array(); // list of valid post/page ids
$type         = get_theme_mod( 'chique_logo_slider_type', 'category' );
$show_content = get_theme_mod( 'chique_logo_slider_content_show', 'hide-content' );

$args = array(
	'post_type'           => 'any',
	'ignore_sticky_posts' => 1, // ignore sticky posts
);

//Get valid number of posts
if ( 'post' === $type || 'page' === $type ) {
	for ( $i = 1; $i <= $quantity; $i++ ) {
		$post_id = '';

		if ( 'post' === $type ) {
			$post_id = get_theme_mod( 'chique_logo_slider_post_' . $i );
		} elseif ( 'page' === $type ) {
			$post_id = get_theme_mod( 'chique_logo_slider_page_' . $i );
		}

		if ( $post_id && '' !== $post_id ) {
			$post_list = array_merge( $post_list, array( $post_id ) );

			$no_of_post++;
		}
	}

	$args['post__in'] = $post_list;
	$args['orderby'] = 'post__in';
} elseif ( 'category' === $type ) {
	$no_of_post = $quantity;

	$args['category__in'] = get_theme_mod( 'chique_logo_slider_select_category' );

	$args['post_type'] = 'post';
} elseif ( 'tag' === $type ) {
	$no_of_post = $quantity;

	$args['tag__in'] = get_theme_mod( 'chique_logo_slider_select_tag' );

	$args['post_type'] = 'post';
}

if ( ! $no_of_post ) {
	return;
}

$args['posts_per_page'] = $no_of_post;

$loop = new WP_Query( $args );

while ( $loop->have_posts() ) :
	$loop->the_post();

	$classes = 'post post-' . get_the_ID() . ' hentry slides';

	// Default value if there is no featurd image or first image.
	$image_url = trailingslashit( esc_url ( get_template_directory_uri() ) ) . 'assets/images/no-thumb-180x120.jpg';

	if ( has_post_thumbnail() ) {
		$image_url = get_the_post_thumbnail_url( get_the_ID(), 'chique-logo' );
	} else {
		// Get the first image in page, returns false if there is no image.
		$first_image_url = chique_get_first_image( get_the_ID(), 'chique-logo', '', true );

		// Set value of image as first image if there is an image present in the page.
		if ( $first_image_url ) {
			$image_url = $first_image_url;
		}
	}
	?>
	<article class="<?php echo esc_attr( $classes ); ?>">
		<div class="hentry-inner">
			<div class="second-content-thumbnail post-thumbnail">
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
						<img src="<?php echo esc_url( $image_url ); ?>" class="wp-post-image" alt="<?php the_title_attribute(); ?>">
					</a>
			</div><!-- .logo_slider-image-wrapper -->

			<div class="entry-container">
				<?php if( get_theme_mod( 'chique_display_logo_title', 0) ) : ?>
					<header class="entry-header">
						<h2 class="entry-title">
							<a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>">
								<?php the_title( '<span>','</span>' ); ?>
							</a>
						</h2>
					</header>
				<?php endif; ?>

				<?php
				if ( 'excerpt' === $show_content ) {
					$excerpt = get_the_excerpt();

					echo '<div class="entry-summary"><p>' . $excerpt . '</p></div><!-- .entry-summary -->';
				} elseif ( 'full-content' === $show_content ) {
					$content = apply_filters( 'the_content', get_the_content() );
					$content = str_replace( ']]>', ']]&gt;', $content );
					echo '<div class="entry-content">' . wp_kses_post( $content ) . '</div><!-- .entry-content -->';
				}
				?>
			</div><!-- .entry-container -->
		</div><!-- .hentry-inner -->
	</article><!-- .slides -->
<?php
endwhile;

wp_reset_postdata();
