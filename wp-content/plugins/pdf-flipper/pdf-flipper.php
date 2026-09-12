<?php
/**
 * Plugin Name: PDF Flipper
 * Description: Turns any element carrying a data-pdf="<url>" attribute into an interactive page-flip book, entirely client-side — no other plugin needs to know PDF Flipper exists. Also offers a [pdf_flipper] shortcode for direct use. Built to replace dflip (its own free tier is CC BY-NC-ND, personal non-commercial use only — not usable on this site) without any proprietary viewer JS.
 * Version: 1.0.0
 * Author: Digital Exhibition
 * Text Domain: pdf-flipper
 *
 * Engine: pdf.js 3.11.174 (Mozilla, Apache-2.0) + page-flip 2.0.7 (MIT,
 * github.com/Nodlik/StPageFlip), vendored under assets/vendor/. Both
 * plain classic scripts (no ES modules — deliberately using pdf.js's
 * last non-module release, so there's no server MIME-type
 * configuration to get right). page-flip specifically for its
 * realistic page-curl animation.
 *
 * ARCHITECTURE — this plugin owns discovery, not just rendering:
 * a tiny bootstrap script is enqueued on every front-end page (cheap:
 * no vendor libs yet) and scans the rendered DOM for [data-pdf]
 * elements. If none exist, it does nothing further. If any do, it
 * loads pdf.js + page-flip itself and turns each one into a book. Any
 * plugin, theme template, or raw post content can opt in just by
 * emitting `<figure data-pdf="https://.../file.pdf"></figure>` (or any
 * element, any tag) — no PHP-level integration with this plugin is
 * needed on the content-producing side. The [pdf_flipper] shortcode
 * below is a convenience that emits exactly that marker for direct use.
 */

if (!defined('ABSPATH')) {
	exit;
}

define('PDF_FLIPPER_DIR', plugin_dir_path(__FILE__));
define('PDF_FLIPPER_URL', plugin_dir_url(__FILE__));
define('PDF_FLIPPER_VERSION', '1.0.0');

/**
 * [pdf_flipper src="https://.../file.pdf" label="Optional caption"]
 *
 * A convenience wrapper for direct/manual use — emits the same
 * data-pdf marker the bootstrap script looks for on every page
 * regardless of how it got there.
 */
function pdf_flipper_shortcode($atts)
{
	$atts = shortcode_atts([
		'src'   => '',
		'label' => '',
	], $atts, 'pdf_flipper');

	$src = esc_url($atts['src']);
	if (!$src) {
		return '';
	}

	ob_start();
	?>
	<div class="pdf-flipper-item">
		<?php if ($atts['label']) : ?>
			<div class="pdf-flipper-label"><?php echo esc_html($atts['label']); ?></div>
		<?php endif; ?>
		<figure data-pdf="<?php echo $src; ?>">
			<a href="<?php echo $src; ?>" target="_blank" rel="noopener">
				<?php esc_html_e('Open PDF', 'pdf-flipper'); ?>
			</a>
		</figure>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode('pdf_flipper', 'pdf_flipper_shortcode');

/**
 * Enqueued on every front-end page. Deliberately small — the bootstrap
 * script itself decides at runtime whether there's any [data-pdf]
 * element to act on before loading anything heavier.
 */
function pdf_flipper_enqueue_bootstrap()
{
	if (is_admin()) {
		return;
	}

	wp_enqueue_style('pdf-flipper', PDF_FLIPPER_URL . 'assets/css/pdf-flipper.css', [], PDF_FLIPPER_VERSION);

	wp_enqueue_script('pdf-flipper-bootstrap', PDF_FLIPPER_URL . 'assets/js/pdf-flipper-bootstrap.js', [], PDF_FLIPPER_VERSION, true);
	wp_add_inline_script('pdf-flipper-bootstrap', 'window.PDF_FLIPPER_CFG = ' . wp_json_encode([
		'pdfjsUrl'    => PDF_FLIPPER_URL . 'assets/vendor/pdfjs/pdf.min.js',
		'workerUrl'   => PDF_FLIPPER_URL . 'assets/vendor/pdfjs/pdf.worker.min.js',
		'pageflipUrl' => PDF_FLIPPER_URL . 'assets/vendor/pageflip/page-flip.browser.js',
		'enhancerUrl' => PDF_FLIPPER_URL . 'assets/js/pdf-flipper.js',
	]) . ';', 'before');
}
add_action('wp_enqueue_scripts', 'pdf_flipper_enqueue_bootstrap');
