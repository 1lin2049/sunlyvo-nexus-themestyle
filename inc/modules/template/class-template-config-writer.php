<?php
/**
 * SunLyvo Nexus — 中台 config.js 生成器
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 生成 admin/config.js。
 */
function slv_write_admin_config_js(): bool {
    $admin_url = (string) get_option( 'slv_admin_url', home_url( '/admin/' ) );
    $admin_dir = slv_get_admin_deploy_dir();

    if ( ! $admin_dir || ! is_dir( $admin_dir ) ) {
        return false;
    }

    // 推断 API Base
    $admin_parsed = wp_parse_url( $admin_url );
    $home_parsed  = wp_parse_url( home_url() );

    $same_domain = ( $admin_parsed['host'] ?? '' ) === ( $home_parsed['host'] ?? '' );

    $api_base = $same_domain
        ? '/wp-json/slv/v1'
        : untrailingslashit( home_url() ) . '/wp-json/slv/v1';

    $config = [
        'apiBase'    => $api_base,
        'adminUrl'   => $admin_url,
        'homeUrl'    => home_url(),
        'locale'     => get_locale(),
        'nonceEndpoint' => '/wp-json/slv/v1/nonce',
    ];

    $content = "// SunLyvo Nexus 中台配置\n";
    $content .= "// 由 WordPress 动态生成，请勿手动编辑\n";
    $content .= "window.SLV_ADMIN_CONFIG = " . wp_json_encode( $config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . ";\n";

    $path = trailingslashit( $admin_dir ) . 'config.js';
    return false !== file_put_contents( $path, $content, LOCK_EX );
}

/**
 * 获取 admin 部署目录。
 */
function slv_get_admin_deploy_dir(): ?string {
    $admin_url = (string) get_option( 'slv_admin_url', home_url( '/admin/' ) );
    $path      = wp_parse_url( $admin_url, PHP_URL_PATH );

    if ( ! $path ) {
        return null;
    }

    // 如果是子目录部署，返回站点根目录下的路径
    return rtrim( ABSPATH, '/' ) . untrailingslashit( $path );
}

/**
 * 后台设置更新时重新生成。
 */
add_action( 'update_option_slv_admin_url', 'slv_write_admin_config_js', 10, 0 );
add_action( 'update_option_slv_admin_deploy_mode', 'slv_write_admin_config_js', 10, 0 );

/**
 * 每天 cron 重新生成一次（防止遗漏）。
 */
add_action( 'init', static function () {
    if ( ! wp_next_scheduled( 'slv_regenerate_admin_config' ) ) {
        wp_schedule_event( time(), 'daily', 'slv_regenerate_admin_config' );
    }
} );
add_action( 'slv_regenerate_admin_config', 'slv_write_admin_config_js' );

/**
 * 提供手动重新生成按钮（后台）。
 */
add_action( 'admin_post_slv_regenerate_admin_config', static function () {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( '无权限' );
    }
    check_admin_referer( 'slv_regenerate_admin_config' );

    $ok = slv_write_admin_config_js();
    $redirect = add_query_arg(
        [ 'page' => 'slv-settings', 'regenerated' => $ok ? '1' : '0' ],
        admin_url( 'admin.php' )
    );
    wp_safe_redirect( $redirect );
    exit;
} );