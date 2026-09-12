/**
 * Turns every unprocessed `[data-pdf]` element into an interactive
 * page-flip book. Loaded by pdf-flipper-bootstrap.js only once it's
 * confirmed at least one such element exists on the page.
 *
 * Engine: pdf.js (window.pdfjsLib) + page-flip (window.St), both plain
 * classic scripts. A book only starts loading once the visitor scrolls
 * near it (IntersectionObserver) — these are large exhibition posters
 * (multi-MB, often A1-sized pages), so that matters a lot more here
 * than for an ordinary PDF. The book becomes visible and interactive
 * as soon as page 1 is ready rather than blocking on the whole
 * document; remaining pages render one at a time in the background and
 * get slotted into the placeholder page nodes page-flip was already
 * given (this build has no "replace this page" API, but mutating a
 * page div's own contents after load works fine — page-flip just uses
 * whatever DOM nodes it's handed).
 *
 * Whatever markup already exists inside a [data-pdf] element (e.g. a
 * plain download link, meant as a no-JS fallback) is left in place,
 * after the book — never removed.
 */

const cfg = window.PDF_FLIPPER_CFG || {};

// Two different widths on purpose: pages render at RENDER_WIDTH (for
// retina-ish sharpness) but the book displays at DISPLAY_WIDTH.
const RENDER_WIDTH = 900;
const DISPLAY_WIDTH = 480;

async function renderPageImage(pdf, pageNumber) {
	const page = await pdf.getPage(pageNumber);
	const nativeViewport = page.getViewport({ scale: 1 });
	const scale = Math.min(RENDER_WIDTH / nativeViewport.width, 2);
	const viewport = page.getViewport({ scale });

	const canvas = document.createElement('canvas');
	canvas.width = viewport.width;
	canvas.height = viewport.height;
	await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;

	return { url: canvas.toDataURL('image/jpeg', 0.82), width: viewport.width, height: viewport.height };
}

function icon(d) {
	return `<svg viewBox="0 0 16 16" width="16" height="16" aria-hidden="true"><path d="${d}" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
}

function buildControls(bookEl, flip, pageCount) {
	const bar = document.createElement('div');
	bar.className = 'pdf-flipper__bar';

	const btn = (label, path, onClick) => {
		const b = document.createElement('button');
		b.type = 'button';
		b.className = 'pdf-flipper__btn';
		b.setAttribute('aria-label', label);
		b.innerHTML = icon(path);
		b.addEventListener('click', onClick);
		return b;
	};

	const prev = btn('Previous page', 'M10 2 4 8l6 6', () => flip.flipPrev());

	const counter = document.createElement('span');
	counter.className = 'pdf-flipper__counter';
	const updateCounter = () => {
		counter.textContent = `${flip.getCurrentPageIndex() + 1} / ${pageCount}`;
	};
	updateCounter();
	flip.on('flip', updateCounter);

	const next = btn('Next page', 'M6 2l6 6-6 6', () => flip.flipNext());
	const fullscreen = btn('Toggle fullscreen', 'M2 6V2h4M14 6V2h-4M2 10v4h4M14 10v4h-4', () => {
		if (document.fullscreenElement) {
			document.exitFullscreen();
		} else {
			bookEl.requestFullscreen?.();
		}
	});

	bar.append(prev, counter, next, fullscreen);
	bookEl.appendChild(bar);
}

async function buildFlipbook(el) {
	const src = el.dataset.pdf;
	if (!src || !window.pdfjsLib || !window.St) {
		return;
	}

	const bookEl = document.createElement('div');
	bookEl.className = 'pdf-flipper pdf-flipper--loading';
	el.prepend(bookEl);

	try {
		window.pdfjsLib.GlobalWorkerOptions.workerSrc = cfg.workerUrl;
		const pdf = await window.pdfjsLib.getDocument(src).promise;
		const pageCount = pdf.numPages;

		// First page decides the book's aspect ratio and is rendered before
		// anything else becomes visible; the rest is background work.
		const first = await renderPageImage(pdf, 1);

		bookEl.classList.remove('pdf-flipper--loading');
		bookEl.innerHTML = '';

		const pageEls = [];
		for (let i = 1; i <= pageCount; i++) {
			const pageEl = document.createElement('div');
			pageEl.className = 'pdf-flipper__page';
			if (i === 1) {
				const img = document.createElement('img');
				img.src = first.url;
				img.alt = 'Page 1';
				pageEl.appendChild(img);
			} else {
				pageEl.classList.add('pdf-flipper__page--pending');
			}
			pageEls.push(pageEl);
		}

		const boxWidth = DISPLAY_WIDTH;
		const boxHeight = Math.round((first.height / first.width) * boxWidth);

		const flip = new window.St.PageFlip(bookEl, {
			width: boxWidth,
			height: boxHeight,
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

		buildControls(bookEl, flip, pageCount);

		// Remaining pages render one at a time in the background — these
		// posters are large enough that rendering several concurrently just
		// competes for the same bandwidth and CPU without finishing any of
		// them sooner.
		(async () => {
			for (let i = 2; i <= pageCount; i++) {
				try {
					const { url } = await renderPageImage(pdf, i);
					const pageEl = pageEls[i - 1];
					pageEl.classList.remove('pdf-flipper__page--pending');
					const img = document.createElement('img');
					img.src = url;
					img.alt = `Page ${i}`;
					pageEl.appendChild(img);
				} catch (err) {
					// eslint-disable-next-line no-console
					console.error('PDF Flipper: failed to render page', i, 'of', src, err);
				}
			}
		})();
	} catch (err) {
		// eslint-disable-next-line no-console
		console.error('PDF Flipper failed to load:', src, err);
		bookEl.classList.remove('pdf-flipper--loading');
		bookEl.classList.add('pdf-flipper--error');
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

document.querySelectorAll('[data-pdf]').forEach((el) => observer.observe(el));
