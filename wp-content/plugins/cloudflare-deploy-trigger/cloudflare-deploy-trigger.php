<?php
/**
 * Plugin Name: Cloudflare Deploy Trigger
 * Description: Adds a "Deploy Frontend" button to the admin bar. Editors click it once they're done making changes, and it triggers a Cloudflare Workers Build deploy hook — the Nuxt frontend re-fetches everything from this site's REST API and redeploys as a static build.
 * Version: 1.0
 * Author: Digital Exhibition
 */

if (!defined('ABSPATH')) {
	exit(); // Exit if accessed directly.
}

define('CFDT_OPTION_HOOK_URL', 'cfdt_deploy_hook_url');
define('CFDT_OPTION_LAST_RUN', 'cfdt_last_run');
define('CFDT_NONCE_ACTION', 'cfdt_trigger_deploy');
define('CFDT_COOLDOWN_SECONDS', 60);
// The capability required to see the button and trigger a deploy.
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

function cfdt_render_settings_page() {
	if (!current_user_can('manage_options')) {
		return;
	}
	$last_run = get_option(CFDT_OPTION_LAST_RUN);
	?>
	<div class="wrap">
		<h1>Deploy Frontend</h1>
		<p>Configures the "Deploy Frontend" button in the admin bar (top of every admin page). Clicking it triggers a Cloudflare Workers Build, which rebuilds and redeploys the Nuxt frontend with whatever is currently published here.</p>
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
 * Admin bar button, visible to anyone who can edit content — not just admins,
 * since any editor finishing up changes should be able to trigger a deploy.
 */
function cfdt_add_admin_bar_button($wp_admin_bar) {
	if (!current_user_can(CFDT_CAPABILITY) || !is_admin_bar_showing()) {
		return;
	}
	$wp_admin_bar->add_node([
		'id' => 'cfdt-deploy',
		'title' => '🚀 Deploy Frontend',
		'href' => '#',
		'meta' => ['class' => 'cfdt-deploy-button'],
	]);
}
add_action('admin_bar_menu', 'cfdt_add_admin_bar_button', 90);

/**
 * Inline script for the admin bar button: confirm, disable while in flight,
 * call the AJAX handler below, show the result. No build step / dependency
 * on anything beyond what wp-admin already loads.
 */
function cfdt_print_admin_bar_script() {
	if (!current_user_can(CFDT_CAPABILITY) || !is_admin_bar_showing()) {
		return;
	}
	$ajax_url = admin_url('admin-ajax.php');
	$nonce = wp_create_nonce(CFDT_NONCE_ACTION);
	?>
	<script>
	(function () {
		document.addEventListener('DOMContentLoaded', function () {
			var link = document.getElementById('wp-admin-bar-cfdt-deploy');
			if (!link) return;
			var anchor = link.querySelector('a');
			if (!anchor) return;

			anchor.addEventListener('click', function (e) {
				e.preventDefault();
				if (anchor.dataset.busy === '1') return;
				if (!window.confirm('Deploy the frontend now with everything currently published?')) return;

				anchor.dataset.busy = '1';
				var original = anchor.textContent;
				anchor.textContent = 'Deploying…';

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
						anchor.dataset.busy = '';
						anchor.textContent = original;
						if (res && res.success) {
							window.alert('Deploy triggered. The frontend will be live again in a couple of minutes.');
						} else {
							window.alert('Could not trigger the deploy: ' + ((res && res.data && res.data.message) || 'unknown error'));
						}
					})
					.catch(function (err) {
						anchor.dataset.busy = '';
						anchor.textContent = original;
						window.alert('Could not trigger the deploy: ' + err.message);
					});
			});
		});
	})();
	</script>
	<?php
}
add_action('wp_footer', 'cfdt_print_admin_bar_script');
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
