<?php
/**
 * The template for displaying portfolio items
 *
 * @package Chique
 */
?>

<?php
$number = get_theme_mod( 'chique_portfolio_number', 6 );

if ( ! $number ) {
	// If number is 0, then this section is disabled
	return;
}

$args = array(
	'ignore_sticky_posts' => 1 // ignore sticky posts
);

$post_list  = array();// list of valid post/page ids

$type = get_theme_mod( 'chique_portfolio_type', 'category' );

if ( 'post' === $type || 'jetpack-portfolio' === $type || 'page' === $type  ) {
	$args['post_type'] = $type;

	for ( $i = 1; $i <= $number; $i++ ) {
		$post_id = '';

		if ( 'post' === $type ) {
			$post_id = get_theme_mod( 'chique_portfolio_post_' . $i );
		} elseif ( 'page' === $type ) {
			$post_id = get_theme_mod( 'chique_portfolio_page_' . $i );
		} elseif ( 'jetpack-portfolio' === $type ) {
			$post_id =  get_theme_mod( 'chique_portfolio_cpt_' . $i );
		}

		if ( $post_id && '' !== $post_id ) {
			// Polylang Support.
			if ( class_exists( 'Polylang' ) ) {
				$post_id = pll_get_post( $post_id, pll_current_language() );
			}

			$post_list = array_merge( $post_list, array( $post_id ) );

		}
	}

	$args['post__in'] = $post_list;
	$args['oder_by'] = 'post__in';
}
elseif ( 'category' === $type ) {
	$no_of_post = $number;

	if ( get_theme_mod( 'chique_portfolio_select_category' ) ) {
		$args['category__in'] = (array) get_theme_mod( 'chique_portfolio_select_category' );
	}

	$args['post_type'] = 'post';
}

$args['posts_per_page'] = $number;
$loop     = new WP_Query( $args );

if ( $loop -> have_posts() ) :
	while ( $loop -> have_posts() ) :
		$loop -> the_post();

		get_template_part( 'template-parts/portfolio/content', 'portfolio' );

	endwhile;
	wp_reset_postdata();
endif;
