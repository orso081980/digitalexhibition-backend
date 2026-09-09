<?php
/**
 * The template for displaying album content
 *
 * @package Chique
 */
?>

<?php
$enable_content = get_theme_mod( 'chique_album_option', 'disabled' );

if ( ! chique_check_section( $enable_content ) ) {
	// Bail if album content is disabled.
	return;
}

$type = get_theme_mod( 'chique_album_type', 'category' );

if ( 'custom' !== $type ) {
	$album_posts = chique_get_album_posts();

	if ( empty( $album_posts ) ) {
		return;
	}
}

$title     = get_theme_mod( 'chique_album_title', esc_html__( 'Our Album', 'chique-pro' ) );
$sub_title = get_theme_mod( 'chique_album_sub_title' );

$layout = get_theme_mod( 'chique_album_layout', 'layout-three' );
$text_align  = get_theme_mod( 'chique_album_text_align', 'text-aligned-left' );

$classes[] = 'album-section';
$classes[] = 'section';
$classes[] = $text_align;

if ( ! $title && ! $sub_title ) {
	$classes[] = 'no-section-heading';
}

?>

<div id="album-content-section" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="wrapper">

		<?php if ( '' !== $title || '' !== $sub_title ) : ?>
			<div class="section-heading-wrapper">
				<?php if ( '' !== $title ) : ?>
					<div class="section-title-wrapper">
						<h2 class="section-title"><?php echo wp_kses_post( $title ); ?></h2>
					</div><!-- .page-title-wrapper -->
				<?php endif; ?>

				<?php if ( $sub_title ) : ?>
					<div class="section-description">
						<?php
						$subtitle = apply_filters( 'the_content', $sub_title );
						echo wp_kses_post( str_replace( ']]>', ']]&gt;', $sub_title ) );
						?>
					</div><!-- .section-description -->
				<?php endif; ?>
			</div><!-- .section-heading-wrapper -->
		<?php endif; ?>

		<div class="album-content-wrapper section-content-wrapper <?php echo esc_attr( $layout ); ?>">

			<?php
			if ( 'custom' === $type ) {
				get_template_part( 'template-parts/album/content', 'custom' );
			} else {	
				get_template_part( 'template-parts/album/post-types-album' );
			}
			?>

			<?php
				$target = get_theme_mod( 'chique_album_target' ) ? '_blank': '_self';
				$link   = get_theme_mod( 'chique_album_link', '#' );
				$text   = get_theme_mod( 'chique_album_text' );

				if ( $text ) :
			?>

			<p class="view-all-button">
				<span class="more-button">
					<a class="more-link" target="<?php echo $target; ?>" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $text ); ?></a>
				</span>
			</p>
			<?php endif; ?>

		</div><!-- .album-wrapper -->
	</div><!-- .wrapper -->
</div><!-- #album-section -->
