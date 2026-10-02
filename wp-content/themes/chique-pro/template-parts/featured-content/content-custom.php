<?php
/**
 * The template for displaying featured image content
 *
 * @package Chique
 */
?>

<?php
$quantity = get_theme_mod( 'chique_featured_content_number', 3 );

for ( $i = 1; $i <= $quantity; $i++ ) {
	$target = get_theme_mod( 'chique_featured_content_target_' . $i ) ? '_blank' : '_self';

	$link = get_theme_mod( 'chique_featured_content_link_' . $i ) ? get_theme_mod( 'chique_featured_content_link_' . $i ) : '#';

	if ( function_exists( 'qtrans_convertURL' ) ) {
		$link = qtrans_convertURL( $link );
	}

	echo '
	<article id="featured-post-' . esc_attr( $i ) . '" class="hentry featured-image-content"><div class="hentry-inner">';

		$title   = get_theme_mod( 'chique_featured_content_title_' . $i );
		$content = get_theme_mod( 'chique_featured_content_content_' . $i );
		$more_button = get_theme_mod( 'chique_featured_content_more_button_text_' . $i );

		$image = get_theme_mod( 'chique_featured_content_image_' . $i ) ? get_theme_mod( 'chique_featured_content_image_' . $i ) : '';

		echo '
		<div class="post-thumbnail">
		<a href="' . esc_url( $link ) . '" target="' . esc_attr( $target ) . '">
			<img src="' . esc_url( $image ) . '" class="wp-post-image" alt="' . esc_attr( $title ) . '" title="' . esc_attr( $title ) . '">
		</a></div>';

		if ( $title || $content || $more_button ) {
			echo '
			<div class="entry-container">
				<header class="entry-header">';

				if ( $title ) {
					echo '
					<h2 class="entry-title">
						<a href="' . esc_url( $link ) . '" rel="bookmark" target="' . esc_attr( $target ) . '">' . wp_kses_post( $title ) . '</a>
					</h2>';
				}

				echo '</header>';

				if ( $content || $more_button ) {
					if ( $more_button ) {
						$content .= '<span class="more-button">
							<a class="more-link" href="' . esc_url( $link ) . '" rel="bookmark" target="' . esc_attr( $target ) . '">' . esc_html( $more_button ) . '</a>
						</span>';
					}

				 	echo '<div class="entry-summary">' . wp_kses_post( $content ) . '</div><!-- .entry-summary -->';
				}


				echo '
			</div><!-- .entry-container -->';
		}

		echo '
		</div>
	</article><!-- .featured-post-' . esc_attr( $i ) . ' -->';
} // End for().
