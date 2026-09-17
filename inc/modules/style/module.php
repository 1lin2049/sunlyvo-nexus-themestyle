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
    'rest-api.php',
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

// ── 输出 data-style 和 data-theme 到 html
add_action( 'wp_head', static function () {
    if ( ! class_exists( 'SLV_Style_Resolver' ) ) {
        return;
    }
    $style = SLV_Style_Resolver::resolve();
    $mode  = SLV_Style_Resolver::resolve_theme_mode();
    ?>
    <script>
    (function(){
        var s = <?php echo wp_json_encode( $style ); ?>;
        var m = <?php echo wp_json_encode( $mode ); ?>;
        document.documentElement.dataset.style = s;
        var eff = m;
        if (m === 'auto') {
            eff = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        document.documentElement.dataset.theme = eff;
        document.documentElement.dataset.themeMode = m;
    })();
    </script>
    <?php
}, 1 );

// ── body_class
add_filter( 'body_class', static function ( array $classes ): array {
    if ( class_exists( 'SLV_Style_Resolver' ) ) {
        $classes[] = 'slv-style-' . SLV_Style_Resolver::resolve();
        $classes[] = 'slv-theme-' . SLV_Style_Resolver::resolve_theme_mode();
    }
    return $classes;
} );

// ── 加载风格 CSS
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
            'slv-style',
            SLV_ASSETS_URL . '/css/styles/' . $file,
            [ 'slv-tokens' ],
            (string) filemtime( $path )
        );
    }
    // 深色模式始终加载（用户可切换）
    $dark_path = SLV_THEME_DIR . '/assets/css/styles/dark.css';
    if ( file_exists( $dark_path ) && $style !== 'dark' ) {
        wp_enqueue_style(
            'slv-theme-dark',
            SLV_ASSETS_URL . '/css/styles/dark.css',
            [ 'slv-tokens' ],
            (string) filemtime( $dark_path )
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
        if ( ! empty( $s['is_theme_mode'] ) ) {
            continue; // 深色模式不在这里
        }
        $choices[ $slug ] = $s['label'];
    }

    $wp_customize->add_control( 'slv_style_variant', [
        'label'   => __( '选择风格', 'sunlyvo-nexus' ),
        'section' => 'slv_style',
        'type'    => 'select',
        'choices' => $choices,
    ] );
} );