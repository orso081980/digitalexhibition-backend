<?php
/**
 * The template for displaying services content
 *
 * @package Chique
 */
?>

<?php
$enable_content = get_theme_mod( 'chique_service_option', 'disabled' );

if ( ! chique_check_section( $enable_content ) ) {
	// Bail if services content is disabled.
	return;
}

$type = get_theme_mod( 'chique_service_type', 'category' );

if ( 'ect-service' === $type ) {
	$title    = get_option( 'ect_service_title', esc_html__( 'Services', 'chique-pro' ) );
	$subtitle = get_option( 'ect_service_content' );
} else {
	$title    = get_theme_mod( 'chique_service_archive_title', esc_html__( 'Services', 'chique-pro' ) );
	$subtitle = get_theme_mod( 'chique_service_sub_title', esc_html__( 'What We Do', 'chique-pro' ) );
}

$classes[] = 'services-section';
$classes[] = 'section';

if ( ! $title && ! $subtitle ) {
	$classes[] = 'no-section-heading';
}
?>

<div id="services-section" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
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

		$wrapper_classes[] = get_theme_mod( 'chique_service_layout', 'layout-one' );
		?>

		<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>">
			<?php
			if ( 'custom' === $type ) {
				get_template_part( 'template-parts/services/content', 'custom' );
			} else {
				get_template_part( 'template-parts/services/post-types', 'services' );
			}
			?>
		</div><!-- .services-wrapper -->
	</div><!-- .wrapper -->
</div><!-- #services-section -->
