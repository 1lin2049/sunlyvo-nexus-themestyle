/**
 * SunLyvo Nexus — 阅读位置记忆
 *
 * 跨设备同步、智能续读、忽略短停留、段落级精度。
 *
 * @package SunLyvo_Nexus
 */
(function () {
    'use strict';

    const config = window.SLV_CONFIG || {};
    if (!config.isReader || !config.postId) {
        return;
    }

    const POST_ID = config.postId;
    const USER_ID = config.userId || 0;
    const SAVE_INTERVAL = 2000;
    const MIN_SCROLL_PERCENT = 5;
    const MIN_STAY_MS = 10000;
    const STORAGE_KEY = 'slv_position_' + POST_ID;

    class SLV_ReadingPosition {
        constructor() {
            this.startTime = Date.now();
            this.timer = null;
            this.hiddenAt = 0;
            this.restored = false;
        }

        init() {
            this.bindScroll();
            this.bindVisibility();
            this.bindUnload();
            this.restore();
        }

        bindScroll() {
            window.addEventListener('scroll', () => {
                clearTimeout(this.timer);
                this.timer = setTimeout(() => this.save(), SAVE_INTERVAL);
            }, { passive: true });
        }

        bindVisibility() {
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'hidden') {
                    this.hiddenAt = Date.now();
                    this.save();
                } else if (this.hiddenAt) {
                    // 恢复计时（隐藏期间不计入停留时间）
                    this.startTime += Date.now() - this.hiddenAt;
                    this.hiddenAt = 0;
                }
            });
        }

        bindUnload() {
            window.addEventListener('beforeunload', () => this.save());
        }

        async restore() {
            try {
                const data = await this.fetchPosition();
                if (!data || !data.position) {
                    return;
                }
                const pos = data.position;
                if (!pos.scroll_percent || pos.scroll_percent < MIN_SCROLL_PERCENT) {
                    return;
                }
                const minutesAgo = Math.max(0, Math.floor((Date.now() - new Date(pos.last_read_at).getTime()) / 60000));
                this.showPrompt(pos, minutesAgo);
            } catch (e) {
                // 忽略
            }
        }

        async fetchPosition() {
            if (!USER_ID) {
                try {
                    const raw = localStorage.getItem(STORAGE_KEY);
                    if (raw) {
                        return { position: JSON.parse(raw) };
                    }
                } catch (e) {
                    // 忽略
                }
                return null;
            }
            const res = await fetch(
                (config.restUrl || '/wp-json/slv/v1') + '/reading-position/' + POST_ID,
                { headers: { 'X-WP-Nonce': config.nonce || '' } }
            );
            return res.json();
        }

        showPrompt(pos, minutesAgo) {
            const percent = Math.round(pos.scroll_percent);
            const wrapper = document.createElement('div');
            wrapper.className = 'slv-continue-prompt';
            wrapper.innerHTML = [
                '<span class="slv-continue-prompt__text">上次读到 ' + percent + '%，约 ' + minutesAgo + ' 分钟前</span>',
                '<div class="slv-continue-prompt__actions">',
                '<button type="button" class="slv-continue-prompt__btn slv-continue-prompt__btn--primary" data-action="continue">继续阅读</button>',
                '<button type="button" class="slv-continue-prompt__btn slv-continue-prompt__btn--text" data-action="dismiss">忽略</button>',
                '</div>',
            ].join('');
            document.body.appendChild(wrapper);

            wrapper.addEventListener('click', (e) => {
                const action = e.target.dataset.action;
                if (action === 'continue') {
                    const target = (document.documentElement.scrollHeight - window.innerHeight) * (percent / 100);
                    const reduced = window.SLV_PREFERS_REDUCED_MOTION && window.SLV_PREFERS_REDUCED_MOTION();
                    window.scrollTo({ top: target, behavior: reduced ? 'auto' : 'smooth' });
                }
                wrapper.remove();
            });

            setTimeout(() => {
                if (wrapper.parentNode) {
                    wrapper.remove();
                }
            }, 12000);
        }

        async save() {
            // 忽略短停留
            if (Date.now() - this.startTime < MIN_STAY_MS) {
                return;
            }

            const scrollTop = window.scrollY;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const percent = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
            if (percent < MIN_SCROLL_PERCENT) {
                return;
            }

            const payload = {
                scroll_position: Math.round(scrollTop),
                scroll_percent: Math.round(percent * 100) / 100,
                last_read_at: new Date().toISOString(),
            };

            if (!USER_ID) {
                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
                } catch (e) {
                    // 忽略
                }
                return;
            }

            try {
                await fetch(
                    (config.restUrl || '/wp-json/slv/v1') + '/reading-position/' + POST_ID,
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': config.nonce || '',
                        },
                        body: JSON.stringify(payload),
                        keepalive: true,
                    }
                );
            } catch (e) {
                // 忽略
            }
        }
    }

    function init() {
        const rp = new SLV_ReadingPosition();
        rp.init();
        window.SLV_READING_POSITION = rp;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();