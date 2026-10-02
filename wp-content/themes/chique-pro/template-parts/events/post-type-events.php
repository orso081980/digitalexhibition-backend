<?php
/**
 * The template used for displaying slider
 *
 * @package Chique
 */
$quantity     = get_theme_mod( 'chique_events_number', 4 );
$no_of_post   = 0; // for number of posts
$post_list    = array(); // list of valid post/page ids
$type         = get_theme_mod( 'chique_events_type', 'category' );
$show_content = get_theme_mod( 'chique_events_content_show', 'hide-content' );
$show_meta    = get_theme_mod( 'chique_events_meta_show', 'show-meta' );

$args = array(
	'post_type'           => 'any',
	'ignore_sticky_posts' => 1, // ignore sticky posts
);

//Get valid number of posts
if ( 'post' === $type || 'page' === $type || 'ect-event' === $type ) {
	for ( $i = 1; $i <= $quantity; $i++ ) {
		$post_id = '';
		$start_time = get_theme_mod( 'chique_events_start_time_' . $i );
		$end_time   = get_theme_mod( 'chique_events_end_time_' . $i ); 

		if ( 'post' === $type ) {
			$post_id = get_theme_mod( 'chique_events_post_' . $i );
		} elseif ( 'page' === $type ) {
			$post_id = get_theme_mod( 'chique_events_page_' . $i );
		} elseif( 'ect-event' === $type ) {
			$post_id =  get_theme_mod( 'chique_events_cpt_' . $i );
		}

		if ( $post_id && '' !== $post_id ) {
			$post_list = array_merge( $post_list, array( $post_id ) );

			$no_of_post++;
		}
	}

	$args['post__in'] = $post_list;
	$args['orderby'] = $post__in;
} elseif ( 'category' === $type ) {
	$no_of_post = $quantity;

	$args['category__in'] = get_theme_mod( 'chique_events_select_category' );

	$args['post_type'] = 'post';
} elseif ( 'tag' === $type ) {
	$no_of_post = $quantity;

	$args['tag__in'] = get_theme_mod( 'chique_events_select_tag' );

	$args['post_type'] = 'post';
}

if ( ! $no_of_post ) {
	return;
}

$args['posts_per_page'] = $no_of_post; ?>

<div class="section-content-wrapper event-content-wrapper events owl-carousel">
<?php
$loop = new WP_Query( $args );

$dates = array();

$i=1;
while ( $loop->have_posts() ) :
	$loop->the_post();

	$classes = 'post post-' . get_the_ID() . ' hentry slides';

	$event_date        = get_theme_mod( 'chique_events_date_' . $i );
	$event_time        = get_theme_mod( 'chique_events_time_' . $i );
	$tab_heading = get_theme_mod( 'chique_events_tabs_'. $i );

	$sep ='';

	if( 'ect-event' == $type ) {
		$location 	 = get_post_meta( get_the_ID(), 'ect_event_location', true );
		$event_date  = get_post_meta( get_the_ID(), 'ect_event_date', true );
		$tab_heading = get_post_meta( get_the_ID(), 'ect_event_text', true );
		$link 		 = get_post_meta( get_the_ID(), 'ect_event_url', true );
		$date 		 = date_create( $event_date ); 
		$final_date  = date_format($date,"d M, Y");
		$start_time  = get_post_meta( get_the_ID(), 'ect_event_time_from', true );
		$end_time 	 = get_post_meta( get_the_ID(), 'ect_event_time_to', true );

		if( $start_time && $end_time ) {
			$sep = ' - ';
		}
		$event_time  = $start_time . $sep . $end_time;
	} else {
		$event_date  = get_theme_mod( 'chique_events_date_' . $i );
		$event_time  = get_theme_mod( 'chique_events_time_' . $i );
		$tab_heading = get_theme_mod( 'chique_events_tabs_'. $i );
	}

	$dates[] = $tab_heading;

	$thumbnail = 'chique-slider';
	?>
	<article class="<?php echo esc_attr( $classes ); ?>">
		<div class="hentry-inner">
			<?php chique_post_thumbnail( $thumbnail, 'html', true, true ); ?>

			<div class="entry-container-wrap">
				<div class="entry-container">
					<header class="entry-header">
						<?php if( $event_time || $event_date ) : ?>
							<div class="entry-meta">
								<?php if( 'ect-event' == $type && $event_date ) :  ?>
										<span class="posted-on">
											<a href="<?php echo esc_url( $link ); ?>" rel="bookmark">
												<?php echo esc_html( $final_date ); ?>
											</a>
									 	</span>	
									<?php else : ?>
										<span class="posted-on">
											<a href="<?php esc_url( get_permalink() ); ?>" rel="bookmark">
												<?php echo esc_html( $event_date ); ?>
											</a>
										</span>
									<?php endif; ?>
								
								<?php 
								if( $event_time ) : 
									if( $event_date && $event_time ) : ?>
										<span class="sep">|</span>
									<?php endif; ?>
									<span class="event-time"> 
										<?php if( 'ect-event' == $type ) : ?>
											<a href="<?php echo esc_urL( $link ); ?>">
												<?php echo esc_html( $event_time ); ?>
											</a>
										<?php else : ?>
											<a href="<?php the_permalink(); ?>">
												<?php echo esc_html( $event_time ); ?>
											</a>
									<?php endif; ?>
									</span>
								<?php endif; ?>
							</div>
						<?php endif; ?>


						<h2 class="entry-title">
							<a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>">
								<?php the_title(); ?>
							</a>
						</h2>
					</header>

					<?php
					if( 'ect-event' == $type ) {
						echo '<div class="entry-content">' . wp_kses_post( $location ) . '</div><!-- .entry-content -->';
					} else {
						if ( 'excerpt' === $show_content ) {
							echo '<div class="entry-summary"><p>' . wp_kses_post( get_the_excerpt() ) . '</p></div><!-- .entry-summary -->';
						} elseif ( 'full-content' === $show_content ) {	
							$content = apply_filters( 'the_content', get_the_content() );
							$content = str_replace( ']]>', ']]&gt;', $content );
							echo '<div class="entry-content">' . wp_kses_post( $content ) . '</div><!-- .entry-content -->';
						}
					}
					?>
				</div><!-- .entry-container -->
			</div> <!-- .entry-container-wrap -->
		</div><!-- .hentry-inner -->
	</article><!-- .slides -->
<?php
$i++;
endwhile;

wp_reset_postdata(); ?>
</div><!-- .section-content-wrapper -->

<?php if( get_theme_mod( 'chique_events_dots', 1) ) : ?>
	<ul id='events-dots' class='owl-dots'>
		<?php
		foreach ( $dates as $date ) {
			echo '<li class="owl-dot"><span>' .esc_attr( $date ) . '</span></li> ';
		}
		?>
	</ul>
<?php endif; ?>

<ul id='events-nav' class='owl-nav'>
</ul>
