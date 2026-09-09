<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Chique
 */

if ( ! function_exists( 'chique_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function chique_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: post date */
			__( '<span class="date-label"> </span>%s', 'chique' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		// Get the author name; wrap it in a link.
		$byline = sprintf(
			/* translators: %s: post author */
			__( '<span class="author-label screen-reader-text">By </span>%s', 'chique' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="byline screen-reader-text"> ' . $byline . '</span><span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.

	}
endif;

if ( ! function_exists( 'chique_entry_category' ) ) :
	/**
	 * Prints HTML with meta information for the category.
	 */
	function chique_entry_category( $echo = true ) {
		$output = '';

		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( ' ' );
			if ( $categories_list ) {
				/* translators: 1: list of categories. */
				$output = sprintf( '<span class="cat-links">%1$s%2$s</span>',
					sprintf( _x( '<span class="cat-text screen-reader-text">Categories</span>', 'Used before category names.', 'chique' ) ),
					$categories_list
				); // WPCS: XSS OK.
			}
		}

		if ( 'ect-service' === get_post_type() || 'featured-content' === get_post_type() || 'jetpack-portfolio' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$term_list = get_the_term_list( get_the_ID(), get_post_type() . '-type' );
			if ( $term_list ) {
				/* translators: 1: list of categories. */
				$output = sprintf( '<span class="cat-links">%1$s%2$s</span>',
					sprintf( _x( '<span class="cat-text screen-reader-text">Categories</span>', 'Used before category names.', 'chique' ) ),
					$term_list
				); // WPCS: XSS OK.
			}
		}

		if ( $echo ) {
			echo $output;
		} else {
			return $output;
		}
	}
endif;

if ( ! function_exists( 'chique_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function chique_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( ' ' );
			if ( $categories_list ) {
				/* translators: 1: list of categories. */
				printf( '<span class="cat-links">%1$s%2$s</span>',
					sprintf( _x( '<span class="cat-text screen-reader-text">Categories</span>', 'Used before category names.', 'chique' ) ),
					$categories_list
				); // WPCS: XSS OK.
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list();
			if ( $tags_list ) {
				/* translators: 1: list of tags. */
				printf( '<span class="tags-links">%1$s%2$s</span>',
					sprintf( _x( '<span class="tags-text screen-reader-text">Tags</span>', 'Used before tag names.', 'chique' ) ),
					$tags_list
				); // WPCS: XSS OK.
			}
		}

		/*if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link(
				sprintf(
					wp_kses(*/
						/* translators: %s: post title */
						/*__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'chique' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					get_the_title()
				)
			);
			echo '</span>';
		}*/

		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'chique' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;

if ( ! function_exists( 'chique_blog_entry_meta_left' ) ) :
	/**
	 * Prints HTML with meta information for author and tag.
	 */
	function chique_blog_entry_meta() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			printf(
				/* translators: %s: post author */
				__( '<span class="byline"><span class="author-label screen-reader-text">By </span>%s', 'chique' ),
				'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span></span>'
			);

			$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
			if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
				$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
			}

			$time_string = sprintf( $time_string,
				esc_attr( get_the_date( 'c' ) ),
				esc_html( get_the_date() ),
				esc_attr( get_the_modified_date( 'c' ) ),
				esc_html( get_the_modified_date() )
			);

			printf(
				/* translators: %s: post date */
				__( '<span class="posted-on"><span class="date-label"> </span>%s', 'chique' ),
				'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a></span>'
			);
		}
	}
endif;

if ( ! function_exists( 'chique_author_bio' ) ) :
	/**
	 * Prints HTML with meta information for the author bio.
	 */
	function chique_author_bio() {
		if ( '' !== get_the_author_meta( 'description' ) ) {
			get_template_part( 'template-parts/biography' );
		}
	}
endif;

if ( ! function_exists( 'chique_header_title' ) ) :
	/**
	 * Display Header Media Title
	 */
	function chique_header_title() {
		$post_title = get_theme_mod( 'chique_recent_posts_heading', esc_html__( 'Blog', 'chique' ) );

		if ( is_front_page() ) {
			$subtitle = get_theme_mod( 'chique_header_media_subtitle' ) ? '<span class="sub-title">' . wp_kses_post( get_theme_mod( 'chique_header_media_subtitle', esc_html__( 'Women Collection', 'chique' ) ) ) . '</span><!-- .sub-title -->' : '';

			echo wp_kses_post( get_theme_mod( 'chique_header_media_title' ) ) . $subtitle;
		} elseif ( is_singular() ) {
			the_title();
		} elseif ( is_404() ) {
			esc_html_e( 'Oops! That page can&rsquo;t be found.', 'chique' );
		} elseif ( is_search() ) {
			/* translators: %s: search query. */
			printf( esc_html__( 'Search Results for: %s', 'chique' ), '<span>' . get_search_query() . '</span>' );
		} elseif( is_home() ) {
			echo esc_html( $post_title );
		}
		else {
			the_archive_title();
		}
	}
endif;

if ( ! function_exists( 'chique_header_text' ) ) :
	/**
	 * Display Header Media Text
	 */
	function chique_header_text() {
		if ( is_front_page() ) {
			$content = get_theme_mod( 'chique_header_media_text' );

			if ( $header_media_url = get_theme_mod( 'chique_header_media_url' ) ) {
				$target = get_theme_mod( 'chique_header_url_target' ) ? '_blank' : '_self';

				$content .= '<span class="more-button"><a href="'. esc_url( $header_media_url ) . '" target="' . $target . '" class="more-link">' .esc_html( get_theme_mod( 'chique_header_media_url_text' ) ) . '<span class="screen-reader-text">' . wp_kses_post( get_theme_mod( 'chique_header_media_title' ) ) . '</span></a></span>';
			}

			$content = apply_filters( 'the_content', $content );
			$content = str_replace( ']]>', ']]&gt;', $content );

			echo '<div class="entry-summary">' . wp_kses_post( $content ) . '</div>';
		} elseif ( is_404() ) {
			esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'chique' );
		} else {
			the_archive_description();
		}
	}
endif;

if ( ! function_exists( 'chique_single_image' ) ) :
	/**
	 * Display Single Page/Post Image
	 */
	function chique_single_image() {
		global $post, $wp_query;

		// Get Page ID outside Loop
		$page_id = $wp_query->get_queried_object_id();
		if ( $post) {
	 		if ( is_attachment() ) {
				$parent = $post->post_parent;
				$metabox_feat_img = get_post_meta( $parent,'chique-featured-image', true );
			} else {
				$metabox_feat_img = get_post_meta( $page_id,'chique-featured-image', true );
			}
		}

		if ( empty( $metabox_feat_img ) || ( !is_page() && !is_single() ) ) {
			$metabox_feat_img = 'default';
		}

		$featured_image = 'disabled';

		if ( ( 'disabled' == $metabox_feat_img  || ! has_post_thumbnail() || ( 'default' == $metabox_feat_img && 'disabled' == $featured_image ) ) ) {
			echo '<!-- Page/Post Single Image Disabled or No Image set in Post Thumbnail -->';
			return false;
		}
		else {
			$class = '';

			if ( 'default' == $metabox_feat_img ) {
				$class = $featured_image;
			}
			else {
				$class = 'from-metabox ' . $metabox_feat_img;
				$featured_image = $metabox_feat_img;
			}

			?>
			<div class="post-thumbnail <?php echo esc_attr( $class ); ?>">
                <?php the_post_thumbnail( $featured_image ); ?>
	        </div>
	   	<?php
		}
	}
endif;

if ( ! function_exists( 'chique_comment' ) ) :
	/**
	 * Template for comments and pingbacks.
	 * Used as a callback by wp_list_comments() for displaying the comments.
	 */
	function chique_comment( $comment, $args, $depth ) {
		if ( 'pingback' == $comment->comment_type || 'trackback' == $comment->comment_type ) : ?>

		<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
			<div class="comment-body">
				<?php esc_html_e( 'Pingback:', 'chique' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( esc_html__( 'Edit', 'chique' ), '<span class="edit-link">', '</span>' ); ?>
			</div>

		<?php else : ?>

		<li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>
			<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">

				<div class="comment-author-container">
					<div class="comment-author vcard">
						<?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
					</div><!-- .comment-author -->
				</div><!-- .comment-container -->

				<div class="comment-container">
					<div class="comment-header">
						<header class="comment-meta">
						<?php printf( __( '%s <span class="says screen-reader-text">says:</span>', 'chique' ), sprintf( '<cite class="fn author-name">%s</cite>', get_comment_author_link() ) ); ?>
						</header><!-- .comment-meta -->
						<div class="comment-metadata">
									<a class="comment-permalink" href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
								<time datetime="<?php comment_time( 'c' ); ?>">
									<?php
										printf(
											/* translators: Comment Date at Comment Time */
											esc_html__( '%1$s at %2$s', 'chique' ),
											get_comment_time( get_option( 'date_format' ) ),
											get_comment_time( get_option( 'time_format' ) )
										);
									?>
								</time></a>
							<?php edit_comment_link( esc_html__( 'Edit', 'chique' ), '<span class="edit-link">', '</span>' ); ?>

							<?php if ( '0' == $comment->comment_approved ) : ?>
								<p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'chique' ); ?></p>
							<?php endif; ?>
						</div> <!-- .comment-metadata -->
					</div><!-- .comment-header -->

					<div class="comment-content">
						<?php comment_text(); ?>
					</div><!-- .comment-content -->


					<div class="comment-metadata">
						<?php
							comment_reply_link( array_merge( $args, array(
								'add_below' => 'div-comment',
								'depth'     => $depth,
								'max_depth' => $args['max_depth'],
								'before'    => '<span class="reply">',
								'after'     => '</span>',
							) ) );
						?>
					</div><!-- .comment-metadata -->

				</div><!-- .comment-container -->
			</article><!-- .comment-body -->
		<?php /* No closing </li> is needed.  WordPress will know where to add it. */ ?>

		<?php
		endif;
	}
endif; // ends check for chique_comment()

if ( ! function_exists( 'chique_slider_entry_category' ) ) :
/**
 * Prints HTML with category and tags for current post.
 *
 * Create your own chique_entry_category_date() function to override in a child theme.
 *
 * @since Chique Pro 1.0
 */
function chique_slider_entry_category() {
	$meta = '<div class="entry-meta">';

	$portfolio_categories_list = get_the_term_list( get_the_ID(), 'jetpack-portfolio-type', '<span class="portfolio-entry-meta entry-meta">', '', '</span>' );

	if ( 'jetpack-portfolio' === get_post_type( ) ) {
		$meta .= sprintf( '<span class="cat-links">' .'<span class="cat-label screen-reader-text">%1$s</span>%2$s</span>',
			sprintf( _x( 'Categories', 'Used before category names.', 'chique' ) ),
			$portfolio_categories_list
		);
	}

	$categories_list = get_the_category_list( ' ' );
	if ( $categories_list && chique_categorized_blog( ) ) {
		$meta .= sprintf( '<span class="cat-links">' . '<span class="cat-label screen-reader-text">%1$s</span>%2$s</span>',
			sprintf( _x( 'Categories', 'Used before category names.', 'chique' ) ),
			$categories_list
		);
	}

	$meta .= '</div><!-- .entry-meta -->';

	return $meta;

}
endif;

if ( ! function_exists( 'chique_categorized_blog' ) ) :
	/**
	 * Determines whether blog/site has more than one category.
	 *
	 * Create your own chique_categorized_blog() function to override in a child theme.
	 *
	 * @since Chique Pro 1.0
	 *
	 * @return bool True if there is more than one category, false otherwise.
	 */
	function chique_categorized_blog() {
		if ( false === ( $all_the_cool_cats = get_transient( 'chique_categories' ) ) ) {
			// Create an array of all the categories that are attached to posts.
			$all_the_cool_cats = get_categories( array(
				'fields'     => 'ids',
				// We only need to know if there is more than one category.
				'number'     => 2,
			) );

			// Count the number of categories that are attached to the posts.
			$all_the_cool_cats = count( $all_the_cool_cats );

			set_transient( 'chique_categories', $all_the_cool_cats );
		}

		if ( $all_the_cool_cats > 1 ) {
			// This blog has more than 1 category so chique_categorized_blog should return true.
			return true;
		} else {
			// This blog has only 1 category so chique_categorized_blog should return false.
			return false;
		}
	}
endif;

if ( ! function_exists( 'chique_portfolio_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function chique_portfolio_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: post date */
			__( '<span class="date-label"> </span>%s', 'chique' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		echo '<span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.
	}
endif;
