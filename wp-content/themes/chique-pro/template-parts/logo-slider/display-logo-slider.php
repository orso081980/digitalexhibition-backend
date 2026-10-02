<?php
/**
 * The template used for displaying logo_slider
 *
 * @package Chique
 */
?>
<?php
$enable_logo_slider = get_theme_mod( 'chique_logo_slider_option', 'disabled' );
$logo_design = get_theme_mod( 'chique_logo_slider_design', 'static-logo' );
$layout = get_theme_mod( 'chique_logo_slider_layout', 3);

if ( ! chique_check_section( $enable_logo_slider ) ) {
	return;
}

$type   = get_theme_mod( 'chique_logo_slider_type', 'category' );
$title     = get_theme_mod( 'chique_logo_slider_title', esc_html__( 'Logo Slider', 'chique-pro' ) );
$sub_title = get_theme_mod( 'chique_logo_slider_sub_title' );

$classes[] = '';

if ( ! $title && ! $sub_title ) {
	$classes[] = 'no-section-heading';
}
?>
<div id="logo-slider-section" class="logo-slider-section section<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="wrapper">
		<?php if ( $title || $sub_title ) : ?>
		<div class="section-heading-wrapper">
			<?php if ( $title ) : ?>
				<div class="section-title-wrapper">
					<h2 class="section-title"><?php echo wp_kses_post( $title ); ?></h2>
				</div><!-- .page-title-wrapper -->
			<?php endif; ?>
			
			<?php if ( $sub_title ) : ?>
				<div class="taxonomy-description-wrapper section-subtitle">
					<?php
	                $sub_title = apply_filters( 'the_content', $sub_title );
	                echo wp_kses_post( str_replace( ']]>', ']]&gt;', $sub_title ) );
	                ?>
				</div><!-- .taxonomy-description-wrapper -->
			<?php endif; ?>
		</div><!-- .section-heading-wrapper -->
		<?php endif; ?>

		<?php if( get_theme_mod( 'chique_logo_slider_dots', 1 ) && 'scrollable-logo' == $logo_design ) : 
			$classes[] ='owl-dots-enabled';
		endif; ?>

		<?php if( 'static-logo' == $logo_design ) : 
			$classes[] ='static-logo';
		endif; ?>

		<?php if( 'scrollable-logo' == $logo_design ) : 
			$classes[] ='owl-carousel';
		endif; ?>

		<?php 
		if( 'static-logo' == $logo_design ) {
			if( 1 == $layout ) {
				$classes[] = 'layout-one';
			}
			if( 2 == $layout ) {
				$classes[] = 'layout-two';
			}
			if( 3 == $layout ) {
				$classes[] = 'layout-three';
			}
			if( 4 == $layout ) {
				$classes[] = 'layout-four';
			}
			if( 5 == $layout ) {
				$classes[] = 'layout-five';
			}
		}
		?>

		<div class="section-content-wrapper logo-slider-content-wrapper <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php
			// Select Slider
			if ( 'post' === $type || 'page' === $type || 'category' === $type ) {
				get_template_part( 'template-parts/logo-slider/post-type-logo-slider' );
			} elseif ( 'custom' === $type ) {
				get_template_part( 'template-parts/logo-slider/custom-logo-slider' );
			}
			?>
		</div><!-- .section-content-wrapper  -->	
	</div><!-- .wrapper -->
</div><!-- #second-section -->
