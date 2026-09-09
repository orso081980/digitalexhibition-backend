<?php
/**
 * Plugin Name: Figuro Media
 * Description: Organize the Media Library into folders — a free, basic replacement for FileBird Pro. Imports any existing FileBird folder structure on activation.
 * Version:     1.0.0
 * Author:      Figuro
 * License:     GPL-2.0-or-later
 * Text Domain: figuro-media
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FIGURO_MEDIA_VERSION', '1.0.1' );
define( 'FIGURO_MEDIA_FILE', __FILE__ );
define( 'FIGURO_MEDIA_DIR', plugin_dir_path( __FILE__ ) );
define( 'FIGURO_MEDIA_URL', plugin_dir_url( __FILE__ ) );
define( 'FIGURO_MEDIA_TAXONOMY', 'figuro_folder' );

require_once FIGURO_MEDIA_DIR . 'includes/class-figuro-taxonomy.php';
require_once FIGURO_MEDIA_DIR . 'includes/class-figuro-migration.php';
require_once FIGURO_MEDIA_DIR . 'includes/class-figuro-admin-page.php';
require_once FIGURO_MEDIA_DIR . 'includes/class-figuro-media-library.php';
require_once FIGURO_MEDIA_DIR . 'includes/class-figuro-ajax.php';

register_activation_hook( __FILE__, array( 'Figuro_Migration', 'maybe_migrate_on_activation' ) );

Figuro_Taxonomy::init();
Figuro_Admin_Page::init();
Figuro_Media_Library::init();
Figuro_Ajax::init();
