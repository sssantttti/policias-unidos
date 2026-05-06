/* Policías Unidos — frontend behavior */

document.addEventListener('DOMContentLoaded', function () {
    // -------- AOS init --------
    if (window.AOS) {
        AOS.init({
            duration: 700,
            easing: 'ease-out-cubic',
            once: true,
            offset: 60,
        });
    }

    // -------- Mobile nav toggle --------
    const toggle = document.querySelector('.navbar__toggle');
    const links = document.querySelector('.navbar__links');
    if (toggle && links) {
        toggle.addEventListener('click', function () {
            links.classList.toggle('is-open');
        });
    }

    // -------- CountUp on stats (when in view) --------
    const counters = document.querySelectorAll('[data-countup]');
    if (counters.length && window.countUp) {
        const { CountUp } = window.countUp;
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const end = parseFloat(el.dataset.countup);
                    const counter = new CountUp(el, end, {
                        duration: 2.2,
                        separator: '.',
                    });
                    if (!counter.error) counter.start();
                    observer.unobserve(el);
                }
            });
        }, { threshold: 0.4 });
        counters.forEach(c => observer.observe(c));
    }

    // -------- Swiper for testimonials (mobile) --------
    if (window.Swiper && document.querySelector('.testimonials-swiper')) {
        // eslint-disable-next-line no-new
        new Swiper('.testimonials-swiper', {
            slidesPerView: 1,
            spaceBetween: 24,
            pagination: { el: '.swiper-pagination', clickable: true },
            breakpoints: {
                760: { slidesPerView: 2 },
                1080: { slidesPerView: 3 },
            },
        });
    }

    // -------- GSAP hero entrance (Removed in favor of AOS) --------

    // -------- News filter (client-side demo) --------
    const filterBtns = document.querySelectorAll('.news-filters button');
    const newsCards = document.querySelectorAll('[data-category]');
    filterBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            filterBtns.forEach(b => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            const cat = btn.dataset.filter;
            newsCards.forEach(function (card) {
                if (cat === 'all' || card.dataset.category === cat) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // -------- Form: prevent default submit (demo only) --------
    document.querySelectorAll('form[data-demo]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const status = form.querySelector('.form__status');
            if (status) {
                status.textContent = 'Gracias. Recibimos tu mensaje y nos pondremos en contacto a la brevedad.';
                status.style.color = '#1A2744';
            }
            form.reset();
        });
    });
});
