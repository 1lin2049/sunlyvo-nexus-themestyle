<?php
/**
 * Title: Hero 区块
 * Slug: sunlyvo-nexus/hero
 * Categories: featured, banner
 * Description: 首页 Hero 区块，包含标题、副标题、CTA 按钮
 *
 * @package SunLyvo_Nexus
 */
?>
<!-- wp:group {"className":"slv-hero","layout":{"type":"constrained","contentSize":"820px"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}}} -->
<div class="wp-block-group slv-hero" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
    <!-- wp:heading {"level":1,"textAlign":"center","fontSize":"4x-large"} -->
    <h1 class="wp-block-heading has-text-align-center has-4-x-large-font-size">内容即货架，AI 即入口</h1>
    <!-- /wp:heading -->

    <!-- wp:paragraph {"align":"center","fontSize":"large"} -->
    <p class="has-text-align-center has-large-font-size">面向全球市场的内容电商 + 知识付费 + 多商户 + B2B + AI 代理商务一体化平台。</p>
    <!-- /wp:paragraph -->

    <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
    <div class="wp-block-buttons">
        <!-- wp:button -->
        <div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/products/">开始探索</a></div>
        <!-- /wp:button -->

        <!-- wp:button {"className":"is-style-outline"} -->
        <div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/about/">了解更多</a></div>
        <!-- /wp:button -->
    </div>
    <!-- /wp:buttons -->
</div>
<!-- /wp:group -->