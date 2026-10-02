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

	if ( 'classic' == get_theme_mod( 'chique_menu_style', 'classic' ) ) {
		$classes[] = 'navigation-classic';

		if ( get_theme_mod( 'chique_primary_subtitle_popup_disable' ) ) {
			$classes[] = 'primary-subtitle-popup-disable';
		}
	} else {
		$classes[] = 'navigation-default';
	}

	// Blog Style
	if ( get_theme_mod( 'chique_blog_style', 0 ) ) {
		$classes[] = 'grid-blog';
	} else {
		$classes[] = 'single-blog';
	}

	if( get_theme_mod( 'chique_blog_style', 0 ) && get_theme_mod( 'chique_blog_style_boxed', 1 ) ) {
			$classes[] = 'grid-blog-fluid';
	}

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

	$header_media_title    = get_theme_mod( 'chique_header_media_title', esc_html__( 'Spring Fantasy', 'chique-pro' ) );
	$header_media_subtitle = get_theme_mod( 'chique_header_media_subtitle', esc_html__( 'Women Collection', 'chique-pro' ) );
	$header_media_text     = get_theme_mod( 'chique_header_media_text' );
	$header_media_url      = get_theme_mod( 'chique_header_media_url', esc_html__( '#', 'chique-pro' ) );
	$header_media_url_text = get_theme_mod( 'chique_header_media_url_text', esc_html__( 'Shop Now', 'chique-pro' ) );

	$header_image = chique_featured_overall_image();

	if ( '' == $header_image ) {
		$classes[] = 'no-header-media-image';
	}

	$header_text_enabled = chique_has_header_media_text();

	if ( ! $header_text_enabled ) {
		$classes[] = 'no-header-media-text';
	}

	//Sticky Playlist Position
	$sticky_playlist = get_theme_mod( 'chique_sticky_playlist_visibility', 'disabled' );
	$position = get_theme_mod( 'chique_sticky_playlist_position', 'top' );

	if( 'disabled' !== $sticky_playlist ) {
		$classes[] = 'sticky-playlist-enabled';

		if( 'bottom' == $position ) {
			$classes[] = 'sticky-playlist-bottom';
		}
		else {
			$classes[] = 'sticky-playlist-top';
		}
	}

	$enable_slider = chique_check_section( get_theme_mod( 'chique_slider_option', 'disabled' ) );

	if ( ! $enable_slider ) {
		$classes[] = 'no-featured-slider';
	}

	if ( '' == $header_image && ! $header_text_enabled && ! $enable_slider ) {
		$classes[] = 'content-has-padding-top';
	}

	if( get_theme_mod( 'chique_shopping_cart', 0 ) ) {
		$classes[] = 'header-shopping-cart';
	}

	if( get_theme_mod( 'chique_floating_shopping_cart', 0 ) ) {
		$classes[] = 'floating-shopping-cart';
	}

	if( class_exists( 'WooCommerce' ) && ( is_shop() || is_archive( 'product' ) ) && get_theme_mod( 'chique_full_width_layout', 1 ) ) {
		$classes[] = 'woo-products-full-width';
	}

	// Add Color Scheme to Body Class.
	$classes[] = esc_attr( 'color-scheme-' . get_theme_mod( 'color_scheme', 'default' ) );

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

if ( ! function_exists( 'chique_comments' ) ) :
	/**
	 * Enable/Disable Comments
	 *
	 * @uses comment_form_default_fields filter
	 * @since Chique Pro 1.0
	 */
	function chique_comments( $open, $post_id ) {
		$comment_select = get_theme_mod( 'chique_comment_option', 'use-wordpress-setting' );

		if( 'disable-completely' === $comment_select ) {
			return false;
		} elseif( 'disable-in-pages' === $comment_select && is_page() ) {
			return false;
		}

		return $open;
	}
endif; // chique_comments.
add_filter( 'comments_open', 'chique_comments', 10, 2 );

if ( ! function_exists( 'chique_comment_form_fields' ) ) :
	/**
	 * Modify Comment Form Fields
	 *
	 * @uses comment_form_default_fields filter
	 * @since Chique Pro 1.0
	 */
	function chique_comment_form_fields( $fields ) {
		$disable_website = get_theme_mod( 'chique_website_field', 1 );

		if ( isset( $fields['url'] ) && ! $disable_website ) {
			unset( $fields['url'] );
		}

		return $fields;
	}
endif; // chique_comment_form_fields.
add_filter( 'comment_form_default_fields', 'chique_comment_form_fields' );

/**
 * Adds font family custom CSS
 */
function chique_get_font_family_css() {
	$font_family_options = chique_font_family_options();

	$fonts = chique_avaliable_fonts();

	$css = array();

	foreach ( $font_family_options as $key => $value ) {
		$option = get_theme_mod( $key );
		if ( $option ) {
			$css[] = $value['selector'] . ' { font-family: ' . $fonts [ $option ]['label'] . '; }';
		}
	}

	$css = implode( PHP_EOL, $css );

	wp_add_inline_style( 'chique-style', $css );
}
add_action( 'wp_enqueue_scripts', 'chique_get_font_family_css', 11 );

/**
 * Adds Promotion Headline BG CSS
 */
function chique_promo_headline_bg_css() {
	$enable_section = get_theme_mod( 'chique_promotion_headline_visibility', 'homepage' );

	if ( ! chique_check_section( $enable_section ) ) {
		// Bail if promotion_headline content is not enabled
		return;
	}

	$type = get_theme_mod( 'chique_promotion_headline_type', 'page' );
	$css = '';

	if ( 'page' === $type || 'post' === $type || 'category' === $type ) {
		if ( 'page' === $type && $id = get_theme_mod( 'chique_promotion_headline_page' ) ) {
			$id = absint( $id );
		} elseif ( 'post' === $type && $id = get_theme_mod( 'chique_promotion_headline_post' ) ) {
			$id = absint( $id );
		} elseif ( 'category' === $type && $cat = get_theme_mod( 'chique_promotion_headline_category' ) ) {
			$args['cat']            = absint( $cat );
			$args['posts_per_page'] = 1;

			$post_id = get_posts( $args );

			$id = $post_id[0]->ID;
		}

		if ( has_post_thumbnail( $id ) ) {
			$css = '
				#promotion-headline {
					background: url(\'' . get_the_post_thumbnail_url( $id, 'chique-slider' ) . '\');
					background-attachment: scroll;
					background-repeat: no-repeat;
					background-size: cover;
					background-position: center center;
				}';
		}

	} else {
		$image = get_theme_mod( 'chique_promotion_headline_image' );

		if ( $image ) {
			$css = '
				#promotion-headline {
					background: url(\'' . esc_url($image ) . '\');
					background-attachment: scroll;
					background-repeat: no-repeat;
					background-size: cover;
					background-position: center center;
				}';
		}
	}

	wp_add_inline_style( 'chique-style', $css );
}
add_action( 'wp_enqueue_scripts', 'chique_promo_headline_bg_css', 11 );

/**
 * Adds Reservation BG CSS
 */
function chique_reservation_bg_css() {
	$enable_desc = get_theme_mod( 'chique_reservation_info_option', 'disabled' );
	$enable_form = get_theme_mod( 'chique_reservation_option', 'disabled' );

	if ( ! chique_check_section( $enable_desc ) && ! chique_check_section( $enable_form ) ) {
		// Bail if featured content is disabled.
		return;
	}

	$css = '';

	$image = get_theme_mod( 'chique_reservation_bg_image' );

	if ( $image ) {
		$css = '
			.reserve-content-wrapper {
				background: url(\'' . esc_url($image ) . '\');
				background-attachment: scroll;
				background-repeat: no-repeat;
				background-size: cover;
				background-position: center center;
			}';
	}

	wp_add_inline_style( 'chique-style', $css );
}
add_action( 'wp_enqueue_scripts', 'chique_reservation_bg_css', 11 );

/**
 * Adds Hero Content background CSS
 */
function chique_hero_content_bg_css() {
	$background = get_theme_mod( 'chique_hero_content_bg_image' );

	$css = '';

	if ( $background ) {
		$image = ' background-image: url("' . esc_url( $background ) . '");';

		// Background Position.
		$position_x = get_theme_mod( 'chique_hero_content_bg_position_x' );
		$position_y = get_theme_mod( 'chique_hero_content_bg_position_y' );

		if ( ! in_array( $position_x, array( 'left', 'center', 'right' ), true ) ) {
			$position_x = 'left';
		}

		if ( ! in_array( $position_y, array( 'top', 'center', 'bottom' ), true ) ) {
			$position_y = 'top';
		}

		$position = ' background-position: ' . esc_attr( $position_x ) . ' ' . esc_attr( $position_y ) . ';';

		// Background Repeat.
		$repeat = get_theme_mod( 'chique_hero_content_bg_repeat', 'repeat' );

		$repeat = ' background-repeat: ' . esc_attr( $repeat ) . ';';

		// Background Scroll.
		$attachment = get_theme_mod( 'chique_hero_content_bg_attachment', 1 );

		if ( $attachment ) {
			$attachment = 'scroll';
		} else {
			$attachment = 'fixed';
		}

		$attachment = ' background-attachment: ' . esc_attr( $attachment ) . ';';

		// Background Size.
		$size = get_theme_mod( 'chique_hero_content_bg_size', 'auto' );

		$size =  ' background-size: ' . esc_attr( $size ) . ';';

		$css = $image . $position . $repeat . $attachment . $size;
	}


	if ( '' !== $css ) {
		$css = '.hero-content-wrapper { ' . $css . '}';
	}

	wp_add_inline_style( 'chique-style', $css );
}
add_action( 'wp_enqueue_scripts', 'chique_hero_content_bg_css', 11 );

/**
 * Adds Gallery background CSS
 */
function chique_gallery_bg_css() {
	$background = get_theme_mod( 'chique_gallery_bg_image' );

	$css = '';

	if ( $background ) {
		$image = ' background-image: url("' . esc_url( $background ) . '");';

		// Background Position.
		$position_x = get_theme_mod( 'chique_gallery_bg_position_x' );
		$position_y = get_theme_mod( 'chique_gallery_bg_position_y' );

		if ( ! in_array( $position_x, array( 'left', 'center', 'right' ), true ) ) {
			$position_x = 'left';
		}

		if ( ! in_array( $position_y, array( 'top', 'center', 'bottom' ), true ) ) {
			$position_y = 'top';
		}

		$position = ' background-position: ' . esc_attr( $position_x ) . ' ' . esc_attr( $position_y ) . ';';

		// Background Repeat.
		$repeat = get_theme_mod( 'chique_gallery_bg_repeat', 'repeat' );

		$repeat = ' background-repeat: ' . esc_attr( $repeat ) . ';';

		// Background Scroll.
		$attachment = get_theme_mod( 'chique_gallery_bg_attachment', 1 );

		if ( $attachment ) {
			$attachment = 'scroll';
		} else {
			$attachment = 'fixed';
		}

		$attachment = ' background-attachment: ' . esc_attr( $attachment ) . ';';

		// Background Size.
		$size = get_theme_mod( 'chique_gallery_bg_size', 'auto' );

		$size =  ' background-size: ' . esc_attr( $size ) . ';';

		$css = $image . $position . $repeat . $attachment . $size;
	}


	if ( '' !== $css ) {
		$css = '.gallery-section { ' . $css . '}';
	}

	wp_add_inline_style( 'chique-style', $css );
}
add_action( 'wp_enqueue_scripts', 'chique_gallery_bg_css', 11 );

/**
 * Adds testimonial background CSS
 */
function chique_woo_products_showcase_bg_css() {
	$background = get_theme_mod( 'chique_woo_products_showcase_bg_image' );

	$css = '';

	if ( $background ) {
		$image = ' background-image: url("' . esc_url( $background ) . '");';

		// Background Position.
		$position_x = get_theme_mod( 'chique_woo_products_showcase_bg_position_x' );
		$position_y = get_theme_mod( 'chique_woo_products_showcase_bg_position_y' );

		if ( ! in_array( $position_x, array( 'left', 'center', 'right' ), true ) ) {
			$position_x = 'left';
		}

		if ( ! in_array( $position_y, array( 'top', 'center', 'bottom' ), true ) ) {
			$position_y = 'top';
		}

		$position = ' background-position: ' . esc_attr( $position_x ) . ' ' . esc_attr( $position_y ) . ';';

		// Background Repeat.
		$repeat = get_theme_mod( 'chique_woo_products_showcase_bg_repeat', 'repeat' );

		$repeat = ' background-repeat: ' . esc_attr( $repeat ) . ';';

		// Background Scroll.
		$attachment = get_theme_mod( 'chique_woo_products_showcase_bg_attachment', 1 );

		if ( $attachment ) {
			$attachment = 'scroll';
		} else {
			$attachment = 'fixed';
		}

		$attachment = ' background-attachment: ' . esc_attr( $attachment ) . ';';

		// Background Size.
		$size = get_theme_mod( 'chique_woo_products_showcase_bg_size', 'auto' );

		$size =  ' background-size: ' . esc_attr( $size ) . ';';

		$css = $image . $position . $repeat . $attachment . $size;
	}


	if ( '' !== $css ) {
		$css = '#product-content-section { ' . $css . '}';
	}

	wp_add_inline_style( 'chique-style', $css );
}
add_action( 'wp_enqueue_scripts', 'chique_woo_products_showcase_bg_css', 11 );

/**
 * Adds Team background CSS
 */
function chique_team_bg_css() {
	$background = get_theme_mod( 'chique_team_bg_image' );

	$css = '';

	if ( $background ) {
		$image = ' background-image: url("' . esc_url( $background ) . '");';

		// Background Position.
		$position_x = get_theme_mod( 'chique_team_bg_position_x' );
		$position_y = get_theme_mod( 'chique_team_bg_position_y' );

		if ( ! in_array( $position_x, array( 'left', 'center', 'right' ), true ) ) {
			$position_x = 'left';
		}

		if ( ! in_array( $position_y, array( 'top', 'center', 'bottom' ), true ) ) {
			$position_y = 'top';
		}

		$position = ' background-position: ' . esc_attr( $position_x ) . ' ' . esc_attr( $position_y ) . ';';

		// Background Repeat.
		$repeat = get_theme_mod( 'chique_team_bg_repeat', 'repeat' );

		$repeat = ' background-repeat: ' . esc_attr( $repeat ) . ';';

		// Background Scroll.
		$attachment = get_theme_mod( 'chique_team_bg_attachment', 1 );

		if ( $attachment ) {
			$attachment = 'scroll';
		} else {
			$attachment = 'fixed';
		}

		$attachment = ' background-attachment: ' . esc_attr( $attachment ) . ';';

		// Background Size.
		$size = get_theme_mod( 'chique_team_bg_size', 'auto' );

		$size =  ' background-size: ' . esc_attr( $size ) . ';';

		$css = $image . $position . $repeat . $attachment . $size;
	}


	if ( '' !== $css ) {
		$css = '.team-section { ' . $css . '}';
	}

	wp_add_inline_style( 'chique-style', $css );
}
add_action( 'wp_enqueue_scripts', 'chique_team_bg_css', 11 );

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
 * Update sidebar width
 */
function chique_sidebar_width() {
	$css = '';

	$width = get_theme_mod( 'chique_sidebar_width', '300' );

	if ( '300' !== $width ) {
		$css = '
@media screen and (min-width: 64em) {
	#masthead,
	.navigation-default .header-overlay {
		max-width: ' . esc_attr( $width - 50 ) . 'px;
	}

	.below-site-header {
		left: ' . esc_attr( $width - 50 ) . 'px;
	}

	.menu-open.navigation-default .below-site-header {
		left: ' . esc_attr(  ( $width - 50 ) * 2 ) . 'px;
	}
}

@media screen and (min-width: 100em) {
	#masthead,
	.navigation-default .header-overlay {
		max-width: ' . esc_attr( $width - 30 ) . 'px;
	}

	.below-site-header {
		left: ' . esc_attr( $width - 30 ) . 'px;
	}

	.menu-open.navigation-default .below-site-header {
		left: ' . esc_attr( ( $width - 30 ) * 2 ) . 'px;
	}
}

@media screen and (min-width: 120em) {
	#masthead,
	.navigation-default .header-overlay {
		max-width: ' . esc_attr( $width ) . 'px;
	}

	.below-site-header {
		left: ' . esc_attr( $width ) . 'px;
	}

	.menu-open.navigation-default .below-site-header {
		left: ' . esc_attr( $width * 2 ) . 'px;
	}
}
';
	}

	wp_add_inline_style( 'chique-style', $css );
}
add_action( 'wp_enqueue_scripts', 'chique_sidebar_width', 11 );



/**
 * Remove first post from blog as it is already show via recent post template
 */
function chique_alter_home( $query ) {
	if ( $query->is_home() && $query->is_main_query() ) {
		$cats = get_theme_mod( 'chique_front_page_category' );

		if ( is_array( $cats ) && ! in_array( '0', $cats ) ) {
			$query->query_vars['category__in'] = $cats;
		}

		if ( get_theme_mod( 'chique_exclude_slider_post' ) ) {
			$quantity = get_theme_mod( 'chique_slider_number', 4 );

			$post_list	= array();	// list of valid post ids

			for( $i = 1; $i <= $quantity; $i++ ){
				if ( get_theme_mod( 'chique_slider_post_' . $i ) && get_theme_mod( 'chique_slider_post_' . $i ) > 0 ) {
					$post_list = array_merge( $post_list, array( get_theme_mod( 'chique_slider_post_' . $i ) ) );
				}
			}

			if ( ! empty( $post_list ) ) {
				$query->query_vars['post__not_in'] = $post_list;
			}
		}
	}
}
add_action( 'pre_get_posts', 'chique_alter_home' );

/**
 * Function to add Scroll Up icon
 */
function chique_scrollup() {
	$disable_scrollup = get_theme_mod( 'chique_display_scrollup', 1 );

	if ( ! $disable_scrollup ) {
		return;
	}

	echo '
		<div class="scrollup">
			<a href="#masthead" id="scrollup" class="fa fa-sort-asc" aria-hidden="true"><span class="screen-reader-text">' . esc_html__( 'Scroll Up', 'chique-pro' ) . '</span></a>
		</div>' ;
}
add_action( 'wp_footer', 'chique_scrollup', 1 );

if ( ! function_exists( 'chique_content_nav' ) ) :
	/**
	 * Display navigation/pagination when applicable
	 *
	 * @since Chique Pro 1.0
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
				'prev_text'          => esc_html__( 'Previous', 'chique-pro' ),
				'next_text'          => esc_html__( 'Next', 'chique-pro' ),
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__( 'Page', 'chique-pro' ) . ' </span>',
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
 * @since Chique Pro 1.0
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
	} elseif ( is_page_template( 'templates/full-width-page.php' ) ) {
		$layout = 'no-sidebar-full-width';
	} elseif ( is_page_template( 'templates/left-sidebar.php' ) ) {
		$layout = 'left-sidebar';
	} elseif ( is_page_template( 'templates/right-sidebar.php' ) ) {
		$layout = 'right-sidebar';
	} else {
		$layout = get_theme_mod( 'chique_default_layout', 'right-sidebar' );

		if ( is_front_page() ) {
			$layout = get_theme_mod( 'chique_homepage_layout', 'no-sidebar' );
		} elseif ( is_home() || is_archive() || is_search() ) {
			$layout = get_theme_mod( 'chique_archive_layout', 'right-sidebar' );
		}

		if ( class_exists( 'WooCommerce' ) && ( is_shop() || is_woocommerce() || is_cart() || is_checkout() ) ) {
			$layout = get_theme_mod( 'chique_woocommerce_layout', 'no-sidebar-full-width' );
		}
	}

	return $layout;
}

function chique_get_sidebar_id() {
	$sidebar = '';

	$layout = chique_get_theme_layout();

	$sidebaroptions = '';

	if ( 'no-sidebar-full-width' === $layout || 'no-sidebar' === $layout ) {
		return $sidebar;
	}

	// WooCommerce Shop Page excluding Cart and checkout.
	if ( class_exists( 'WooCommerce' ) && is_woocommerce() ) {
		$shop_id        = get_option( 'woocommerce_shop_page_id' );
		$sidebaroptions = get_post_meta( $shop_id, 'chique-sidebar-options', true );
	} else {
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
	}

	if ( is_active_sidebar( 'woocommerce-sidebar' ) && class_exists( 'WooCommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() ) ) {
		$sidebar = 'woocommerce-sidebar'; // WooCommerce Sidebar.
	} elseif ( is_active_sidebar( 'sidebar-optional-one' ) && 'optional-sidebar-one' === $sidebaroptions ) {
		$sidebar = 'sidebar-optional-one';
	} elseif ( is_active_sidebar( 'sidebar-optional-two' ) && 'optional-sidebar-two' === $sidebaroptions ) {
		$sidebar = 'sidebar-optional-two';
	} elseif ( is_active_sidebar( 'sidebar-optional-three' ) && 'optional-sidebar-three' === $sidebaroptions ) {
		$sidebar = 'sidebar-optional-three';
	} elseif ( is_active_sidebar( 'sidebar-optional-homepage' ) && ( is_front_page() || ( is_home() && $page_id != $page_for_posts ) ) ) {
		$sidebar = 'sidebar-optional-homepage';
	} elseif ( is_active_sidebar( 'sidebar-optional-archive' ) && ( is_archive() || ( is_home() && $page_id != $page_for_posts ) ) ) {
		$sidebar = 'sidebar-optional-archive';
	} elseif ( is_page() && is_active_sidebar( 'sidebar-optional-page' ) ) {
		$sidebar = 'sidebar-optional-page';
	} elseif ( is_single() && is_active_sidebar( 'sidebar-optional-post' ) ) {
		$sidebar = 'sidebar-optional-post';
	} elseif ( is_active_sidebar( 'sidebar-1' ) ) {
		$sidebar = 'sidebar-1'; // Primary Sidebar.
	}

	return $sidebar;
}

/**
 * Featured content posts
 */
function chique_get_featured_posts() {
	$type = get_theme_mod( 'chique_featured_content_type', 'category' );

	$number = get_theme_mod( 'chique_featured_content_number', 3 );

	$post_list    = array();

	$args = array(
		'posts_per_page'      => $number,
		'post_type'           => 'post',
		'ignore_sticky_posts' => 1, // ignore sticky posts.
	);

	// Get valid number of posts.
	if ( 'post' === $type || 'page' === $type || 'featured-content' === $type ) {
		$args['post_type'] = $type;

		for ( $i = 1; $i <= $number; $i++ ) {
			$post_id = '';

			if ( 'post' === $type ) {
				$post_id = get_theme_mod( 'chique_featured_content_post_' . $i );
			} elseif ( 'page' === $type ) {
				$post_id = get_theme_mod( 'chique_featured_content_page_' . $i );
			} elseif ( 'featured-content' === $type ) {
				$post_id = get_theme_mod( 'chique_featured_content_cpt_' . $i );
			}

			if ( $post_id && '' !== $post_id ) {
				$post_list = array_merge( $post_list, array( $post_id ) );
			}
		}

		$args['post__in'] = $post_list;
		$args['orderby']  = 'post__in';
	} elseif ( 'category' === $type && $cat = get_theme_mod( 'chique_featured_content_select_category' ) ) {
		$args['category__in'] = $cat;
	}

	$featured_posts = get_posts( $args );

	return $featured_posts;
}


/**
 * Services content posts
 */
function chique_get_services_posts() {
	$type = get_theme_mod( 'chique_service_type', 'category' );

	$number = get_theme_mod( 'chique_service_number', 3 );

	$post_list    = array();

	$args = array(
		'posts_per_page'      => $number,
		'post_type'           => 'post',
		'ignore_sticky_posts' => 1, // ignore sticky posts.
	);

	// Get valid number of posts.
	if ( 'post' === $type || 'page' === $type || 'ect-service' === $type ) {
		$args['post_type'] = $type;

		for ( $i = 1; $i <= $number; $i++ ) {
			$post_id = '';

			if ( 'post' === $type ) {
				$post_id = get_theme_mod( 'chique_service_post_' . $i );
			} elseif ( 'page' === $type ) {
				$post_id = get_theme_mod( 'chique_service_page_' . $i );
			} elseif ( 'ect-service' === $type ) {
				$post_id = get_theme_mod( 'chique_service_cpt_' . $i );
			}

			if ( $post_id && '' !== $post_id ) {
				$post_list = array_merge( $post_list, array( $post_id ) );
			}
		}

		$args['post__in'] = $post_list;
		$args['orderby']  = 'post__in';
	} elseif ( 'category' === $type && $cat = get_theme_mod( 'chique_service_select_category' ) ) {
		$args['category__in'] = $cat;
	}

	$services_posts = get_posts( $args );

	return $services_posts;
}

/**
 * Team posts
 */
function chique_get_team_posts() {
	$type = get_theme_mod( 'chique_team_type', 'category' );

	$number = get_theme_mod( 'chique_team_number', 5 );

	$post_list    = array();

	$args = array(
		'posts_per_page'      => $number,
		'post_type'           => 'post',
		'ignore_sticky_posts' => 1, // ignore sticky posts.
	);

	// Get valid number of posts.
	if ( 'post' === $type || 'page' === $type || 'ect-team' === $type ) {
		$args['post_type'] = $type;

		for ( $i = 1; $i <= $number; $i++ ) {
			$post_id = '';

			if ( 'post' === $type ) {
				$post_id = get_theme_mod( 'chique_team_post_' . $i );
			} elseif ( 'page' === $type ) {
				$post_id = get_theme_mod( 'chique_team_page_' . $i );
			} elseif ( 'ect-team' === $type ) {
				$post_id = get_theme_mod( 'chique_team_cpt_' . $i );
			}

			if ( $post_id && '' !== $post_id ) {
				$post_list = array_merge( $post_list, array( $post_id ) );
			}
		}

		$args['post__in'] = $post_list;
		$args['orderby']  = 'post__in';
	} elseif ( 'category' === $type && $cat = get_theme_mod( 'chique_team_select_category' ) ) {
		$args['category__in'] = $cat;
	}

	$team_posts = get_posts( $args );

	return $team_posts;
}

/**
 * Album posts
 */
function chique_get_album_posts() {
	$type = get_theme_mod( 'chique_album_type', 'category' );

	$number = get_theme_mod( 'chique_album_number', 3 );

	$post_list    = array();

	$args = array(
		'posts_per_page'      => $number,
		'post_type'           => 'post',
		'ignore_sticky_posts' => 1, // ignore sticky posts.
	);

	// Get valid number of posts.
	if ( 'post' === $type || 'page' === $type ) {
		$args['post_type'] = $type;

		for ( $i = 1; $i <= $number; $i++ ) {
			$post_id = '';

			if ( 'post' === $type ) {
				$post_id = get_theme_mod( 'chique_album_post_' . $i );
			} elseif ( 'page' === $type ) {
				$post_id = get_theme_mod( 'chique_album_page_' . $i );
			}

			if ( $post_id && '' !== $post_id ) {
				$post_list = array_merge( $post_list, array( $post_id ) );
			}
		}

		$args['post__in'] = $post_list;
		$args['orderby']  = 'post__in';
	} elseif ( 'category' === $type && $cat = get_theme_mod( 'chique_album_select_category' ) ) {
		$args['category__in'] = $cat;
	}

	$album_posts = get_posts( $args );

	return $album_posts;
}

/**
 * Stats posts
 */
function chique_get_stats_posts() {
	$type = get_theme_mod( 'chique_stats_type', 'category' );

	$number = get_theme_mod( 'chique_stats_number', 4 );

	$post_list    = array();

	$args = array(
		'posts_per_page'      => $number,
		'post_type'           => 'post',
		'ignore_sticky_posts' => 1, // ignore sticky posts.
	);

	// Get valid number of posts.
	if ( 'post' === $type || 'page' === $type ) {
		$args['post_type'] = $type;

		for ( $i = 1; $i <= $number; $i++ ) {
			$post_id = '';

			if ( 'post' === $type ) {
				$post_id = get_theme_mod( 'chique_stats_post_' . $i );
			} elseif ( 'page' === $type ) {
				$post_id = get_theme_mod( 'chique_stats_page_' . $i );
			}

			if ( $post_id && '' !== $post_id ) {
				$post_list = array_merge( $post_list, array( $post_id ) );
			}
		}

		$args['post__in'] = $post_list;
		$args['orderby']  = 'post__in';
	} elseif ( 'category' === $type && $cat = get_theme_mod( 'chique_stats_select_category' ) ) {
		$args['category__in'] = $cat;
	}

	$stats_posts = get_posts( $args );

	return $stats_posts;
}

/**
 * Stats posts
 */
function chique_get_why_choose_us_posts() {
	$type = get_theme_mod( 'chique_why_choose_us_type', 'category' );

	$number = get_theme_mod( 'chique_why_choose_us_number', 4 );

	$post_list    = array();

	$args = array(
		'posts_per_page'      => $number,
		'post_type'           => 'post',
		'ignore_sticky_posts' => 1, // ignore sticky posts.
	);

	// Get valid number of posts.
	if ( 'post' === $type || 'page' === $type ) {
		$args['post_type'] = $type;

		for ( $i = 1; $i <= $number; $i++ ) {
			$post_id = '';

			if ( 'post' === $type ) {
				$post_id = get_theme_mod( 'chique_why_choose_us_post_' . $i );
			} elseif ( 'page' === $type ) {
				$post_id = get_theme_mod( 'chique_why_choose_us_page_' . $i );
			}

			if ( $post_id && '' !== $post_id ) {
				$post_list = array_merge( $post_list, array( $post_id ) );
			}
		}

		$args['post__in'] = $post_list;
		$args['orderby']  = 'post__in';
	} elseif ( 'category' === $type && $cat = get_theme_mod( 'chique_why_choose_us_select_category' ) ) {
		$args['category__in'] = $cat;
	}

	$stats_posts = get_posts( $args );

	return $stats_posts;
}

if ( ! function_exists( 'chique_enable_homepage_posts' ) ) :
	/**
	 * Determine Homepage Content disabled or not
	 * @return boolean
	 */
	function chique_enable_homepage_posts() {
		if ( ! is_front_page() ) {
			return true;
		}

		if ( get_theme_mod( 'chique_display_homepage_posts', 1 ) ) {
			return true;
		}

		return false;
	}
endif; // chique_enable_homepage_posts.

if ( ! function_exists( 'chique_sections' ) ) :
	/**
	 * Display Sections on header and footer with respect to the section option set in chique_sections_sort
	 */
	function chique_sections( $selector = 'header' ) {
		$sections = get_theme_mod( 'chique_sections_sort', chique_get_default_sections_value() );

		$separated_sections = explode( 'main-content,', $sections );

		$sections = $separated_sections[0];

		if ( 'footer' === $selector ) {
			$sections = $separated_sections[1];
		}

		$sections =  ! empty( $sections ) ? explode( ',', $sections ) : array();

		foreach ( $sections as $section ) {
			$all_sections = chique_get_sortable_sections();

			if ( isset( $all_sections[ $section ]['template-part'] ) ) {
				get_template_part( $all_sections[ $section ]['template-part'] );
			}
		}
	}
endif;

if ( ! function_exists( 'chique_get_sortable_sections' ) ) :
	/**
	 * Returns list of sortable sections
	 */
	function chique_get_sortable_sections() {
		$sortable_sections = array(
			'header-media'       => array(
				'label'         => esc_html__( 'Header Media', 'chique-pro' ),
				'section'       => 'header_image',
				'template-part' => 'template-parts/header/header-media',
			),
			'slider'             => array(
				'label'         => esc_html__( 'Slider', 'chique-pro' ),
				'section'       => 'chique_featured_slider',
				'template-part' => 'template-parts/slider/display-slider',
			),
			'events'             => array(
				'label'         => esc_html__( 'Events', 'chique-pro' ),
				'section'       => 'chique_events',
				'template-part' => 'template-parts/events/display-events',
			),
			'hero-content'       => array(
				'label'         => esc_html__( 'Hero Content', 'chique-pro' ),
				'section'       => 'chique_hero_content_options',
				'template-part' => 'template-parts/hero-content/content-hero'
			),
			'countdown'       => array(
				'label'         => esc_html__( 'Countdown', 'chique-pro' ),
				'section'       => 'chique_countdown',
				'template-part' => 'template-parts/countdown/display-countdown',
			),
			'venue'       => array(
				'label'         => esc_html__( 'Venue', 'chique-pro' ),
				'section'       => 'chique_venue',
				'template-part' => 'template-parts/venue/display-venue',
			),
			'why-choose-us' => array(
				'label'         => esc_html__( 'Why Choose Us', 'chique-pro' ),
				'section'       => 'chique_why_choose_us',
				'template-part' => 'template-parts/why-choose-us/display-choose',
			),
			'portfolio' => array(
				'label'         => esc_html__( 'Portfolio', 'chique-pro' ),
				'section'       => 'chique_portfolio',
				'template-part' => 'template-parts/portfolio/display-portfolio',
			),
			'stats'       => array(
				'label'         => esc_html__( 'Stats', 'chique-pro' ),
				'section'       => 'chique_stats',
				'template-part' => 'template-parts/stats/display-stats',
			),
			'featured-video'           => array(
				'label'         => esc_html__( 'Featured Video', 'chique-pro' ),
				'section'       => 'chique_featured_video',
				'template-part' => 'template-parts/featured-video/display-featured',
			),
			'services'           => array(
				'label'         => esc_html__( 'Services', 'chique-pro' ),
				'section'       => 'chique_service',
				'template-part' => 'template-parts/services/display-services',
			),
			'album'           => array(
				'label'         => esc_html__( 'Album', 'chique-pro' ),
				'section'       => 'chique_album',
				'template-part' => 'template-parts/album/display-album',
			),
			'timeline'           => array(
				'label'         => esc_html__( 'Timeline', 'chique-pro' ),
				'section'       => 'chique_timeline',
				'template-part' => 'template-parts/timeline/content-timeline',
			),
			'promotion-headline' => array(
				'label'         => esc_html__( 'Promotion Headline', 'chique-pro' ),
				'section'       => 'chique_promotion_headline',
				'template-part' => 'template-parts/promotion-headline/content-promotion-headline',
			),
			'woo-products' => array(
				'label'         => esc_html__( 'Woo Products', 'chique-pro' ),
				'section'       => 'chique_woo_products',
				'template-part' => 'template-parts/woo-products-showcase/display-products',
			),
			'promotion-sale' => array(
				'label'         => esc_html__( 'Promotion Sale', 'chique-pro' ),
				'section'       => 'chique_promotion_sale',
				'template-part' => 'template-parts/promotion-sale/content-promotion-sale',
			),
			'pricing' => array(
				'label'         => esc_html__( 'Pricing', 'chique-pro' ),
				'section'       => 'chique_pricing',
				'template-part' => 'template-parts/pricing/display-pricing',
			),
			'testimonial'        => array(
				'label'         => esc_html__( 'Testimonial', 'chique-pro' ),
				'section'       => 'chique_testimonials',
				'template-part' => 'template-parts/testimonials/display-testimonial',
			),
			'team' => array(
				'label'         => esc_html__( 'Team', 'chique-pro' ),
				'section'       => 'chique_team',
				'template-part' => 'template-parts/team/display-team',
			),
			'featured-content'   => array(
				'label'         => esc_html__( 'Featured Content', 'chique-pro' ),
				'section'       => 'chique_featured_content',
				'template-part' => 'template-parts/featured-content/display-featured',
			),
			'skills'   => array(
				'label'         => esc_html__( 'Skills', 'chique-pro' ),
				'section'       => 'chique_skills',
				'template-part' => 'template-parts/skills/display-skills',
			),
			'gallery'     => array(
				'label'         => esc_html__( 'Gallery', 'chique-pro' ),
				'section'       => 'chique_gallery_options',
				'template-part' => 'template-parts/gallery/content-gallery',
			),
			'reservation' => array(
				'label'         => esc_html__( 'Reservation', 'chique-pro' ),
				'section'       => 'chique_reservation',
				'template-part' => 'template-parts/reservation/display-reservation',
			),
			'playlist' => array(
				'label'         => esc_html__( 'Playlist', 'chique-pro' ),
				'section'       => 'chique_playlist',
				'template-part' => 'template-parts/playlist/content-playlist',
			),
			'main-content'       => array(
				'label' => esc_html__( 'Main Content', 'chique-pro' ),
			),
			'recent-posts'       => array(
				'label'         => esc_html__( 'Recent Posts ( Only on homepage )', 'chique-pro' ),
				'section'       => 'chique_homepage_options',
				'template-part' => 'template-parts/recent-posts/front-recent-posts',
			),
			'newsletter'       => array(
				'label'         => esc_html__( 'Newsletter', 'chique-pro' ),
				'section'       => 'sidebar-widgets-sidebar-newsletter',
				'template-part' => 'template-parts/footer/footer-newsletter',
			),
			'logo-slider'       => array(
				'label'         => esc_html__( 'Logo Slider', 'chique-pro' ),
				'section'       => 'chique_logo_slider',
				'template-part' => 'template-parts/logo-slider/display-logo-slider',
			),
			'contact-info'       => array(
				'label'         => esc_html__( 'Contact Info', 'chique-pro' ),
				'section'       => 'chique_contact',
				'template-part' => 'template-parts/contact-info/display-contact-info',
			),
		);

		if ( class_exists( 'Catch_Instagram_Feed_Gallery_Widget' ) ||  class_exists( 'Catch_Instagram_Feed_Gallery_Widget_Pro' ) ) {
			$sortable_sections = $sortable_sections + array(
				'instagram' => array(
					'label'         => esc_html__( 'Instagram', 'chique-pro' ),
					'section'       => 'sidebar-widgets-sidebar-instagram',
					'template-part' => 'template-parts/footer/footer-instagram',
				)
			);
		}

		return $sortable_sections;
	}
endif;

if ( ! function_exists( 'chique_team_social_links' ) ) :
	/**
	 * Displays team social links html
	 */
	function chique_team_social_links( $counter ) {
		$type = get_theme_mod( 'chique_team_type', 'category' );

		if( 'ect-team' == $type ) {
			$social_link_one   = get_post_meta( get_the_ID(),'ect_team_social_link_1', true );
			$social_link_two   = get_post_meta( get_the_ID(),'ect_team_social_link_2', true );
			$social_link_three = get_post_meta( get_the_ID(),'ect_team_social_link_3', true );
			$social_link_four  = get_post_meta( get_the_ID(),'ect_team_social_link_4', true );
		} else {
			$social_link_one = get_theme_mod( 'chique_team_social_link_one_' . $counter );
			$social_link_two = get_theme_mod( 'chique_team_social_link_two_' . $counter );
			$social_link_three = get_theme_mod( 'chique_team_social_link_three_' . $counter );
			$social_link_four = get_theme_mod( 'chique_team_social_link_four_' . $counter );
		}

		if ( empty( $social_link_one || $social_link_two ||  $social_link_three || $social_link_four  ) ) {
			return;
		}
		?>
		<div class="team-social-profile">
			<nav class="social-navigation" role="navigation" aria-label="Social Menu">
				<div class="menu-social-container">
					<ul id="menu-social-menu" class="social-links-menu">
						<?php if ( $social_link_one ): ?>
							<li class="menu-item-one">
								<a target="_blank" rel="nofollow" href="<?php echo esc_url( $social_link_one ); ?>"> <span class="screen-reader-text"><?php echo esc_html( $social_link_one ); ?></span></a>
							</li>
						<?php endif;  ?>

						<?php if ( $social_link_two ): ?>
							<li class="menu-item-two">
								<a target="_blank" rel="nofollow" href="<?php echo esc_url( $social_link_two ); ?>"> <span class="screen-reader-text"><?php echo esc_html( $social_link_two ); ?></span></a>
							</li>
						<?php endif;  ?>

						<?php if ( $social_link_three ): ?>
							<li class="menu-item-three">
								<a target="_blank" rel="nofollow" href="<?php echo esc_url( $social_link_three ); ?>"> <span class="screen-reader-text"><?php echo esc_html( $social_link_three ); ?></span></a>
							</li>
						<?php endif;  ?>

						<?php if ( $social_link_four ): ?>
							<li class="menu-item-four">
								<a target="_blank" rel="nofollow" href="<?php echo esc_url( $social_link_four ); ?>"> <span class="screen-reader-text"><?php echo esc_html( $social_link_four ); ?></span></a>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			</nav>
		</div><!-- .artist-social-profile -->
		<?php
	}
endif;

/**
 * Returns an array of feature slider transition effects
 *
 * @since Chique Pro 1.0
 */
function chique_transition_effects() {
	$options = array(
		'default'            => 'default',
		'bounce'             => 'bounce',
		'flash'              => 'flash',
		'pulse'              => 'pulse',
		'rubberBand'         => 'rubberBand',
		'shake'              => 'shake',
		'headShake'          => 'headShake',
		'swing'              => 'swing',
		'tada'               => 'tada',
		'wobble'             => 'wobble',
		'jello'              => 'jello',
		'bounceIn'           => 'bounceIn',
		'bounceInDown'       => 'bounceInDown',
		'bounceInLeft'       => 'bounceInLeft',
		'bounceInRight'      => 'bounceInRight',
		'bounceInUp'         => 'bounceInUp',
		'bounceOut'          => 'bounceOut',
		'bounceOutDown'      => 'bounceOutDown',
		'bounceOutLeft'      => 'bounceOutLeft',
		'bounceOutRight'     => 'bounceOutRight',
		'bounceOutUp'        => 'bounceOutUp',
		'fadeIn'             => 'fadeIn',
		'fadeInDown'         => 'fadeInDown',
		'fadeInDownBig'      => 'fadeInDownBig',
		'fadeInLeft'         => 'fadeInLeft',
		'fadeInLeftBig'      => 'fadeInLeftBig',
		'fadeInRight'        => 'fadeInRight',
		'fadeInRightBig'     => 'fadeInRightBig',
		'fadeInUp'           => 'fadeInUp',
		'fadeInUpBig'        => 'fadeInUpBig',
		'fadeOut'            => 'fadeOut',
		'fadeOutDown'        => 'fadeOutDown',
		'fadeOutDownBig'     => 'fadeOutDownBig',
		'fadeOutLeft'        => 'fadeOutLeft',
		'fadeOutLeftBig'     => 'fadeOutLeftBig',
		'fadeOutRight'       => 'fadeOutRight',
		'fadeOutRightBig'    => 'fadeOutRightBig',
		'fadeOutUp'          => 'fadeOutUp',
		'fadeOutUpBig'       => 'fadeOutUpBig',
		'flipInX'            => 'flipInX',
		'flipInY'            => 'flipInY',
		'flipOutX'           => 'flipOutX',
		'flipOutY'           => 'flipOutY',
		'lightSpeedIn'       => 'lightSpeedIn',
		'lightSpeedOut'      => 'lightSpeedOut',
		'rotateIn'           => 'rotateIn',
		'rotateInDownLeft'   => 'rotateInDownLeft',
		'rotateInDownRight'  => 'rotateInDownRight',
		'rotateInUpLeft'     => 'rotateInUpLeft',
		'rotateInUpRight'    => 'rotateInUpRight',
		'rotateOut'          => 'rotateOut',
		'rotateOutDownLeft'  => 'rotateOutDownLeft',
		'rotateOutDownRight' => 'rotateOutDownRight',
		'rotateOutUpLeft'    => 'rotateOutUpLeft',
		'rotateOutUpRight'   => 'rotateOutUpRight',
		'hinge'              => 'hinge',
		'jackInTheBox'       => 'jackInTheBox',
		'rollIn'             => 'rollIn',
		'rollOut'            => 'rollOut',
		'zoomIn'             => 'zoomIn',
		'zoomInDown'         => 'zoomInDown',
		'zoomInLeft'         => 'zoomInLeft',
		'zoomInRight'        => 'zoomInRight',
		'zoomInUp'           => 'zoomInUp',
		'zoomOut'            => 'zoomOut',
		'zoomOutDown'        => 'zoomOutDown',
		'zoomOutLeft'        => 'zoomOutLeft',
		'zoomOutRight'       => 'zoomOutRight',
		'zoomOutUp'          => 'zoomOutUp',
		'slideInDown'        => 'slideInDown',
		'slideInLeft'        => 'slideInLeft',
		'slideInRight'       => 'slideInRight',
		'slideInUp'          => 'slideInUp',
		'slideOutDown'       => 'slideOutDown',
		'slideOutLeft'       => 'slideOutLeft',
		'slideOutRight'      => 'slideOutRight',
		'slideOutUp'         => 'slideOutUp',
		'heartBeat'          => 'heartBeat',
	);

	return apply_filters( 'chique_transition_effects', $options );
}

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
				$image      = '<img src="' . esc_url( $image_url ) . '" />';
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

if ( ! function_exists( 'chique_get_pricing_posts' ) ) :

	/**
	 * Pricing content posts
	 */
	function chique_get_pricing_posts() {
		$type = get_theme_mod( 'chique_pricing_type', 'category' );

		$number = get_theme_mod( 'chique_pricing_number', 3 );

		$post_list    = array();

		$args = array(
			'posts_per_page'      => $number,
			'post_type'           => 'post',
			'ignore_sticky_posts' => 1, // ignore sticky posts.
		);

		// Get valid number of posts.
		if ( 'post' === $type || 'page' === $type ) {
			$args['post_type'] = $type;

			for ( $i = 1; $i <= $number; $i++ ) {
				$post_id = '';

				if ( 'post' === $type ) {
					$post_id = get_theme_mod( 'chique_pricing_post_' . $i );
				} elseif ( 'page' === $type ) {
					$post_id = get_theme_mod( 'chique_pricing_page_' . $i );
				} elseif ( 'ect-service' === $type ) {
					$post_id = get_theme_mod( 'chique_pricing_cpt_' . $i );
				}

				if ( $post_id && '' !== $post_id ) {
					$post_list = array_merge( $post_list, array( $post_id ) );
				}
			}

			$args['post__in'] = $post_list;
			$args['orderby']  = 'post__in';
		} elseif ( 'category' === $type && $cat = get_theme_mod( 'chique_pricing_select_category' ) ) {
			$args['category__in'] = $cat;
		}

		$pricing_posts = get_posts( $args );

		return $pricing_posts;
	}
endif;

if ( ! function_exists( 'chique_testimonial_posts_args' ) ) :

	function chique_testimonial_posts_args() {
		$number = get_theme_mod( 'chique_testimonial_number', 5 );

		$args = array(
			'ignore_sticky_posts' => 1 // ignore sticky posts
		);

		$type = get_theme_mod( 'chique_testimonial_type', 'category' );

		$post_list  = array();// list of valid post/page ids


		if ( 'post' === $type || 'jetpack-testimonial' === $type || 'page' === $type  ) {
			$args['post_type'] = $type;

			for ( $i = 1; $i <= $number; $i++ ) {
				$post_id = '';

				if ( 'post' === $type ) {
					$post_id = get_theme_mod( 'chique_testimonial_post_' . $i );
				} elseif ( 'page' === $type ) {
					$post_id = get_theme_mod( 'chique_testimonial_page_' . $i );
				} elseif ( 'jetpack-testimonial' === $type ) {
					$post_id =  get_theme_mod( 'chique_testimonial_cpt_' . $i );
				}

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
		} elseif ( 'category' === $type ) {
			$no_of_post = $number;

			if ( get_theme_mod( 'chique_testimonial_select_category' ) ) {
				$args['category__in'] = (array) get_theme_mod( 'chique_testimonial_select_category' );
			}

			$args['post_type'] = 'post';
		} elseif ( 'tag' === $type ) {
			$no_of_post = $number;

			if ( get_theme_mod( 'chique_testimonial_select_tag' ) ) {
				$args['tag__in'] = (array) get_theme_mod( 'chique_testimonial_select_tag' );
			}

			$args['post_type'] = 'post';
		}

		$args['posts_per_page'] = $number;

		return $args;
	}
endif;

if ( ! function_exists( 'chique_sortable_sections_update' ) ) :
	/**
	 * Update list of sortable sections
	 */
	function chique_sortable_sections_update() {
		if ( '1' !== get_theme_mod( 'chique_sortable_section_updated' ) ) {
			$defaults = chique_get_default_sections_value();
			$sections = get_theme_mod( 'chique_sections_sort', $defaults );
			if ( set_theme_mod( 'chique_sections_sort', $sections ) ) {
				set_theme_mod( 'chique_sortable_section_updated', '1' );
			}
		}
	}
endif;
add_action( 'after_setup_theme', 'chique_sortable_sections_update' );
