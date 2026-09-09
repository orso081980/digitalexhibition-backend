<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Chique
 */
?>

<?php
$grid_style = get_theme_mod( 'chique_blog_style', 0 );

if ( $grid_style ) : ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class('grid-item'); ?>>
<?php else : ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php endif; ?>
	<div class="hentry-inner">
		<?php if ( has_post_thumbnail() ) : ?>
		<div class="post-thumbnail">
			<a href="<?php the_permalink(); ?>" rel="bookmark">
				<?php
				$thumbnail = 'post-thumbnail';

				if ( $grid_style ) {
					$thumbnail = 'chique-grid-blog';
				}

				$layout  = chique_get_theme_layout();

				if ( 'no-sidebar-full-width' === $layout && ! $grid_style ) {
					$thumbnail = 'chique-slider';
				}

				the_post_thumbnail( $thumbnail );
				?>
			</a>
		</div>
		<?php endif; ?>

		<div class="entry-container">
			<?php if ( is_sticky() ) : ?>
			<span class="sticky-label"><?php esc_html_e( 'Featured', 'chique-pro' ); ?></span>
			<?php endif; ?>

			<header class="entry-header">
				<?php
				if ( is_singular() ) :
					the_title( '<h1 class="entry-title">', '</h1>' );
				else :
					the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
				endif; 

				if ( 'post' === get_post_type() ) : ?>
					<div class="entry-meta">
							<?php chique_blog_entry_meta(); ?>
					</div><!-- .entry-meta -->
				<?php endif; ?>
			</header><!-- .entry-header -->
			
			<?php
            $show_content = get_theme_mod( 'chique_archive_content_show', 'excerpt' );
			
			if ( 'excerpt' === $show_content ) {
				?>
				<div class="entry-summary"><?php the_excerpt(); ?></div><!-- .entry-summary -->
				<?php
			} elseif ( 'full-content' === $show_content ) {
				?>
				<div class="entry-content"><?php the_content(); ?></div><!-- .entry-content -->
				<?php
			} ?>
		</div> <!-- .entry-container -->
	</div> <!-- .hentry-inner -->
</article><!-- #post-<?php the_ID(); ?> -->
