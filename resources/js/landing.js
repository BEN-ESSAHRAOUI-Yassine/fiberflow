document.addEventListener('DOMContentLoaded', () => {
    // --- Sticky Nav ---
    const nav = document.getElementById('landing-nav');
    const handleScroll = () => {
        if (!nav) return;
        if (window.scrollY > 50) {
            nav.classList.remove('ff-nav-transparent');
            nav.classList.add('ff-nav-solid');
        } else {
            nav.classList.remove('ff-nav-solid');
            nav.classList.add('ff-nav-transparent');
        }
    };
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();

    // --- Scroll Reveal (Intersection Observer) ---
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });

    document.querySelectorAll('.ff-animate-in').forEach((el) => observer.observe(el));

    // --- Counter Animation ---
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('[data-count-to]').forEach((el) => {
        counterObserver.observe(el);
    });

    function animateCounter(el) {
        const target = parseInt(el.getAttribute('data-count-to'), 10);
        const duration = 1500;
        const start = performance.now();

        function update(now) {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = Math.round(target * eased);
            el.textContent = current.toLocaleString();

            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                el.textContent = target.toLocaleString();
            }
        }

        requestAnimationFrame(update);
    }

    // --- FAQ Accordion ---
    document.querySelectorAll('[data-faq]').forEach((item) => {
        const trigger = item.querySelector('.ff-faq-trigger');
        const content = item.querySelector('.ff-faq-content');
        const icon = item.querySelector('.faq-icon');

        trigger.addEventListener('click', () => {
            const isOpen = content.style.maxHeight && content.style.maxHeight !== '0px';

            document.querySelectorAll('[data-faq]').forEach((other) => {
                if (other !== item) {
                    other.querySelector('.ff-faq-content').style.maxHeight = '0px';
                    other.querySelector('.ff-faq-content').style.opacity = '0';
                    other.querySelector('.ff-faq-trigger').setAttribute('aria-expanded', 'false');
                    other.querySelector('.faq-icon').style.transform = 'rotate(0deg)';
                }
            });

            if (isOpen) {
                content.style.maxHeight = '0px';
                content.style.opacity = '0';
                trigger.setAttribute('aria-expanded', 'false');
                icon.style.transform = 'rotate(0deg)';
            } else {
                content.style.maxHeight = content.scrollHeight + 'px';
                content.style.opacity = '1';
                trigger.setAttribute('aria-expanded', 'true');
                icon.style.transform = 'rotate(180deg)';
            }
        });
    });

    // --- Smooth Scroll for Anchor Links ---
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', (e) => {
            const target = document.querySelector(anchor.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
});
