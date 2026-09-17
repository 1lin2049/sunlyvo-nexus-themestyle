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