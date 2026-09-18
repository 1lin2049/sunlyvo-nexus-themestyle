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
    $admin_url = (string) get_option( 'slv_admin_url', home_url( '/app/' ) );
    $admin_dir = slv_get_admin_deploy_dir();

    if ( ! $admin_dir ) {
        return false;
    }

    if ( ! is_dir( $admin_dir ) ) {
        wp_mkdir_p( $admin_dir );
    }

    $admin_parsed = wp_parse_url( $admin_url );
    $home_parsed  = wp_parse_url( home_url() );
    $same_domain  = ( $admin_parsed['host'] ?? '' ) === ( $home_parsed['host'] ?? '' );

    $api_base = $same_domain
        ? '/wp-json/slv/v1'
        : untrailingslashit( home_url() ) . '/wp-json/slv/v1';

    // 从 WordPress 读取 Logo
    $logo_url = '';
    $custom_logo_id = (int) get_theme_mod( 'custom_logo' );
    if ( $custom_logo_id ) {
        $logo_url = (string) wp_get_attachment_image_url( $custom_logo_id, 'full' );
    } else {
        $site_icon_id = (int) get_option( 'site_icon' );
        if ( $site_icon_id ) {
            $logo_url = (string) wp_get_attachment_image_url( $site_icon_id, 'full' );
        }
    }

    $config = [
        'apiBase'  => $api_base,
        'adminUrl' => $admin_url,
        'homeUrl'  => untrailingslashit( home_url() ),
        'locale'   => get_locale(),
        'logoUrl'  => $logo_url,
        'siteName' => get_bloginfo( 'name' ),
    ];

    $content  = "// SunLyvo Nexus 中台配置\n";
    $content .= "// 由 WordPress 动态生成，请勿手动编辑\n";
    $content .= "// 生成时间：" . current_time( 'mysql' ) . "\n";
    $content .= "window.SLV_ADMIN_CONFIG = " . wp_json_encode( $config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ";\n";

    $path = trailingslashit( $admin_dir ) . 'config.js';
    return false !== file_put_contents( $path, $content, LOCK_EX );
}

function slv_get_admin_deploy_dir(): ?string {
    $admin_url = (string) get_option( 'slv_admin_url', home_url( '/app/' ) );
    $path      = wp_parse_url( $admin_url, PHP_URL_PATH );

    if ( ! $path || '/' === $path ) {
        return null;
    }

    return rtrim( ABSPATH, '/' ) . untrailingslashit( $path );
}

add_action( 'update_option_slv_admin_url', 'slv_write_admin_config_js', 10, 0 );
add_action( 'update_option_slv_admin_deploy_mode', 'slv_write_admin_config_js', 10, 0 );
add_action( 'update_option_site_icon', 'slv_write_admin_config_js', 10, 0 );
add_action( 'customize_save_after', 'slv_write_admin_config_js', 10, 0 );

add_action( 'init', static function () {
    if ( ! wp_next_scheduled( 'slv_regenerate_admin_config' ) ) {
        wp_schedule_event( time(), 'daily', 'slv_regenerate_admin_config' );
    }
} );
add_action( 'slv_regenerate_admin_config', 'slv_write_admin_config_js' );
