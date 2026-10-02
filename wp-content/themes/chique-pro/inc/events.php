<?php
/**
 * The template for displaying the Events
 *
 * @package Chique
 */


if ( ! function_exists( 'chique_events_display' ) ) :
	/**
	* Add Events
	*
	* @uses action hook chique_before_content.
	*
	* @since Chique Pro 1.0
	*/
	function chique_events_display() {
		$enable = get_theme_mod( 'chique_events_option', 'disabled' );

		if ( chique_check_section( $enable ) ) {
			$title          = get_theme_mod( 'chique_events_headline', esc_html( 'Upcoming Events', 'chique-pro' ) );
			$sub_title      = get_theme_mod( 'chique_events_subheadline', esc_html( 'Discover our newest albums and singles', 'chique-pro' ) );
			$content_select = get_theme_mod( 'chique_events_type', 'category' );
			$background = get_theme_mod( 'chique_events_bg_image', trailingslashit( esc_url( get_template_directory_uri() ) ) . 'assets/images/events-section-bg.jpg' );
			
			$classes[] = $content_select;

			if ( $background ) {
				$classes[] = 'has-background-image';
			}

			echo '<!-- refreshing cache -->';

			$output ='
				<div id="events-section" class="section ' . esc_attr( implode( ' ', $classes ) ) . '">
					<div class="wrapper">';
						if ( $title || $sub_title ) {
							$output .='<div class="section-heading-wrapper events-section-headline">';

							if ( '' !== $title ) {
								$output .='<div class="section-title-wrapper"><h2 class="section-title">' . wp_kses_post( $title ) . '</h2></div>';
							}

							if ( $sub_title )  {
								$sub_title =apply_filters( 'the_content', $sub_title );
								$output .='<div class="section-description-wrapper section-subtitle">' . wp_kses_post( str_replace( ']]>', ']]&gt;', $sub_title ) ) . '</div>';
							}

							$output .='</div><!-- .section-heading-wrap -->';
						}

							$output .='<div class="section-content-container">';							
							$output .='
						<div class="events-content-wrapper section-content-wrapper">';							
							$output .='<div class="events-article-wrapper">';
						
							if ( 'post' === $content_select || 'page' === $content_select || 'category' === $content_select ) {
								$output .= chique_post_page_category_events();
							} elseif ( 'custom' === $content_select ) {
								$output .= chique_custom_events();
							}
							
							$output .='</div><!-- .events-article-wrapper -->';

			$target = get_theme_mod( 'chique_events_target' ) ? '_blank': '_self';
			$link   = get_theme_mod( 'chique_events_link', '#' );
			$text   = get_theme_mod( 'chique_events_text', esc_html__( 'View More', 'chique-pro' ) );
			$output .='</div><!-- .section-content-wrap -->';
			$output .='</div><!-- .section-content-container -->';

			if ( $text ) {
				$output .= '
				<p class="view-more">
					<a class="button" target="' . $target . '" href="' . esc_url( $link ) . '">' . esc_html( $text ) . '</a>
				</p>';
			}
					$output .='</div><!-- .wrapper -->
				</div><!-- #events-section -->';

			echo $output;
		}
	}
endif;

if ( ! function_exists( 'chique_post_page_category_events' ) ) :
	/**
	 * Display Page/Post/Category Events
	 *
	 * @since Chique Pro 1.0
	 */
	function chique_post_page_category_events() {
		global $post;

		$quantity   = get_theme_mod( 'chique_events_number', 3 );
		$no_of_post = 0; // for number of posts
		$post_list  = array();// list of valid post/page ids
		$type       = get_theme_mod( 'chique_events_type', 'category' );
		$output     = '';

		$args = array(
			'post_type'           => 'any',
			'orderby'             => 'post__in',
			'ignore_sticky_posts' => 1 // ignore sticky posts
		);

		//Get valid number of posts
		if ( 'post' == $type || 'page' == $type  ) {
			for( $i = 1; $i <= $quantity; $i++ ){
				$post_id = '';

				if ( 'post' == $type ) {
					$post_id = get_theme_mod( 'chique_events_post_' . $i );
				} elseif ( 'page' == $type ) {
					$post_id = get_theme_mod( 'chique_events_page_' . $i ) ;
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
		} elseif ( 'category' == $type ) {
			$no_of_post = $quantity;

			if ( get_theme_mod( 'chique_events_select_category' ) ) {
				$args['category__in'] = (array) get_theme_mod( 'chique_events_select_category' );
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
				<article id="event-post-' . esc_attr( $loop->current_post + 1 ) . '" class="event-list-item post hentry post">
					<div class="entry-container">';

				if ( get_theme_mod( 'chique_events_display_date', 1 ) ) {
					$event_date_day        = get_the_date( 'j' );
					$event_date_month      = get_the_date( 'M' );
					$event_date_day_meta   = get_post_meta( $post->ID, 'chique-event-date-day', true );
					$event_date_month_meta = get_post_meta( $post->ID, 'chique-event-date-month', true );

					if ( '' !== $event_date_day_meta ) {
						$event_date_day = $event_date_day_meta;
					}

					if ( '' !== $event_date_month_meta ) {
						$event_date_month = $event_date_month_meta;
					}

					$event_date_month = date( 'M', mktime(0, 0, 0, absint( $event_date_month ), 10 ) );

					$output .= '<div class="entry-meta"><span class="posted-on"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark"><time class="entry-date"><span class="date-week-day">' . esc_html( $event_date_month ) . '</span><span class="date-month">' . esc_html( $event_date_day ) . '</span></time></a></span></div>';
				}

				$output .= '<div class="event-list-description">';

				if ( get_theme_mod( 'chique_events_enable_title', 1 ) ) {
					$output .= '
					<div class="event-title">
						<h2 class="entry-title">
							' . the_title( '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">','</a>', false ) . '
						</h2>
					</div>';
				}

				$text = get_theme_mod( 'chique_events_individual_text_' . absint( $loop->current_post + 1 )  );

				$content = apply_filters( 'the_content', get_the_content() );
				$content = str_replace( ']]>', ']]&gt;', $content );

				$output .= '<div class="entry-summary">' . wp_kses_post( $content ) . '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark" class="more-link"><span class="readmore">' . esc_html( $text ) . '</span></a></div><!-- .entry-summary -->';

				$output .= '</div><!-- .event-list-description -->';				

				$output .= '
					</div><!-- .entry-container -->
				</article><!-- .event-post-' . esc_attr( $loop->current_post + 1 ) . ' -->';
			} //endwhile

		wp_reset_postdata();

		return $output;
	}
endif; // chique_post_page_category_events


if ( ! function_exists( 'chique_custom_events' ) ) :
	/**
	 * Display Custom Events
	 *
	 * @since Chique Pro 1.0
	 */
	function chique_custom_events() {
		$quantity = get_theme_mod( 'chique_events_number', 3 );
		$output   = '';

		for ( $i = 1; $i <= $quantity; $i++ ) {
			$target = get_theme_mod( 'chique_events_target_' . $i ) ? '_blank' : '_self';

			$link = get_theme_mod( 'chique_events_link_' . $i, '#' );

			//support qTranslate plugin
			if ( function_exists( 'qtrans_convertURL' ) ) {
				$link = qtrans_convertURL( $link );
			}

			$title   = get_theme_mod( 'chique_events_title_' . $i );
			$content = get_theme_mod( 'chique_events_content_' . $i );
			$text    = get_theme_mod( 'chique_events_individual_text_' . $i );

			if ( class_exists( 'Polylang' ) ) {
				$title = pll__( $title );
			}

			$date_day = get_theme_mod( 'chique_events_date_day_' . $i );

			$date_month = get_theme_mod( 'chique_events_date_month_' . $i );

			if ( $date_month ) {
				// Convert 1 to Jan, 2 to Feb and so on
				$date_month = date( 'M', mktime(0, 0, 0, $date_month, 10 ) );
			}

			$output .= '
				<article id="event-post-' . esc_html( $i ) . '" class="event-list-item post hentry image">
					<div class="entry-container">';

					if ( $date_day || $date_month ) {
						$output .= '<div class="entry-meta"><span class="posted-on"><a target="' . $target . '" href="' . esc_url( $link ) . '" rel="bookmark"><time class="entry-date"><span class="date-week-day">' . esc_html( $date_month ) . '</span><span class="date-month">' . esc_html( $date_day ) . '</span></time></a></span></div>';
					}

					if ( $title || $content || $text ) {
						$output .= '<div class="event-list-description">';
					}

					if ( $title ) {
						$output .= '
								<div class="event-title">
									<h2 class="entry-title">
										' . wp_kses_post( $title ) . '
									</h2>
								</div>';
					}

					
						
					if ( $content || $text ) {
						$output .= '<div class="entry-summary">';

						if ( $content ) {
							$output .= '<p>' . $content . '</p>';
						}
						
						if ( $text ) {
							$output .= '<a href="' . esc_url( $link ) . '" target="' . $target . '" rel="bookmark" class="more-link"><span class="readmore">' . esc_html( $text ) . '</span></a>';
						}

						$output .= '</div><!-- .entry-summary -->';
						
					}

					if ( $title || $content ) {
						$output .= '</div><!-- .event-list-description -->';
					}



				$output .= '
					</div><!-- .entry-container -->
				</article><!-- .event-post-' . esc_attr( $i ) . ' -->';
		}
		return $output;
	}
endif; //chique_custom_events
