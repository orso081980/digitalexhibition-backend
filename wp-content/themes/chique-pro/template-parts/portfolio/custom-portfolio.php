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

$slider_select = get_theme_mod( 'chique_portfolio_slider', 1 );

for ( $i = 1; $i <= $number; $i++ ) {
	$target   = get_theme_mod( 'chique_portfolio_target_' . $i ) ? '_blank': '_self';
	$link     = get_theme_mod( 'chique_portfolio_link_' . $i, '#' );
	$title    = get_theme_mod( 'chique_portfolio_title_' . $i );
	$image    = get_theme_mod( 'chique_portfolio_image_' . $i ) ? get_theme_mod( 'chique_portfolio_image_' . $i ) : trailingslashit( esc_url( get_template_directory_uri() ) ) . 'assets/images/no-thumb-666x499.jpg';

	if ( function_exists( 'qtrans_convertURL' ) ) {
		$link = qtrans_convertURL( $link );
	}

	?>
	<article id="post-<?php echo esc_attr( $i ) ?>" class="hentry post-image grid-item">
		<div class="hentry-inner">
			<div class="post-thumbnail">
				<?php if ( $link ) : ?>
				<a href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( $target ); ?>">
				<?php endif; ?>
					<img src="<?php echo esc_url( $image ); ?>" class="wp-post-image" alt="<?php echo esc_attr( $title ); ?>" title="<?php echo esc_attr( $title ); ?>">
				<?php if ( $link ) : ?>
				</a>
				<?php endif; ?>
			</div>

			<div class="entry-container">
				<div class="inner-wrap">
					<?php
					if ( $title ) : ?>
						<header class="entry-header">
							<h2 class="entry-title">
								<?php if ( $link ) : ?>
								<a class="post-thumbnail" href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( $target ); ?>">
								<?php endif; ?>
									<?php echo wp_kses_post( $title ); ?></h2>
								<?php if ( $link ) : ?>
								</a>
								<?php endif; ?>
						</header>
					<?php endif; ?>
				</div>
			</div><!-- .entry-container -->
		</div><!-- .hentry-inner -->
	</article>
<?php
}
