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
    $id   = 'slv-icon-' . $name;

    $classes = 'slv-icon';
    if ( $size && ! in_array( $size, [ 16, 20, 24, 32 ], true ) ) {
        $classes .= ' slv-icon--' . $size;
    } elseif ( $size ) {
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
        $aria,  // 已 esc
        esc_attr( $id )
    );
}

/**
 * 返回图标 HTML 字符串（用于拼接）。
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