/**
 * SunLyvo Nexus — 商品卡多图轮播
 *
 * 参考 Ant Design Carousel 交互规范：
 *   1. 悬停显示左右箭头 + 圆点
 *   2. 点击箭头/圆点切换
 *   3. 移动端支持滑动
 *
 * @package SunLyvo_Nexus
 * @since 6.0.0
 */

(function () {
    'use strict';

    function initCarousel(card) {
        var slides = card.querySelectorAll('[data-slv-slide]');
        var dots = card.querySelectorAll('[data-slv-dot]');
        var prev = card.querySelector('[data-slv-carousel-prev]');
        var next = card.querySelector('[data-slv-carousel-next]');
        if (slides.length <= 1) return;

        var current = 0;

        function goTo(index) {
            if (index < 0) index = slides.length - 1;
            if (index >= slides.length) index = 0;
            current = index;

            slides.forEach(function (slide, i) {
                slide.classList.toggle('is-active', i === current);
            });
            dots.forEach(function (dot, i) {
                dot.classList.toggle('is-active', i === current);
            });
        }

        if (prev) {
            prev.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                goTo(current - 1);
            });
        }
        if (next) {
            next.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                goTo(current + 1);
            });
        }
        dots.forEach(function (dot, i) {
            dot.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                goTo(i);
            });
        });

        // 移动端滑动
        var startX = 0, startY = 0, isDown = false;
        var media = card.querySelector('.slv-product-card__media');
        if (media) {
            media.addEventListener('touchstart', function (e) {
                if (e.touches.length !== 1) return;
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
                isDown = true;
            }, { passive: true });

            media.addEventListener('touchend', function (e) {
                if (!isDown) return;
                isDown = false;
                var endX = e.changedTouches[0].clientX;
                var endY = e.changedTouches[0].clientY;
                var dx = endX - startX;
                var dy = endY - startY;
                if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) {
                    if (dx < 0) goTo(current + 1);
                    else goTo(current - 1);
                }
            }, { passive: true });
        }
    }

    function initAll() {
        document.querySelectorAll('.slv-product-card.has-multiple-images').forEach(initCarousel);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    // 兼容动态插入（如筛选后重新渲染）
    var observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (m) {
            m.addedNodes.forEach(function (node) {
                if (node.nodeType === 1) {
                    if (node.classList && node.classList.contains('slv-product-card')) {
                        initCarousel(node);
                    }
                    node.querySelectorAll && node.querySelectorAll('.slv-product-card.has-multiple-images').forEach(initCarousel);
                }
            });
        });
    });
    observer.observe(document.body, { childList: true, subtree: true });
})();