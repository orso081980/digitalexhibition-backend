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
?>

<div id="feature-slider-section" class="section">
	<div class="wrapper section-content-wrapper feature-slider-wrapper">
		<div class="progress-bg">
			<span></span>
			<div class="slide-progress"></div>
		</div>
		<div class="main-slider owl-carousel">
			<?php
			// Select Slider
				get_template_part( 'template-parts/slider/post-type-slider' );

			?>
		</div><!-- .main-slider -->
	</div><!-- .wrapper -->

	<div class="scroll-down">
		<span><?php esc_html_e( 'Scroll', 'chique' ) ?></span>
		<span class="fa fa-angle-down" aria-hidden="true"></span>
	</div><!-- .scroll-down -->
</div><!-- #feature-slider -->

