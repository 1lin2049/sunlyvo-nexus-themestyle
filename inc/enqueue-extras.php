<?php
/**
 * SunLyvo Nexus — 附加样式加载
 *
 * 加载 dark-fix.css 和 layout-fix.css。
 * 独立文件避免修改 enqueue.php。
 *
 * @package SunLyvo_Nexus
 * @since 1.0.4
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_enqueue_scripts', function (): void {
    // reader 环境下不加载
    if ( function_exists( 'slv_is_reader_context' ) && slv_is_reader_context() ) {
        return;
    }

    $css = get_template_directory_uri() . '/assets/css';
    $dir = get_template_directory() . '/assets/css';

    // 依赖 slv-main（enqueue.php 已加载）
    $deps = [ 'slv-main' ];

    // layout-fix.css
    $layout_file = $dir . '/layout-fix.css';
    if ( file_exists( $layout_file ) ) {
        wp_enqueue_style(
            'slv-layout-fix',
            $css . '/layout-fix.css',
            $deps,
            (string) filemtime( $layout_file )
        );
    }

    // dark-fix.css
    $dark_file = $dir . '/dark-fix.css';
    if ( file_exists( $dark_file ) ) {
        wp_enqueue_style(
            'slv-dark-fix',
            $css . '/dark-fix.css',
            array_merge( $deps, [ 'slv-layout-fix' ] ),
            (string) filemtime( $dark_file )
        );
    }
}, 50 );
add_action( 'wp_enqueue_scripts', function (): void {
    if ( is_admin() ) return;
    $css = get_template_directory_uri() . '/assets/css';
    $dir = get_template_directory() . '/assets/css';
    $file = $dir . '/final-override.css';
    if ( file_exists( $file ) ) {
        wp_enqueue_style(
            'slv-final-override',
            $css . '/final-override.css',
            [ 'slv-main' ],
            (string) filemtime( $file )
        );
    }
}, 100 );
