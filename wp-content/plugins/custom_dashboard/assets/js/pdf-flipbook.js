/**
 * Renders each `.pdf-flipbook[data-pdf-src]` element as an interactive
 * page-flip book: pdf.js rasterises every page to an image, page-flip (both
 * vendored under assets/vendor/, MIT/Apache-2.0, no proprietary viewer)
 * turns those images into a flip animation. Replaces the dflip plugin,
 * which this site no longer runs.
 *
 * Loaded as a module (see the inline bootstrap that enqueues this file) so
 * it can `import()` pdf.js's ESM build; page-flip's UMD build must already
 * be loaded as a plain, non-module <script> before this runs — it attaches
 * a global `St.PageFlip`.
 */

const cfg = window.CD_PDF_FLIPBOOK_CFG || {};

async function buildFlipbook(container) {
	const src = container.dataset.pdfSrc;
	if (!src || !cfg.pdfjsUrl || !cfg.workerUrl) {
		return;
	}

	container.classList.add('pdf-flipbook--loading');

	try {
		const pdfjsLib = await import(cfg.pdfjsUrl);
		pdfjsLib.GlobalWorkerOptions.workerSrc = cfg.workerUrl;

		const pdf = await pdfjsLib.getDocument({ url: src }).promise;
		const pages = [];

		for (let i = 1; i <= pdf.numPages; i++) {
			const page = await pdf.getPage(i);
			const viewport = page.getViewport({ scale: 1.5 });

			const canvas = document.createElement('canvas');
			canvas.width = viewport.width;
			canvas.height = viewport.height;
			await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;

			const pageEl = document.createElement('div');
			pageEl.className = 'pdf-flipbook__page';
			const img = document.createElement('img');
			img.src = canvas.toDataURL('image/jpeg', 0.85);
			img.alt = `Page ${i}`;
			pageEl.appendChild(img);
			pages.push(pageEl);
		}

		if (!window.St || !window.St.PageFlip) {
			throw new Error('page-flip library did not load');
		}

		container.classList.remove('pdf-flipbook--loading');
		container.innerHTML = '';

		const flip = new window.St.PageFlip(container, {
			width: 550,
			height: 733,
			size: 'stretch',
			minWidth: 300,
			maxWidth: 1000,
			minHeight: 400,
			maxHeight: 1350,
			showCover: true,
			usePortrait: true,
			maxShadowOpacity: 0.5,
		});
		flip.loadFromHTML(pages);

		addNavButtons(container, flip);
	} catch (err) {
		// eslint-disable-next-line no-console
		console.error('PDF flipbook failed to load:', src, err);
		container.classList.remove('pdf-flipbook--loading');
		container.classList.add('pdf-flipbook--error');
	}
}

function addNavButtons(container, flip) {
	const nav = document.createElement('div');
	nav.className = 'pdf-flipbook__nav';

	const prev = document.createElement('button');
	prev.type = 'button';
	prev.className = 'pdf-flipbook__prev';
	prev.setAttribute('aria-label', 'Previous page');
	prev.textContent = '‹';
	prev.addEventListener('click', () => flip.flipPrev());

	const next = document.createElement('button');
	next.type = 'button';
	next.className = 'pdf-flipbook__next';
	next.setAttribute('aria-label', 'Next page');
	next.textContent = '›';
	next.addEventListener('click', () => flip.flipNext());

	nav.append(prev, next);
	container.insertAdjacentElement('afterend', nav);
}

document.querySelectorAll('.pdf-flipbook[data-pdf-src]').forEach(buildFlipbook);
