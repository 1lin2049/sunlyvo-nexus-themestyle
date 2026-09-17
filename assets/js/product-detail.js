/**
 * SunLyvo Nexus — 商品详情页交互
 *
 * @package SunLyvo_Nexus
 */
(function () {
    'use strict';

    var productId = (window.SLV_CONFIG && window.SLV_CONFIG.postId) || 0;

    /* ═══ 数量加减 ═══ */
    function initQuantity() {
        var input = document.querySelector('[data-slv-qty]');
        var minus = document.querySelector('[data-slv-qty-minus]');
        var plus = document.querySelector('[data-slv-qty-plus]');
        if (!input) return;

        if (minus) minus.addEventListener('click', function () {
            var v = parseInt(input.value, 10) || 1;
            if (v > 1) input.value = v - 1;
        });
        if (plus) plus.addEventListener('click', function () {
            var v = parseInt(input.value, 10) || 1;
            input.value = v + 1;
        });
    }

    /* ═══ Tab 切换 ═══ */
    function initTabs() {
        var tabs = document.querySelectorAll('[data-slv-tab]');
        var panels = document.querySelectorAll('[data-slv-panel]');

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                var key = tab.dataset.slvTab;
                tabs.forEach(function (t) {
                    t.classList.toggle('is-active', t === tab);
                    t.setAttribute('aria-selected', t === tab ? 'true' : 'false');
                });
                panels.forEach(function (p) {
                    var isActive = p.dataset.slvPanel === key;
                    p.classList.toggle('is-active', isActive);
                    p.hidden = !isActive;
                });
            });
        });
    }

    /* ═══ 图册缩略图 ═══ */
    function initGallery() {
        var main = document.querySelector('.slv-product-gallery__main img');
        var thumbs = document.querySelector('[data-slv-gallery-thumbs]');
        if (!main || !thumbs) return;

        var src = main.src;
        var alt = main.alt || '';
        thumbs.innerHTML = '<div class="slv-product-gallery__thumb is-active"><img src="' + src + '" alt="' + alt + '"></div>';

        thumbs.addEventListener('click', function (e) {
            var thumb = e.target.closest('.slv-product-gallery__thumb');
            if (!thumb) return;
            var img = thumb.querySelector('img');
            if (!img) return;
            main.src = img.src;
            thumbs.querySelectorAll('.slv-product-gallery__thumb').forEach(function (t) {
                t.classList.toggle('is-active', t === thumb);
            });
        });
    }

    /* ═══ 加入购物车 ═══ */
    function initAddToCart() {
        var btns = document.querySelectorAll('[data-slv-add-to-cart], [data-slv-buy-now]');
        var qtyInput = document.querySelector('[data-slv-qty]');

        btns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var qty = qtyInput ? parseInt(qtyInput.value, 10) || 1 : 1;
                var cfg = window.SLV_CONFIG || {};

                btn.disabled = true;
                var originalHTML = btn.innerHTML;
                btn.innerHTML = '处理中...';

                fetch((cfg.restUrl || '/wp-json/slv/v1') + '/cart/items', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': cfg.nonce || '',
                    },
                    body: JSON.stringify({ product_id: productId, quantity: qty }),
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        btn.innerHTML = originalHTML;
                        btn.disabled = false;
                        if (data && data.success) {
                            if (window.SLV_TOAST) window.SLV_TOAST('已加入购物车');
                            if (btn.hasAttribute('data-slv-buy-now')) {
                                window.location.href = '/checkout/';
                            }
                        } else {
                            if (window.SLV_TOAST) window.SLV_TOAST((data && data.error) || '加入失败');
                        }
                    })
                    .catch(function () {
                        btn.innerHTML = originalHTML;
                        btn.disabled = false;
                        if (window.SLV_TOAST) window.SLV_TOAST('网络错误');
                    });
            });
        });
    }

    /* ═══ 询盘弹窗 ═══ */
    function initInquiry() {
        var modal = document.querySelector('[data-slv-inquiry-modal]');
        var openBtn = document.querySelector('[data-slv-open-inquiry]');
        if (!modal || !openBtn) return;

        openBtn.addEventListener('click', function () {
            modal.hidden = false;
            document.body.style.overflow = 'hidden';
        });
        modal.querySelectorAll('[data-slv-close-inquiry]').forEach(function (el) {
            el.addEventListener('click', function () {
                modal.hidden = true;
                document.body.style.overflow = '';
            });
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.hidden) {
                modal.hidden = true;
                document.body.style.overflow = '';
            }
        });

        var form = modal.querySelector('[data-slv-inquiry-form]');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var data = Object.fromEntries(new FormData(form));
                data.product_id = productId;
                var cfg = window.SLV_CONFIG || {};
                fetch((cfg.restUrl || '/wp-json/slv/v1') + '/inquiries', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': cfg.nonce || '',
                    },
                    body: JSON.stringify(data),
                })
                    .then(function () {
                        modal.hidden = true;
                        document.body.style.overflow = '';
                        form.reset();
                        if (window.SLV_TOAST) window.SLV_TOAST('询盘已提交，我们会尽快联系您');
                    })
                    .catch(function () {
                        if (window.SLV_TOAST) window.SLV_TOAST('提交失败');
                    });
            });
        }
    }

    /* ═══ 从 REST API 加载商品元数据 ═══ */
    function loadProductMeta() {
        if (!productId) return;
        var cfg = window.SLV_CONFIG || {};
        fetch((cfg.restUrl || '/wp-json/slv/v1') + '/products/' + productId)
            .then(function (res) { return res.ok ? res.json() : null; })
            .then(function (data) {
                if (!data || !data.data) return;
                var p = data.data;
                var set = function (sel, val) {
                    document.querySelectorAll(sel).forEach(function (el) {
                        el.textContent = val != null && val !== '' ? val : '—';
                    });
                };
                set('[data-slv-sku]', p.sku);
                set('[data-slv-moq]', p.moq);
                set('[data-slv-hs]', p.hs_code);
                set('[data-slv-lead-time]', p.lead_time);

                var stockEl = document.querySelector('[data-slv-stock]');
                if (stockEl) {
                    if (p.stock_status === 'instock') {
                        stockEl.textContent = '有货（' + (p.stock_quantity || 0) + '）';
                    } else {
                        stockEl.textContent = '缺货';
                        stockEl.style.color = 'var(--slv-color-danger)';
                    }
                }
                if (p.price) {
                    var amtEl = document.querySelector('.slv-product-info__amount');
                    if (amtEl) amtEl.textContent = p.price;
                }
            })
            .catch(function () {});
    }

    function init() {
        initQuantity();
        initTabs();
        initGallery();
        initAddToCart();
        initInquiry();
        loadProductMeta();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();