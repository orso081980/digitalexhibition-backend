<?php
/**
 * The template for displaying the footer.
 *
 * Contains the body & html closing tags.
 *
 * @package HelloElementor
 */
?>

</div>
</div>

</div>
</div>
<script>
window.addEventListener('load', function () {

    // Aggiunge 'blocco-custom' alla inner-section boxed senza heading (single post)
    if (document.body.classList.contains('single') && !document.body.classList.contains('page')) {
        document.querySelectorAll('.elementor-inner-section.elementor-section-boxed').forEach(function(section) {
            const hasHeading = section.querySelector('.elementor-heading-title');
            if (!hasHeading && !section.classList.contains('blocco-custom')) {
                section.classList.add('blocco-custom');
            }
        });
    }

    // Seleziona tutti i caroselli nella pagina
    const carousels = document.querySelectorAll('.project-timeline-carousel');

    carousels.forEach(carousel => {
        if (!carousel.classList.contains('has-navigation')) return;

        const track      = carousel.querySelector('.carousel-track');
        const items      = Array.from(track.children);
        const nextButton = carousel.querySelector('.carousel-button.next');
        const prevButton = carousel.querySelector('.carousel-button.prev');

        const visibleCount = 3;
        let index = 0;

        const currentIndex = items.findIndex(li => li.classList.contains('current'));
        if (currentIndex > 0) index = currentIndex;

        const maxIndex = () => Math.max(0, items.length - visibleCount);

        const updateCarousel = () => {
            // Su viewport largo i bottoni sono nascosti: non applicare transform
            if (window.innerWidth > 400) {
                track.style.transform = '';
                return;
            }
            const clampedIndex = Math.min(index, maxIndex());
            const itemWidth = items[0].getBoundingClientRect().width;
            const targetOffset = clampedIndex * itemWidth;
            track.style.transform = `translateX(-${targetOffset}px)`;
            prevButton.disabled = clampedIndex === 0;
            nextButton.disabled = clampedIndex >= maxIndex();
        };

        window.addEventListener('resize', updateCarousel);

        nextButton.addEventListener('click', () => {
            if (index < maxIndex()) {
                index++;
                updateCarousel();
            }
        });

        prevButton.addEventListener('click', () => {
            if (index > 0) {
                index--;
                updateCarousel();
            }
        });

        updateCarousel();
    });
});
</script>
<?php wp_footer(); ?>

</body>
</html>
