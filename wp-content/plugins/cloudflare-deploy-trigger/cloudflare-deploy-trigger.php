<?php
/**
 * Plugin Name: Frontend Deploy Trigger
 * Description: Adds a "Deploy Frontend" button to the WordPress admin bar (top bar, every wp-admin page). Click it once you're done making changes, confirm in the dialog, and it triggers a rebuild — a GitHub Actions workflow (linux3) or a Cloudflare Workers Build deploy hook. The Nuxt frontend re-fetches everything from this site's REST API and redeploys as a static build. If a Frontend URL is set, a progress notice follows the build and confirms when the new version is live. The deploy hook URL and Frontend URL are configured under Settings → Deploy Frontend.
 * Version: 1.4
 * Author: Digital Exhibition
 */

if (!defined('ABSPATH')) {
	exit(); // Exit if accessed directly.
}

define('CFDT_OPTION_HOOK_URL', 'cfdt_deploy_hook_url');
define('CFDT_OPTION_FRONTEND_URL', 'cfdt_frontend_url');
// Optional access token, sent as `Authorization: Bearer …` (GitHub needs one).
define('CFDT_OPTION_TOKEN', 'cfdt_deploy_token');
// GitHub only: the branch the workflow builds from.
define('CFDT_OPTION_GIT_REF', 'cfdt_git_ref');
define('CFDT_OPTION_LAST_RUN', 'cfdt_last_run');
// A deploy that's been triggered but not yet seen live: ['baseline' => build id
// the frontend served at trigger time, 'started' => unix time].
define('CFDT_OPTION_PENDING', 'cfdt_pending');
// Optional: the address of the Plesk Git webhook on this same server. While a
// deploy is being watched, WordPress calls it so Plesk pulls and deploys the
// finished build — no inbound webhook from GitHub needed (see cfdt_poke_plesk()).
define('CFDT_OPTION_PULL_URL', 'cfdt_pull_url');
// The last call made to it: ['time', 'code', 'error'], shown on the settings page.
define('CFDT_OPTION_LAST_PULL', 'cfdt_last_pull');
// A build takes minutes; don't bother Plesk before this many seconds have passed,
// then call it this often until the new version shows up.
define('CFDT_PULL_AFTER', 120);
define('CFDT_PULL_EVERY', 30);
// WP-Cron hook that keeps calling it when no admin page is open.
define('CFDT_CRON_HOOK', 'cfdt_poke_plesk');
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
	register_setting('cfdt_settings', CFDT_OPTION_TOKEN, [
		'type' => 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'default' => '',
	]);
	register_setting('cfdt_settings', CFDT_OPTION_GIT_REF, [
		'type' => 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'default' => 'linux',
	]);
	register_setting('cfdt_settings', CFDT_OPTION_FRONTEND_URL, [
		'type' => 'string',
		'sanitize_callback' => 'esc_url_raw',
		'default' => '',
	]);
	register_setting('cfdt_settings', CFDT_OPTION_PULL_URL, [
		'type' => 'string',
		'sanitize_callback' => 'esc_url_raw',
		'default' => '',
	]);

	add_settings_section('cfdt_main', '', '__return_false', 'cfdt_settings');

	add_settings_field(
		'cfdt_hook_url_field',
		'Deploy hook URL',
		'cfdt_render_hook_url_field',
		'cfdt_settings',
		'cfdt_main'
	);
	add_settings_field(
		'cfdt_token_field',
		'Access token (GitHub)',
		'cfdt_render_token_field',
		'cfdt_settings',
		'cfdt_main'
	);
	add_settings_field(
		'cfdt_git_ref_field',
		'Branch to build (GitHub)',
		'cfdt_render_git_ref_field',
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
	add_settings_field(
		'cfdt_pull_url_field',
		'Plesk pull URL (optional)',
		'cfdt_render_pull_url_field',
		'cfdt_settings',
		'cfdt_main'
	);
}
add_action('admin_init', 'cfdt_register_settings');

function cfdt_render_hook_url_field() {
	$value = get_option(CFDT_OPTION_HOOK_URL, '');
	printf(
		'<input type="url" name="%s" value="%s" class="regular-text" placeholder="https://api.github.com/repos/OWNER/REPO/actions/workflows/deploy.yml/dispatches" />',
		esc_attr(CFDT_OPTION_HOOK_URL),
		esc_attr($value)
	);
	echo '<p class="description">GitHub: <code>https://api.github.com/repos/OWNER/REPO/actions/workflows/deploy.yml/dispatches</code>. Cloudflare: your Worker → Settings → Builds → Deploy Hooks.</p>';
}

function cfdt_render_token_field() {
	$value = get_option(CFDT_OPTION_TOKEN, '');
	printf(
		'<input type="password" name="%s" value="%s" class="regular-text" autocomplete="new-password" />',
		esc_attr(CFDT_OPTION_TOKEN),
		esc_attr($value)
	);
	echo '<p class="description">A GitHub fine-grained personal access token for the repository with the permission <strong>Actions: Read and write</strong>. Leave empty for a Cloudflare hook.</p>';
}

function cfdt_render_git_ref_field() {
	$value = get_option(CFDT_OPTION_GIT_REF, 'linux');
	printf(
		'<input type="text" name="%s" value="%s" class="regular-text" placeholder="linux" />',
		esc_attr(CFDT_OPTION_GIT_REF),
		esc_attr($value)
	);
	echo '<p class="description">The Git branch the workflow builds. Only used with a GitHub URL.</p>';
}

/** True for the GitHub "run this workflow" API address. */
function cfdt_is_github_dispatch($url) {
	return (bool) preg_match('#^https://api\.github\.com/repos/[^/]+/[^/]+/actions/workflows/[^/]+/dispatches$#', (string) $url);
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

function cfdt_render_pull_url_field() {
	$value = get_option(CFDT_OPTION_PULL_URL, '');
	printf(
		'<input type="password" name="%s" value="%s" class="regular-text" autocomplete="new-password" placeholder="https://127.0.0.1:8443/modules/git/public/web-hook.php?uuid=…" />',
		esc_attr(CFDT_OPTION_PULL_URL),
		esc_attr($value)
	);
	echo '<p class="description">Only when the frontend is hosted on this same server with Plesk Git. The webhook address from Plesk → Git → the repository, with the host replaced by <code>127.0.0.1</code>. While a deploy is being watched, WordPress calls it so Plesk pulls and deploys the finished build by itself (GitHub can\'t reach Plesk\'s webhook from outside). Needs the Frontend URL above, so the plugin knows when the new version is live. The address contains a secret — keep it private.</p>';
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
		<p>Set the deploy hook URL below. To trigger a deploy, click "🚀 Deploy Frontend" in the admin bar at the top of the screen (available on every admin page).</p>
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
		<?php $last_pull = get_option(CFDT_OPTION_LAST_PULL); ?>
		<?php if (!empty($last_pull['time'])) : ?>
			<p>
				<strong>Last Plesk pull:</strong>
				<?php echo esc_html(wp_date('Y-m-d H:i:s', $last_pull['time'])); ?>
				<?php if (!empty($last_pull['error'])) : ?>
					— failed: <?php echo esc_html($last_pull['error']); ?>
				<?php else : ?>
					— HTTP <?php echo esc_html((string) ($last_pull['code'] ?? '?')); ?>
					<?php echo ((int) ($last_pull['code'] ?? 0)) >= 200 && ((int) ($last_pull['code'] ?? 0)) < 300 ? '(accepted)' : '(rejected — check the address)'; ?>
				<?php endif; ?>
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
			'title' => 'Rebuild and redeploy the frontend',
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
						show('warn', 'Not live yet', 'The new version hasn’t appeared after 20 minutes. The build may have failed — check the run in the GitHub Actions tab (or the build log in Cloudflare).');
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
			show('', 'Starting the deploy…', 'Contacting the deploy hook.');

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
 * so repeated clicks can't hammer the deploy hook or burn build minutes,
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

	$args = ['timeout' => 15, 'headers' => []];
	$token = (string) get_option(CFDT_OPTION_TOKEN, '');
	if ($token !== '') {
		$args['headers']['Authorization'] = 'Bearer ' . $token;
	}
	if (cfdt_is_github_dispatch($hook_url)) {
		// GitHub's "create a workflow dispatch event": answers 204 with no body.
		$ref = trim((string) get_option(CFDT_OPTION_GIT_REF, 'linux'));
		$args['headers']['Accept'] = 'application/vnd.github+json';
		$args['headers']['X-GitHub-Api-Version'] = '2022-11-28';
		$args['headers']['Content-Type'] = 'application/json';
		$args['body'] = wp_json_encode(['ref' => $ref !== '' ? $ref : 'linux']);
	}
	$response = wp_remote_post($hook_url, $args);

	if (is_wp_error($response)) {
		wp_send_json_error(['message' => $response->get_error_message()]);
	}

	$status = wp_remote_retrieve_response_code($response);
	if ($status < 200 || $status >= 300) {
		wp_send_json_error(['message' => "The deploy hook returned HTTP {$status}" . ($status === 401 || $status === 403 || $status === 404 ? ' — check the URL and the access token.' : '.')]);
	}

	$user = wp_get_current_user();
	update_option(CFDT_OPTION_LAST_RUN, [
		'time' => time(),
		'user' => $user ? $user->user_login : 'unknown',
	]);

	if ($baseline !== null) {
		$started = time();
		update_option(CFDT_OPTION_PENDING, ['baseline' => $baseline, 'started' => $started], false);
		// Also keep calling Plesk if the browser tab gets closed: WP-Cron takes over.
		if (get_option(CFDT_OPTION_PULL_URL, '') !== '') {
			wp_schedule_single_event($started + CFDT_PULL_AFTER, CFDT_CRON_HOOK, [$started]);
		}
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

	$result = cfdt_resolve_pending();
	if ($result['state'] === 'building') {
		cfdt_poke_plesk(); // no-op unless a Plesk pull URL is set and it's time
	}
	wp_send_json_success($result);
}
add_action('wp_ajax_cfdt_deploy_status', 'cfdt_handle_deploy_status');

/**
 * Where is the deploy that was triggered? Compares the frontend's current
 * build id with the one noted at trigger time, and clears the pending state
 * once it's live or has timed out. Shared by the status handler (browser) and
 * the WP-Cron fallback. States: building | done | timeout | idle.
 */
function cfdt_resolve_pending() {
	$pending = get_option(CFDT_OPTION_PENDING);
	if (empty($pending['started']) || empty($pending['baseline'])) {
		return ['state' => 'idle'];
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
		return ['state' => 'done', 'elapsed' => $elapsed];
	}

	if ($elapsed > CFDT_TRACK_TIMEOUT) {
		delete_option(CFDT_OPTION_PENDING);
		return ['state' => 'timeout', 'elapsed' => $elapsed];
	}

	return ['state' => 'building', 'elapsed' => $elapsed];
}

/**
 * Asks Plesk (on this same server) to pull and deploy the finished build.
 *
 * GitHub builds the site and pushes it to the `linux-deploy` branch, but Plesk
 * only learns of it through its Git webhook, which GitHub can't reach from
 * outside. WordPress lives on the same server, so it makes the call itself,
 * while a deploy is being watched. Until the build is pushed the call is a
 * harmless no-op ("nothing new to fetch"); once it is, Plesk pulls and, with
 * automatic deployment on, deploys it. Throttled, so a 15 s status poll and the
 * cron fallback together don't call it more than every CFDT_PULL_EVERY seconds.
 */
function cfdt_poke_plesk() {
	$url = (string) get_option(CFDT_OPTION_PULL_URL, '');
	$pending = get_option(CFDT_OPTION_PENDING);
	if ($url === '' || empty($pending['started'])) {
		return;
	}

	$now = time();
	if ($now - (int) $pending['started'] < CFDT_PULL_AFTER) {
		return; // the build can't be finished yet
	}
	if (!empty($pending['poked']) && $now - (int) $pending['poked'] < CFDT_PULL_EVERY - 2) {
		return;
	}
	// Note the attempt first, so two overlapping requests don't both call.
	$pending['poked'] = $now;
	update_option(CFDT_OPTION_PENDING, $pending, false);

	// A call to this machine's own panel (self-signed certificate) can't be
	// verified; anything else is verified as usual.
	$host = (string) wp_parse_url($url, PHP_URL_HOST);
	$is_local = in_array($host, ['127.0.0.1', 'localhost', '::1', '[::1]'], true);

	$response = wp_remote_post($url, [
		'timeout' => 30,
		'redirection' => 0,
		'sslverify' => !$is_local,
		'headers' => ['User-Agent' => 'frontend-deploy-trigger'],
	]);

	$last = ['time' => $now];
	if (is_wp_error($response)) {
		$last['error'] = $response->get_error_message();
	} else {
		$last['code'] = (int) wp_remote_retrieve_response_code($response);
	}
	update_option(CFDT_OPTION_LAST_PULL, $last, false);
}

/**
 * WP-Cron fallback for the call above, for when nobody has an admin page open
 * (the status handler is driven by the browser). Re-schedules itself every
 * CFDT_PULL_EVERY seconds until the deploy is live, times out, or a newer
 * deploy replaces it. `$started` identifies the deploy this chain belongs to.
 */
function cfdt_cron_poke_plesk($started = 0) {
	$pending = get_option(CFDT_OPTION_PENDING);
	if (empty($pending['started']) || (int) $pending['started'] !== (int) $started) {
		return;
	}
	if (cfdt_resolve_pending()['state'] !== 'building') {
		return;
	}
	cfdt_poke_plesk();
	wp_schedule_single_event(time() + CFDT_PULL_EVERY, CFDT_CRON_HOOK, [(int) $started]);
}
add_action(CFDT_CRON_HOOK, 'cfdt_cron_poke_plesk');
