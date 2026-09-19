<?php
/**
 * SunLyvo Nexus — 中台运行时配置生成器
 *
 * 生成 /app/config.js，注入 window.SLV_ADMIN_CONFIG。
 * 避免中台硬编码域名，支持多站点/多域名部署。
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 生成中台 config.js。
 */
function slv_write_admin_config_js(): bool {
    $config = [
        'apiBase'  => home_url( '/wp-json/slv/v1' ),
        'homeUrl'  => home_url( '/' ),
        'adminUrl' => home_url( '/app/' ),
        'locale'   => get_locale(),
        'siteName' => get_bloginfo( 'name' ),
        'wpNonce'  => wp_create_nonce( 'wp_rest' ),
    ];

    $js = 'window.SLV_ADMIN_CONFIG = ' . wp_json_encode( $config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ';';

    $target_dir = ABSPATH . 'app';
    if ( ! is_dir( $target_dir ) ) {
        if ( ! wp_mkdir_p( $target_dir ) ) {
            error_log( '[SLV] Cannot create /app/ directory' );
            return false;
        }
    }

    $target = $target_dir . '/config.js';
    $result = file_put_contents( $target, $js, LOCK_EX );

    if ( $result === false ) {
        error_log( "[SLV] Cannot write {$target}" );
        return false;
    }

    return true;
}

/**
 * 主题切换 / 站点 URL 变更时重新生成。
 */
add_action( 'update_option_home', 'slv_write_admin_config_js' );
add_action( 'update_option_siteurl', 'slv_write_admin_config_js' );
add_action( 'switch_theme', 'slv_write_admin_config_js' );

/**
 * WP-CLI 命令：wp slv write-admin-config
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
    WP_CLI::add_command( 'slv write-admin-config', function () {
        if ( slv_write_admin_config_js() ) {
            WP_CLI::success( 'Admin config.js written.' );
        } else {
            WP_CLI::error( 'Failed to write admin config.js.' );
        }
    } );
}