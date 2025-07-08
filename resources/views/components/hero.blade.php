<!-- resources/views/components/hero.blade.php -->
@php
    $heroImages = [
        asset('images/hero1.jpg'),
        asset('images/hero2.jpg'),
        asset('images/hero3.jpg'),
    ];
@endphp

<header id="hero" class="hero" data-images='@json($heroImages)'>
    <h1>Experience the Hype</h1>
    <p>Book tickets to the hottest concerts, festivals & more</p>
    <a href={{ route('buy_tickets') }} class="cta-button">Explore Events</a>
    <div class="slider-dots" id="slider-dots"></div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const hero = document.getElementById('hero');
        const images = JSON.parse(hero.dataset.images);
        let currentIndex = 0;

        const updateHeroImage = () => {
            hero.style.backgroundImage = `url(${images[currentIndex]})`;
            updateDots();
        };

        const updateDots = () => {
            const dotsContainer = document.getElementById('slider-dots');
            dotsContainer.innerHTML = '';
            images.forEach((_, idx) => {
                const dot = document.createElement('span');
                dot.className = 'dot' + (idx === currentIndex ? ' active' : '');
                dot.addEventListener('click', () => {
                    currentIndex = idx;
                    updateHeroImage();
                });
                dotsContainer.appendChild(dot);
            });
        };

        setInterval(() => {
            currentIndex = (currentIndex + 1) % images.length;
            updateHeroImage();
        }, 4000);

        updateHeroImage();
    });
</script>