<?php
/**
  * The template used for displaying simage/custom lider
  *
  * @package Chique
  */
$quantity = get_theme_mod( 'chique_events_number', 4 );

$output = ''; ?>

<div class="section-content-wrapper event-content-wrapper events owl-carousel">

<?php
for ( $i = 1; $i <= $quantity; $i++ ) {
	$image = get_theme_mod( 'chique_events_image_' . $i );

	$tab_heading = get_theme_mod( 'chique_events_tabs_' . $i );

	$dates[] = $tab_heading;

	$event_date = get_theme_mod('chique_events_date_' . $i);

	// Check Image Not Empty to add in the slides.
	if ( $image ) {
		$imagetitle = get_theme_mod( 'chique_events_title_' . $i ) ? get_theme_mod( 'chique_events_title_' . $i ) : '';

		$title  = '';
		$link   = get_theme_mod( 'chique_events_link_' . $i , '#' );
		$target = get_theme_mod( 'chique_events_target_' . $i ) ? '_blank' : '_self';

		$title_start = '<header class="entry-header">';
		

		if ( $link ) {
			$title_content = '<h2 class="entry-title"><a title="' . esc_attr( $imagetitle ) . '" href="' . esc_url( $link ) . '" target="' . $target . '">' . esc_html( $imagetitle ) . '</a></h2>';
		} else {
			$title_content = '<h2 class="entry-title">' . esc_html( $imagetitle ) . '</h2>';
		}

		$title_end = '</header>';

		$event_time = get_theme_mod( 'chique_events_time_' . $i );

		$meta_open = '<div class="entry-meta">';

		$date = '';
		if( $event_date ) {
			$date = '<span class="posted-on"><a href="' . esc_url( $link ) . '">'. esc_html( $event_date ) .'</a></span>';
		}

		$sep = '';
		if( $event_date && $event_time ) {
			$sep = '<span class="sep">|</span>';
		}

		$time = '';
		if( $event_time ) {
			$time = '<span class="event-time"><a href="' . esc_url( $link ) . '">'. esc_html( $event_time ) .'</a></span>';
		}

		$meta_close = '</div>';

		$content = get_theme_mod( 'chique_events_content_' . $i ) ? '<div class="entry-summary"><p>' . get_theme_mod( 'chique_events_content_' . $i ) . '</p></div><!-- .entry-summary -->' : '';

		$contentopening = '';
		$contentclosing = '';

		// Content Opening and Closing.
		if ( ! empty( $title ) || ! empty( $content ) ) {
			$contentopening = '<div class="entry-container-wrap"><div class="entry-container">';
			$contentclosing = '</div></div><!-- .entry-container -->';
		}

		$output .= '
		<article class="image-slides hentry slider-image images-' . esc_attr( $i ) . ' slides">
			<div class="hentry-inner">
				<div class="post-thumbnail">
					<a href="' . esc_url( $link ) . '" title="' . esc_attr( $imagetitle ) . ' "target="' . $target . '">
						<img src="' . esc_url( $image ) . '" class="wp-post-image" alt="' . esc_attr( $imagetitle ) . '">
					</a>
				</div>
				' . $contentopening . $title_start . $meta_open . $date . $sep . $time . $meta_close . $title_content . $title_end . $content . $contentclosing . '
			</div><!-- .hentry-inner -->
		</article><!-- .slides -->';
	} // End if ().
} // End for().

echo $output;
?>
</div>

<?php if( get_theme_mod( 'chique_events_dots', 1) ) : ?>
	<ul id='event-slider-dots' class='owl-dots'>
		<?php
		foreach ( $dates as $date ) {
			echo '<li class="owl-dot"><span>' .esc_attr( $date ) . '</span></li> ';
		}
		?>
	</ul>
<?php endif; ?>

<ul id='event-slider-nav' class='owl-nav'>
</ul>
