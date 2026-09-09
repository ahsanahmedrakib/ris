import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.hero-swiper').forEach((el) => {
        new Swiper(el, {
            loop: true,
            speed: 800,
            effect: 'fade',
            fadeEffect: { crossFade: true },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            pagination: {
                el: el.querySelector('.swiper-pagination'),
                clickable: true,
            },
        });
    });

    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const revealSelector = '.reveal, .reveal-left, .reveal-right, .reveal-zoom';
    const revealEls = document.querySelectorAll(revealSelector);

    document.querySelectorAll('.reveal-stagger').forEach((parent) => {
        [...parent.querySelectorAll(`:scope > ${revealSelector}`)].forEach((child, i) => {
            child.style.transitionDelay = `${Math.min(i * 90, 720)}ms`;
        });
    });

    if (!prefersReduced && revealEls.length && 'IntersectionObserver' in window) {
        const io = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        io.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.1, rootMargin: '0px 0px -48px 0px' }
        );
        revealEls.forEach((el) => io.observe(el));
    } else {
        revealEls.forEach((el) => el.classList.add('revealed'));
    }

    const countEls = document.querySelectorAll('[data-count]');
    if (countEls.length) {
        const format = (n) => new Intl.NumberFormat('bn-BD').format(n);
        const ioCount = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                ioCount.unobserve(el);
                const target = parseInt(el.dataset.count, 10) || 0;
                if (prefersReduced) {
                    el.textContent = format(target);
                    return;
                }
                const dur = 1500;
                const start = performance.now();
                const tick = (now) => {
                    const p = Math.min((now - start) / dur, 1);
                    const eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = format(Math.round(target * eased));
                    if (p < 1) requestAnimationFrame(tick);
                };
                requestAnimationFrame(tick);
            });
        }, { threshold: 0.4 });
        countEls.forEach((el) => ioCount.observe(el));
    }
});