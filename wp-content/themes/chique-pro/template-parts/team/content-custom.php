<?php
/**
 * The template for displaying team image content
 *
 * @package Chique
 */
?>

<?php
$quantity = get_theme_mod( 'chique_team_number', 5 );

for ( $i = 1; $i <= $quantity; $i++ ) {
	$target = get_theme_mod( 'chique_team_target_' . $i ) ? '_blank' : '_self';

	$link = get_theme_mod( 'chique_team_link_' . $i ) ? get_theme_mod( 'chique_team_link_' . $i ) : '#';

	if ( function_exists( 'qtrans_convertURL' ) ) {
		$link = qtrans_convertURL( $link );
	}

	echo '
	<article id="team-post-' . esc_attr( $i ) . '" class="hentry"> <div class="hentry-inner">';

		$title       = get_theme_mod( 'chique_team_title_' . $i );
		$position    = get_theme_mod( 'chique_team_position_' . $i );
		$content     = get_theme_mod( 'chique_team_content_' . $i );
		$more_button = get_theme_mod( 'chique_team_more_button_text_' . $i );

		$image = get_theme_mod( 'chique_team_image_' . $i ) ? get_theme_mod( 'chique_team_image_' . $i ) : trailingslashit( esc_url( get_template_directory_uri() ) ) . 'assets/images/no-thumb-825x825.jpg';

		echo '
		<div class="post-thumbnail">
			<a href="' . esc_url( $link ) . '" target="' . esc_attr( $target ) . '">
			<img src="' . $image .' "class="attachment-chique-hero-content size-chique-hero-content wp-post-image">
			</a>
		</div>';

		if ( $title || $position || $content || $more_button) {
			echo '
			<div class="entry-container">
				<header class="entry-header">';

				if ( $title ) {
					echo '
					<h2 class="entry-title">
						<a href="' . esc_url( $link ) . '" rel="bookmark" target="' . esc_attr( $target ) . '">' . wp_kses_post( $title ) . '</a>
					</h2>';
				}

				if ( $position ) {
					echo '
					<div class="entry-meta">
                        	<span class="job-label">' . esc_html( $position ) . '</span>
	                </div> <!-- .entry-meta -->';
	            }

				echo '</header>';

				chique_team_social_links( $i );

				if ( $content || $more_button ) {
					if ( $more_button ) {
						$content .= '<span class="more-button">
							<a class="more-link" href="' . esc_url( $link ) . '" rel="bookmark" target="' . esc_attr( $target ) . '">' . esc_html( $more_button ) . '</a>
						</span>';
					}

				 	echo '<div class="entry-summary">' . wp_kses_post( $content ) . '</div><!-- .entry-summary -->';
				}

				echo '
			</div><!-- .entry-container -->';		}

		echo '
		</div>
	</article><!-- .team-post-' . esc_attr( $i ) . ' -->';
} // End for().
