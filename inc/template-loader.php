<?php
/**
 * SunLyvo Nexus — 行业模板加载器
 *
 * 一条线：行业模板 = 区块模板 + CSS + 配置
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 获取当前启用的行业模板 slug。
 */
function slv_get_current_template(): string {
    $valid = slv_template_registry_valid_slugs();
    $default = 'brand';

    // 1. URL 参数（仅测试用）
    if ( isset( $_GET['slv_template'] ) ) {
        $t = sanitize_key( wp_unslash( $_GET['slv_template'] ) );
        if ( in_array( $t, $valid, true ) ) {
            return $t;
        }
    }

    // 2. 单页 meta
    if ( is_singular() ) {
        $page_template = get_post_meta( get_the_ID(), '_slv_template', true );
        if ( $page_template && in_array( $page_template, $valid, true ) ) {
            return $page_template;
        }
    }

    // 3. 全站默认
    $site_template = (string) get_option( 'slv_site_template', $default );
    if ( in_array( $site_template, $valid, true ) ) {
        return $site_template;
    }

    return $default;
}

/**
 * 获取有效的模板 slug 列表（缓存）。
 */
function slv_template_registry_valid_slugs(): array {
    static $cache = null;
    if ( null !== $cache ) {
        return $cache;
    }

    $cache = [];
    $dir   = SLV_THEME_DIR . '/assets/templates';
    if ( ! is_dir( $dir ) ) {
        return $cache;
    }

    foreach ( (array) glob( $dir . '/*', GLOB_ONLYDIR ) as $sub ) {
        $slug = basename( $sub );
        if ( file_exists( $sub . '/template.json' ) ) {
            $cache[] = $slug;
        }
    }

    return $cache;
}

/**
 * 从行业模板目录加载区块模板（覆盖主题默认）。
 */
add_filter( 'locate_block_template', static function ( $template, $type, $templates ) {
    $slug = slv_get_current_template();
    if ( ! $slug ) {
        return $template;
    }

    $custom_dir = SLV_THEME_DIR . '/assets/templates/' . $slug . '/templates/';
    if ( ! is_dir( $custom_dir ) ) {
        return $template;
    }

    foreach ( (array) $templates as $tpl ) {
        $candidate = $custom_dir . $tpl;
        if ( file_exists( $candidate ) ) {
            return $candidate;
        }
    }

    return $template;
}, 10, 3 );

/**
 * 输出 data-template 到 <html>。
 */
add_action( 'wp_head', static function () {
    $slug = slv_get_current_template();
    echo '<script>(function(){document.documentElement.dataset.template=' . wp_json_encode( $slug ) . ';})();</script>' . "\n";
}, 1 );

/**
 * 加载行业模板的 CSS（tokens / components / layout）。
 */
add_action( 'wp_enqueue_scripts', static function () {
    $slug = slv_get_current_template();
    if ( ! $slug ) {
        return;
    }

    $dir = SLV_THEME_DIR . '/assets/templates/' . $slug;
    $url = SLV_THEME_URL . '/assets/templates/' . $slug;

    $files = [
        'tokens'     => [ 'tokens.css',     [ 'slv-tokens' ] ],
        'components' => [ 'components.css', [ 'slv-tokens' ] ],
        'layout'     => [ 'layout.css',     [ 'slv-tokens', 'slv-components' ] ],
    ];

    foreach ( $files as $key => [ $file, $deps ] ) {
        $path = "{$dir}/{$file}";
        if ( file_exists( $path ) && filesize( $path ) > 0 ) {
            wp_enqueue_style(
                "slv-template-{$key}",
                "{$url}/{$file}",
                $deps,
                (string) filemtime( $path )
            );
        }
    }
}, 20 );