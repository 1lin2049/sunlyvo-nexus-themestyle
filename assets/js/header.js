/**
 * SunLyvo Nexus — Header 交互
 *
 * 包含：深色/浅色模式切换、移动端菜单、搜索框展开、粘性头部
 *
 * @package SunLyvo_Nexus
 * @since 1.0.1
 */

(function () {
    'use strict';

    var STORAGE_KEY = 'slv_theme';
    var MODE_KEY = 'slv_theme_mode';

    /* ═══════════════════════════════════════════════
       1. 深色模式
       ═══════════════════════════════════════════════ */

    function getPreferredTheme() {
        try {
            var stored = localStorage.getItem(STORAGE_KEY);
            if (stored === 'light' || stored === 'dark') return stored;
        } catch (e) {}
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function applyTheme(theme, mode) {
        var root = document.documentElement;
        root.dataset.theme = theme;
        if (mode) root.dataset.themeMode = mode;
        updateToggleUI(theme);
    }

    function updateToggleUI(theme) {
        var btns = document.querySelectorAll(
            '[data-theme-toggle], .slv-theme-toggle, .theme-toggle, button[aria-label*="主题"], button[aria-label*="模式"], button[aria-label*="深色"], button[aria-label*="浅色"]'
        );
        for (var i = 0; i < btns.length; i++) {
            btns[i].setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
            btns[i].setAttribute('title', theme === 'dark' ? '切换到浅色模式' : '切换到深色模式');
        }
    }

    function toggleTheme() {
        var root = document.documentElement;
        var current = root.dataset.theme;
        if (current !== 'light' && current !== 'dark') {
            current = getPreferredTheme();
        }
        var next = current === 'dark' ? 'light' : 'dark';
        try {
            localStorage.setItem(STORAGE_KEY, next);
            localStorage.setItem(MODE_KEY, 'manual');
        } catch (e) {}
        applyTheme(next, 'manual');
        document.dispatchEvent(new CustomEvent('slv:theme-change', { detail: { theme: next } }));
    }

    document.addEventListener('click', function (e) {
        var el = e.target;
        while (el && el !== document) {
            if (el.matches && el.matches(
                '[data-theme-toggle], .slv-theme-toggle, .theme-toggle, button[aria-label*="主题"], button[aria-label*="模式"], button[aria-label*="深色"], button[aria-label*="浅色"]'
            )) {
                e.preventDefault();
                toggleTheme();
                return;
            }
            el = el.parentNode;
        }
    });

    (function initTheme() {
        var stored = null;
        var mode = 'auto';
        try {
            stored = localStorage.getItem(STORAGE_KEY);
            mode = localStorage.getItem(MODE_KEY) || 'auto';
        } catch (e) {}

        if (stored === 'light' || stored === 'dark') {
            applyTheme(stored, mode);
        } else {
            applyTheme(getPreferredTheme(), 'auto');
        }

        try {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
                var m = 'auto';
                try { m = localStorage.getItem(MODE_KEY) || 'auto'; } catch (_) {}
                if (m === 'auto') {
                    applyTheme(e.matches ? 'dark' : 'light', 'auto');
                }
            });
        } catch (e) {}
    })();

    /* ═══════════════════════════════════════════════
       2. 移动端菜单
       ═══════════════════════════════════════════════ */

    document.addEventListener('click', function (e) {
        var btn = e.target.closest && e.target.closest('[data-menu-toggle], .slv-menu-toggle, .menu-toggle');
        if (btn) {
            e.preventDefault();
            var nav = document.querySelector('[data-menu], .slv-nav, .main-navigation');
            if (nav) {
                var open = nav.classList.toggle('is-open');
                btn.setAttribute('aria-expanded', open ? 'true' : 'false');
                document.body.classList.toggle('slv-menu-open', open);
            }
        }
    });

    /* ═══════════════════════════════════════════════
       3. 搜索框展开
       ═══════════════════════════════════════════════ */

    document.addEventListener('click', function (e) {
        var btn = e.target.closest && e.target.closest('[data-search-toggle], .slv-search-toggle, .search-toggle');
        if (btn) {
            e.preventDefault();
            var form = document.querySelector('[data-search-form], .slv-search-form, .search-form');
            if (form) {
                form.classList.toggle('is-open');
                var input = form.querySelector('input[type="search"]');
                if (input && form.classList.contains('is-open')) input.focus();
            }
            return;
        }
        if (!e.target.closest || !e.target.closest('[data-search-form], .slv-search-form, .search-form, [data-search-toggle], .slv-search-toggle, .search-toggle')) {
            var openForms = document.querySelectorAll('.slv-search-form.is-open, .search-form.is-open, [data-search-form].is-open');
            for (var i = 0; i < openForms.length; i++) openForms[i].classList.remove('is-open');
        }
    });

    /* ═══════════════════════════════════════════════
       4. 粘性头部
       ═══════════════════════════════════════════════ */

    var header = document.querySelector('.slv-header, header.site-header');
    if (header) {
        var lastY = 0;
        var ticking = false;
        window.addEventListener('scroll', function () {
            if (ticking) return;
            ticking = true;
            requestAnimationFrame(function () {
                var y = window.scrollY;
                if (y > 100) header.classList.add('is-scrolled');
                else header.classList.remove('is-scrolled');

                if (y > lastY && y > 300) header.classList.add('is-hidden');
                else header.classList.remove('is-hidden');

                lastY = y;
                ticking = false;
            });
        }, { passive: true });
    }
})();