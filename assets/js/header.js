/**
 * SunLyvo Nexus — Header 交互
 *
 * @package SunLyvo_Nexus
 */
(function () {
    'use strict';

    /* ═══ 深色模式切换（三态：light / dark / auto）═══ */
    function getStoredTheme() {
        try { return localStorage.getItem('slv_theme') || 'auto'; } catch (e) { return 'auto'; }
    }
    function setStoredTheme(mode) {
        try { localStorage.setItem('slv_theme', mode); } catch (e) {}
    }
    function applyTheme(mode) {
        var eff = mode;
        if (mode === 'auto') {
            eff = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        document.documentElement.setAttribute('data-theme', eff);
        document.documentElement.setAttribute('data-theme-mode', mode);
    }
    function nextTheme(current) {
        return current === 'light' ? 'dark' : (current === 'dark' ? 'auto' : 'light');
    }

    function initThemeToggle() {
        var stored = getStoredTheme();
        applyTheme(stored);

        document.querySelectorAll('[data-slv-theme-toggle]').forEach(function (btn) {
            btn.dataset.mode = stored;
            btn.addEventListener('click', function () {
                var next = nextTheme(btn.dataset.mode);
                btn.dataset.mode = next;
                setStoredTheme(next);
                applyTheme(next);
            });
        });

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function () {
            if (getStoredTheme() === 'auto') applyTheme('auto');
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