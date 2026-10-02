<?php
/**
 * The template for displaying the Timeline
 *
 * @package Chique
 */


if ( ! function_exists( 'chique_timeline_display' ) ) :
	/**
	* Add Timeline
	*
	* @uses action hook chique_before_content.
	*
	* @since Chique Pro 1.0
	*/
	function chique_timeline_display() {
		$enable = get_theme_mod( 'chique_timeline_option', 'disabled' );

		if ( chique_check_section( $enable ) ) {
			$title          = get_theme_mod( 'chique_timeline_headline', esc_html( 'Timeline', 'chique-pro' ) );
			$sub_title      = get_theme_mod( 'chique_timeline_subheadline' );
			$content_select = get_theme_mod( 'chique_timeline_type', 'category' );

			echo '<!-- refreshing cache -->';

			$classes = $content_select ;

			$output ='
				<div id="timeline-section" class="section ' . esc_attr( $classes ) . '">
					<div class="wrapper">';
						if ( $title || $sub_title ) {
							$output .='<div class="section-heading-wrapper">';

							if ( '' !== $title ) {
								$output .='<div class="section-title-wrapper"><h2 class="section-title">' . wp_kses_post( $title ) . '</h2></div>';
							}

							if ( $sub_title )  {
								$output .='<div class="section-description"><p class="section-subtitle">' . wp_kses_post( $sub_title ) . '</p></div>';
							}

							$output .='</div><!-- .section-heading-wrap -->';
						}

						$output .='
						<div class="section-content-wrapper">';
							// Select content
							if ( 'post' === $content_select || 'page' === $content_select || 'category' === $content_select ) {
								$output .= chique_post_page_category_timeline();
							} elseif ( 'custom' === $content_select ) {
								$output .= chique_custom_timeline();
							}

			$output .='
						</div><!-- .section-content-wrapper -->
					</div><!-- .wrapper -->
				</div><!-- #timeline-section -->';

			echo $output;
		}
	}
endif;

if ( ! function_exists( 'chique_post_page_category_timeline' ) ) :
	/**
	 * Display Page/Post/Category Timeline
	 *
	 * @since Chique Pro 1.0
	 */
	function chique_post_page_category_timeline() {
		global $post;

		$quantity     = get_theme_mod( 'chique_timeline_number', 4 );
		$no_of_post   = 0; // for number of posts
		$post_list    = array();// list of valid post/page ids
		$type         = get_theme_mod( 'chique_timeline_type', 'category' );
		$show_content = get_theme_mod( 'chique_timeline_show', 'full-content' );
		$output       = '';

		$args = array(
			'post_type'           => 'any',
			'ignore_sticky_posts' => 1 // ignore sticky posts
		);

		//Get valid number of posts
		if ( 'post' == $type || 'page' == $type  ) {
			for( $i = 1; $i <= $quantity; $i++ ){
				$post_id = '';

				if ( 'post' == $type ) {
					$post_id = get_theme_mod( 'chique_timeline_post_' . $i );
				} elseif ( 'page' == $type ) {
					$post_id = get_theme_mod( 'chique_timeline_page_' . $i ) ;
				}

				if ( $post_id ) {
					if ( class_exists( 'Polylang' ) ) {
						$post_id = pll_get_post( $post_id, pll_current_language() );
					}

					$post_list = array_merge( $post_list, array( $post_id ) );

					$no_of_post++;
				}
			}

			$args['post__in'] = $post_list;
			$args['orderby']  = $post__in;
		} elseif ( 'category' == $type ) {
			$no_of_post = $quantity;

			if ( get_theme_mod( 'chique_timeline_select_category' ) ) {
				$args['category__in'] = (array) get_theme_mod( 'chique_timeline_select_category' );
			}

			$args['post_type'] = 'post';
		}

		if ( 0 == $no_of_post ) {
			return;
		}

		$args['posts_per_page'] = $no_of_post;

		$loop = new WP_Query( $args );

		while ( $loop->have_posts() ) {
			$loop->the_post();

			$title_attribute = the_title_attribute( 'echo=0' );
			$output .= '
				<article id="timeline-post-' . esc_attr( $loop->current_post + 1 ) . '" class="post hentry post">
					<div class="hentry-inner">
						<div class="post-thumbnail">
							<a href="' . esc_url( get_the_permalink() ) . '">';

							if ( has_post_thumbnail() ) {
								$output .= get_the_post_thumbnail( null, 'chique-grid-blog');
							} else {
								$output .= '<img class="wp-post-image" src="' . trailingslashit( esc_url ( get_template_directory_uri() ) ) . 'assets/images/no-thumb-666x499.jpg" >';
							}

							$output .='
							</a>
						</div>';

					$event_date = date( 'd F, Y', strtotime( get_theme_mod( 'chique_events_timeline_date_' . absint( $loop->current_post + 1 ) ) ) );

				$output .=	'<div class="entry-container">';

				if( get_theme_mod( 'chique_timeline_display_date', 1 ) || get_theme_mod( 'chique_timeline_display_title', 1 ) ) {
					$output .= '<header class="entry-header">';
				}	
				
				if( get_theme_mod( 'chique_timeline_display_date', 1 ) ) {
					$output .= '<div class="entry-meta">
									<span class="posted-on">
										<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">
											<time class="entry-date">' . esc_html( $event_date ) . '</time>
										</a>
									</span>
								</div>';
				}

				if ( get_theme_mod( 'chique_timeline_display_title', 1 ) ) {
					$output .= '
								<h2 class="entry-title">
									' . the_title( '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">','</a>', false ) . '
								</h2>';
				}

				if( get_theme_mod( 'chique_timeline_display_date', 1 ) || get_theme_mod( 'chique_timeline_display_title', 1 ) ) {
					$output .= '</header>';
				}

				if ( 'excerpt' === $show_content ) {
					$excerpt = get_the_excerpt();

					$output .= '<div class="entry-summary"><p>' . $excerpt . '</p></div><!-- .entry-summary -->';
				} elseif ( 'full-content' === $show_content ) {
					$content = apply_filters( 'the_content', get_the_content() );
					$content = str_replace( ']]>', ']]&gt;', $content );
					$output .= '<div class="entry-content">' . wp_kses_post( $content ) . '</div><!-- .entry-content -->';
				}

				$output .= '
						</div><!-- .entry-container -->
					</div><!-- .hentry-inner -->
				</article><!-- .timeline-post -->';
			} //endwhile

		wp_reset_postdata();

		return $output;
	}
endif; // chique_post_page_category_timeline


if ( ! function_exists( 'chique_custom_timeline' ) ) :
	/**
	 * Display Custom Timeline
	 *
	 * @since Chique Pro 1.0
	 */
	function chique_custom_timeline() {
		$quantity = get_theme_mod( 'chique_timeline_number', 4 );
		$output   = '';

		for ( $i = 1; $i <= $quantity; $i++ ) {
			$target = get_theme_mod( 'chique_timeline_target_' . $i ) ? '_blank' : '_self';

			$link = get_theme_mod( 'chique_timeline_link_' . $i, '#' );

			//support qTranslate plugin
			if ( function_exists( 'qtrans_convertURL' ) ) {
				$link = qtrans_convertURL( $link );
			}

			$title = get_theme_mod( 'chique_timeline_title_' . $i );

			if ( class_exists( 'Polylang' ) ) {
				$title = pll__( esc_attr( $title ) );
			}

			$date_day = get_theme_mod( 'chique_timeline_date_day_' . $i );

			$date_month = get_theme_mod( 'chique_timeline_date_month_' . $i );

			if ( $date_month ) {
				// Convert 1 to Jan, 2 to Feb and so on
				$date_month = date( 'M', mktime(0, 0, 0, $date_month, 10 ) );
			}

			$output .= '
				<article id="event-post-' . esc_html( $i ) . '" class="post hentry image">
					<div class="hentry-inner">';

			$image = get_theme_mod( 'chique_events_timeline_image_' . $i ) ?  get_theme_mod( 'chique_events_timeline_image_' . $i ) : trailingslashit( esc_url ( get_template_directory_uri() ) ) . 'assets/images/no-thumb-666x499.jpg';

			if ( $image ) {
				$output .= '
						<div class="post-thumbnail">
							<a href="' . esc_url( $link ) . '">
								<img class="wp-post-image" src="' . esc_url( $image ) . '" >
							</a>
						</div>';
			}

			$output .= '
					<div class="entry-container">';

					$event_date = date( 'd F, Y', strtotime( get_theme_mod( 'chique_events_timeline_date_' . absint( $i ) ) ) );

					if ( $title || $event_date ) {
						$output .= '
								<header class="entry-header">';

						if ( $event_date ) {
							$output .= '<div class="entry-meta"><span class="posted-on"><a target="' . $target . '" href="' . esc_url( $link ) . '" rel="bookmark"><time class="entry-date">' . esc_html( $event_date ) . '</time></a></span></div>';
						}

						if( $title ) {
							$output .= '<h2 class="entry-title">
											' . wp_kses_post( $title ) . '
										</h2>';
						}

						$output .= '</header>';
					}									

					$content = get_theme_mod( 'chique_timeline_content_' . $i );

					if ( $content ) {
						$output .= '<div class="entry-content">
									<p>' . $content . '</p>
								</div><!-- .entry-summary -->';
					}

				$output .='
					</div><!-- .entry-container -->
				</article><!-- .event-post-' . esc_attr( $i ) . ' -->';
		}
		return $output;
	}
endif; //chique_custom_timeline
