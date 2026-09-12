/**
 * Loaded on every front-end page (small — no vendor libs). Scans for
 * [data-pdf] elements; if none exist, does nothing else. If any do,
 * loads page-flip, then pdf.js, then the actual enhancer script, in
 * that order, then hands off. Nothing here needs to know which plugin
 * or template produced a [data-pdf] element.
 */
(function () {
	function loadScript(src, onload) {
		var s = document.createElement('script');
		s.src = src;
		s.onload = onload;
		s.onerror = function () {
			// eslint-disable-next-line no-console
			console.error('PDF Flipper: failed to load', src);
		};
		document.body.appendChild(s);
	}

	function boot() {
		var cfg = window.PDF_FLIPPER_CFG;
		if (!cfg || !document.querySelector('[data-pdf]')) {
			return;
		}

		loadScript(cfg.pageflipUrl, function () {
			loadScript(cfg.pdfjsUrl, function () {
				loadScript(cfg.enhancerUrl, function () {});
			});
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
