<?php
/**
 * SunLyvo Nexus — 图标辅助函数
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 图标清单（与 assets/icons/lucide/ 保持同步）。
 *
 * @since 1.0.0
 * @return array
 */
function slv_icon_manifest(): array {
    return [
        'arrow-right', 'arrow-left', 'chevron-down', 'chevron-up',
        'chevron-right', 'chevron-left', 'menu', 'x', 'search',
        'external-link',
        'user', 'user-circle', 'log-in', 'log-out', 'settings', 'bell',
        'sun', 'moon', 'sun-moon',
        'shopping-cart', 'credit-card', 'truck', 'shield-check',
        'tag', 'gift', 'package', 'store',
        'check', 'plus', 'minus', 'trash-2', 'pencil', 'copy',
        'share-2', 'download', 'upload', 'filter', 'list',
        'star', 'heart', 'info', 'triangle-alert', 'circle-check',
        'circle-x', 'circle-alert',
        'book-open', 'bookmark', 'quote', 'link', 'clock', 'eye',
        'file-text', 'image', 'video', 'music',
        'chart-line', 'chart-bar', 'trending-up', 'trending-down',
        'activity', 'gauge',
        'map-pin', 'globe', 'navigation',
        'sparkles', 'bot', 'brain', 'lightbulb',
        'twitter', 'linkedin', 'github', 'facebook', 'youtube',
        'instagram', 'rss',
    ];
}

/**
 * 校验图标名是否合法。
 *
 * @since 1.0.0
 */
function slv_is_valid_icon( string $name ): bool {
    $name = preg_replace( '/^slv-icon-/', '', $name );
    return in_array( $name, slv_icon_manifest(), true );
}

/**
 * 渲染图标。
 *
 * @since 1.0.0
 * @param string $name   图标名（不含 slv-icon- 前缀）。
 * @param int    $size   尺寸（px），默认 16。
 * @param string $class  额外 class。
 * @param string $label  无障碍标签（不传则视为装饰性）。
 */
function slv_icon( string $name, int $size = 16, string $class = '', string $label = '' ): void {
    if ( '' === $name ) {
        return;
    }

    $name = preg_replace( '/^slv-icon-/', '', $name );

    if ( ! slv_is_valid_icon( $name ) && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( "[SunLyvo Nexus] 图标名不在清单中：{$name}" );
    }

    $id = 'slv-icon-' . $name;

    $classes = 'slv-icon';
    if ( $size ) {
        $classes .= ' slv-icon--' . $size;
    }
    if ( $class ) {
        $classes .= ' ' . $class;
    }

    $aria = $label
        ? 'role="img" aria-label="' . esc_attr( $label ) . '"'
        : 'aria-hidden="true"';

    printf(
        '<svg class="%s" width="%d" height="%d" %s><use href="#%s"></use></svg>',
        esc_attr( $classes ),
        $size,
        $size,
        $aria,
        esc_attr( $id )
    );
}

/**
 * 返回图标 HTML 字符串。
 *
 * @since 1.0.0
 */
function slv_get_icon( string $name, int $size = 16, string $class = '', string $label = '' ): string {
    ob_start();
    slv_icon( $name, $size, $class, $label );
    return (string) ob_get_clean();
}

/**
 * 输出图标 sprite 到页面底部。
 *
 * @since 1.0.0
 */
function slv_render_icon_sprite(): void {
    $sprite = SLV_THEME_DIR . '/assets/icons/slv-icons.svg';
    if ( ! file_exists( $sprite ) ) {
        return;
    }
    echo '<div hidden aria-hidden="true" style="position:absolute;width:0;height:0;overflow:hidden">';
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo file_get_contents( $sprite );
    echo '</div>';
}
add_action( 'wp_footer', 'slv_render_icon_sprite', 1 );