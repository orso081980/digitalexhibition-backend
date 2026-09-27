<?php
/**
 * Plugin Name: Cloudflare Deploy Trigger
 * Description: Adds a "Deploy Frontend" button to the WordPress admin bar (top bar, every wp-admin page). Click it once you're done making changes, and it triggers a Cloudflare Workers Build deploy hook — the Nuxt frontend re-fetches everything from this site's REST API and redeploys as a static build. The deploy hook URL is configured under Settings → Deploy Frontend.
 * Version: 1.1
 * Author: Digital Exhibition
 */

if (!defined('ABSPATH')) {
	exit(); // Exit if accessed directly.
}

define('CFDT_OPTION_HOOK_URL', 'cfdt_deploy_hook_url');
define('CFDT_OPTION_LAST_RUN', 'cfdt_last_run');
define('CFDT_NONCE_ACTION', 'cfdt_trigger_deploy');
define('CFDT_COOLDOWN_SECONDS', 60);
// The capability required to trigger a deploy. The settings page itself is
// already gated to `manage_options` (see `add_options_page` below), so in
// practice only administrators ever reach the button.
define('CFDT_CAPABILITY', 'edit_posts');

/**
 * Settings page: Settings → Deploy Frontend. One field — the deploy hook URL
 * — plus a read-only line showing who last triggered a deploy and when.
 */
function cfdt_register_settings() {
	register_setting('cfdt_settings', CFDT_OPTION_HOOK_URL, [
		'type' => 'string',
		'sanitize_callback' => 'esc_url_raw',
		'default' => '',
	]);

	add_settings_section('cfdt_main', '', '__return_false', 'cfdt_settings');

	add_settings_field(
		'cfdt_hook_url_field',
		'Cloudflare deploy hook URL',
		'cfdt_render_hook_url_field',
		'cfdt_settings',
		'cfdt_main'
	);
}
add_action('admin_init', 'cfdt_register_settings');

function cfdt_render_hook_url_field() {
	$value = get_option(CFDT_OPTION_HOOK_URL, '');
	printf(
		'<input type="url" name="%s" value="%s" class="regular-text" placeholder="https://api.cloudflare.com/client/v4/workers/builds/deploy_hooks/..." />',
		esc_attr(CFDT_OPTION_HOOK_URL),
		esc_attr($value)
	);
	echo '<p class="description">From the Cloudflare dashboard: your Worker → Settings → Builds → Deploy Hooks.</p>';
}

function cfdt_register_settings_page() {
	add_options_page(
		'Deploy Frontend',
		'Deploy Frontend',
		'manage_options',
		'cfdt-settings',
		'cfdt_render_settings_page'
	);
}
add_action('admin_menu', 'cfdt_register_settings_page');

/**
 * Settings page markup: just the deploy hook URL field and a last-triggered
 * line. The actual "Deploy Frontend" button lives in the admin bar (top bar)
 * on every wp-admin page — see cfdt_add_admin_bar_node() below — so it's
 * visible wherever someone happens to be, not tucked away in Settings.
 */
function cfdt_render_settings_page() {
	if (!current_user_can('manage_options')) {
		return;
	}
	$last_run = get_option(CFDT_OPTION_LAST_RUN);
	?>
	<div class="wrap">
		<h1>Deploy Frontend</h1>
		<p>Set the Cloudflare deploy hook URL below. To trigger a deploy, click "🚀 Deploy Frontend" in the admin bar at the top of the screen (available on every admin page).</p>
		<form method="post" action="options.php">
			<?php
			settings_fields('cfdt_settings');
			do_settings_sections('cfdt_settings');
			submit_button('Save');
			?>
		</form>
		<?php if (!empty($last_run) && !empty($last_run['time'])) : ?>
			<p>
				<strong>Last triggered:</strong>
				<?php echo esc_html(wp_date('Y-m-d H:i', $last_run['time'])); ?>
				by <?php echo esc_html($last_run['user'] ?? 'unknown'); ?>
			</p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Admin bar: adds "🚀 Deploy Frontend" as the last node in the main
 * (left-aligned) toolbar, on every wp-admin page, for anyone who's allowed
 * to trigger a deploy. Added at a very late priority so other plugins'
 * admin bar items register first and this one lands last.
 */
function cfdt_add_admin_bar_node($wp_admin_bar) {
	if (!is_admin() || !current_user_can(CFDT_CAPABILITY)) {
		return;
	}

	$wp_admin_bar->add_node([
		'id' => 'cfdt-deploy-frontend',
		'title' => '🚀 Deploy Frontend',
		'href' => '#',
		'meta' => [
			'class' => 'cfdt-deploy-frontend-node',
			'title' => 'Trigger a Cloudflare Workers Build to redeploy the frontend',
		],
	]);
}
add_action('admin_bar_menu', 'cfdt_add_admin_bar_node', PHP_INT_MAX);

/**
 * Prints the click handler for the admin bar button in the wp-admin footer.
 * Same AJAX call the old settings-page button used, just wired to the new
 * admin bar node instead.
 */
function cfdt_print_admin_bar_script() {
	if (!current_user_can(CFDT_CAPABILITY)) {
		return;
	}

	$ajax_url = admin_url('admin-ajax.php');
	$nonce = wp_create_nonce(CFDT_NONCE_ACTION);
	?>
	<script>
	(function () {
		var link = document.getElementById('wp-admin-bar-cfdt-deploy-frontend');
		if (!link) return;
		var button = link.querySelector('a.ab-item');
		if (!button) return;

		button.addEventListener('click', function (event) {
			event.preventDefault();
			if (button.dataset.busy === '1') return;
			if (!window.confirm('Deploy the frontend now with everything currently published?')) return;

			button.dataset.busy = '1';
			var original = button.textContent;
			button.textContent = 'Deploying…';

			var body = new URLSearchParams();
			body.set('action', 'cfdt_trigger_deploy');
			body.set('nonce', <?php echo wp_json_encode($nonce); ?>);

			fetch(<?php echo wp_json_encode($ajax_url); ?>, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: body.toString(),
			})
				.then(function (r) { return r.json(); })
				.then(function (res) {
					button.dataset.busy = '';
					button.textContent = original;
					if (res && res.success) {
						window.alert('Deploy triggered. The frontend will be live again in a couple of minutes.');
					} else {
						window.alert('Could not trigger the deploy: ' + ((res && res.data && res.data.message) || 'unknown error'));
					}
				})
				.catch(function (err) {
					button.dataset.busy = '';
					button.textContent = original;
					window.alert('Could not trigger the deploy: ' + err.message);
				});
		});
	})();
	</script>
	<?php
}
add_action('admin_footer', 'cfdt_print_admin_bar_script');

/**
 * AJAX handler: verifies the nonce + capability, enforces a short cooldown
 * so repeated clicks can't hammer the Cloudflare API or burn build minutes,
 * then POSTs to the deploy hook.
 */
function cfdt_handle_trigger_deploy() {
	check_ajax_referer(CFDT_NONCE_ACTION, 'nonce');

	if (!current_user_can(CFDT_CAPABILITY)) {
		wp_send_json_error(['message' => 'You are not allowed to do this.'], 403);
	}

	$last_run = get_option(CFDT_OPTION_LAST_RUN);
	if (!empty($last_run['time']) && (time() - $last_run['time']) < CFDT_COOLDOWN_SECONDS) {
		$wait = CFDT_COOLDOWN_SECONDS - (time() - $last_run['time']);
		wp_send_json_error(['message' => "A deploy was just triggered — please wait {$wait}s before trying again."]);
	}

	$hook_url = get_option(CFDT_OPTION_HOOK_URL, '');
	if (empty($hook_url)) {
		wp_send_json_error(['message' => 'No deploy hook URL configured — set one under Settings → Deploy Frontend.']);
	}

	$response = wp_remote_post($hook_url, ['timeout' => 15]);

	if (is_wp_error($response)) {
		wp_send_json_error(['message' => $response->get_error_message()]);
	}

	$status = wp_remote_retrieve_response_code($response);
	if ($status < 200 || $status >= 300) {
		wp_send_json_error(['message' => "Cloudflare returned HTTP {$status}."]);
	}

	$user = wp_get_current_user();
	update_option(CFDT_OPTION_LAST_RUN, [
		'time' => time(),
		'user' => $user ? $user->user_login : 'unknown',
	]);

	wp_send_json_success(['message' => 'Deploy triggered.']);
}
add_action('wp_ajax_cfdt_trigger_deploy', 'cfdt_handle_trigger_deploy');
