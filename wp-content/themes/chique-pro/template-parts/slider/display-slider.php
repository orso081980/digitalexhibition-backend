<?php
/**
 * The template used for displaying slider
 *
 * @package Chique
 */
?>
<?php
$enable_slider = get_theme_mod( 'chique_slider_option', 'disabled' );

if ( ! chique_check_section( $enable_slider ) ) {
	return;
}

$type   	   = get_theme_mod( 'chique_slider_type', 'category' );
$content_align = get_theme_mod( 'chique_slider_content_align', 'content-aligned-center');
$text_align    = get_theme_mod( 'chique_slider_text_align', 'text-aligned-center');

$classes[] = $content_align;
$classes[] = $text_align; 
?>


<div id="feature-slider-section" class="section <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="wrapper section-content-wrapper feature-slider-wrapper">
		<div class="progress-bg">
			<span></span>
			<div class="slide-progress"></div>
		</div>
		<div class="main-slider owl-carousel">
			<?php
			// Select Slider
			if ( 'post' === $type || 'page' === $type || 'category' === $type || 'tag' === $type ) {
				get_template_part( 'template-parts/slider/post-type-slider' );
			} elseif ( 'custom' === $type ) {
				get_template_part( 'template-parts/slider/custom-slider' );
			}
			?>
		</div><!-- .main-slider -->
	</div><!-- .wrapper -->

	<?php if ( get_theme_mod( 'chique_slider_scroll_down', 1 ) ) : ?>
		<div class="scroll-down">
			<span><?php esc_html_e( 'Scroll', 'chique-pro' ) ?></span>
			<span class="fa fa-angle-down" aria-hidden="true"></span>
		</div><!-- .scroll-down -->
  	<?php endif; ?>
</div><!-- #feature-slider -->

