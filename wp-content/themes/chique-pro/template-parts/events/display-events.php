<?php
/**
 * The template used for displaying slider
 *
 * @package Chique
 */
?> 
<?php 
$enable_slider = get_theme_mod( 'chique_events_option', 'disabled' );

if ( ! chique_check_section( $enable_slider ) ) {
	return;
}

$type = get_theme_mod( 'chique_events_type', 'category' );
?>

<div id="events-section" class="section events-section">
	<div class="wrapper">
		<?php
		// Select Slider
		if ( 'post' === $type || 'page' === $type || 'category' === $type || 'ect-event' === $type ) {
			get_template_part( 'template-parts/events/post-type-events' );
		} elseif ( 'custom' === $type ) {
			get_template_part( 'template-parts/events/custom-events' );
		}
		?>
	</div><!-- .wrapper -->
</div><!-- #feature-slider -->

