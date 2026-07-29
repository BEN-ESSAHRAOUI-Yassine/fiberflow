<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FiberFlow') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white">
    {{-- Navigation --}}
    <x-landing.nav />

    {{-- Hero --}}
    <x-landing.hero />

    {{-- Social Proof --}}
    <x-landing.social-proof />

    {{-- Features --}}
    <x-landing.features />

    {{-- How It Works --}}
    <x-landing.how-it-works />

    {{-- Product Preview --}}
    <x-landing.product-preview />

    {{-- Comparison --}}
    <x-landing.comparison />

    {{-- Pricing --}}
    <x-landing.pricing />

    {{-- Testimonials --}}
    <x-landing.testimonials />

    {{-- FAQ --}}
    <x-landing.faq />

    {{-- Final CTA --}}
    <x-landing.cta />

    {{-- Footer --}}
    <x-landing.footer />

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- Sticky Nav ---
            const nav = document.getElementById('landing-nav');
            const handleScroll = () => {
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
    </script>
</body>
</html>
