/**
 * SunLyvo Nexus — 分享 FAB（页面固定分享按钮）
 *
 * @package SunLyvo_Nexus
 */
(function () {
    'use strict';

    const config = window.SLV_CONFIG || {};
    if (!config.isReader) {
        return;
    }

    function createShareFab() {
        const fab = document.createElement('button');
        fab.type = 'button';
        fab.className = 'slv-share-fab';
        fab.setAttribute('aria-label', '分享');
        fab.innerHTML = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="6" cy="10" r="2.5"/><circle cx="14" cy="5" r="2.5"/><circle cx="14" cy="15" r="2.5"/><path d="M8 9l4-3M8 11l4 3"/></svg>';
        fab.style.cssText = [
            'position:fixed',
            'right:var(--slv-space-4)',
            'bottom:calc(var(--slv-space-6) + 56px)',
            'width:48px',
            'height:48px',
            'border-radius:var(--slv-radius-full)',
            'background:var(--slv-color-bg-base)',
            'color:var(--slv-color-text-primary)',
            'border:1px solid var(--slv-color-border-base)',
            'box-shadow:0 8px 24px rgba(0,0,0,0.12)',
            'display:flex',
            'align-items:center',
            'justify-content:center',
            'cursor:pointer',
            'z-index:900',
        ].join(';');
        document.body.appendChild(fab);

        fab.addEventListener('click', () => {
            const sel = window.getSelection();
            const text = sel && sel.toString().trim();
            if (window.SLV_SELECTION_MENU) {
                window.SLV_SELECTION_MENU.currentText = text && text.length >= 3 ? text : document.title;
                window.SLV_SELECTION_MENU.share();
            }
        });
    }

    function init() {
        createShareFab();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();