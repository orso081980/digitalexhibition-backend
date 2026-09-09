<?php
/**
 * The template for displaying testimonial items
 *
 * @package Chique
 */
?>

<?php
$enable = get_theme_mod( 'chique_testimonial_option', 'disabled' );

if ( ! chique_check_section( $enable ) ) {
	// Bail if featured content is disabled
	return;
}

$type = get_theme_mod( 'chique_testimonial_type', 'category' );

if ( 'jetpack-testimonial' === $type ) {
	// Get Jetpack options for testimonial.
	$jetpack_defaults = array(
		'page-title' => esc_html__( 'Testimonials', 'chique-pro' ),
	);

	// Get Jetpack options for testimonial.
	$jetpack_options = get_theme_mod( 'jetpack_testimonials', $jetpack_defaults );

	$headline    = isset( $jetpack_options['page-title'] ) ? $jetpack_options['page-title'] : esc_html__( 'Testimonials', 'chique-pro' );
	$subheadline = isset( $jetpack_options['page-content'] ) ? $jetpack_options['page-content'] : '';
} else {
	$headline    = get_theme_mod( 'chique_testimonial_headline', esc_html__( 'Testimonials', 'chique-pro' ) );
	$subheadline = get_theme_mod( 'chique_testimonial_subheadline' );
}

$classes[] = 'section testimonial-content-section';

if ( ! $headline && ! $subheadline ) {
	$classes[] = 'no-section-heading';
}
?>

<div id="testimonial-content-section" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="wrapper">

	<?php if ( $headline || $subheadline ) : ?>
		<div class="section-heading-wrapper testimonial-content-section-headline">
		<?php if ( $headline ) : ?>
			<div class="section-title-wrapper">
				<h2 class="section-title"><?php echo wp_kses_post( $headline ); ?></h2>
			</div><!-- .section-title-wrapper -->
		<?php endif; ?>

		<?php if ( $subheadline ) : ?>
			<div class="section-description">
				<?php
	            $subheadline = apply_filters( 'the_content', $subheadline );
	            echo str_replace( ']]>', ']]&gt;', $subheadline );
                ?>
			</div><!-- .section-description-wrapper -->
		<?php endif; ?>
		</div><!-- .section-heading-wrapper -->
	<?php endif;
	 
		if ( 'post' === $type || 'jetpack-testimonial' === $type || 'page' === $type || 'category' === $type || 'tag' === $type ) {
			get_template_part( 'template-parts/testimonials/post-types-testimonial' );
		} elseif ( 'custom' === $type ) {
			get_template_part( 'template-parts/testimonials/custom-testimonial' );
		}
	?>
	</div><!-- .wrapper -->
</div><!-- .testimonial-content-section -->
