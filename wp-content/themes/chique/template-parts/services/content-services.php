<?php
/**
 * The template for displaying services posts on the front page
 *
 * @package Chique
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="hentry-inner">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="post-thumbnail" style="background-image: url( <?php the_post_thumbnail_url( 'chique-hero-content' ); ?> );">
				<a class="cover-link" href="<?php the_permalink(); ?>"></a>
		</div>
	<?php else : ?>
		<div class="post-thumbnail" style="background-image: url( <?php echo trailingslashit( esc_url( get_template_directory_uri() ) ) . 'assets/images/no-thumb-825x825.jpg';?> )">
				<a class="cover-link" href="<?php the_permalink(); ?>"></a>
		</div>
	<?php endif; ?>

		<div class="entry-container">
			<header class="entry-header">
				<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">','</a></h2>' ); ?>
			</header>

			<?php
				$excerpt = get_the_excerpt();

				echo '<div class="entry-summary"><p>' . $excerpt . '</p></div><!-- .entry-summary -->';
			?>
		</div><!-- .entry-container -->
	</div> <!-- .hentry-inner -->
</article> <!-- .article -->
