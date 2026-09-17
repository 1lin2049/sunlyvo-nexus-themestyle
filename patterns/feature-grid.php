<?php
/**
 * Title: 特性网格
 * Slug: sunlyvo-nexus/feature-grid
 * Categories: featured
 * Description: 3 列特性网格
 *
 * @package SunLyvo_Nexus
 */
?>
<!-- wp:group {"className":"slv-feature-grid","layout":{"type":"constrained"}} -->
<div class="wp-block-group slv-feature-grid">
    <!-- wp:heading {"level":2,"textAlign":"center"} -->
    <h2 class="wp-block-heading has-text-align-center">核心能力</h2>
    <!-- /wp:heading -->

    <!-- wp:columns -->
    <div class="wp-block-columns">
        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:group {"className":"slv-card"} -->
            <div class="wp-block-group slv-card">
                <!-- wp:heading {"level":3} -->
                <h3 class="wp-block-heading">内容即货架</h3>
                <!-- /wp:heading -->
                <!-- wp:paragraph -->
                <p>文章内嵌入商品卡片，从看到内容到完成支付不超过 3 次点击。</p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:group {"className":"slv-card"} -->
            <div class="wp-block-group slv-card">
                <!-- wp:heading {"level":3} -->
                <h3 class="wp-block-heading">AI 可发现</h3>
                <!-- /wp:heading -->
                <!-- wp:paragraph -->
                <p>llms.txt + 语义分块 + AI 爬虫管理，让内容被生成式引擎引用为信源。</p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column -->
        <div class="wp-block-column">
            <!-- wp:group {"className":"slv-card"} -->
            <div class="wp-block-group slv-card">
                <!-- wp:heading {"level":3} -->
                <h3 class="wp-block-heading">一键购买</h3>
                <!-- /wp:heading -->
                <!-- wp:paragraph -->
                <p>自研电商引擎 + Stripe/微信支付双栈，支持 B2C/B2B/多商户全场景。</p>
                <!-- /wp:paragraph -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</div>
<!-- /wp:group -->