<?php
/**
 * The template for displaying pricing content
 *
 * @package Chique
 */
?>

<?php
$enable_content = get_theme_mod( 'chique_pricing_option', 'disabled' );

if ( ! chique_check_section( $enable_content ) ) {
	// Bail if pricing content is disabled.
	return;
}

$type = get_theme_mod( 'chique_pricing_type', 'category' );

if ( 'custom' !== $type ) {
	$pricing_posts = chique_get_pricing_posts();

	if ( empty( $pricing_posts ) ) {
		return;
	}
}

if ( 'ect-service' === $type ) {
	$title    = get_option( 'ect_pricing_title', esc_html__( 'Services', 'chique-pro' ) );
	$subtitle = get_option( 'ect_pricing_content' );
} else {
	$title    = get_theme_mod( 'chique_pricing_archive_title', esc_html__( 'Pricing Table', 'chique-pro' ) );
	$subtitle = get_theme_mod( 'chique_pricing_sub_title' );
}

$classes[] = 'pricing-section';
$classes[] = 'section';

if ( ! $title && ! $subtitle ) {
	$classes[] = 'no-section-heading';
}
?>

<div id="pricing-section" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="wrapper">
		<?php if ( '' !== $title || $subtitle ) : ?>
			<div class="section-heading-wrapper">
				<?php if ( '' !== $title ) : ?>
					<div class="section-title-wrapper">
						<h2 class="section-title"><?php echo wp_kses_post( $title ); ?></h2>
					</div><!-- .page-title-wrapper -->
				<?php endif; ?>

				<?php if ( $subtitle ) : ?>
					<div class="section-description">
						<?php
						$subtitle = apply_filters( 'the_content', $subtitle );
						echo wp_kses_post( str_replace( ']]>', ']]&gt;', $subtitle ) );
						?>
					</div><!-- .section-description -->
				<?php endif; ?>
			</div><!-- .section-heading-wrapper -->
		<?php endif; ?>

		<?php

		$wrapper_classes[] = 'section-content-wrapper';

		$wrapper_classes[] = get_theme_mod( 'chique_pricing_layout', 'layout-three' );
		?>

		<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>">
			<?php
			if ( 'custom' === $type ) {
				get_template_part( 'template-parts/pricing/content', 'custom' );
			} else {
				$j = 1;
				$highlights_count = 0;
				foreach ( $pricing_posts as $pricing_post ){
					$highlight = get_theme_mod( 'chique_pricing_highlight_' . $j );
					if( ! $highlight ) {
						$highlights_count++;
					}
					$j++;
				}

				$i = 1;
				$currency = get_theme_mod( 'chique_pricing_currency' );

				foreach ( $pricing_posts as $post ) {
					setup_postdata( $post );

					$show_content = get_theme_mod( 'chique_pricing_show', 'full-content' );
					$show_meta    = get_theme_mod( 'chique_pricing_meta_show', 'hide-meta' );
					$highlight    = get_theme_mod( 'chique_pricing_highlight_' . $i );

					$highlight_class = '';

					if( count( $pricing_posts ) != $highlights_count ) {
						$highlight_class = $highlight ? 'highlight' : 'highlight-off';
					}
					?>

					<article id="post-<?php the_ID(); ?>" <?php post_class( $highlight_class ); ?>>
						<div class="hentry-inner">
							<div class="post-thumbnail">
								<a href="<?php the_permalink(); ?>">
									<?php

									// Default value if there is no first image
									$image = '';

									if ( has_post_thumbnail() ) {
										the_post_thumbnail();
									} else {
										// Get the first image in page, returns false if there is no image.
										$first_image = chique_get_first_image( get_the_ID(), 'post-thumbnail', '' );

										// Set value of image as first image if there is an image present in the page.
										if ( $first_image ) {
											$image = $first_image;
										}

										echo $image;
									}
									?>
								</a>
							</div>

							<div class="entry-container">
								<div class="entry-header-wrap">
									<header class="entry-header">
										<?php if ( 'show-meta' === $show_meta  && ( 'post' === $type || 'category' === $type ) ) : ?>
											<div class="entry-meta">
												<?php chique_entry_category(); ?>
											</div>
										<?php endif; ?>
										<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">','</a></h2>' ); ?>

										<?php if ( 'show-meta' === $show_meta  && 'custom' !== $type ) : ?>
										<div class="entry-meta">
											<?php chique_posted_on(); ?>
										</div><!-- .entry-meta -->
										<?php endif; ?>
									</header>

									<?php
										$amount         = get_theme_mod( 'chique_pricing_amount_' . $i );
										$amount_remarks = get_theme_mod( 'chique_pricing_amount_remarks_' . $i );

										if ( $currency || $amount || $amount_remarks ) {
											echo '<div class="package-price">
												<sup>' . esc_html( $currency ) . '</sup>' . esc_html( $amount ) . '
												<p class="package-month">' . esc_html( $amount_remarks ) . '</p>
											  </div>';
										}
									?>
								</div>
								<?php
								if ( 'excerpt' === $show_content ) {
									$excerpt = get_the_excerpt();

									echo '<div class="entry-summary"><p>' . $excerpt . '</p></div><!-- .entry-summary -->';
								} elseif ( 'full-content' === $show_content ) {
									$content = apply_filters( 'the_content', get_the_content() );
									$content = str_replace( ']]>', ']]&gt;', $content );
									echo '<div class="entry-content">' . wp_kses_post( $content ) . '</div><!-- .entry-content -->';
								} ?>
							</div><!-- .entry-container -->
						</div> <!-- .hentry-inner -->
					</article> <!-- .article -->
					<?php
					$i++;
				}

				wp_reset_postdata();
			}
			?>
		</div><!-- .pricing-wrapper -->
	</div><!-- .wrapper -->
</div><!-- #pricing-section -->
