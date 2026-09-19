<?php
/**
 * SunLyvo Nexus — 内联 SVG 图标函数
 *
 * @package SunLyvo_Nexus
 * @since 5.1.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'slv_icon' ) ) {
    /**
     * 输出内联 SVG 图标。
     *
     * @param string $name  图标名
     * @param int    $size  尺寸
     * @param array  $attrs 附加属性
     */
    function slv_icon( string $name, int $size = 16, array $attrs = [] ): string {
        $paths = [
            'search'      => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
            'sun'         => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>',
            'moon'        => '<path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>',
            'cart'        => '<circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>',
            'user'        => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
            'chevron-down'  => '<path d="m6 9 6 6 6-6"/>',
            'chevron-up'    => '<path d="m18 15-6-6-6 6"/>',
            'chevron-right' => '<path d="m9 18 6-6-6-6"/>',
            'chevron-left'  => '<path d="m15 18-6-6 6-6"/>',
            'arrow-right'   => '<path d="M5 12h14M12 5l7 7-7 7"/>',
            'arrow-left'    => '<path d="m12 19-7-7 7-7M19 12H5"/>',
            'x'             => '<path d="M18 6 6 18M6 6l12 12"/>',
            'menu'          => '<line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>',
            'grid'          => '<rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/>',
            'list'          => '<line x1="8" x2="21" y1="6" y2="6"/><line x1="8" x2="21" y1="12" y2="12"/><line x1="8" x2="21" y1="18" y2="18"/><line x1="3" x2="3.01" y1="6" y2="6"/><line x1="3" x2="3.01" y1="12" y2="12"/><line x1="3" x2="3.01" y1="18" y2="18"/>',
            'filter'        => '<polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>',
            'package'       => '<path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/>',
            'star'          => '<path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"/>',
            'copy'          => '<rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>',
            'share'         => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"/><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"/>',
            'quote'         => '<path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/>',
            'check'         => '<polyline points="20 6 9 17 4 12"/>',
            'plus'          => '<path d="M5 12h14M12 5v14"/>',
            'minus'         => '<path d="M5 12h14"/>',
            'book-open'     => '<path d="M12 7v14M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z"/>',
            'image'         => '<rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>',
            'tag'           => '<path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"/><circle cx="7.5" cy="7.5" r=".5" fill="currentColor"/>',
            'clock'         => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
            'eye'           => '<path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/>',
        ];

        if ( ! isset( $paths[ $name ] ) ) {
            return '';
        }

        $default_attrs = [
            'width'  => $size,
            'height' => $size,
            'viewBox' => '0 0 24 24',
            'fill'    => 'none',
            'stroke'  => 'currentColor',
            'stroke-width' => '2',
            'stroke-linecap' => 'round',
            'stroke-linejoin' => 'round',
            'aria-hidden' => 'true',
        ];

        $attrs = array_merge( $default_attrs, $attrs );

        $attr_str = '';
        foreach ( $attrs as $key => $value ) {
            $attr_str .= ' ' . esc_attr( $key ) . '="' . esc_attr( (string) $value ) . '"';
        }

        return '<svg' . $attr_str . '>' . $paths[ $name ] . '</svg>';
    }
}

if ( ! function_exists( 'slv_icon_e' ) ) {
    /**
     * 直接输出图标。
     */
    function slv_icon_e( string $name, int $size = 16, array $attrs = [] ): void {
        echo slv_icon( $name, $size, $attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}