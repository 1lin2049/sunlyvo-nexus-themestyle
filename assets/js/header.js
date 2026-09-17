/**
 * SunLyvo Nexus — Header 交互
 *
 * 主题切换为两态：light ↔ dark。
 * 首次访问跟随系统偏好，用户点击一次后永久固定。
 *
 * @package SunLyvo_Nexus
 */
(function () {
    'use strict';

    /* ═══ 深色模式切换（两态：light / dark）═══ */
    var THEME_KEY = 'slv_theme';

    function getStoredTheme() {
        try {
            var v = localStorage.getItem(THEME_KEY);
            return v === 'light' || v === 'dark' ? v : null;
        } catch (e) {
            return null;
        }
    }

    function setStoredTheme(mode) {
        try { localStorage.setItem(THEME_KEY, mode); } catch (e) {}
    }

    function applyTheme(mode) {
        document.documentElement.setAttribute('data-theme', mode);
    }

    function getInitialTheme() {
        var stored = getStoredTheme();
        if (stored) return stored;
        // 首次访问：跟随系统偏好
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    function initThemeToggle() {
        var current = getInitialTheme();
        applyTheme(current);

        document.querySelectorAll('[data-slv-theme-toggle]').forEach(function (btn) {
            btn.dataset.mode = current;
            btn.addEventListener('click', function () {
                current = current === 'dark' ? 'light' : 'dark';
                btn.dataset.mode = current;
                setStoredTheme(current);
                applyTheme(current);
            });
        });
    }

    /* ═══ 购物车角标 ═══ */
    function updateCartCount() {
        var el = document.querySelector('[data-slv-cart-count]');
        if (!el) return;
        var cfg = window.SLV_CONFIG || {};
        fetch((cfg.restUrl || '/wp-json/slv/v1') + '/cart/count', {
            headers: { 'X-WP-Nonce': cfg.nonce || '' },
        })
            .then(function (res) { return res.ok ? res.json() : null; })
            .then(function (data) {
                if (!data || !data.count) {
                    el.hidden = true;
                    return;
                }
                el.textContent = data.count > 99 ? '99+' : String(data.count);
                el.hidden = false;
            })
            .catch(function () {});
    }

    /* ═══ 搜索按钮 ═══ */
    function initSearch() {
        document.querySelectorAll('[data-slv-open-search]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                window.location.href = '/?s=';
            });
        });
    }

    /* ═══ 订阅表单 ═══ */
    function initSubscribe() {
        document.querySelectorAll('[data-slv-subscribe]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var input = form.querySelector('input[type="email"]');
                if (!input || !input.value) return;

                var cfg = window.SLV_CONFIG || {};
                fetch((cfg.restUrl || '/wp-json/slv/v1') + '/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': cfg.nonce || '',
                    },
                    body: JSON.stringify({ email: input.value }),
                })
                    .then(function (res) { return res.ok ? res.json() : Promise.reject(); })
                    .then(function () {
                        input.value = '';
                        if (window.SLV_TOAST) window.SLV_TOAST('订阅成功');
                    })
                    .catch(function () {
                        if (window.SLV_TOAST) window.SLV_TOAST('订阅失败，请稍后再试');
                    });
            });
        });
    }

    function init() {
        initThemeToggle();
        updateCartCount();
        initSearch();
        initSubscribe();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();