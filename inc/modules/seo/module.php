<?php
/**
 * SunLyvo Nexus — SEO/GEO/AEO 模块入口
 *
 * 目录结构：
 *   inc/modules/seo/  ← 本文件 + 11 个 SEO 文件
 *   inc/modules/geo/  ← 7 个 GEO 文件
 *   inc/modules/aeo/  ← 8 个 AEO 文件
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$slv_seo_dir     = __DIR__;                    // inc/modules/seo/
$slv_modules_dir = dirname( $slv_seo_dir );    // inc/modules/

$slv_seo_files = [
    // ── SEO 核心（在 inc/modules/seo/ 内）
    $slv_seo_dir . '/meta-output.php',
    $slv_seo_dir . '/og-twitter.php',
    $slv_seo_dir . '/schema-basic.php',
    $slv_seo_dir . '/breadcrumb.php',
    $slv_seo_dir . '/sitemap.php',
    $slv_seo_dir . '/robots.php',
    $slv_seo_dir . '/404-logger.php',
    $slv_seo_dir . '/redirects.php',
    $slv_seo_dir . '/push.php',
    $slv_seo_dir . '/core-web-vitals.php',
    $slv_seo_dir . '/score-panel.php',

    // ── GEO 生成式引擎（在 inc/modules/geo/ 内）
    $slv_modules_dir . '/geo/llms-txt.php',
    $slv_modules_dir . '/geo/crawler.php',
    $slv_modules_dir . '/geo/semantic-chunks.php',
    $slv_modules_dir . '/geo/bluf-checker.php',
    $slv_modules_dir . '/geo/geo-score.php',
    $slv_modules_dir . '/geo/enhanced-graph.php',
    $slv_modules_dir . '/geo/citation-tracker.php',

    // ── AEO 答案引擎（在 inc/modules/aeo/ 内）
    $slv_modules_dir . '/aeo/faq-schema.php',
    $slv_modules_dir . '/aeo/howto-schema.php',
    $slv_modules_dir . '/aeo/qapage-schema.php',
    $slv_modules_dir . '/aeo/speakable-schema.php',
    $slv_modules_dir . '/aeo/snippet-optimizer.php',
    $slv_modules_dir . '/aeo/paa-generator.php',
    $slv_modules_dir . '/aeo/aeo-score.php',
    $slv_modules_dir . '/aeo/faq-metabox.php',
];

foreach ( $slv_seo_files as $slv_file ) {
    if ( file_exists( $slv_file ) ) {
        require_once $slv_file;
    } elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( "[SunLyvo Nexus] SEO module missing file: {$slv_file}" );
    }
}

unset( $slv_seo_dir, $slv_modules_dir, $slv_seo_files, $slv_file );