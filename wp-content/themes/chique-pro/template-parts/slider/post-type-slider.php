<?php
/**
 * The template used for displaying slider
 *
 * @package Chique
 */

$quantity     = get_theme_mod( 'chique_slider_number', 4 );
$no_of_post   = 0; // for number of posts
$post_list    = array(); // list of valid post/page ids
$type         = get_theme_mod( 'chique_slider_type', 'category' );
$show_content = get_theme_mod( 'chique_slider_content_show', 'hide-content' );
$show_meta    = get_theme_mod( 'chique_slider_meta_show', 'show-meta' );

$args = array(
	'post_type'           => 'any',
	'ignore_sticky_posts' => 1, // ignore sticky posts
);
//Get valid number of posts
if ( 'post' === $type || 'page' === $type ) {
	for ( $i = 1; $i <= $quantity; $i++ ) {
		$post_id = '';

		if ( 'post' === $type ) {
			$post_id = get_theme_mod( 'chique_slider_post_' . $i );
		} elseif ( 'page' === $type ) {
			$post_id = get_theme_mod( 'chique_slider_page_' . $i );
		}

		if ( $post_id && '' !== $post_id ) {
			$post_list = array_merge( $post_list, array( $post_id ) );

			$no_of_post++;
		}
	}

	$args['post__in'] = $post_list;
	$args['orderby'] = $post__in;
} elseif ( 'category' === $type ) {
	$no_of_post = $quantity;

	$args['category__in'] = get_theme_mod( 'chique_slider_select_category' );

	$args['post_type'] = 'post';
} elseif ( 'tag' === $type ) {
	$no_of_post = $quantity;

	$args['tag__in'] = get_theme_mod( 'chique_slider_select_tag' );

	$args['post_type'] = 'post';
}

if ( ! $no_of_post ) {
	return;
}

$args['posts_per_page'] = $no_of_post;

$loop = new WP_Query( $args );

$i=1;
while ( $loop->have_posts() ) :
	$loop->the_post();

	$classes = 'post post-' . get_the_ID() . ' hentry slides';

	$layout = absint( get_theme_mod( 'chique_slider_layout', 1 ) );

	$thumbnail = 'chique-slider';
	?>
	<article class="<?php echo esc_attr( $classes ); ?>">
		<div class="hentry-inner">
			<?php chique_post_thumbnail( $thumbnail, 'html', true, true ); ?>

			<?php
			$header_media_logo = get_theme_mod( 'chique_slider_logo_' .$i );
			if ( $header_media_logo && 'category' !== $type ) : ?>
				<div class="entry-header-image">
					<img src="<?php echo esc_url( $header_media_logo ); ?>" >
				</div><!-- .entry-header-image -->
			<?php endif; ?>

			<div class="entry-container-wrap">
				<div class="entry-container">
					<header class="entry-header">
						<h2 class="entry-title">
							<a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>">
								<?php the_title(); ?>
							</a>
						</h2>
					</header>

					<?php
					if ( 'excerpt' === $show_content ) {
						echo '<div class="entry-summary"><p>' . wp_kses_post( get_the_excerpt() ) . '</p></div><!-- .entry-summary -->';
					} elseif ( 'full-content' === $show_content ) {
						$content = apply_filters( 'the_content', get_the_content() );
						$content = str_replace( ']]>', ']]&gt;', $content );
						echo '<div class="entry-content">' . wp_kses_post( $content ) . '</div><!-- .entry-content -->';
					}
					?>
				</div><!-- .entry-container -->
			</div> <!-- .entry-container-wrap -->
		</div><!-- .hentry-inner -->
	</article><!-- .slides -->
<?php
$i++;
endwhile;

wp_reset_postdata();
