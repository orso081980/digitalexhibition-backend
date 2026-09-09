document.addEventListener('DOMContentLoaded', function() {
	// Seleziona tutti i caroselli nella pagina
	const carousels = document.querySelectorAll('.devwebai-carousel-component');

	// Se non ci sono caroselli o le librerie necessarie mancano, esci.
	if (carousels.length === 0 || typeof Swiper === 'undefined') {
		return;
	}

	// Itera su ogni carosello e inizializzalo
	carousels.forEach(carousel => {
		const swiperContainer = carousel.querySelector('.swiper');
		if (!swiperContainer) return;

		const swiperInstance = new Swiper(swiperContainer, {
			loop: true,
			slidesPerView: 1,
			spaceBetween: 10,
			pagination: {
				el: swiperContainer.querySelector('.swiper-pagination'),
				clickable: true,
			},
			navigation: {
				nextEl: swiperContainer.querySelector('.swiper-button-next'),
				prevEl: swiperContainer.querySelector('.swiper-button-prev'),
			},
			breakpoints: {
				768: {
					slidesPerView: 2,
					spaceBetween: 20,
				},
			},
			on: {
				// Gestione del click per la lightbox
				click: function(swiper, event) {
					const clickedSlide = event.target.closest('.swiper-slide');
					if (!clickedSlide || !clickedSlide.dataset.fullSrc) {
						return;
					}

					// Controlla se la lightbox di Elementor è disponibile
					if (typeof elementorFrontend === 'undefined' || !elementorFrontend.getModule('lightbox')) {
						console.warn('Elementor Lightbox not available. Opening image in a new tab as fallback.');
						window.open(clickedSlide.dataset.fullSrc, '_blank');
						return;
					}

					// Costruisce la galleria per la lightbox
					const allSlides = swiper.el.querySelectorAll('.swiper-slide:not(.swiper-slide-duplicate)');
					const gallery = [];
					allSlides.forEach(slide => {
						if (slide.dataset.fullSrc) {
							gallery.push({
								url: slide.dataset.fullSrc,
								type: 'image',
							});
						}
					});

					// Trova l'indice dell'immagine cliccata
					const dataIndex = clickedSlide.getAttribute('data-swiper-slide-index');
					const clickedIndex = dataIndex ? parseInt(dataIndex, 10) : 0;

					const lightboxOptions = {
						slides: gallery,
						initialSlide: clickedIndex,
					};

					// Mostra la lightbox
					elementorFrontend.getModule('lightbox').show(lightboxOptions);
				},
			},
		});
	});
});
