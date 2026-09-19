/**
 * SunLyvo Nexus — 商品卡多图轮播（Swiper 封装）
 *
 * 依赖：Swiper 11（由 enqueue.php 加载）
 *
 * @package SunLyvo_Nexus
 * @since 7.0.0
 */

(function () {
    'use strict';

    function initProductSwipers() {
        if (typeof Swiper === 'undefined') {
            console.warn('[SunLyvo] Swiper 未加载');
            return;
        }

        document.querySelectorAll('.slv-product-card__swiper').forEach(function (el) {
            // 避免重复初始化
            if (el.dataset.swiperInitialized === '1') return;
            el.dataset.swiperInitialized = '1';

            var slideCount = el.querySelectorAll('.swiper-slide').length;
            if (slideCount <= 1) return;

            new Swiper(el, {
                loop: false,
                speed: 300,
                spaceBetween: 0,
                watchSlidesProgress: true,
                // 圆点
                pagination: {
                    el: el.querySelector('.slv-product-card__pagination'),
                    clickable: true,
                    bulletClass: 'slv-product-card__dot',
                    bulletActiveClass: 'slv-product-card__dot--active',
                },
                // 箭头
                navigation: {
                    nextEl: el.querySelector('.slv-product-card__nav--next'),
                    prevEl: el.querySelector('.slv-product-card__nav--prev'),
                    disabledClass: 'slv-product-card__nav--disabled',
                },
                // 移动端滑动
                touchRatio: 1,
                simulateTouch: true,
                grabCursor: false,
                // 阻止卡片的 <a> 链接在滑动时被误触
                preventClicks: true,
                preventClicksPropagation: true,
                // 无障碍
                a11y: {
                    enabled: true,
                    prevSlideMessage: '上一张',
                    nextSlideMessage: '下一张',
                },
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initProductSwipers);
    } else {
        initProductSwipers();
    }

    // 动态插入时重新初始化
    var observer = new MutationObserver(function (mutations) {
        var shouldInit = false;
        mutations.forEach(function (m) {
            m.addedNodes.forEach(function (node) {
                if (node.nodeType === 1 && (
                    node.classList?.contains('slv-product-card__swiper') ||
                    node.querySelector?.('.slv-product-card__swiper')
                )) {
                    shouldInit = true;
                }
            });
        });
        if (shouldInit) initProductSwipers();
    });
    observer.observe(document.body, { childList: true, subtree: true });
})();