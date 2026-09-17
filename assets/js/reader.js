/**
 * SunLyvo Nexus — Reader 主入口
 *
 * @package SunLyvo_Nexus
 */
(function () {
    'use strict';

    const config = window.SLV_CONFIG || {};
    const isReader = !!config.isReader;

    if (!isReader) {
        return;
    }

    /**
     * 主题切换（记忆到 localStorage）。
     */
    function initTheme() {
        const saved = localStorage.getItem('slv_theme');
        if (saved === 'dark' || saved === 'light') {
            document.documentElement.setAttribute('data-theme', saved);
        }
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (!saved && prefersDark) {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    }

    function toggleTheme() {
        const current = document.documentElement.getAttribute('data-theme') || 'light';
        const next = current === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('slv_theme', next);
    }

    window.SLV_TOGGLE_THEME = toggleTheme;

    /**
     * 阅读进度条。
     */
    function initProgressBar() {
        const container = document.querySelector('.slv-reader-progress');
        if (!container) {
            return;
        }
        const bar = document.createElement('div');
        bar.className = 'slv-reader-progress__bar';
        container.appendChild(bar);

        let ticking = false;
        const update = () => {
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const percent = docHeight > 0 ? (window.scrollY / docHeight) * 100 : 0;
            bar.style.width = Math.min(100, Math.max(0, percent)) + '%';
            ticking = false;
        };
        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(update);
                ticking = true;
            }
        }, { passive: true });
        update();
    }

    /**
     * Toast 提示。
     */
    function showToast(message) {
        const existing = document.querySelector('.slv-toast');
        if (existing) {
            existing.remove();
        }
        const toast = document.createElement('div');
        toast.className = 'slv-toast';
        toast.textContent = message;
        document.body.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add('is-visible'));
        setTimeout(() => {
            toast.classList.remove('is-visible');
            setTimeout(() => toast.remove(), 300);
        }, 2000);
    }

    window.SLV_TOAST = showToast;

    /**
     * 尊重 prefers-reduced-motion。
     */
    function prefersReducedMotion() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    window.SLV_PREFERS_REDUCED_MOTION = prefersReducedMotion;

    /**
     * 初始化。
     */
    function init() {
        initTheme();
        initProgressBar();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

(function () {
    'use strict';

    function getStored() {
        try { return localStorage.getItem('slv_theme') || 'auto'; } catch { return 'auto'; }
    }
    function setStored(mode) {
        try { localStorage.setItem('slv_theme', mode); } catch {}
    }

    function applyMode(mode) {
        let effective = mode;
        if (mode === 'auto') {
            effective = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        document.documentElement.setAttribute('data-theme', effective);
    }

    function nextMode(current) {
        return current === 'light' ? 'dark' : (current === 'dark' ? 'auto' : 'light');
    }

    function initThemeToggle() {
        const stored = getStored();
        applyMode(stored);

        document.querySelectorAll('.slv-theme-toggle').forEach((btn) => {
            btn.dataset.mode = stored;
            btn.addEventListener('click', async () => {
                const next = nextMode(btn.dataset.mode);
                btn.dataset.mode = next;
                setStored(next);
                applyMode(next);

                const cfg = window.SLV_CONFIG || {};
                if (cfg.userId) {
                    try {
                        await fetch((cfg.restUrl || '/wp-json/slv/v1') + '/user/theme', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-WP-Nonce': cfg.nonce || '',
                            },
                            body: JSON.stringify({ mode: next }),
                        });
                    } catch {}
                }
            });
        });

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (getStored() === 'auto') applyMode('auto');
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initThemeToggle);
    } else {
        initThemeToggle();
    }
})();