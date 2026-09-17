<?php
/**
 * SunLyvo Nexus — 布局系统
 *
 * 6 种布局：fullscreen / wide / narrow / sidebar-left / sidebar-right / custom
 * 优先级链：单篇 meta > 全站默认 > 硬编码默认(wide)
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 获取当前页面的布局 slug。
 *
 * @since 1.0.0
 * @return string
 */
function slv_get_current_layout(): string {
    $allowed = [ 'fullscreen', 'wide', 'narrow', 'sidebar-left', 'sidebar-right', 'custom' ];

    // 1. 单篇 meta
    if ( is_singular() ) {
        $meta_layout = get_post_meta( get_the_ID(), '_slv_layout', true );
        if ( $meta_layout && in_array( $meta_layout, $allowed, true ) ) {
            return $meta_layout;
        }
    }

    // 2. 全站默认
    $default = slv_get_config( 'default_layout', 'wide' );
    if ( in_array( $default, $allowed, true ) ) {
        return $default;
    }

    // 3. 硬编码默认
    return 'wide';
}

/**
 * 输出布局 CSS 类。
 *
 * @since 1.0.0
 */
function slv_layout_class(): void {
    $layout = slv_get_current_layout();
    echo 'slv-layout slv-layout--' . esc_attr( $layout );
}

/**
 * 输出自定义宽度双端独立 vw。
 *
 * @since 1.0.0
 */
function slv_layout_custom_width(): void {
    if ( 'custom' !== slv_get_current_layout() ) {
        return;
    }

    $pc     = (int) slv_get_config( 'custom_width_pc', 60 );
    $mobile = (int) slv_get_config( 'custom_width_mobile', 95 );

    $pc     = max( 30, min( 100, $pc ) );
    $mobile = max( 30, min( 100, $mobile ) );

    printf(
        '<style>:root{--slv-custom-width:%dvw}@media(max-width:640px){:root{--slv-custom-width:%dvw}}</style>',
        $pc, $mobile
    );
}
add_action( 'wp_head', 'slv_layout_custom_width', 5 );

/**
 * 注册 6 种布局的页面模板元数据。
 *
 * @since 1.0.0
 */
function slv_register_layout_meta(): void {
    register_post_meta( '', '_slv_layout', [
        'type'              => 'string',
        'single'            => true,
        'default'           => '',
        'show_in_rest'      => true,
        'sanitize_callback' => function( $value ) {
            $allowed = [ '', 'fullscreen', 'wide', 'narrow', 'sidebar-left', 'sidebar-right', 'custom' ];
            return in_array( $value, $allowed, true ) ? $value : '';
        },
        'auth_callback'     => function() {
            return current_user_can( 'edit_posts' );
        },
    ] );
}
add_action( 'init', 'slv_register_layout_meta' );