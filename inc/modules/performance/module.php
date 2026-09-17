<?php
/**
 * SunLyvo Nexus — 性能模块入口
 *
 * 提供后台「性能」设置：Redis 对象缓存、页面缓存、图片优化。
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$slv_perf_dir   = __DIR__;
$slv_perf_files = [
    'class-performance-settings.php',
    'class-object-cache-installer.php',
];

foreach ( $slv_perf_files as $slv_rel ) {
    $slv_file = $slv_perf_dir . '/' . $slv_rel;
    if ( file_exists( $slv_file ) ) {
        require_once $slv_file;
    } elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( "[SunLyvo Nexus] Performance module missing: {$slv_rel}" );
    }
}

unset( $slv_perf_dir, $slv_perf_files, $slv_rel, $slv_file );

// 注册后台子菜单
add_action( 'admin_menu', static function () {
    if ( class_exists( 'SLV_Performance_Settings' ) ) {
        SLV_Performance_Settings::register_menu();
    }
} );