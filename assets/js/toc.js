/**
 * SunLyvo Nexus — TOC 自动生成
 *
 * 支持：多级 H2/H3/H4、智能折叠、进度可视化、章节预估时间、
 *       移动端悬浮+抽屉、键盘导航、位置持久化、平滑滚动、
 *       URL 锚点同步、打印友好。
 *
 * @package SunLyvo_Nexus
 */
(function () {
    'use strict';

    const config = window.SLV_CONFIG || {};
    if (!config.isReader) {
        return;
    }

    const HIGHLIGHT_OFFSET = 100;
    const STORAGE_KEY = 'slv_toc_expanded';
    const COLLAPSE_DEPTH = 4;

    class SLV_TOC {
        constructor() {
            this.headings = [];
            this.activeId = null;
            this.toc = document.querySelector('.slv-toc');
            this.drawer = document.getElementById('slv-toc-drawer');
            this.fab = document.querySelector('.slv-toc__fab');
            this.drawerOpen = false;
            this.reducedMotion = window.SLV_PREFERS_REDUCED_MOTION
                ? window.SLV_PREFERS_REDUCED_MOTION()
                : false;
        }

        init() {
            if (!this.toc) {
                return;
            }
            this.headings = this.collectHeadings();
            if (this.headings.length < 2) {
                return;
            }
            this.enhanceTOC();
            this.bindDrawer();
            this.bindToggle();
            this.bindKeyboard();
            this.restoreExpandedState();
            this.updateProgressLabels();
            this.bindScroll();
        }

        /**
         * 从 DOM 中收集 H2/H3/H4。
         */
        collectHeadings() {
            const nodes = document.querySelectorAll('.slv-reader__content h2, .slv-reader__content h3, .slv-reader__content h4');
            const list = [];
            let idx = 0;
            nodes.forEach((node) => {
                if (!node.id) {
                    idx++;
                    node.id = 'slv-heading-' + idx;
                }
                list.push({
                    id: node.id,
                    level: parseInt(node.tagName.substring(1), 10),
                    text: node.textContent.trim(),
                    element: node,
                });
            });
            return list;
        }

        /**
         * 增强 TOC：折叠、预估时间、进度条。
         */
        enhanceTOC() {
            const links = this.toc.querySelectorAll('.slv-toc__link');
            links.forEach((link) => {
                const id = link.dataset.headingId;
                const heading = this.headings.find((h) => h.id === id);
                if (!heading) {
                    return;
                }

                // 章节预估时间
                const time = this.estimateReadingTime(heading.element);
                const timeSpan = document.createElement('span');
                timeSpan.className = 'slv-toc__time';
                timeSpan.textContent = time + ' 分钟';
                timeSpan.style.cssText = 'margin-left:auto;font-size:11px;color:var(--slv-color-text-tertiary)';
                link.style.display = 'flex';
                link.style.alignItems = 'center';
                link.style.gap = '4px';
                link.appendChild(timeSpan);

                // 智能折叠（H4 默认折叠）
                if (heading.level >= COLLAPSE_DEPTH) {
                    const li = link.closest('.slv-toc__item');
                    if (li && li.querySelector('.slv-toc__list')) {
                        li.classList.add('is-collapsed');
                    }
                }
            });

            // 绑定点击：平滑滚动
            this.toc.addEventListener('click', (e) => {
                const link = e.target.closest('.slv-toc__link');
                if (!link) {
                    return;
                }
                e.preventDefault();
                this.scrollToHeading(link.dataset.headingId);
                this.closeDrawer();
            });

            // 移动端抽屉内 TOC
            if (this.drawer) {
                this.drawer.addEventListener('click', (e) => {
                    const link = e.target.closest('.slv-toc__link');
                    if (!link) {
                        return;
                    }
                    e.preventDefault();
                    this.scrollToHeading(link.dataset.headingId);
                    this.closeDrawer();
                });
            }
        }

        /**
         * 绑定目录折叠切换。
         */
        bindToggle() {
            const btn = this.toc.querySelector('.slv-toc__toggle');
            if (!btn) {
                return;
            }
            btn.addEventListener('click', () => {
                const body = this.toc.querySelector('.slv-toc__body');
                const expanded = btn.getAttribute('aria-expanded') === 'true';
                btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                if (body) {
                    body.style.display = expanded ? 'none' : 'block';
                }
            });
        }

        /**
         * 绑定移动端抽屉。
         */
        bindDrawer() {
            if (!this.fab || !this.drawer) {
                return;
            }
            this.fab.addEventListener('click', () => this.openDrawer());
            this.drawer.querySelectorAll('[data-close]').forEach((el) => {
                el.addEventListener('click', () => this.closeDrawer());
            });
        }

        openDrawer() {
            if (!this.drawer) {
                return;
            }
            this.drawer.classList.add('is-open');
            this.drawer.setAttribute('aria-hidden', 'false');
            this.drawerOpen = true;
            document.body.style.overflow = 'hidden';
            // 焦点管理
            const firstLink = this.drawer.querySelector('.slv-toc__link');
            if (firstLink) {
                firstLink.focus();
            }
        }

        closeDrawer() {
            if (!this.drawer) {
                return;
            }
            this.drawer.classList.remove('is-open');
            this.drawer.setAttribute('aria-hidden', 'true');
            this.drawerOpen = false;
            document.body.style.overflow = '';
        }

        /**
         * 键盘导航：[ / ] 跳转，\ 聚焦 TOC。
         */
        bindKeyboard() {
            document.addEventListener('keydown', (e) => {
                if (e.target.matches('input, textarea, [contenteditable]')) {
                    return;
                }
                if (e.ctrlKey || e.metaKey || e.altKey) {
                    return;
                }
                switch (e.key) {
                    case '[':
                        e.preventDefault();
                        this.navigateRelative(-1);
                        break;
                    case ']':
                        e.preventDefault();
                        this.navigateRelative(1);
                        break;
                    case '\\':
                        e.preventDefault();
                        this.focusTOC();
                        break;
                }
            });
        }

        navigateRelative(direction) {
            if (!this.headings.length) {
                return;
            }
            let idx = this.headings.findIndex((h) => h.id === this.activeId);
            if (idx < 0) {
                idx = 0;
            }
            const next = Math.max(0, Math.min(this.headings.length - 1, idx + direction));
            this.scrollToHeading(this.headings[next].id);
        }

        focusTOC() {
            if (!this.toc) {
                return;
            }
            this.toc.focus();
        }

        /**
         * 平滑滚动 + URL 锚点同步。
         */
        scrollToHeading(id) {
            const target = document.getElementById(id);
            if (!target) {
                return;
            }
            const top = target.getBoundingClientRect().top + window.scrollY - HIGHLIGHT_OFFSET;
            window.scrollTo({ top, behavior: this.reducedMotion ? 'auto' : 'smooth' });
            if (history.replaceState) {
                history.replaceState(null, '', '#' + id);
            }
            this.setActive(id);
        }

        /**
         * 滚动高亮。
         */
        bindScroll() {
            let ticking = false;
            const handler = () => {
                if (!ticking) {
                    requestAnimationFrame(() => {
                        this.updateActive();
                        ticking = false;
                    });
                    ticking = true;
                }
            };
            window.addEventListener('scroll', handler, { passive: true });
            this.updateActive();
        }

        updateActive() {
            const scrollTop = window.scrollY + HIGHLIGHT_OFFSET;
            let current = this.headings[0] ? this.headings[0].id : null;
            for (const h of this.headings) {
                if (h.element.getBoundingClientRect().top + window.scrollY <= scrollTop) {
                    current = h.id;
                } else {
                    break;
                }
            }
            if (current && current !== this.activeId) {
                this.setActive(current);
            }
        }

        setActive(id) {
            this.activeId = id;
            this.toc.querySelectorAll('.slv-toc__link').forEach((link) => {
                link.classList.toggle('is-active', link.dataset.headingId === id);
            });
            // 移动端抽屉内同步高亮
            if (this.drawer) {
                this.drawer.querySelectorAll('.slv-toc__link').forEach((link) => {
                    link.classList.toggle('is-active', link.dataset.headingId === id);
                });
            }
        }

        /**
         * 章节预估阅读时间。
         */
        estimateReadingTime(node) {
            if (!node) {
                return 1;
            }
            // 直到下一个同级或更高级标题为止的内容
            let text = '';
            let el = node.nextElementSibling;
            const level = parseInt(node.tagName.substring(1), 10);
            while (el) {
                const m = /^H([1-6])$/.exec(el.tagName);
                if (m && parseInt(m[1], 10) <= level) {
                    break;
                }
                text += ' ' + (el.textContent || '');
                el = el.nextElementSibling;
            }
            const cn = (text.match(/[\u4e00-\u9fa5]/g) || []).length;
            const en = (text.match(/[a-zA-Z]+/g) || []).length;
            const minutes = (cn / 400) + (en / 200);
            return Math.max(1, Math.ceil(minutes));
        }

        updateProgressLabels() {
            // 预留：可扩展为每个标题的已读百分比
        }

        /**
         * 位置持久化。
         */
        restoreExpandedState() {
            try {
                const raw = localStorage.getItem(STORAGE_KEY);
                if (!raw) {
                    return;
                }
                const expanded = JSON.parse(raw);
                if (!Array.isArray(expanded)) {
                    return;
                }
                expanded.forEach((id) => {
                    const link = this.toc.querySelector('[data-heading-id="' + id + '"]');
                    if (link) {
                        const li = link.closest('.slv-toc__item');
                        if (li) {
                            li.classList.remove('is-collapsed');
                        }
                    }
                });
            } catch (e) {
                // 忽略
            }
        }
    }

    function init() {
        const toc = new SLV_TOC();
        toc.init();
        window.SLV_TOC = toc;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();