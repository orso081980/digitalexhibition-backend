<?php
/**
 * Reference wp-config for deploys.
 *
 * The real wp-config.php is git-ignored because it carries live database
 * credentials and authentication salts. On the server, keep the existing
 * wp-config.php or copy this file to wp-config.php and fill in real values.
 *
 * Fresh salts: https://api.wordpress.org/secret-key/1.1/salt/
 *
 * @package WordPress
 */

// ** Database settings ** //
define( 'DB_NAME', 'REPLACE_ME' );
define( 'DB_USER', 'REPLACE_ME' );
define( 'DB_PASSWORD', 'REPLACE_ME' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

/* Production connects to MySQL over SSL (disabled for local/MAMP): */
// define( 'MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL );
// define( 'MYSQL_SSL_CA', '/etc/ssl/cert.pem' );

// ** Authentication unique keys and salts ** //
define( 'AUTH_KEY',          'REPLACE_ME' );
define( 'SECURE_AUTH_KEY',   'REPLACE_ME' );
define( 'LOGGED_IN_KEY',     'REPLACE_ME' );
define( 'NONCE_KEY',         'REPLACE_ME' );
define( 'AUTH_SALT',         'REPLACE_ME' );
define( 'SECURE_AUTH_SALT',  'REPLACE_ME' );
define( 'LOGGED_IN_SALT',    'REPLACE_ME' );
define( 'NONCE_SALT',        'REPLACE_ME' );
define( 'WP_CACHE_KEY_SALT', 'REPLACE_ME' );

// ** Table prefix (must match the live database) ** //
$table_prefix = '5U6Nk_';

if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';
