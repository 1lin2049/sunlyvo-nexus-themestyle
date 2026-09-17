/**
 * SunLyvo Nexus — 前端风格切换
 *
 * @package SunLyvo_Nexus
 */
(function () {
    'use strict';

    const STORAGE_KEY = 'slv_style';
    const config = window.SLV_CONFIG || {};

    class SLV_StyleSwitcher {
        constructor() {
            this.current = document.documentElement.dataset.style || 'brand';
            this.init();
        }

        init() {
            const toggle = document.querySelector('.slv-style-switcher__toggle');
            const panel  = document.querySelector('.slv-style-switcher__panel');
            if ( ! toggle || ! panel ) return;

            toggle.addEventListener('click', () => {
                const expanded = toggle.getAttribute('aria-expanded') === 'true';
                toggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                panel.hidden = expanded;
            });

            document.addEventListener('click', (e) => {
                if ( ! e.target.closest('.slv-style-switcher') ) {
                    panel.hidden = true;
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });

            panel.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-style]');
                if ( ! btn ) return;
                this.apply( btn.dataset.style );
            });
        }

        apply(style) {
            document.documentElement.dataset.style = style;
            try { localStorage.setItem( STORAGE_KEY, style ); } catch (e) {}

            document.querySelectorAll('.slv-style-switcher__item').forEach(el => {
                el.classList.toggle('is-active', el.dataset.style === style);
            });

            // 重新加载对应 CSS（如果是动态加载模式）
            this.swapStylesheet(style);

            // 同步到服务端
            this.syncToServer(style);

            // 埋点
            if ( window.SLV_TRACKER ) {
                window.SLV_TRACKER.track( 'style_switch', { style } );
            }
        }

        swapStylesheet(style) {
            const link = document.getElementById('slv-style-current');
            if ( ! link ) return;
            const base = link.href.replace(/\/styles\/[^/]+\.css(\?.*)?$/, '/styles/');
            link.href = base + style + '.css';
        }

        async syncToServer(style) {
            if ( ! config.userId ) return;
            try {
                await fetch( ( config.restUrl || '/wp-json/slv/v1' ) + '/user/style', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': config.nonce || '',
                    },
                    body: JSON.stringify({ style }),
                } );
            } catch (e) {}
        }
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', () => new SLV_StyleSwitcher() );
    } else {
        new SLV_StyleSwitcher();
    }
})();