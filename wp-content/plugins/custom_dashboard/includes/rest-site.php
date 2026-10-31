<?php
/**
 * REST route for the headless frontend: GET /wp-json/cd/v1/site
 *
 * Everything the frontend needs to draw the site around the content, in one
 * request, so it carries no copy of it:
 *   - identity     site name, tagline, logo (Customize → Site Identity → Logo),
 *                  footer logo and favicon (Site Icon)
 *   - menus        the "Header" and "Footer" menu locations (Appearance → Menus)
 *   - footer       the small print around the footer logo
 *   - archives     how each content section (Projects, Courses, Seminars,
 *                  Archiprix) is listed: its page, entry URLs, category tabs
 *
 * Menus, logos and page titles are edited in WordPress as usual. The archive
 * definitions are a plain array below; it and the footer text can be changed
 * from a theme or plugin with the `cd_site_archive_config` and `cd_site_footer`
 * filters.
 */

if (!defined('ABSPATH')) {
	exit();
}

add_action('rest_api_init', function () {
	register_rest_route('cd/v1', '/site', [
		'methods' => 'GET',
		'callback' => 'cd_rest_site_callback',
		'permission_callback' => '__return_true',
	]);
});

function cd_rest_site_callback() {
	return rest_ensure_response([
		'name' => trim(html_entity_decode(get_bloginfo('name'), ENT_QUOTES)),
		'tagline' => trim(html_entity_decode(get_bloginfo('description'), ENT_QUOTES)),
		'logo' => cd_site_attachment((int) get_option('site_logo')),
		'footerLogo' => cd_site_attachment((int) get_option('site_icon')),
		'favicon' => get_site_icon_url(),
		'menus' => [
			'header' => cd_site_menu('menu-1'),
			'footer' => cd_site_footer_menu(),
		],
		'footer' => apply_filters('cd_site_footer', [
			'tagline' => 'A platform from',
			'owner' => 'TU/e',
		]),
		'archives' => cd_site_archives(),
	]);
}

/** An image attachment as `{ url, alt, width, height }`, or null when there is none. */
function cd_site_attachment(int $id): ?array {
	if (!$id) {
		return null;
	}
	$url = wp_get_attachment_url($id);
	if (!$url) {
		return null;
	}
	$meta = wp_get_attachment_metadata($id);
	return [
		'url' => $url,
		'alt' => (string) get_post_meta($id, '_wp_attachment_image_alt', true),
		'width' => isset($meta['width']) ? (int) $meta['width'] : null,
		'height' => isset($meta['height']) ? (int) $meta['height'] : null,
	];
}

/** A URL as a site-relative path when it points at this site, else null. */
function cd_site_local_path(string $url): ?string {
	$home = wp_parse_url(home_url());
	$parts = wp_parse_url($url);
	if (!$parts) {
		return null;
	}
	if (!empty($parts['host']) && $parts['host'] !== ($home['host'] ?? '')) {
		return null;
	}
	$path = $parts['path'] ?? '/';
	return trailingslashit($path);
}

/** The items of one menu location: `{ label, url, path, external, target }`. */
function cd_site_menu(string $location): array {
	$locations = get_nav_menu_locations();
	$menu_id = $locations[$location] ?? 0;
	$items = $menu_id ? wp_get_nav_menu_items($menu_id) : [];
	if (!$items) {
		return [];
	}

	$out = [];
	foreach ($items as $item) {
		// Top-level items only: the frontend draws a flat menu.
		if ((int) $item->menu_item_parent !== 0) {
			continue;
		}
		$path = cd_site_local_path($item->url);
		$out[] = [
			'label' => html_entity_decode($item->title, ENT_QUOTES),
			'url' => $item->url,
			'path' => $path,
			'external' => $path === null,
			'target' => $item->target ?: null,
		];
	}
	return $out;
}

/**
 * The Footer menu, or just the Privacy Policy page when that menu is empty:
 * the page chosen under Settings → Privacy, else the page with the slug
 * `privacy-policy` (the address the theme's own footer links to).
 */
function cd_site_footer_menu(): array {
	$menu = cd_site_menu('menu-2');
	if ($menu) {
		return $menu;
	}
	$privacy = (int) get_option('wp_page_for_privacy_policy');
	if (!$privacy) {
		$page = get_page_by_path('privacy-policy');
		$privacy = $page ? (int) $page->ID : 0;
	}
	if (!$privacy || get_post_status($privacy) !== 'publish') {
		return [];
	}
	$url = get_permalink($privacy);
	return [
		[
			'label' => get_the_title($privacy),
			'url' => $url,
			'path' => cd_site_local_path($url),
			'external' => false,
			'target' => null,
		],
	];
}

/**
 * The content sections. `page` is the WordPress page that introduces the
 * section (its title is the section's name, its address the section's URL);
 * everything else describes how its entries are filtered and grouped.
 */
function cd_site_archive_config(): array {
	return apply_filters('cd_site_archive_config', [
		'projects' => [
			'postType' => 'post',
			'page' => 'projects',
			'singularLabel' => 'Project',
			'categoryTaxonomy' => 'category',
			'tabOrder' => ['bachelor', 'master', 'graduation'],
			'showCategoryTabs' => true,
			'hasUnitFilter' => true,
			'groupTaxonomy' => 'project_year',
			'curriculumYears' => 'any-category',
		],
		'courses' => [
			'postType' => 'courses',
			'page' => 'courses',
			'singularLabel' => 'Course',
			'categoryTaxonomy' => 'course_category',
			'tabOrder' => ['bachelor', 'master'],
			'showCategoryTabs' => true,
			'hasUnitFilter' => true,
			'groupTaxonomy' => 'project_year',
			'curriculumYears' => 'bachelor-only',
		],
		'seminars' => [
			'postType' => 'seminars',
			'page' => 'seminars',
			'singularLabel' => 'Seminar',
			'entryBadges' => 'type',
			'categoryTaxonomy' => 'seminar_category',
			'showCategoryTabs' => false,
			'hasUnitFilter' => false,
		],
		'archiprix' => [
			'postType' => 'archiprix',
			'page' => 'archiprix',
			'singularLabel' => 'Archiprix',
			'categoryTaxonomy' => 'archiprix_category',
			'showCategoryTabs' => false,
			'hasUnitFilter' => false,
			'groupByYear' => true,
			'introVideoAtEnd' => true,
		],
	]);
}

function cd_site_archives(): array {
	$out = [];
	foreach (cd_site_archive_config() as $key => $config) {
		$post_type = get_post_type_object($config['postType']);
		$page = get_page_by_path($config['page']);
		if (!$post_type || !$page) {
			continue;
		}

		// Entries live at /{slug}/ for posts and at /{rewrite slug}/{slug}/ for the rest.
		$rewrite = is_array($post_type->rewrite) ? ($post_type->rewrite['slug'] ?? '') : '';
		$entry_base = $config['postType'] === 'post' ? '/' : '/' . trim($rewrite ?: $post_type->name, '/') . '/';

		$out[] = [
			'key' => $key,
			'postType' => $config['postType'],
			'restBase' => $post_type->rest_base ?: $post_type->name,
			'label' => html_entity_decode(get_the_title($page), ENT_QUOTES),
			'singularLabel' => $config['singularLabel'] ?? html_entity_decode(get_the_title($page), ENT_QUOTES),
			// What an entry shows next to its title: its categories ("Bachelor Project") or just its type ("Seminar").
			'entryBadges' => $config['entryBadges'] ?? 'category',
			'path' => cd_site_local_path(get_permalink($page)),
			'entryBase' => $entry_base,
			'categoryTaxonomy' => $config['categoryTaxonomy'] ?? null,
			'categoryTabs' => cd_site_category_tabs($config, $page),
			'showCategoryTabs' => $config['showCategoryTabs'] ?? true,
			'hasUnitFilter' => $config['hasUnitFilter'] ?? false,
			'groupTaxonomy' => $config['groupTaxonomy'] ?? null,
			'curriculumYears' => $config['curriculumYears'] ?? null,
			'groupByYear' => $config['groupByYear'] ?? false,
			'introVideoAtEnd' => $config['introVideoAtEnd'] ?? false,
		];
	}
	return $out;
}

/**
 * The category tabs of a section: the non-empty terms of its category
 * taxonomy, each with the child page of the section's page that carries its
 * own address (e.g. term "bachelor" → /projects/bachelor-projects/).
 */
function cd_site_category_tabs(array $config, WP_Post $page): array {
	if (empty($config['categoryTaxonomy'])) {
		return [];
	}
	$terms = get_terms(['taxonomy' => $config['categoryTaxonomy'], 'hide_empty' => true]);
	if (is_wp_error($terms)) {
		return [];
	}
	$terms = array_values(array_filter($terms, fn($t) => $t->slug !== 'uncategorized'));

	$order = $config['tabOrder'] ?? [];
	usort($terms, function ($a, $b) use ($order) {
		$ia = array_search($a->slug, $order, true);
		$ib = array_search($b->slug, $order, true);
		$ia = $ia === false ? PHP_INT_MAX : $ia;
		$ib = $ib === false ? PHP_INT_MAX : $ib;
		return $ia <=> $ib ?: strcasecmp($a->name, $b->name);
	});

	$children = get_pages(['child_of' => $page->ID]) ?: [];

	return array_map(function ($term) use ($children) {
		$path = null;
		foreach ($children as $child) {
			if (strpos($child->post_name, $term->slug) !== false) {
				$path = cd_site_local_path(get_permalink($child));
				break;
			}
		}
		return [
			'slug' => $term->slug,
			'label' => html_entity_decode($term->name, ENT_QUOTES),
			'path' => $path,
		];
	}, $terms);
}
