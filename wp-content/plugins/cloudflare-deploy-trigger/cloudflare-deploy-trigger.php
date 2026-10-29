<?php
/**
 * Plugin Name: Cloudflare Deploy Trigger
 * Description: Adds a "Deploy Frontend" button to the WordPress admin bar (top bar, every wp-admin page). Click it once you're done making changes, confirm in the dialog, and it triggers a Cloudflare Workers Build deploy hook — the Nuxt frontend re-fetches everything from this site's REST API and redeploys as a static build. If a Frontend URL is set, a progress notice follows the build and confirms when the new version is live. The deploy hook URL and Frontend URL are configured under Settings → Deploy Frontend.
 * Version: 1.2
 * Author: Digital Exhibition
 */

if (!defined('ABSPATH')) {
	exit(); // Exit if accessed directly.
}

define('CFDT_OPTION_HOOK_URL', 'cfdt_deploy_hook_url');
define('CFDT_OPTION_FRONTEND_URL', 'cfdt_frontend_url');
define('CFDT_OPTION_LAST_RUN', 'cfdt_last_run');
// A deploy that's been triggered but not yet seen live: ['baseline' => build id
// the frontend served at trigger time, 'started' => unix time].
define('CFDT_OPTION_PENDING', 'cfdt_pending');
define('CFDT_NONCE_ACTION', 'cfdt_trigger_deploy');
define('CFDT_COOLDOWN_SECONDS', 60);
// Stop watching for the new version after this long. A build that fails never
// goes live, so without a limit the notice would spin forever.
define('CFDT_TRACK_TIMEOUT', 20 * MINUTE_IN_SECONDS);
// The capability required to trigger a deploy. The settings page itself is
// already gated to `manage_options` (see `add_options_page` below), so in
// practice only administrators ever reach the button.
define('CFDT_CAPABILITY', 'edit_posts');

/**
 * Settings page: Settings → Deploy Frontend. The deploy hook URL, the
 * (optional) public frontend URL used to detect when a deploy is live, and a
 * read-only line showing who last triggered a deploy and when.
 */
function cfdt_register_settings() {
	register_setting('cfdt_settings', CFDT_OPTION_HOOK_URL, [
		'type' => 'string',
		'sanitize_callback' => 'esc_url_raw',
		'default' => '',
	]);
	register_setting('cfdt_settings', CFDT_OPTION_FRONTEND_URL, [
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
	add_settings_field(
		'cfdt_frontend_url_field',
		'Frontend URL (optional)',
		'cfdt_render_frontend_url_field',
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

function cfdt_render_frontend_url_field() {
	$value = get_option(CFDT_OPTION_FRONTEND_URL, '');
	printf(
		'<input type="url" name="%s" value="%s" class="regular-text" placeholder="https://digitalexhibition.example.com" />',
		esc_attr(CFDT_OPTION_FRONTEND_URL),
		esc_attr($value)
	);
	echo '<p class="description">The public address of the frontend. With this set, after a deploy is triggered a notice shows progress and confirms when the new version is live. Leave empty to skip that.</p>';
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
 * Settings page markup: the two URL fields and a last-triggered line. The
 * actual "Deploy Frontend" button lives in the admin bar (top bar) on every
 * wp-admin page — see cfdt_add_admin_bar_node() below — so it's visible
 * wherever someone happens to be, not tucked away in Settings.
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
			<?php if (!empty($last_run['completed'])) : ?>
				<p>
					<strong>Went live:</strong>
					<?php echo esc_html(wp_date('Y-m-d H:i', $last_run['completed'])); ?>
				</p>
			<?php endif; ?>
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
 * Reads the build id the public frontend is serving right now. Nuxt writes a
 * fresh id to `/_nuxt/builds/latest.json` on every build, so "the id changed"
 * is a reliable sign a new deploy went live — no Cloudflare API token needed.
 * Returns null when no Frontend URL is set or the file can't be read.
 */
function cfdt_fetch_build_id() {
	$base = untrailingslashit((string) get_option(CFDT_OPTION_FRONTEND_URL, ''));
	if ($base === '') {
		return null;
	}

	$response = wp_remote_get($base . '/_nuxt/builds/latest.json?cfdt=' . time(), [
		'timeout' => 10,
		'headers' => ['Cache-Control' => 'no-cache'],
	]);
	if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
		return null;
	}

	$data = json_decode(wp_remote_retrieve_body($response), true);
	return is_array($data) && !empty($data['id']) ? (string) $data['id'] : null;
}

/**
 * Prints the dialog / progress notice and the click handler for the admin bar
 * button in the wp-admin footer.
 */
function cfdt_print_admin_bar_script() {
	if (!current_user_can(CFDT_CAPABILITY)) {
		return;
	}

	$pending = get_option(CFDT_OPTION_PENDING);
	$config = [
		'ajaxUrl' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce(CFDT_NONCE_ACTION),
		'frontendUrl' => (string) get_option(CFDT_OPTION_FRONTEND_URL, ''),
		// A deploy still being watched from an earlier page: resume it here.
		'pendingElapsed' => !empty($pending['started'])
			? max(0, time() - (int) $pending['started'])
			: null,
	];
	?>
	<style>
		#cfdt-overlay {
			position: fixed; inset: 0; z-index: 100001;
			display: flex; align-items: center; justify-content: center;
			background: rgba(15, 23, 42, .45);
		}
		#cfdt-overlay[hidden] { display: none; }
		#cfdt-dialog {
			box-sizing: border-box; width: 420px; max-width: calc(100vw - 32px);
			padding: 28px 28px 22px; border-radius: 12px; background: #fff;
			box-shadow: 0 20px 50px rgba(15, 23, 42, .3);
			font-size: 14px; line-height: 1.5; color: #1d2327;
		}
		#cfdt-dialog h2 { margin: 0 0 8px; font-size: 18px; line-height: 1.3; }
		#cfdt-dialog p { margin: 0 0 6px; color: #50575e; }
		#cfdt-dialog .cfdt-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px; }
		#cfdt-dialog .cfdt-btn {
			padding: 8px 18px; border-radius: 6px; border: 1px solid #c3c4c7;
			background: #f6f7f7; color: #1d2327; font-size: 14px; cursor: pointer;
		}
		#cfdt-dialog .cfdt-btn:hover { background: #fff; border-color: #8c8f94; }
		#cfdt-dialog .cfdt-btn-primary { background: #2271b1; border-color: #2271b1; color: #fff; font-weight: 600; }
		#cfdt-dialog .cfdt-btn-primary:hover { background: #135e96; border-color: #135e96; }
		#cfdt-toast {
			position: fixed; right: 20px; bottom: 20px; z-index: 100000;
			display: flex; align-items: flex-start; gap: 12px;
			box-sizing: border-box; width: 340px; max-width: calc(100vw - 40px);
			padding: 14px 16px; border-radius: 10px; background: #fff;
			border-left: 4px solid #2271b1;
			box-shadow: 0 8px 28px rgba(15, 23, 42, .22);
			font-size: 13px; line-height: 1.45; color: #1d2327;
		}
		#cfdt-toast[hidden] { display: none; }
		#cfdt-toast.is-done { border-left-color: #00a32a; }
		#cfdt-toast.is-warn { border-left-color: #dba617; }
		#cfdt-toast.is-error { border-left-color: #d63638; }
		#cfdt-toast .cfdt-icon { flex: none; width: 22px; height: 22px; margin-top: 1px; text-align: center; font-size: 18px; line-height: 22px; }
		#cfdt-toast .cfdt-spinner {
			box-sizing: border-box; border: 3px solid #dcdcde; border-top-color: #2271b1;
			border-radius: 50%; animation: cfdt-spin .8s linear infinite;
		}
		#cfdt-toast .cfdt-body { flex: 1; min-width: 0; }
		#cfdt-toast .cfdt-title { display: block; font-weight: 600; }
		#cfdt-toast .cfdt-text { display: block; color: #50575e; }
		#cfdt-toast a { color: #2271b1; font-weight: 600; }
		#cfdt-toast .cfdt-close {
			flex: none; margin: -4px -6px 0 0; padding: 2px 6px; border: 0;
			background: none; color: #787c82; font-size: 20px; line-height: 1; cursor: pointer;
		}
		#cfdt-toast .cfdt-close:hover { color: #1d2327; }
		@keyframes cfdt-spin { to { transform: rotate(360deg); } }
		@media (prefers-reduced-motion: reduce) { #cfdt-toast .cfdt-spinner { animation-duration: 3s; } }
	</style>

	<div id="cfdt-overlay" hidden>
		<div id="cfdt-dialog" role="dialog" aria-modal="true" aria-labelledby="cfdt-dialog-title">
			<h2 id="cfdt-dialog-title">Deploy the frontend?</h2>
			<p>This republishes the public site with everything that is currently published. It usually takes around 5–10 minutes.</p>
			<p id="cfdt-dialog-track" hidden>You'll get a confirmation here as soon as the new version is live.</p>
			<div class="cfdt-actions">
				<button type="button" class="cfdt-btn" id="cfdt-cancel">Cancel</button>
				<button type="button" class="cfdt-btn cfdt-btn-primary" id="cfdt-confirm">Deploy now</button>
			</div>
		</div>
	</div>

	<div id="cfdt-toast" role="status" aria-live="polite" hidden>
		<span class="cfdt-icon" id="cfdt-toast-icon"></span>
		<span class="cfdt-body">
			<span class="cfdt-title" id="cfdt-toast-title"></span>
			<span class="cfdt-text" id="cfdt-toast-text"></span>
		</span>
		<button type="button" class="cfdt-close" id="cfdt-toast-close" aria-label="Dismiss">&times;</button>
	</div>

	<script>
	(function () {
		var cfg = <?php echo wp_json_encode($config); ?>;
		var POLL_MS = 15000;

		var node = document.getElementById('wp-admin-bar-cfdt-deploy-frontend');
		var button = node && node.querySelector('a.ab-item');
		if (!button) return;
		var originalLabel = button.textContent;

		var overlay = document.getElementById('cfdt-overlay');
		var confirmBtn = document.getElementById('cfdt-confirm');
		var cancelBtn = document.getElementById('cfdt-cancel');
		var toast = document.getElementById('cfdt-toast');
		var icon = document.getElementById('cfdt-toast-icon');
		var title = document.getElementById('cfdt-toast-title');
		var text = document.getElementById('cfdt-toast-text');
		var closeBtn = document.getElementById('cfdt-toast-close');
		if (cfg.frontendUrl) document.getElementById('cfdt-dialog-track').hidden = false;

		var elapsed = 0, tick = null, poll = null, busy = false;

		function post(action) {
			var body = new URLSearchParams();
			body.set('action', action);
			body.set('nonce', cfg.nonce);
			return fetch(cfg.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: body.toString(),
			}).then(function (r) { return r.json(); });
		}

		function mmss(s) {
			var m = Math.floor(s / 60), r = s % 60;
			return m + ':' + (r < 10 ? '0' : '') + r;
		}

		function show(kind, heading, message, html) {
			toast.className = kind ? 'is-' + kind : '';
			icon.className = 'cfdt-icon' + (kind === '' ? ' cfdt-spinner' : '');
			icon.textContent = { done: '✓', warn: '!', error: '✕' }[kind] || '';
			icon.style.color = { done: '#00a32a', warn: '#dba617', error: '#d63638' }[kind] || '';
			title.textContent = heading;
			if (html) text.innerHTML = message; else text.textContent = message;
			toast.hidden = false;
		}

		function stopWatching() {
			clearInterval(tick); clearInterval(poll);
			tick = poll = null;
			busy = false;
			button.textContent = originalLabel;
		}

		function renderBuilding() {
			show('', 'Deploying the frontend…', 'Building the new version (' + mmss(elapsed) + '). You can keep working — this notice follows you.');
			button.textContent = '⏳ Deploying…';
		}

		function watch(startElapsed) {
			busy = true;
			elapsed = startElapsed;
			renderBuilding();
			tick = setInterval(function () { elapsed += 1; renderBuilding(); }, 1000);

			function check() {
				post('cfdt_deploy_status').then(function (res) {
					if (!res || !res.success) return;
					var state = res.data.state;
					if (state === 'done') {
						stopWatching();
						var link = cfg.frontendUrl
							? ' <a href="' + encodeURI(cfg.frontendUrl) + '" target="_blank" rel="noopener">Open the site ↗</a>'
							: '';
						show('done', 'Frontend deployed', 'The new version is live (took ' + mmss(res.data.elapsed) + ').' + link, true);
					} else if (state === 'timeout') {
						stopWatching();
						show('warn', 'Not live yet', 'The new version hasn’t appeared after 20 minutes. The build may have failed — check the build log in Cloudflare.');
					} else if (state === 'idle') {
						stopWatching();
						toast.hidden = true;
					}
				}).catch(function () { /* transient network error: try again next tick */ });
			}
			poll = setInterval(check, POLL_MS);
			check();
		}

		function openDialog() {
			overlay.hidden = false;
			confirmBtn.focus();
		}
		function closeDialog() {
			overlay.hidden = true;
			button.focus();
		}

		button.addEventListener('click', function (event) {
			event.preventDefault();
			if (busy) { toast.hidden = false; return; }
			openDialog();
		});
		cancelBtn.addEventListener('click', closeDialog);
		overlay.addEventListener('click', function (e) { if (e.target === overlay) closeDialog(); });
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && !overlay.hidden) closeDialog();
		});
		closeBtn.addEventListener('click', function () { toast.hidden = true; });

		confirmBtn.addEventListener('click', function () {
			closeDialog();
			if (busy) return;
			busy = true;
			button.textContent = 'Triggering…';
			show('', 'Starting the deploy…', 'Contacting Cloudflare.');

			post('cfdt_trigger_deploy')
				.then(function (res) {
					if (!res || !res.success) {
						stopWatching();
						show('error', 'Could not start the deploy', (res && res.data && res.data.message) || 'Unknown error.');
						return;
					}
					if (res.data.tracking) {
						watch(0);
					} else {
						stopWatching();
						show('done', 'Deploy triggered', 'The frontend will be live again in a few minutes.');
					}
				})
				.catch(function (err) {
					stopWatching();
					show('error', 'Could not start the deploy', err.message);
				});
		});

		// A deploy started on an earlier admin page and not yet seen live.
		if (cfg.pendingElapsed !== null) watch(cfg.pendingElapsed);
	})();
	</script>
	<?php
}
add_action('admin_footer', 'cfdt_print_admin_bar_script');

/**
 * AJAX handler: verifies the nonce + capability, enforces a short cooldown
 * so repeated clicks can't hammer the Cloudflare API or burn build minutes,
 * then POSTs to the deploy hook. When a Frontend URL is set, it first notes
 * which build the site is serving so the status handler can tell when a
 * newer one has gone live.
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

	$baseline = cfdt_fetch_build_id();

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

	if ($baseline !== null) {
		update_option(CFDT_OPTION_PENDING, ['baseline' => $baseline, 'started' => time()], false);
	} else {
		delete_option(CFDT_OPTION_PENDING);
	}

	wp_send_json_success(['message' => 'Deploy triggered.', 'tracking' => $baseline !== null]);
}
add_action('wp_ajax_cfdt_trigger_deploy', 'cfdt_handle_trigger_deploy');

/**
 * AJAX handler: has the deploy that was triggered gone live yet? Compares the
 * frontend's current build id with the one noted at trigger time.
 * States: building | done | timeout | idle (nothing being watched).
 */
function cfdt_handle_deploy_status() {
	check_ajax_referer(CFDT_NONCE_ACTION, 'nonce');

	if (!current_user_can(CFDT_CAPABILITY)) {
		wp_send_json_error(['message' => 'You are not allowed to do this.'], 403);
	}

	$pending = get_option(CFDT_OPTION_PENDING);
	if (empty($pending['started']) || empty($pending['baseline'])) {
		wp_send_json_success(['state' => 'idle']);
	}

	$elapsed = time() - (int) $pending['started'];
	$current = cfdt_fetch_build_id();

	if ($current !== null && $current !== $pending['baseline']) {
		delete_option(CFDT_OPTION_PENDING);
		$last_run = get_option(CFDT_OPTION_LAST_RUN);
		if (is_array($last_run)) {
			$last_run['completed'] = time();
			update_option(CFDT_OPTION_LAST_RUN, $last_run);
		}
		wp_send_json_success(['state' => 'done', 'elapsed' => $elapsed]);
	}

	if ($elapsed > CFDT_TRACK_TIMEOUT) {
		delete_option(CFDT_OPTION_PENDING);
		wp_send_json_success(['state' => 'timeout', 'elapsed' => $elapsed]);
	}

	wp_send_json_success(['state' => 'building', 'elapsed' => $elapsed]);
}
add_action('wp_ajax_cfdt_deploy_status', 'cfdt_handle_deploy_status');
