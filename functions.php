<?php
/**
 * SunLyvo Nexus 主题入口
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 * @author 李咏燊 <getthink-info>
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$slv_theme_dir = get_template_directory();

/**
 * 安全 require：文件不存在时记录日志，不致命。
 *
 * @since 1.0.0
 */
function slv_safe_require( string $relative_path ): void {
    $file = get_template_directory() . '/' . ltrim( $relative_path, '/' );
    if ( file_exists( $file ) ) {
        require_once $file;
    } elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( "[SunLyvo Nexus] Missing file: {$relative_path}" );
    }
}

// 核心文件（按依赖顺序）
$slv_core_files = [
    'inc/constants.php',
    'inc/schema.php',
    'inc/roles.php',
    'inc/permissions.php',
    'inc/config.php',
    'inc/cpt.php',
    'inc/taxonomy.php',
    'inc/fields.php',
    'inc/layout.php',
    'inc/enqueue.php',
    'inc/helpers.php',
    'inc/helpers-icon.php',
    'inc/security.php',
    'inc/performance.php',
    'inc/admin-bar.php',
    'inc/react-mounts.php',
    'inc/template-loader.php',
    'inc/modules/template/class-template-config-writer.php',   // ← 新增
];

foreach ( $slv_core_files as $slv_file ) {
    slv_safe_require( $slv_file );
}

// 外部服务客户端（可选）
$slv_api_client_files = glob( $slv_theme_dir . '/inc/infrastructure/api-clients/*.php' );
if ( is_array( $slv_api_client_files ) ) {
    foreach ( $slv_api_client_files as $slv_file ) {
        require_once $slv_file;
    }
}

// 业务模块（可选）
$slv_modules = [
    'commerce', 'collections', 'membership', 'points', 'affiliate',
    'warehouse', 'store-profile', 'sync', 'seo', 'ai', 'acp',
    'analytics', 'reader', 'comments', 'security', 'performance',
    'demo', 'qa', 'settings',
    'template', 'setup',
];

foreach ( $slv_modules as $slv_module ) {
    $module_file = $slv_theme_dir . "/inc/modules/{$slv_module}/module.php";
    if ( file_exists( $module_file ) ) {
        require_once $module_file;
    }
}

unset( $slv_theme_dir, $slv_core_files, $slv_api_client_files, $slv_modules, $slv_file, $slv_module );