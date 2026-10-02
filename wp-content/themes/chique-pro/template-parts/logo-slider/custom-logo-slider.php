<?php
/**
  * The template used for displaying simage/custom lider
  *
  * @package Chique
  */

$quantity = get_theme_mod( 'chique_logo_slider_number', 6 );

$output = '';

for ( $i = 1; $i <= $quantity; $i++ ) {
	$image = get_theme_mod( 'chique_logo_slider_image_' . $i );

	// Check Image Not Empty to add in the slides.
	if ( $image ) {
		$imagetitle = get_theme_mod( 'chique_logo_slider_title_' . $i ) ? get_theme_mod( 'chique_logo_slider_title_' . $i ) : '';

		$title  = '';
		$link   = get_theme_mod( 'chique_logo_slider_link_' . $i );
		$target = get_theme_mod( 'chique_logo_slider_target_' . $i ) ? '_blank' : '_self';

		$title = '<header class="entry-header"><h2 class="entry-title"><span>' . esc_html( $imagetitle ) . '</span></h2></header>';

		if ( $link ) {
			$title = '<header class="entry-header"><h2 class="entry-title"><a title="' . esc_attr( $imagetitle ) . '" href="' . esc_url( $link ) . '" target="' . $target . '"><span>' . esc_html( $imagetitle ) . '</span></a></h2></header>';
		}

		$content = get_theme_mod( 'chique_logo_slider_content_' . $i ) ? '<div class="entry-summary"><p>' . get_theme_mod( 'chique_logo_slider_content_' . $i ) . '</p></div><!-- .entry-summary -->' : '';

		$contentopening = '';
		$contentclosing = '';

		// Content Opening and Closing.
		if ( ! empty( $title ) || ! empty( $content ) ) {
			$contentopening = '<div class="entry-container">';
			$contentclosing = '</div><!-- .entry-container -->';
		}

		$output .= '
		<article class="image-slides hentry logo_slider-image images-' . esc_attr( $i ) . ' slides">
			<div class="hentry-inner">
				<div class="second-content-thumbnail post-thumbnail">
					<a href="' . esc_url( $link ) . '" title="' . esc_attr( $imagetitle ) . ' "target="' . $target . '">
						<img src="' . esc_url( $image ) . '" class="wp-post-image" alt="' . esc_attr( $imagetitle ) . '">
					</a>
				</div>
				' . $contentopening . $title . $content . $contentclosing . '
			</div><!-- .hentry-inner -->
		</article><!-- .slides -->';
	} // End if ().
} // End for().
echo $output;
