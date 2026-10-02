<?php
/**
  * The template used for displaying simage/custom lider
  *
  * @package Chique
  */

$quantity = get_theme_mod( 'chique_slider_number', 4 );

$output = '';

for ( $i = 1; $i <= $quantity; $i++ ) {
	$image = get_theme_mod( 'chique_slider_image_' . $i );

	// Check Image Not Empty to add in the slides.
	if ( $image ) {
		$imagetitle = get_theme_mod( 'chique_slider_title_' . $i ) ? get_theme_mod( 'chique_slider_title_' . $i ) : '';
		$sub_title = get_theme_mod( 'chique_slider_sub_title_' . $i ) ? get_theme_mod( 'chique_slider_sub_title_' . $i ) : '';

		$title  = '';
		$link   = get_theme_mod( 'chique_slider_link_' . $i );
		$target = get_theme_mod( 'chique_slider_target_' . $i ) ? '_blank' : '_self';


		$title = '<header class="entry-header"><h2 class="entry-title">' . esc_html( $imagetitle ) . '<span class="sub-title">'. esc_html( $sub_title ) .'</span></h2></header>';

		if ( $link ) {
			$title = '<header class="entry-header"><h2 class="entry-title"><a title="' . esc_attr( $imagetitle ) . '" href="' . esc_url( $link ) . '" target="' . $target . '">' . esc_html( $imagetitle ) . '</a><span class="sub-title">'. esc_html( $sub_title ) .'</span></h2></header>';
		}


		$content = get_theme_mod( 'chique_slider_content_' . $i ) ? '<div class="entry-summary"><p>' . get_theme_mod( 'chique_slider_content_' . $i ) . '</p></div><!-- .entry-summary -->' : '';

		$contentopening = '';
		$contentclosing = '';

		// Content Opening and Closing.
		if ( ! empty( $title ) || ! empty( $content ) ) {
			$contentopening = '<div class="entry-container-wrap"><div class="entry-container">';
			$contentclosing = '</div></div><!-- .entry-container -->';
		}

		$header_media_logo = get_theme_mod( 'chique_slider_logo_' .$i ) ? get_theme_mod( 'chique_slider_logo_' .$i ) : '';
		$slider_logo = '';
		if ( $header_media_logo ) :
			$slider_logo = '<div class="entry-header-image">
				<img src="' . esc_url( $header_media_logo ) . '" >
			</div><!-- .entry-header-image -->';
		 endif;

		$button_text = get_theme_mod( 'chique_featured_slider_button_text_' . $i) ? get_theme_mod( 'chique_featured_slider_button_text_' . $i) : '';
		$button_link = get_theme_mod( 'chique_featured_slider_button_link_' . $i);

		$more_button = '';
		if( $button_text ) {
			$more_button = '<span class="more-button">
				<a href="'. esc_url( $button_link ) .'" target="_self" class="more-link">' . esc_html( $button_text ) .'<span class="screen-reader-text">Spring Fantasy</span></a>
			</span>';
		}

		$output .= '
		<article class="image-slides hentry slider-image images-' . esc_attr( $i ) . ' slides">
			<div class="hentry-inner">
				<div class="post-thumbnail">
					<a href="' . esc_url( $link ) . '" title="' . esc_attr( $imagetitle ) . ' "target="' . $target . '">
						<img src="' . esc_url( $image ) . '" class="wp-post-image" alt="' . esc_attr( $imagetitle ) . '">
					</a>
				</div>
				' . $slider_logo . $contentopening . $title . $content . $more_button . $contentclosing . '
			</div><!-- .hentry-inner -->
		</article><!-- .slides -->';
	} // End if ().
} // End for().

echo $output;
