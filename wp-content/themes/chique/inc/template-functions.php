<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Chique
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function chique_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}
	
	$classes[] = 'navigation-classic';

	// Adds a class with respect to layout selected.
	$layout  = chique_get_theme_layout();
	$sidebar = chique_get_sidebar_id();

	if ( 'no-sidebar' === $layout ) {
		$classes[] = 'no-sidebar content-width-layout';
	}
	elseif ( 'no-sidebar-full-width' === $layout ) {
		$classes[] = 'no-sidebar full-width-layout';
	} elseif ( 'left-sidebar' === $layout ) {
		if ( '' !== $sidebar ) {
			$classes[] = 'two-columns-layout content-right';
		}
	} elseif ( 'right-sidebar' === $layout ) {
		if ( '' !== $sidebar ) {
			$classes[] = 'two-columns-layout content-left';
		}
	}

	$header_media_title    = get_theme_mod( 'chique_header_media_title' );
	$header_media_subtitle = get_theme_mod( 'chique_header_media_subtitle' );
	$header_media_text     = get_theme_mod( 'chique_header_media_text' );
	$header_media_url      = get_theme_mod( 'chique_header_media_url' );
	$header_media_url_text = get_theme_mod( 'chique_header_media_url_text' );

	$header_image = chique_featured_overall_image();

	if ( '' == $header_image ) {
		$classes[] = 'no-header-media-image';
		
	}

	//Blog Style
	$classes[] = 'single-blog';

	$header_text_enabled = chique_has_header_media_text();

	if ( ! $header_text_enabled ) {
		$classes[] = 'no-header-media-text';
	}

	$enable_slider = chique_check_section( get_theme_mod( 'chique_slider_option', 'disabled' ) );

	if ( ! $enable_slider ) {
		$classes[] = 'no-featured-slider';
	}

	if ( '' == $header_image && ! $header_text_enabled && ! $enable_slider ) {
		$classes[] = 'content-has-padding-top';
	}

	// Add Color Scheme to Body Class.
	$classes['color-scheme'] = esc_attr( 'color-scheme-' . get_theme_mod( 'color_scheme', 'default' ) );

	return $classes;
}
add_filter( 'body_class', 'chique_body_classes' );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function chique_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'chique_pingback_header' );

/**
 * Adds Hero Content background CSS
 */
function chique_hero_content_bg_css() {
	$background = get_theme_mod( 'chique_hero_content_bg_image' );

	$css = '';

	if ( $background ) {
		$image = ' background-image: url("' . esc_url( $background ) . '");';

		// Background Scroll.
		$attachment = 'scroll';
		
		$attachment = ' background-attachment: ' . esc_attr( $attachment ) . ';';

		$css = $image . $attachment;
	}


	if ( '' !== $css ) {
		$css = '.hero-content-wrapper { ' . $css . '}';
	}

	wp_add_inline_style( 'chique-style', $css );
}
add_action( 'wp_enqueue_scripts', 'chique_hero_content_bg_css', 11 );

/**
 * Adds header image overlay for each section
 */
function chique_header_image_overlay_css() {
	$css = '';

	$homepage_css = '';

	$homepage_overlay = get_theme_mod( 'chique_header_media_homepage_opacity', '0' );

	$homepage_overlay_bg = $homepage_overlay / 100;

	if ( '0' !== $homepage_overlay_bg ) {
		$homepage_css = '.home .custom-header:after { background-color: rgba(0, 0, 0, ' . esc_attr( $homepage_overlay_bg ) . '); } '; // Dividing by 100 as the option is shown as % for user
	}

	$overlay = get_theme_mod( 'chique_header_media_except_homepage_opacity', '50');

	$overlay_bg = $overlay / 100;

	if ( '0' !== $overlay_bg ) {
		$css = 'body:not(.home) .custom-header:after { background-color: rgba(0, 0, 0, ' . esc_attr( $overlay_bg ) . '); } '; // Dividing by 100 as the option is shown as % for user
	}
	
	wp_add_inline_style( 'chique-style', $homepage_css );
	wp_add_inline_style( 'chique-style', $css );
}
add_action( 'wp_enqueue_scripts', 'chique_header_image_overlay_css', 11 );

/**
 * Remove first post from blog as it is already show via recent post template
 */
function chique_alter_home( $query ) {
	if ( $query->is_home() && $query->is_main_query() ) {
		$cats = get_theme_mod( 'chique_front_page_category' );

		if ( is_array( $cats ) && ! in_array( '0', $cats ) ) {
			$query->query_vars['category__in'] = $cats;
		}
	}
}
add_action( 'pre_get_posts', 'chique_alter_home' );

if ( ! function_exists( 'chique_content_nav' ) ) :
	/**
	 * Display navigation/pagination when applicable
	 *
	 * @since Chique 0.1
	 */
	function chique_content_nav() {
		global $wp_query;

		// Don't print empty markup in archives if there's only one page.
		if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) ) {
			return;
		}

		$pagination_type = get_theme_mod( 'chique_pagination_type', 'default' );

		/**
		 * Check if navigation type is Jetpack Infinite Scroll and if it is enabled, else goto default pagination
		 * if it's active then disable pagination
		 */
		if ( ( 'infinite-scroll' === $pagination_type ) && class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'infinite-scroll' ) ) {
			return false;
		}

		if ( 'numeric' === $pagination_type && function_exists( 'the_posts_pagination' ) ) {
			the_posts_pagination( array(
				'prev_text'          => esc_html__( 'Previous', 'chique' ),
				'next_text'          => esc_html__( 'Next', 'chique' ),
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__( 'Page', 'chique' ) . ' </span>',
			) );
		} else {
			the_posts_navigation();
		}
	}
endif; // chique_content_nav

/**
 * Check if a section is enabled or not based on the $value parameter
 * @param  string $value Value of the section that is to be checked
 * @return boolean return true if section is enabled otherwise false
 */
function chique_check_section( $value ) {
	global $wp_query;

	// Get Page ID outside Loop
	$page_id = absint( $wp_query->get_queried_object_id() );

	// Front page displays in Reading Settings
	$page_for_posts = absint( get_option( 'page_for_posts' ) );

	return ( 'entire-site' == $value  || ( ( is_front_page() || ( is_home() && $page_for_posts !== $page_id ) ) && 'homepage' == $value ) );
}

/**
 * Return the first image in a post. Works inside a loop.
 * @param [integer] $post_id [Post or page id]
 * @param [string/array] $size Image size. Either a string keyword (thumbnail, medium, large or full) or a 2-item array representing width and height in pixels, e.g. array(32,32).
 * @param [string/array] $attr Query string or array of attributes.
 * @return [string] image html
 *
 * @since Chique 0.1
 */

function chique_get_first_image( $postID, $size, $attr, $src = false ) {
	ob_start();

	ob_end_clean();

	$image 	= '';

	$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', get_post_field( 'post_content', $postID ) , $matches );

	if( isset( $matches[1][0] ) ) {
		//Get first image
		$first_img = $matches[1][0];

		if ( $src ) {
			//Return url of src is true
			return $first_img;
		}

		return '<img class="pngfix wp-post-image" src="' . $first_img . '">';
	}

	return false;
}

function chique_get_theme_layout() {
	$layout = '';

	if ( is_page_template( 'templates/no-sidebar.php' ) ) {
		$layout = 'no-sidebar';
	} elseif ( is_page_template( 'templates/right-sidebar.php' ) ) {
		$layout = 'right-sidebar';
	} else {
		$layout = get_theme_mod( 'chique_default_layout', 'right-sidebar' );

		if ( is_front_page() ) {
			$layout = get_theme_mod( 'chique_homepage_layout', 'no-sidebar' );
		} elseif ( is_home() || is_archive() || is_search() ) {
			$layout = get_theme_mod( 'chique_archive_layout', 'right-sidebar' );
		}
	}

	return $layout;
}

function chique_get_sidebar_id() {
	$sidebar = '';

	$layout = chique_get_theme_layout();

	$sidebaroptions = '';

	if ( 'no-sidebar' === $layout ) {
		return $sidebar;
	}

	global $post, $wp_query;

	// Front page displays in Reading Settings.
	$page_on_front  = get_option( 'page_on_front' );
	$page_for_posts = get_option( 'page_for_posts' );

	// Get Page ID outside Loop.
	$page_id = $wp_query->get_queried_object_id();

	// Blog Page or Front Page setting in Reading Settings.
	if ( $page_id == $page_for_posts || $page_id == $page_on_front ) {
		$sidebaroptions = get_post_meta( $page_id, 'chique-sidebar-option', true );
	} elseif ( is_singular() ) {
		if ( is_attachment() ) {
			$parent 		= $post->post_parent;
			$sidebaroptions = get_post_meta( $parent, 'chique-sidebar-option', true );

		} else {
			$sidebaroptions = get_post_meta( $post->ID, 'chique-sidebar-option', true );
		}
	}

	if ( is_active_sidebar( 'sidebar-1' ) ) {
		$sidebar = 'sidebar-1'; // Primary Sidebar.
	}

	return $sidebar;
}

/**
 * Featured content posts
 */
function chique_get_featured_posts() {

	$number = get_theme_mod( 'chique_featured_content_number', 3 );

	$post_list    = array();

	$args = array(
		'posts_per_page'      => $number,
		'post_type'           => 'post',
		'ignore_sticky_posts' => 1, // ignore sticky posts.
	);

	// Get valid number of posts.

	$args['post_type'] = 'featured-content';

	for ( $i = 1; $i <= $number; $i++ ) {
		$post_id = '';

		$post_id = get_theme_mod( 'chique_featured_content_cpt_' . $i );
		

		if ( $post_id && '' !== $post_id ) {
			$post_list = array_merge( $post_list, array( $post_id ) );
		}
	}

	$args['post__in'] = $post_list;
	$args['orderby']  = 'post__in';

	$featured_posts = get_posts( $args );

	return $featured_posts;
}


/**
 * Services content posts
 */
function chique_get_services_posts() {
	$number = get_theme_mod( 'chique_service_number', 3 );

	$post_list    = array();

	$args = array(
		'posts_per_page'      => $number,
		'post_type'           => 'post',
		'ignore_sticky_posts' => 1, // ignore sticky posts.
	);

	// Get valid number of posts.

	$args['post_type'] = 'ect-service';

	for ( $i = 1; $i <= $number; $i++ ) {
		$post_id = '';

		$post_id = get_theme_mod( 'chique_service_cpt_' . $i );
		

		if ( $post_id && '' !== $post_id ) {
			$post_list = array_merge( $post_list, array( $post_id ) );
		}
	}

	$args['post__in'] = $post_list;
	$args['orderby']  = 'post__in';

	$services_posts = get_posts( $args );

	return $services_posts;
}

if ( ! function_exists( 'chique_sections' ) ) :
	/**
	 * Display Sections on header and footer with respect to the section option set in chique_sections_sort
	 */
	function chique_sections( $selector = 'header' ) {
		get_template_part( 'template-parts/header/header', 'media' );
		get_template_part( 'template-parts/slider/display', 'slider' );
		get_template_part( 'template-parts/hero-content/content','hero' );
		get_template_part( 'template-parts/portfolio/display', 'portfolio' );
		get_template_part( 'template-parts/services/display', 'services' );
		get_template_part( 'template-parts/testimonials/display', 'testimonial' );
		get_template_part( 'template-parts/featured-content/display', 'featured' );
	}
endif;

if ( ! function_exists( 'chique_post_thumbnail' ) ) :
	/**
	 * $image_size post thumbnail size
	 * $type html, html-with-bg, url
	 * $echo echo true/false
	 * $no_thumb display no-thumb image or not
	 */
	function chique_post_thumbnail( $image_size = 'post-thumbnail', $type = 'html', $echo = true, $no_thumb = false ) {
		$image = $image_url = '';

		if ( has_post_thumbnail() ) {
			$image_url = get_the_post_thumbnail_url( get_the_ID(), $image_size );
			$image     = get_the_post_thumbnail( get_the_ID(), $image_size );
		} else {
			if ( $no_thumb ) {
				global $_wp_additional_image_sizes;

				$image_url  = trailingslashit( get_template_directory_uri() ) . 'assets/images/no-thumb-' . $_wp_additional_image_sizes[ $image_size ]['width'] . 'x' . $_wp_additional_image_sizes[ $image_size ]['height'] . '.jpg';
				$image      = '<img src="' . esc_url( $image_url ) . '" alt="" />';
			}

			// Get the first image in page, returns false if there is no image.
			$first_image_url = chique_get_first_image( get_the_ID(), $image_size, '', true );

			// Set value of image as first image if there is an image present in the page.
			if ( $first_image_url ) {
				$image_url = $first_image_url;
				$image = '<img class="wp-post-image" src="'. esc_url( $image_url ) .'">';
			}
		}

		if ( ! $image_url ) {
			// Bail if there is no image url at this stage.
			return;
		}

		if ( 'url' === $type ) {
			return $image_url;
		}

		$output = '<div';

		if ( 'html-with-bg' === $type ) {
			$output .= ' class="post-thumbnail-background" style="background-image: url( ' . esc_url( $image_url ) . ' )"';
		} else {
			$output .= ' class="post-thumbnail"';
		}

		$output .= '>';

		if ( 'html-with-bg' !== $type ) {
			$output .= '<a href="' . esc_url( get_the_permalink() ) . '" title="' . the_title_attribute( 'echo=0' ) . '">' . $image;
		} else {
			$output .= '<a class="cover-link" href="' . esc_url( get_the_permalink() ) . '" title="' . the_title_attribute( 'echo=0' ) . '">';
		}

		$output .= '</a></div><!-- .post-thumbnail -->';

		if ( ! $echo ) {
			return $output;
		}

		echo $output;
	}
endif;

if ( ! function_exists( 'chique_testimonial_posts_args' ) ) :

	function chique_testimonial_posts_args() {
		$number = get_theme_mod( 'chique_testimonial_number', 5 );

		$args = array(
			'ignore_sticky_posts' => 1 // ignore sticky posts
		);

		$post_list  = array();// list of valid post/page ids

		$args['post_type'] = 'jetpack-testimonial';

			for ( $i = 1; $i <= $number; $i++ ) {
				$post_id = '';

				$post_id =  get_theme_mod( 'chique_testimonial_cpt_' . $i );

				if ( $post_id && '' !== $post_id ) {
					// Polylang Support.
					if ( class_exists( 'Polylang' ) ) {
						$post_id = pll_get_post( $post_id, pll_current_language() );
					}

					$post_list = array_merge( $post_list, array( $post_id ) );

				}
			}

			$args['post__in'] = $post_list;
			$args['orderby'] = 'post__in';

		$args['posts_per_page'] = $number;

		return $args;
	}
endif;
