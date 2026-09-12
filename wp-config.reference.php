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

/* Advanced Media Offloader — Cloudflare R2 credentials (media → task 4.1).
   Checked by the plugin before its DB option, so these never need re-entry
   in wp-admin. Leave commented until real values are filled in — a
   REPLACE_ME value still counts as "configured" and the plugin will start
   making failing R2 API calls.
   Token: https://developers.cloudflare.com/r2/api/tokens/ (scope it to this
   bucket only). Endpoint: https://<account-id>.r2.cloudflarestorage.com
   Domain: the bucket's r2.dev public URL for now (Settings > Public access
   in the R2 dashboard) — swap to a custom domain later by changing only
   this constant. */
// define( 'ADVMO_CLOUDFLARE_R2_KEY', 'REPLACE_ME' );
// define( 'ADVMO_CLOUDFLARE_R2_SECRET', 'REPLACE_ME' );
// define( 'ADVMO_CLOUDFLARE_R2_ENDPOINT', 'REPLACE_ME' );
// define( 'ADVMO_CLOUDFLARE_R2_BUCKET', 'REPLACE_ME' );
// define( 'ADVMO_CLOUDFLARE_R2_DOMAIN', 'REPLACE_ME' );

if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';
