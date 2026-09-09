<?php

/**
 * Function to register control and setting
 */
function chique_register_option( $wp_customize, $option ) {
	// Initialize Setting.
	$wp_customize->add_setting( $option['name'], array(
		'sanitize_callback'    => $option['sanitize_callback'],
		'default'              => isset( $option['default'] ) ? $option['default'] : '',
		'transport'            => isset( $option['transport'] ) ? $option['transport'] : 'refresh',
		'theme_supports'       => isset( $option['theme_supports'] ) ? $option['theme_supports'] : '',
	) );

	$control = array(
		'label'    => $option['label'],
		'section'  => $option['section'],
		'settings' => isset( $option['settings'] ) ? $option['settings'] : $option['name'],
	);

	if ( isset( $option['active_callback'] ) ) {
		$control['active_callback'] = $option['active_callback'];
	}

	if ( isset( $option['priority'] ) ) {
		$control['priority'] = $option['priority'];
	}

	if ( isset( $option['choices'] ) ) {
		$control['choices'] = $option['choices'];
	}

	if ( isset( $option['type'] ) ) {
		$control['type'] = $option['type'];
	}

	if ( isset( $option['input_attrs'] ) ) {
		$control['input_attrs'] = $option['input_attrs'];
	}

	if ( isset( $option['description'] ) ) {
		$control['description'] = $option['description'];
	}

	if ( isset( $option['custom_control'] ) ) {
		$wp_customize->add_control( new $option['custom_control']( $wp_customize, $option['name'], $control ) );
	} else {
		$wp_customize->add_control( $option['name'], $control );
	}
}

/**
 * Render the site title for the selective refresh partial.
 *
 * @since Chique 1.0
 * @see chique_customize_register()
 *
 * @return void
 */
function chique_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @since Chique 1.0
 * @see chique_customize_register()
 *
 * @return void
 */
function chique_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Alphabetically sort theme options sections
 *
 * @param  wp_customize object $wp_customize wp_customize object.
 */
function chique_sort_sections_list( $wp_customize ) {
	foreach ( $wp_customize->sections() as $section_key => $section_object ) {
		if ( false !== strpos( $section_key, 'chique_' ) && 'chique_important_links' !== $section_key ) {
			$options[] = $section_key;
		}
	}

	sort( $options );

	$priority = 1;
	foreach ( $options as  $option ) {
		$wp_customize->get_section( $option )->priority = $priority++;
	}
}
add_action( 'customize_register', 'chique_sort_sections_list' );

/**
 * Returns an array of visibility options for featured sections
 *
 * @since Chique Pro 1.0
 */
function chique_section_visibility_options() {
	$options = array(
		'disabled'    => esc_html__( 'Disabled', 'chique-pro' ),
		'homepage'    => esc_html__( 'Homepage / Frontpage', 'chique-pro' ),
		'entire-site' => esc_html__( 'Entire Site', 'chique-pro' ),
	);

	return apply_filters( 'chique_section_visibility_options', $options );
}

/**
 * Returns an array of featured content options
 *
 * @since Chique Pro 1.0
 */
function chique_sections_layout_options() {
	$options = array(
		'layout-one'   => esc_html__( '1 column', 'chique-pro' ),
		'layout-two'   => esc_html__( '2 columns', 'chique-pro' ),
		'layout-three' => esc_html__( '3 columns', 'chique-pro' ),
		'layout-four'  => esc_html__( '4 columns', 'chique-pro' ),
	);

	return apply_filters( 'chique_sections_layout_options', $options );
}

/**
 * Returns an array of section types
 *
 * @since Chique Pro 1.0
 */
function chique_section_type_options() {
	$options = array(
		'post'     => esc_html__( 'Post', 'chique-pro' ),
		'page'     => esc_html__( 'Page', 'chique-pro' ),
		'category' => esc_html__( 'Category', 'chique-pro' ),
		'custom'   => esc_html__( 'Custom', 'chique-pro' ),
	);

	return apply_filters( 'chique_section_type_options', $options );
}

/**
 * Returns an array of comment options for Foodie World.
 *
 * @since Chique Pro 1.0
 */
function chique_comment_options() {
	$comment_options = array(
		'use-wordpress-setting' => esc_html__( 'Use WordPress Setting', 'chique-pro' ),
		'disable-in-pages'      => esc_html__( 'Disable in Pages', 'chique-pro' ),
		'disable-completely'    => esc_html__( 'Disable Completely', 'chique-pro' ),
	);

	return apply_filters( 'chique_comment_options', $comment_options );
}

/**
 * Returns an array of color schemes registered for catchresponsive.
 *
 * @since Chique Pro 1.0
 */
function chique_get_pagination_types() {
	$pagination_types = array(
		'default' => esc_html__( 'Default(Older Posts/Newer Posts)', 'chique-pro' ),
		'numeric' => esc_html__( 'Numeric', 'chique-pro' ),
	);

	return apply_filters( 'chique_get_pagination_types', $pagination_types );
}

/**
 * Generate a list of all available post array
 *
 * @param  string $post_type post type.
 * @return post_array
 */
function chique_generate_post_array( $post_type = 'post' ) {
	$output = array();
	$posts = get_posts( array(
		'post_type'        => $post_type,
		'post_status'      => 'publish',
		'suppress_filters' => false,
		'posts_per_page'   => -1,
		)
	);

	$output['0']= esc_html__( '-- Select --', 'chique-pro' );

	foreach ( $posts as $post ) {
		/* translators: 1: post id. */
		$output[ $post->ID ] = ! empty( $post->post_title ) ? $post->post_title : sprintf( __( '#%d (no title)', 'chique-pro' ), $post->ID );
	}

	return $output;
}

/**
 * Generate a list of all available taxonomy
 *
 * @param  string $post_type post type.
 * @return post_array
 */
function chique_generate_taxonomy_array( $taxonomy = 'category' ) {
	$output = array();
	$taxonomy = get_categories( array( 'taxonomy' => $taxonomy ) );

	$output['0']= esc_html__( '-- Select --', 'chique-pro' );

	foreach ( $taxonomy as $tax ) {
		$output[ $tax->term_id ] = ! empty($tax->name ) ?$tax->name : sprintf( __( '#%d (no title)', 'chique-pro' ), $tax->term_id );
	}

	return $output;
}

if ( ! function_exists( 'chique_get_default_sections_value' ) ) :
	/**
	 * Returns default sections value
	 */
	function chique_get_default_sections_value() {
		$sections = chique_get_sortable_sections();
		$value    = array_keys( $sections );
		$value    = implode( ',', $value );

		return $value;
	}
endif;

/**
 * Returns an array of featured content show registered for vogue.
 *
 * @since Chique Pro 1.0
 */
function chique_content_show() {
	$options = array(
		'excerpt'      => esc_html__( 'Show Excerpt', 'chique-pro' ),
		'full-content' => esc_html__( 'Full Content', 'chique-pro' ),
		'hide-content' => esc_html__( 'Hide Content', 'chique-pro' ),
	);
	return apply_filters( 'chique_content_show', $options );
}

/**
 * Returns an array of featured content show registered for vogue.
 *
 * @since Chique Pro 1.0
 */
function chique_meta_show() {
	$options = array(
		'show-meta' => esc_html__( 'Show Meta', 'chique-pro' ),
		'hide-meta' => esc_html__( 'Hide Meta', 'chique-pro' ),
	);
	return apply_filters( 'chique_meta_show', $options );
}

/**
 * Returns an array of featured content show registered for vogue.
 *
 * @since Chique Pro 1.0
 */
function chique_category_show() {
	$options = array(
		'show-cat' => esc_html__( 'Show Category', 'chique-pro' ),
		'hide-cat' => esc_html__( 'Hide Category', 'chique-pro' ),
	);
	return apply_filters( 'chique_content_show', $options );
}
