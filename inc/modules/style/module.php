<?php
/**
 * SunLyvo Nexus — 风格模块入口
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$slv_style_dir   = __DIR__;
$slv_style_files = [
    'class-style-registry.php',
    'class-style-resolver.php',
    'class-style-switcher.php',
    'rest-api.php',   // ← 必须加载，负责 /styles 和 /user/style 路由
];

foreach ( $slv_style_files as $slv_rel ) {
    $slv_file = $slv_style_dir . '/' . $slv_rel;
    if ( file_exists( $slv_file ) ) {
        require_once $slv_file;
    } elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( "[SunLyvo Nexus] Style module missing: {$slv_rel}" );
    }
}

unset( $slv_style_dir, $slv_style_files, $slv_rel, $slv_file );

// ── 输出 data-style 到 html 元素
add_action( 'wp_head', static function () {
    if ( ! class_exists( 'SLV_Style_Resolver' ) ) {
        return;
    }
    $style = SLV_Style_Resolver::resolve();
    echo '<script>(function(){document.documentElement.dataset.style=' . wp_json_encode( $style ) . ';})();</script>' . "\n";
}, 1 );

// ── body_class 加风格类
add_filter( 'body_class', static function ( array $classes ): array {
    if ( class_exists( 'SLV_Style_Resolver' ) ) {
        $classes[] = 'slv-style-' . SLV_Style_Resolver::resolve();
    }
    return $classes;
} );

// ── 加载对应风格 CSS
add_action( 'wp_enqueue_scripts', static function () {
    if ( ! class_exists( 'SLV_Style_Resolver' ) || ! class_exists( 'SLV_Style_Registry' ) ) {
        return;
    }
    $style  = SLV_Style_Resolver::resolve();
    $styles = SLV_Style_Registry::all();
    if ( ! isset( $styles[ $style ] ) ) {
        return;
    }
    $file = $styles[ $style ]['css'];
    $path = SLV_THEME_DIR . '/assets/css/styles/' . $file;
    if ( file_exists( $path ) ) {
        wp_enqueue_style(
            'slv-style-' . $style,
            SLV_ASSETS_URL . '/css/styles/' . $file,
            [ 'slv-tokens' ],
            (string) filemtime( $path )
        );
    }
}, 20 );

// ── Customizer
add_action( 'customize_register', static function ( $wp_customize ) {
    if ( ! class_exists( 'SLV_Style_Registry' ) ) {
        return;
    }

    $wp_customize->add_section( 'slv_style', [
        'title'    => __( '行业风格', 'sunlyvo-nexus' ),
        'priority' => 20,
    ] );

    $wp_customize->add_setting( 'slv_style_variant', [
        'default'           => 'brand',
        'sanitize_callback' => 'sanitize_key',
    ] );

    $choices = [];
    foreach ( SLV_Style_Registry::all() as $slug => $s ) {
        $choices[ $slug ] = $s['label'];
    }

    $wp_customize->add_control( 'slv_style_variant', [
        'label'   => __( '选择风格', 'sunlyvo-nexus' ),
        'section' => 'slv_style',
        'type'    => 'select',
        'choices' => $choices,
    ] );
} );