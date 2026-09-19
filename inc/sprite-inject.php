<?php
/**
 * SunLyvo Nexus — SVG Sprite 注入
 *
 * 在 <body> 开头注入 sprite 定义，使 <use href="#slv-icon-xxx">
 * 能正确引用 <symbol id="slv-icon-xxx">。
 *
 * @package SunLyvo_Nexus
 * @since 1.0.3
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 读取 sprite 内容（带缓存）。
 */
function slv_get_icon_sprite_content(): string {
    static $cached = null;
    if ( $cached !== null ) {
        return $cached;
    }

    $file = get_template_directory() . '/assets/icons/slv-icons.svg';
    if ( ! file_exists( $file ) ) {
        $cached = '';
        return $cached;
    }

    $svg = (string) file_get_contents( $file );
    if ( $svg === '' ) {
        $cached = '';
        return $cached;
    }

    // 移除 XML 声明（在 HTML 内嵌时不需要）
    $svg = preg_replace( '/<\?xml[^>]*\?>/i', '', $svg );

    // 确保 <svg> 标签带 style 隐藏 + aria-hidden
    // 不同文件的 svg 开头可能不同，做通用替换
    $svg = preg_replace(
        '/<svg\b([^>]*)>/i',
        '<svg$1 style="position:absolute;width:0;height:0;overflow:hidden" aria-hidden="true" focusable="false">',
        $svg,
        1
    );

    $cached = $svg;
    return $cached;
}

/**
 * 在 <body> 打开后立即注入 sprite。
 */
add_action( 'wp_body_open', function (): void {
    $sprite = slv_get_icon_sprite_content();
    if ( $sprite === '' ) {
        return;
    }
    echo "\n<!-- SunLyvo Icon Sprite -->\n";
    echo $sprite;
    echo "\n<!-- /SunLyvo Icon Sprite -->\n";
}, 1 );

/**
 * 兼容：某些主题或缓存插件不触发 wp_body_open 时，用 wp_footer 兜底。
 */
add_action( 'wp_footer', function (): void {
    if ( did_action( 'wp_body_open' ) ) {
        return;
    }
    $sprite = slv_get_icon_sprite_content();
    if ( $sprite === '' ) {
        return;
    }
    echo "\n<!-- SunLyvo Icon Sprite (fallback) -->\n";
    echo $sprite;
    echo "\n<!-- /SunLyvo Icon Sprite -->\n";
}, 1 );