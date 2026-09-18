/**
 * SunLyvo Nexus — 主站脚本
 *
 * 职责：
 *   1. Toast 提示（供 header.js / reader.js 调用）
 *   2. 平滑锚点滚动
 *   3. prefers-reduced-motion 检测
 *   4. 全局错误捕获
 *
 * 注意：主题模式（dark/light）由 header.js + 内联脚本处理，
 *      main.js 不请求任何 /theme 端点。
 *
 * @package SunLyvo_Nexus
 */
(function () {
    'use strict';

    /**
     * Toast 提示（全局导出）
     */
    function showToast(message, duration) {
        duration = duration || 2000;
        var existing = document.querySelector('.slv-toast');
        if (existing) existing.remove();

        var toast = document.createElement('div');
        toast.className = 'slv-toast';
        toast.textContent = message;
        document.body.appendChild(toast);
        requestAnimationFrame(function () {
            toast.classList.add('is-visible');
        });
        setTimeout(function () {
            toast.classList.remove('is-visible');
            setTimeout(function () { toast.remove(); }, 300);
        }, duration);
    }

    /**
     * 偏好检测：减少动效
     */
    function prefersReducedMotion() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    /**
     * 平滑锚点滚动
     */
    function initSmoothAnchors() {
        document.querySelectorAll('a[href^="#"]:not([href="#"])').forEach(function (link) {
            link.addEventListener('click', function (e) {
                var id = link.getAttribute('href').slice(1);
                var target = document.getElementById(id);
                if (!target) return;
                e.preventDefault();
                target.scrollIntoView({
                    behavior: prefersReducedMotion() ? 'auto' : 'smooth',
                    block: 'start',
                });
                if (history.replaceState) {
                    history.replaceState(null, '', '#' + id);
                }
            });
        });
    }

    /**
     * 图片懒加载兜底
     */
    function initLazyImages() {
        if ('loading' in HTMLImageElement.prototype) return;
        document.querySelectorAll('img[data-src]').forEach(function (img) {
            img.src = img.dataset.src;
        });
    }

    /**
     * 全局错误捕获
     */
    function initErrorHandler() {
        window.addEventListener('error', function (e) {
            if (window.SLV_CONFIG && window.SLV_CONFIG.debug) {
                console.error('[SLV Error]', e.message, e.filename, e.lineno);
            }
        });
    }

    /**
     * 初始化
     */
    function init() {
        initSmoothAnchors();
        initLazyImages();
        initErrorHandler();
    }

    /* 全局导出 */
    window.SLV_TOAST = showToast;
    window.SLV_PREFERS_REDUCED_MOTION = prefersReducedMotion;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();