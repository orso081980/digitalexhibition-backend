/**
 * Renders each `.pdf-flipbook[data-pdf-src]` element as an interactive
 * page-flip book, replacing the dflip plugin (this site no longer runs it —
 * its own free tier is non-commercial-only, CC BY-NC-ND, not usable here).
 *
 * Two vendored libraries, both plain classic scripts (no ES modules, no
 * server MIME config needed — a real bug the previous version hit):
 *  - pdf.js 3.11.174 (Mozilla, Apache-2.0) — last version with a classic
 *    UMD build; window.pdfjsLib
 *  - flipbook-viewer 1.6.1 (MIT, github.com/theproductiveprogrammer) —
 *    window.flipbook. Purpose-built for exactly this case: it calls a
 *    `getPage(n, cb)` provider on demand for whichever spread is actually
 *    on screen, instead of needing the whole document rendered upfront.
 *    These posters run 5-45MB for a handful of A1 pages — that matters.
 *
 * A book only starts loading once the visitor scrolls near it
 * (IntersectionObserver) — the library's own laziness only covers
 * individual pages, not the initial "start fetching this PDF" decision.
 */

const cfg = window.CD_PDF_FLIPBOOK_CFG || {};
let workerConfigured = false;

// Two different widths on purpose: pages are rendered at RENDER_WIDTH (for
// retina-ish sharpness) but the book itself is displayed at DISPLAY_WIDTH —
// flipbook-viewer's width/height options set the box's actual on-page
// pixel size, not a render target, so passing RENDER_WIDTH there directly
// blew the book up far past its container.
const RENDER_WIDTH = 900;
const DISPLAY_WIDTH = 480;

function pdfBookProvider(pdf) {
	const cache = [];
	return {
		numPages: () => pdf.numPages,
		getPage(n, cb) {
			if (!n || n > pdf.numPages) return cb();
			if (cache[n]) return cb(null, cache[n]);

			pdf.getPage(n)
				.then((page) => {
					const native = page.getViewport({ scale: 1 });
					const scale = Math.min(RENDER_WIDTH / native.width, 2);
					const viewport = page.getViewport({ scale });
					const outputScale = window.devicePixelRatio || 1;

					const canvas = document.createElement('canvas');
					canvas.width = Math.floor(viewport.width * outputScale);
					canvas.height = Math.floor(viewport.height * outputScale);

					const transform = outputScale !== 1 ? [outputScale, 0, 0, outputScale, 0, 0] : null;

					page.render({ canvasContext: canvas.getContext('2d'), transform, viewport }).promise
						.then(() => {
							const img = new Image();
							img.addEventListener('load', () => {
								cache[n] = { img, num: n, width: img.width, height: img.height };
								cb(null, cache[n]);
							});
							img.src = canvas.toDataURL('image/jpeg', 0.85);
						})
						.catch((err) => cb(err));
				})
				.catch((err) => cb(err));
		},
	};
}

function buildControls(container, viewer, pageCount) {
	const nav = document.createElement('div');
	nav.className = 'pdf-flipbook__controls';

	const icon = (d) => `<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true"><path d="${d}" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>`;

	const prev = document.createElement('button');
	prev.type = 'button';
	prev.className = 'pdf-flipbook__btn';
	prev.setAttribute('aria-label', 'Previous page');
	prev.innerHTML = icon('M10 2 4 8l6 6');
	prev.addEventListener('click', () => viewer.flip_back());

	const counter = document.createElement('span');
	counter.className = 'pdf-flipbook__counter';
	counter.textContent = `1 / ${pageCount}`;
	viewer.on('seen', (n) => {
		counter.textContent = `${Number(n) + 1} / ${pageCount}`;
	});

	const next = document.createElement('button');
	next.type = 'button';
	next.className = 'pdf-flipbook__btn';
	next.setAttribute('aria-label', 'Next page');
	next.innerHTML = icon('M6 2l6 6-6 6');
	next.addEventListener('click', () => viewer.flip_forward());

	const zoom = document.createElement('button');
	zoom.type = 'button';
	zoom.className = 'pdf-flipbook__btn';
	zoom.setAttribute('aria-label', 'Zoom');
	zoom.innerHTML = icon('M7 2a5 5 0 1 0 0 10A5 5 0 0 0 7 2ZM11 11l3.5 3.5');
	zoom.addEventListener('click', () => viewer.zoom());

	const fullscreen = document.createElement('button');
	fullscreen.type = 'button';
	fullscreen.className = 'pdf-flipbook__btn';
	fullscreen.setAttribute('aria-label', 'Toggle fullscreen');
	fullscreen.innerHTML = icon('M2 6V2h4M14 6V2h-4M2 10v4h4M14 10v4h-4');
	fullscreen.addEventListener('click', () => {
		if (document.fullscreenElement) {
			document.exitFullscreen();
		} else {
			container.requestFullscreen?.();
		}
	});

	nav.append(prev, counter, next, zoom, fullscreen);
	container.insertAdjacentElement('afterend', nav);
}

async function buildFlipbook(container) {
	const src = container.dataset.pdfSrc;
	if (!src || !window.pdfjsLib || !window.flipbook) {
		return;
	}

	if (!workerConfigured) {
		window.pdfjsLib.GlobalWorkerOptions.workerSrc = cfg.workerUrl;
		workerConfigured = true;
	}

	container.classList.add('pdf-flipbook--loading');

	try {
		const pdf = await window.pdfjsLib.getDocument(src).promise;
		const book = pdfBookProvider(pdf);

		// Box dimensions come from the real page size so the book isn't
		// stuck at flipbook-viewer's 800x600 default for a differently
		// shaped poster; this is metadata only, not a render.
		const firstPage = await pdf.getPage(1);
		const native = firstPage.getViewport({ scale: 1 });
		const boxWidth = DISPLAY_WIDTH;
		const boxHeight = Math.round((native.height / native.width) * boxWidth);

		window.flipbook.init(book, container, { width: boxWidth, height: boxHeight, backgroundColor: 'transparent', boxColor: '#f3f3f3' }, (err, viewer) => {
			if (err) {
				// eslint-disable-next-line no-console
				console.error('PDF flipbook failed to load:', src, err);
				container.classList.remove('pdf-flipbook--loading');
				container.classList.add('pdf-flipbook--error');
				return;
			}
			container.classList.remove('pdf-flipbook--loading');
			buildControls(container, viewer, viewer.page_count);
		});
	} catch (err) {
		// eslint-disable-next-line no-console
		console.error('PDF flipbook failed to load:', src, err);
		container.classList.remove('pdf-flipbook--loading');
		container.classList.add('pdf-flipbook--error');
	}
}

const observer = new IntersectionObserver(
	(entries) => {
		for (const entry of entries) {
			if (entry.isIntersecting) {
				observer.unobserve(entry.target);
				buildFlipbook(entry.target);
			}
		}
	},
	{ rootMargin: '300px 0px' }
);

document.querySelectorAll('.pdf-flipbook[data-pdf-src]').forEach((el) => observer.observe(el));
