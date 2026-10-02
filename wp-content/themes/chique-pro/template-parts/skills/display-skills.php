<?php
/**
 * The template for displaying skills
 *
 * @package Chique
 */
?>

<?php
$enable = get_theme_mod( 'chique_skills_option', 'disabled' );

if ( ! chique_check_section( $enable ) ) {
	// Bail if featured content is disabled.
	return;
}

$type      = get_theme_mod( 'chique_skills_type', 'category' );

$classes[] = 'section';
$classes[] = esc_attr( $type );
?>

<div id="skill-section" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="wrapper">
		<?php
		$content_layout = 'layout-one';

		$image = get_theme_mod( 'chique_skills_image' );

		if ( $image ) {
			$content_layout = 'layout-two';
		}
		?>

		<div class="section-content-wrapper skill-content-wrapper <?php echo $content_layout; // WPCS: XSS OK. ?>">
		<?php
			get_template_part( 'template-parts/skills/custom', 'skills' );
		?>
		</div><!-- .section-content-wrap -->
	</div><!-- .wrapper -->
</div><!-- #numbers-section -->
