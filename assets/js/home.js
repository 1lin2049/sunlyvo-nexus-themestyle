/**
 * SunLyvo Nexus — 首页交互
 *
 * @package SunLyvo_Nexus
 */
(function () {
    'use strict';

    /**
     * 数字滚动动效。
     */
    function initStatsCounters() {
        var stats = document.querySelectorAll('.slv-stat[data-animate="count"]');
        if (!stats.length || !('IntersectionObserver' in window)) return;

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                var el = entry.target;
                if (el.dataset.animated === '1') return;
                el.dataset.animated = '1';
                animateCounter(el);
                observer.unobserve(el);
            });
        }, { threshold: 0.3 });

        stats.forEach(function (stat) { observer.observe(stat); });
    }

    function animateCounter(el) {
        var target = parseInt(el.dataset.target || '0', 10);
        var suffix = el.dataset.suffix || '';
        var valueEl = el.querySelector('.slv-stat__value');
        if (!valueEl) return;

        var reduced = window.SLV_PREFERS_REDUCED_MOTION && window.SLV_PREFERS_REDUCED_MOTION();
        if (reduced) {
            valueEl.textContent = target + suffix;
            return;
        }

        var duration = 1500;
        var start = performance.now();
        var easeOut = function (t) { return 1 - Math.pow(1 - t, 3); };

        function tick(now) {
            var elapsed = now - start;
            var progress = Math.min(1, elapsed / duration);
            var current = Math.round(target * easeOut(progress));
            valueEl.textContent = current + suffix;
            if (progress < 1) {
                requestAnimationFrame(tick);
            }
        }
        requestAnimationFrame(tick);
    }

    /**
     * 卡片入场动效。
     */
    function initRevealAnimations() {
        var targets = document.querySelectorAll(
            '.slv-feature-card, .slv-product-card, .slv-testimonial, .slv-pricing__card, .slv-faq__item'
        );
        if (!targets.length || !('IntersectionObserver' in window)) return;

        var reduced = window.SLV_PREFERS_REDUCED_MOTION && window.SLV_PREFERS_REDUCED_MOTION();
        if (reduced) return;

        targets.forEach(function (el) {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 600ms cubic-bezier(0.4,0,0.2,1), transform 600ms cubic-bezier(0.34,1.56,0.64,1)';
        });

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        targets.forEach(function (el) { observer.observe(el); });
    }

    /**
     * FAQ 手风琴（仅同时打开一个）。
     */
    function initFaqAccordion() {
        var items = document.querySelectorAll('.slv-faq__item');
        items.forEach(function (item) {
            item.addEventListener('toggle', function () {
                if (item.open) {
                    items.forEach(function (other) {
                        if (other !== item && other.open) other.open = false;
                    });
                }
            });
        });
    }

    /**
     * Hero 视差（鼠标移动）。
     */
    function initHeroParallax() {
        var hero = document.querySelector('.slv-hero');
        if (!hero) return;
        var reduced = window.SLV_PREFERS_REDUCED_MOTION && window.SLV_PREFERS_REDUCED_MOTION();
        if (reduced) return;

        var blob1 = hero.querySelector('.slv-hero__blob--1');
        var blob2 = hero.querySelector('.slv-hero__blob--2');
        if (!blob1 || !blob2) return;

        hero.addEventListener('mousemove', function (e) {
            var rect = hero.getBoundingClientRect();
            var x = (e.clientX - rect.left) / rect.width - 0.5;
            var y = (e.clientY - rect.top) / rect.height - 0.5;

            blob1.style.transform = 'translate(' + (x * 30) + 'px, ' + (y * 30) + 'px)';
            blob2.style.transform = 'translate(' + (x * -30) + 'px, ' + (y * -30) + 'px)';
        });
    }

    function init() {
        initStatsCounters();
        initRevealAnimations();
        initFaqAccordion();
        initHeroParallax();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();