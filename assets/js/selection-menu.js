/**
 * SunLyvo Nexus — 选区菜单（复制 / 引用 / 分享）
 *
 * 支持：四格式复制、四格式引用、多平台分享、二维码、短链、
 *       埋点、选区智能识别、键盘快捷键。
 *
 * @package SunLyvo_Nexus
 */
(function () {
    'use strict';

    const config = window.SLV_CONFIG || {};
    if (!config.isReader) {
        return;
    }

    const MIN_SELECTION = 3;
    const POST_META = config.postMeta || {
        title: document.title,
        url: window.location.href,
        author: '',
        publishedAt: '',
    };

    class SLV_SelectionMenu {
        constructor() {
            this.menu = null;
            this.citePanel = null;
            this.currentText = '';
        }

        init() {
            this.menu = this.buildMenu();
            this.citePanel = this.buildCitePanel();
            document.body.appendChild(this.menu);
            document.body.appendChild(this.citePanel);
            this.bind();
        }

        buildMenu() {
            const menu = document.createElement('div');
            menu.className = 'slv-selection-menu';
            menu.innerHTML = [
                '<button type="button" data-action="copy-text" title="复制文本" aria-label="复制文本">',
                '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="5" y="5" width="9" height="9" rx="1"/><path d="M3 11V3a1 1 0 011-1h8"/></svg>',
                '</button>',
                '<button type="button" data-action="copy-markdown" title="复制为 Markdown" aria-label="复制为 Markdown">',
                '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 12V4h2l2.5 4L9 4h2v8"/><path d="M12 4v6l2-2M12 10l-2-2"/></svg>',
                '</button>',
                '<button type="button" data-action="cite" title="引用" aria-label="引用">',
                '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 5h2v3a2 2 0 01-2 2M9 5h2v3a2 2 0 01-2 2"/></svg>',
                '</button>',
                '<button type="button" data-action="share" title="分享" aria-label="分享">',
                '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="4" cy="8" r="2"/><circle cx="12" cy="4" r="2"/><circle cx="12" cy="12" r="2"/><path d="M6 8l4-2M6 8l4 2"/></svg>',
                '</button>',
            ].join('');
            return menu;
        }

        buildCitePanel() {
            const panel = document.createElement('div');
            panel.className = 'slv-cite-panel';
            panel.innerHTML = [
                '<div class="slv-cite-panel__title">选择引用格式</div>',
                '<ul class="slv-cite-panel__list">',
                '<li><button type="button" data-cite="apa">APA</button></li>',
                '<li><button type="button" data-cite="mla">MLA</button></li>',
                '<li><button type="button" data-cite="chicago">Chicago</button></li>',
                '<li><button type="button" data-cite="gb7714">GB/T 7714</button></li>',
                '<li><button type="button" data-cite="plain">纯文本</button></li>',
                '</ul>',
            ].join('');
            return panel;
        }

        bind() {
            document.addEventListener('mouseup', (e) => this.onSelectionChange(e));
            document.addEventListener('touchend', (e) => this.onSelectionChange(e));

            document.addEventListener('mousedown', (e) => {
                if (e.target.closest('.slv-selection-menu') || e.target.closest('.slv-cite-panel')) {
                    return;
                }
                this.hideAll();
            });

            this.menu.addEventListener('click', (e) => {
                const btn = e.target.closest('button');
                if (!btn) {
                    return;
                }
                e.preventDefault();
                const action = btn.dataset.action;
                if (action === 'copy-text') {
                    this.copy(this.currentText, 'text');
                } else if (action === 'copy-markdown') {
                    this.copy(this.currentText, 'markdown');
                } else if (action === 'cite') {
                    this.showCitePanel(btn);
                } else if (action === 'share') {
                    this.share();
                }
            });

            this.citePanel.addEventListener('click', (e) => {
                const btn = e.target.closest('button[data-cite]');
                if (!btn) {
                    return;
                }
                e.preventDefault();
                this.cite(btn.dataset.cite);
                this.hideAll();
            });

            // Ctrl/Cmd+Shift+C 引用
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'C' || e.key === 'c')) {
                    const sel = window.getSelection();
                    const text = sel ? sel.toString().trim() : '';
                    if (text.length >= MIN_SELECTION) {
                        e.preventDefault();
                        this.currentText = text;
                        this.cite('apa');
                    }
                }
            });
        }

        onSelectionChange() {
            const sel = window.getSelection();
            if (!sel || sel.rangeCount === 0) {
                this.hideAll();
                return;
            }
            const text = sel.toString().trim();
            if (text.length < MIN_SELECTION) {
                this.hideAll();
                return;
            }
            if (sel.anchorNode && sel.anchorNode.parentElement) {
                if (sel.anchorNode.parentElement.closest('.slv-toc, .slv-selection-menu, .slv-cite-panel, .slv-chapter-comments')) {
                    this.hideAll();
                    return;
                }
            }
            this.currentText = text;
            const range = sel.getRangeAt(0);
            const rect = range.getBoundingClientRect();
            this.showMenu(rect);
        }

        showMenu(rect) {
            const top = rect.top + window.scrollY - 48;
            const left = rect.left + window.scrollX + rect.width / 2;
            this.menu.style.top = top + 'px';
            this.menu.style.left = left + 'px';
            this.menu.classList.add('is-visible');
            this.citePanel.classList.remove('is-visible');
        }

        showCitePanel(btn) {
            const rect = btn.getBoundingClientRect();
            const top = rect.bottom + window.scrollY + 4;
            const left = rect.left + window.scrollX;
            this.citePanel.style.top = top + 'px';
            this.citePanel.style.left = left + 'px';
            this.citePanel.classList.add('is-visible');
        }

        hideAll() {
            this.menu.classList.remove('is-visible');
            this.citePanel.classList.remove('is-visible');
        }

        async copy(text, format) {
            let payload = text;
            if (format === 'markdown') {
                payload = '> ' + text.replace(/\n/g, '\n> ');
            }
            try {
                await navigator.clipboard.writeText(payload);
                this.toast(format === 'markdown' ? '已复制为 Markdown' : '已复制');
                this.track('copy', format);
            } catch (e) {
                this.fallbackCopy(payload);
            }
            this.hideAll();
        }

        async cite(style) {
            const citation = this.formatCitation(style, this.currentText);
            try {
                await navigator.clipboard.writeText(citation);
                this.toast('已复制引用');
                this.track('cite', style);
            } catch (e) {
                this.fallbackCopy(citation);
            }
        }

        formatCitation(style, text) {
            const t = text.replace(/\s+/g, ' ').trim();
            const { title, url, author } = POST_META;
            const year = new Date().getFullYear();
            switch (style) {
                case 'apa':
                    return `"${t}" ${author} (${year}). ${title}. ${url}`;
                case 'mla':
                    return `"${t}" ${author}. "${title}." ${year}, ${url}.`;
                case 'chicago':
                    return `"${t}" ${author}. "${title}." Accessed ${new Date().toLocaleDateString()}. ${url}.`;
                case 'gb7714':
                    return `${author}. ${title}[EB/OL]. (${year})[${new Date().toLocaleDateString()}]. ${url}.`;
                default:
                    return `${t}\n— ${title} (${url})`;
            }
        }

        async share() {
            const url = await this.getShortUrl();
            const text = this.currentText;

            const targets = {
                twitter:  `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`,
                linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`,
                reddit:   `https://www.reddit.com/submit?url=${encodeURIComponent(url)}&title=${encodeURIComponent(text)}`,
                weibo:    `https://service.weibo.com/share/share.php?url=${encodeURIComponent(url)}&title=${encodeURIComponent(text)}`,
                qzone:    `https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=${encodeURIComponent(url)}&title=${encodeURIComponent(text)}`,
            };

            // 简化：弹出选择面板（复用 citePanel 样式）
            const choice = window.prompt(
                '选择分享方式：\n1. Twitter\n2. LinkedIn\n3. Reddit\n4. 微博\n5. QQ空间\n6. 复制链接\n7. 二维码',
                '6'
            );
            if (!choice) {
                return;
            }
            const n = parseInt(choice, 10);
            switch (n) {
                case 1: if (targets.twitter)  window.open(targets.twitter, '_blank'); break;
                case 2: if (targets.linkedin) window.open(targets.linkedin, '_blank'); break;
                case 3: if (targets.reddit)   window.open(targets.reddit, '_blank'); break;
                case 4: if (targets.weibo)    window.open(targets.weibo, '_blank'); break;
                case 5: if (targets.qzone)    window.open(targets.qzone, '_blank'); break;
                case 6: await this.copy(url, 'text'); break;
                case 7: this.showQRCode(url); break;
            }
            this.track('share', String(n));
            this.hideAll();
        }

        async getShortUrl() {
            try {
                const res = await fetch((config.restUrl || '/wp-json/slv/v1') + '/shortlink', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': config.nonce || '',
                    },
                    body: JSON.stringify({ url: window.location.href }),
                });
                const data = await res.json();
                return (data && data.short_url) ? data.short_url : window.location.href;
            } catch (e) {
                return window.location.href;
            }
        }

        showQRCode(url) {
            // 无第三方库时用 Google Chart API（可后续替换为本地 qrcode.min.js）
            const modal = document.createElement('div');
            modal.className = 'slv-qr-modal';
            modal.innerHTML = [
                '<div class="slv-qr-modal__box">',
                '<div class="slv-qr-modal__canvas">',
                '<img alt="QR" width="220" height="220" src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' + encodeURIComponent(url) + '">',
                '</div>',
                '<p class="slv-qr-modal__tip">扫码分享</p>',
                '<button type="button" class="slv-qr-modal__close">关闭</button>',
                '</div>',
            ].join('');
            document.body.appendChild(modal);
            modal.querySelector('.slv-qr-modal__close').addEventListener('click', () => modal.remove());
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        }

        toast(message) {
            if (window.SLV_TOAST) {
                window.SLV_TOAST(message);
            }
        }

        fallbackCopy(text) {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            try {
                document.execCommand('copy');
                this.toast('已复制');
            } catch (e) {
                // 忽略
            }
            document.body.removeChild(ta);
        }

        track(event, value) {
            if (!config.postId) {
                return;
            }
            try {
                fetch((config.restUrl || '/wp-json/slv/v1') + '/share-track', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': config.nonce || '',
                    },
                    body: JSON.stringify({ post_id: config.postId, target: event + ':' + value }),
                    keepalive: true,
                }).catch(() => {});
            } catch (e) {
                // 忽略
            }
        }
    }

    function init() {
        const menu = new SLV_SelectionMenu();
        menu.init();
        window.SLV_SELECTION_MENU = menu;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();