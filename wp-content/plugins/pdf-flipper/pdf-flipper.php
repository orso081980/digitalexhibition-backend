<?php
/**
 * Plugin Name: PDF Flipper
 * Description: Renders a PDF as an interactive page-flip book via the [pdf_flipper] shortcode. Built to replace the dflip plugin (its own free tier is CC BY-NC-ND, personal non-commercial use only — not usable on this site) without any proprietary viewer JS.
 * Version: 1.0.0
 * Author: Digital Exhibition
 * Text Domain: pdf-flipper
 *
 * Engine: pdf.js 3.11.174 (Mozilla, Apache-2.0) + page-flip 2.0.7 (MIT,
 * github.com/Nodlik/StPageFlip), vendored under assets/vendor/. Both are
 * plain classic scripts (no ES modules — deliberately using pdf.js's
 * last non-module release so there's no server MIME-type configuration
 * to get right). page-flip specifically for its realistic page-curl
 * animation — closest free match to dflip's own page-turn feel.
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
 * Renders a placeholder the front-end JS turns into a flipbook once the
 * visitor scrolls near it. If JS fails to load, the fallback "Download
 * PDF" link (always present) still works.
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

	pdf_flipper_enqueue_assets();

	ob_start();
	?>
	<div class="pdf-flipper-item">
		<?php if ($atts['label']) : ?>
			<div class="pdf-flipper-label"><?php echo esc_html($atts['label']); ?></div>
		<?php endif; ?>
		<figure class="pdf-flipper" data-pdf-src="<?php echo $src; ?>"></figure>
		<a class="pdf-flipper-download" href="<?php echo $src; ?>" target="_blank" rel="noopener">
			<?php esc_html_e('Download PDF', 'pdf-flipper'); ?>
		</a>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode('pdf_flipper', 'pdf_flipper_shortcode');

/**
 * Guarded so it only enqueues once per page even if the shortcode
 * appears more than once.
 */
function pdf_flipper_enqueue_assets()
{
	static $done = false;
	if ($done) {
		return;
	}
	$done = true;

	wp_enqueue_style('pdf-flipper', PDF_FLIPPER_URL . 'assets/css/pdf-flipper.css', [], PDF_FLIPPER_VERSION);

	wp_enqueue_script('pdf-flipper-pdfjs', PDF_FLIPPER_URL . 'assets/vendor/pdfjs/pdf.min.js', [], '3.11.174', true);
	wp_enqueue_script('pdf-flipper-pageflip', PDF_FLIPPER_URL . 'assets/vendor/pageflip/page-flip.browser.js', [], '2.0.7', true);
	wp_add_inline_script('pdf-flipper-pageflip', 'window.PDF_FLIPPER_CFG = ' . wp_json_encode([
		'workerUrl' => PDF_FLIPPER_URL . 'assets/vendor/pdfjs/pdf.worker.min.js',
	]) . ';', 'before');

	wp_enqueue_script('pdf-flipper', PDF_FLIPPER_URL . 'assets/js/pdf-flipper.js', ['pdf-flipper-pdfjs', 'pdf-flipper-pageflip'], PDF_FLIPPER_VERSION, true);
}
