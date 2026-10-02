<?php
/**
 * The template for displaying why choose us content
 *
 * @package Chique
 */

$enable = get_theme_mod( 'chique_why_choose_us_option', 'disabled' );

if ( ! chique_check_section( $enable ) ) {
	// Bail if why choose us content is disabled.
	return;
}

$type = get_theme_mod( 'chique_why_choose_us_type', 'category' );

$title     = get_theme_mod( 'chique_why_choose_us_title', esc_html__( 'Why Choose Us', 'chique-pro' ) );
$sub_title = get_theme_mod( 'chique_why_choose_us_sub_title' );

$classes[] = '';
	
if( ! $title && ! $sub_title ) {
	$classes[] = 'no-section-heading';
}

$style = get_theme_mod( 'chique_why_choose_us_style', 'modern');
if ( 'modern' == $style ) {
	$classes[] ='modern-style';
}

if ( 'classic' == $style ) {
	$classes[] = get_theme_mod( 'chique_why_choose_us_content_align', 'content-aligned-center');

	$classic_layout = get_theme_mod( 'chique_why_choose_us_layout', 'layout-three'); 	
}
?>

<div class="why-choose-us-section section<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="wrapper">
		<?php if ( $title || $sub_title ) : ?>
			<div class="section-heading-wrapper">
				<?php if ( $title ) : ?>
					<div class="section-title-wrapper">
						<h2 class="section-title"><?php echo wp_kses_post( $title ); ?></h2>
					</div><!-- .page-title-wrapper -->
				<?php endif; ?>

				<?php if ( $sub_title ) : ?>
					<div class="section-description">
						<?php echo wp_kses_post( $sub_title ); ?>
					</div><!-- .section-description -->
				<?php endif; ?>
			</div><!-- .section-heading-wrapper -->
		<?php endif; ?>

		<?php if( 'classic' == $style ) : ?>
			<div class="section-content-wrapper <?php echo esc_attr( $classic_layout ); ?>">
		<?php else : ?>
			<div class="section-content-wrapper">
		<?php endif; ?>		
				
			<?php
			if ( 'custom' === $type ) {
				get_template_part( 'template-parts/why-choose-us/content', 'custom' );
			} else {
					get_template_part( 'template-parts/why-choose-us/post-types', 'why-choose-us' );
			}
			?>

			<?php
				$target = get_theme_mod( 'chique_why_choose_us_target' ) ? '_blank': '_self';
				$link   = get_theme_mod( 'chique_why_choose_us_link', '#' );
				$text   = get_theme_mod( 'chique_why_choose_us_text' );

				if ( $text ) :
			?>
			<p class="view-all-button">
				<span class="more-button"><a class="more-link" target="<?php echo $target; ?>" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $text ); ?></a></span>
			</p>
			<?php endif; ?>

		</div><!-- .section-content-wrapper -->
	</div><!-- .wrapper -->
</div><!-- #why-choose-us-section -->
