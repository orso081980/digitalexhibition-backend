<?php
/**
 * The template for displaying pricing image content
 *
 * @package Chique
 */
?>

<?php
$quantity = get_theme_mod( 'chique_pricing_number', 3 );
$currency = get_theme_mod( 'chique_pricing_currency' );

$j = 1;
$highlights_count = 0;
for( $i = 1; $i <= $quantity; $i++ ){
	$highlight = get_theme_mod( 'chique_pricing_highlight_' . $i );
	if( ! $highlight ) {
		$highlights_count++;
	}
	$j++;
}

$i = 1;
$currency = get_theme_mod( 'chique_pricing_currency' );

for ( $i = 1; $i <= $quantity; $i++ ) {

	$highlight    = get_theme_mod( 'chique_pricing_highlight_' . $i );

	$highlight_class = '';

	if( $quantity != $highlights_count ) {
		$highlight_class = $highlight ? ' highlight' : ' highlight-off';
	}

	$target = get_theme_mod( 'chique_pricing_target_' . $i ) ? '_blank' : '_self';

	$link = get_theme_mod( 'chique_pricing_link_' . $i ) ? get_theme_mod( 'chique_pricing_link_' . $i ) : '#';

	if ( function_exists( 'qtrans_convertURL' ) ) {
		$link = qtrans_convertURL( $link );
	}

	echo '
	<article id="pricing-post-' . esc_attr( $i ) . '" class="hentry' . $highlight_class . '"> <div class="hentry-inner">';

		$title   = get_theme_mod( 'chique_pricing_archive_title_' . $i );
		$content = get_theme_mod( 'chique_pricing_content_' . $i );
		$more_button = get_theme_mod( 'chique_pricing_more_button_text_' . $i );

		$image = get_theme_mod( 'chique_pricing_image_' . $i ) ? get_theme_mod( 'chique_pricing_image_' . $i ) : '';
		if ( $image ) {
		echo '
			<div class="post-thumbnail">
				<a href="' . esc_url( $link ) . '" target="' . esc_attr( $target ) . '">
					<img src="' . esc_url( $image ) . '" class="wp-post-image" alt="' . esc_attr( $title ) . '" title="' . esc_attr( $title ) . '">
				</a>
			</div>';
		}

		if ( $title || $content || $more_button) {
			echo '
			<div class="entry-container">
			<div class="entry-header-wrap">
				<header class="entry-header">';

				if ( $title ) {
					echo '
					<h2 class="entry-title">
						<a href="' . esc_url( $link ) . '" rel="bookmark" target="' . esc_attr( $target ) . '">' . wp_kses_post( $title ) . '</a>
					</h2>';
				}

				echo '</header>';

				$amount         = get_theme_mod( 'chique_pricing_amount_' . $i );
				$amount_remarks = get_theme_mod( 'chique_pricing_amount_remarks_' . $i );

				if ( $currency || $amount || $amount_remarks ) {
					echo '<div class="package-price">
						<sup>' . esc_html( $currency ) . '</sup>' . esc_html( $amount ) . '
						<p class="package-month">' . esc_html( $amount_remarks ) . '</p>
					  </div>';
				}

				echo '</div>';

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
	</article><!-- .pricing-post-' . esc_attr( $i ) . ' -->';
} // End for().
