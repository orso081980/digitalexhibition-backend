/**
 * Renders each `.pdf-flipbook[data-pdf-src]` element as an interactive
 * page-flip book: pdf.js rasterises pages to images, page-flip (both
 * vendored under assets/vendor/, MIT/Apache-2.0, no proprietary viewer)
 * turns those into a flip animation. Replaces the dflip plugin, which
 * this site no longer runs.
 *
 * These are exhibition posters, not ordinary documents — real ones on
 * this site run 5-45MB for a handful of A1-sized pages. Two things that
 * matter a lot more here than for a typical small PDF:
 *  - never start work on a book the visitor hasn't scrolled to yet
 *    (IntersectionObserver-gated init)
 *  - show the book as soon as its first page is ready rather than
 *    blocking on the whole document; remaining pages render in the
 *    background and get slotted into the placeholders already handed
 *    to page-flip (no page-flip "replace this page" API needed — the
 *    library just uses whatever DOM nodes it was given, so mutating a
 *    page's own contents after load works fine)
 *
 * Loaded as a module (see the inline bootstrap that enqueues this file) so
 * it can `import()` pdf.js's ESM build; page-flip's UMD build must already
 * be loaded as a plain, non-module <script> before this runs — it attaches
 * a global `St.PageFlip`. Load order between the two doesn't matter: a
 * `type="module"` script always runs after every earlier classic script,
 * regardless of where either tag lands in the document.
 */

const cfg = window.CD_PDF_FLIPBOOK_CFG || {};

// Cap the rendered pixel width regardless of the PDF's native page size —
// these posters are often A1+, and rendering at their native resolution for
// a ~550px-wide book is pure waste (and, multiplied by every page, a large
// part of why this used to hang the page).
const TARGET_PAGE_WIDTH = 900;

let pdfjsLibPromise = null;
function getPdfjsLib() {
	if (!pdfjsLibPromise) {
		pdfjsLibPromise = import(cfg.pdfjsUrl).then((lib) => {
			lib.GlobalWorkerOptions.workerSrc = cfg.workerUrl;
			return lib;
		});
	}
	return pdfjsLibPromise;
}

/** Render one PDF page to a data-URL image at a display-appropriate size. */
async function renderPageImage(pdf, pageNumber) {
	const page = await pdf.getPage(pageNumber);
	const nativeViewport = page.getViewport({ scale: 1 });
	const scale = Math.min(TARGET_PAGE_WIDTH / nativeViewport.width, 2);
	const viewport = page.getViewport({ scale });

	const canvas = document.createElement('canvas');
	canvas.width = viewport.width;
	canvas.height = viewport.height;
	await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;

	const url = canvas.toDataURL('image/jpeg', 0.82);
	return { url, width: viewport.width, height: viewport.height };
}

function buildControls(container, flip, pageCount) {
	const nav = document.createElement('div');
	nav.className = 'pdf-flipbook__controls';

	const prev = document.createElement('button');
	prev.type = 'button';
	prev.className = 'pdf-flipbook__btn pdf-flipbook__prev';
	prev.setAttribute('aria-label', 'Previous page');
	prev.innerHTML = '<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true"><path d="M10 2 4 8l6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
	prev.addEventListener('click', () => flip.flipPrev());

	const counter = document.createElement('span');
	counter.className = 'pdf-flipbook__counter';
	const updateCounter = () => {
		counter.textContent = `${flip.getCurrentPageIndex() + 1} / ${pageCount}`;
	};
	updateCounter();
	flip.on('flip', updateCounter);

	const next = document.createElement('button');
	next.type = 'button';
	next.className = 'pdf-flipbook__btn pdf-flipbook__next';
	next.setAttribute('aria-label', 'Next page');
	next.innerHTML = '<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true"><path d="M6 2l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
	next.addEventListener('click', () => flip.flipNext());

	const fullscreen = document.createElement('button');
	fullscreen.type = 'button';
	fullscreen.className = 'pdf-flipbook__btn pdf-flipbook__fullscreen';
	fullscreen.setAttribute('aria-label', 'Toggle fullscreen');
	fullscreen.innerHTML = '<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true"><path d="M2 6V2h4M14 6V2h-4M2 10v4h4M14 10v4h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
	fullscreen.addEventListener('click', () => {
		if (document.fullscreenElement) {
			document.exitFullscreen();
		} else {
			container.requestFullscreen?.();
		}
	});

	nav.append(prev, counter, next, fullscreen);
	container.insertAdjacentElement('afterend', nav);
}

async function buildFlipbook(container) {
	const src = container.dataset.pdfSrc;
	if (!src || !cfg.pdfjsUrl || !cfg.workerUrl) {
		return;
	}

	container.classList.add('pdf-flipbook--loading');

	try {
		const pdfjsLib = await getPdfjsLib();
		const pdf = await pdfjsLib.getDocument({ url: src }).promise;
		const pageCount = pdf.numPages;

		// First page decides the book's aspect ratio; it's rendered before
		// anything else becomes visible, everything past it is background work.
		const first = await renderPageImage(pdf, 1);

		container.classList.remove('pdf-flipbook--loading');
		container.innerHTML = '';
		container.style.setProperty('--pdf-flipbook-ratio', `${first.width} / ${first.height}`);

		const pageEls = [];
		for (let i = 1; i <= pageCount; i++) {
			const pageEl = document.createElement('div');
			pageEl.className = 'pdf-flipbook__page';
			if (i === 1) {
				const img = document.createElement('img');
				img.src = first.url;
				img.alt = 'Page 1';
				pageEl.appendChild(img);
			} else {
				pageEl.classList.add('pdf-flipbook__page--pending');
			}
			pageEls.push(pageEl);
		}

		const flip = new window.St.PageFlip(container, {
			width: first.width,
			height: first.height,
			size: 'stretch',
			minWidth: 200,
			maxWidth: 1400,
			minHeight: 260,
			maxHeight: 1800,
			showCover: true,
			usePortrait: true,
			maxShadowOpacity: 0.5,
		});
		flip.loadFromHTML(pageEls);

		buildControls(container, flip, pageCount);

		// Remaining pages render in the background, one at a time — these
		// posters are large enough that rendering several concurrently just
		// competes for the same bandwidth and CPU without finishing any of
		// them sooner.
		(async () => {
			for (let i = 2; i <= pageCount; i++) {
				try {
					const { url } = await renderPageImage(pdf, i);
					const pageEl = pageEls[i - 1];
					pageEl.classList.remove('pdf-flipbook__page--pending');
					const img = document.createElement('img');
					img.src = url;
					img.alt = `Page ${i}`;
					pageEl.appendChild(img);
				} catch (err) {
					// eslint-disable-next-line no-console
					console.error('Failed to render page', i, 'of', src, err);
				}
			}
		})();
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
