<?php
/**
 * The template for displaying featured image content
 *
 * @package Chique
 */
?>

<?php
$quantity = get_theme_mod( 'chique_venue_number', 3 );

for ( $i = 1; $i <= $quantity; $i++ ) {
	$target = get_theme_mod( 'chique_venue_target_' . $i ) ? '_blank' : '_self';

	$link = get_theme_mod( 'chique_venue_link_' . $i ) ? get_theme_mod( 'chique_venue_link_' . $i ) : '#';

	if ( function_exists( 'qtrans_convertURL' ) ) {
		$link = qtrans_convertURL( $link );
	}

	echo '
	<article id="featured-post-' . esc_attr( $i ) . '" class="hentry featured-image-content">
		<div class="hentry-inner">';

			$title   = get_theme_mod( 'chique_venue_title_' . $i );
			$content = get_theme_mod( 'chique_venue_content_' . $i );

			$image = get_theme_mod( 'chique_venue_image_' . $i ) ? get_theme_mod( 'chique_venue_image_' . $i ) : '';

			echo '
			<div class="post-thumbnail">
				<a href="' . esc_url( $link ) . '" target="' . esc_attr( $target ) . '">
					<img src="' . esc_url( $image ) . '" class="wp-post-image" alt="' . esc_attr( $title ) . '" title="' . esc_attr( $title ) . '">
				</a>
			</div>';

			if ( $title || $content ) {
				echo '
				<div class="entry-container">';

				if ( $title ) {
					echo '
					<header class="featured-content entry-header">
						<h2 class="entry-title">
							<a href="' . esc_url( $link ) . '" rel="bookmark" target="' . esc_attr( $target ) . '">' . wp_kses_post( $title ) . '</a>
						</h2>
					</header>';
				}

				if ( '' !== $content ) {
					echo '
					<div class="entry-summary">' . wp_kses_post( apply_filters( 'the_content', $content ) ) . '</div><!-- .entry-summary -->';
				}

				echo '
				</div><!-- .entry-container -->';
			}

		echo '</div><!-- .hentry-inner -->
	</article><!-- .featured-post-' . esc_attr( $i ) . ' -->';
} // End for().
