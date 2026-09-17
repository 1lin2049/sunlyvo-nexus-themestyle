/**
 * SunLyvo Nexus — 商品列表交互
 *
 * @package SunLyvo_Nexus
 */
(function () {
    'use strict';

    var state = {
        view: 'grid',
        sort: 'default',
        categories: [],
        tags: [],
        rating: null,
        minPrice: null,
        maxPrice: null,
    };

    /* ═══ 筛选组折叠 ═══ */
    function initFilterGroups() {
        document.querySelectorAll('[data-slv-filter-toggle]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var expanded = btn.getAttribute('aria-expanded') === 'true';
                btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            });
        });
    }

    /* ═══ 视图切换 ═══ */
    function initViewToggle() {
        var grid = document.querySelector('[data-slv-product-grid]');
        var buttons = document.querySelectorAll('[data-slv-view]');
        if (!grid) return;

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                state.view = btn.dataset.slvView;
                buttons.forEach(function (b) { b.classList.toggle('is-active', b === btn); });
                grid.classList.toggle('is-list', state.view === 'list');
                try { localStorage.setItem('slv_pl_view', state.view); } catch (e) {}
            });
        });

        try {
            var saved = localStorage.getItem('slv_pl_view');
            if (saved === 'list' || saved === 'grid') {
                var savedBtn = document.querySelector('[data-slv-view="' + saved + '"]');
                if (savedBtn) savedBtn.click();
            }
        } catch (e) {}
    }

    /* ═══ 排序 ═══ */
    function initSort() {
        var select = document.querySelector('[data-slv-sort]');
        if (!select) return;
        select.addEventListener('change', function () {
            state.sort = select.value;
        });
    }

    /* ═══ 分类勾选 ═══ */
    function initCategories() {
        document.querySelectorAll('input[name="category"]').forEach(function (cb) {
            cb.addEventListener('change', function () {
                state.categories = Array.from(document.querySelectorAll('input[name="category"]:checked'))
                    .map(function (el) { return el.value; });
            });
        });
    }

    /* ═══ 标签按钮 ═══ */
    function initTags() {
        document.querySelectorAll('[data-tag]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var tag = btn.dataset.tag;
                var idx = state.tags.indexOf(tag);
                if (idx >= 0) {
                    state.tags.splice(idx, 1);
                    btn.classList.remove('is-active');
                } else {
                    state.tags.push(tag);
                    btn.classList.add('is-active');
                }
            });
        });
    }

    /* ═══ 价格应用 ═══ */
    function initPrice() {
        var btn = document.querySelector('[data-slv-price-apply]');
        if (!btn) return;
        btn.addEventListener('click', function () {
            var min = document.querySelector('input[name="min_price"]');
            var max = document.querySelector('input[name="max_price"]');
            state.minPrice = min ? min.value : null;
            state.maxPrice = max ? max.value : null;
        });
    }

    /* ═══ 评分 ═══ */
    function initRating() {
        document.querySelectorAll('input[name="rating"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                state.rating = radio.value;
            });
        });
    }

    /* ═══ 清空筛选 ═══ */
    function initClearFilter() {
        document.querySelectorAll('[data-slv-filter-clear]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                state.categories = [];
                state.tags = [];
                state.rating = null;
                state.minPrice = null;
                state.maxPrice = null;

                document.querySelectorAll('input[name="category"]:checked').forEach(function (el) { el.checked = false; });
                document.querySelectorAll('input[name="rating"]:checked').forEach(function (el) { el.checked = false; });
                document.querySelectorAll('[data-tag]').forEach(function (el) { el.classList.remove('is-active'); });
                var min = document.querySelector('input[name="min_price"]');
                var max = document.querySelector('input[name="max_price"]');
                if (min) min.value = '';
                if (max) max.value = '';
            });
        });
    }

    /* ═══ 移动端筛选抽屉 ═══ */
    function initFilterDrawer() {
        var drawer = document.querySelector('[data-slv-filter]');
        var openBtn = document.querySelector('[data-slv-filter-open]');
        if (!drawer || !openBtn) return;

        openBtn.addEventListener('click', function () {
            drawer.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        });

        drawer.addEventListener('click', function (e) {
            if (e.target === drawer) {
                drawer.classList.remove('is-open');
                document.body.style.overflow = '';
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && drawer.classList.contains('is-open')) {
                drawer.classList.remove('is-open');
                document.body.style.overflow = '';
            }
        });
    }

    /* ═══ 加入购物车 ═══ */
    function initAddToCart() {
        document.querySelectorAll('.slv-product-card__add').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (window.SLV_TOAST) window.SLV_TOAST('已加入购物车');
            });
        });
    }

    function init() {
        initFilterGroups();
        initViewToggle();
        initSort();
        initCategories();
        initTags();
        initPrice();
        initRating();
        initClearFilter();
        initFilterDrawer();
        initAddToCart();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();