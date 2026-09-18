<?php
/**
 * SunLyvo Nexus — 模板模块入口
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$slv_template_dir   = __DIR__;
$slv_template_files = [
    'class-template-registry.php',
    'class-template-resolver.php',
    'class-template-switcher.php',
    'rest-api.php',
];

foreach ( $slv_template_files as $slv_rel ) {
    $slv_file = $slv_template_dir . '/' . $slv_rel;
    if ( file_exists( $slv_file ) ) {
        require_once $slv_file;
    } elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( "[SunLyvo Nexus] Template module missing: {$slv_rel}" );
    }
}

unset( $slv_template_dir, $slv_template_files, $slv_rel, $slv_file );

// 输出 data-template
add_filter( 'body_class', static function ( array $classes ): array {
    if ( function_exists( 'slv_get_current_template' ) ) {
        $classes[] = 'slv-template-' . slv_get_current_template();
    }
    return $classes;
} );

// 深色模式（用户级）—— data-theme 属性
add_action( 'wp_head', static function () {
    if ( ! class_exists( 'SLV_Template_Resolver' ) ) {
        return;
    }
    $mode = SLV_Template_Resolver::resolve_theme_mode();
    ?>
    <script>
    (function(){
        var m = <?php echo wp_json_encode( $mode ); ?>;
        var eff = m;
        if (m === 'auto') {
            eff = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        document.documentElement.dataset.theme = eff;
        document.documentElement.dataset.themeMode = m;
    })();
    </script>
    <?php
}, 2 );

// 深色模式 CSS（始终加载，用户可切换）
add_action( 'wp_enqueue_scripts', static function () {
    $dark = SLV_THEME_DIR . '/assets/templates/dark/tokens.css';
    if ( file_exists( $dark ) && filesize( $dark ) > 0 ) {
        wp_enqueue_style(
            'slv-template-dark',
            SLV_THEME_URL . '/assets/templates/dark/tokens.css',
            [ 'slv-tokens' ],
            (string) filemtime( $dark )
        );
    }
}, 25 );

// Customizer
add_action( 'customize_register', static function ( $wp_customize ) {
    if ( ! class_exists( 'SLV_Template_Registry' ) ) {
        return;
    }

    $wp_customize->add_section( 'slv_template', [
        'title'    => __( '行业模板', 'sunlyvo-nexus' ),
        'priority' => 20,
    ] );

    $wp_customize->add_setting( 'slv_template_variant', [
        'default'           => 'brand',
        'sanitize_callback' => 'sanitize_key',
    ] );

    $choices = [];
    foreach ( SLV_Template_Registry::all() as $slug => $meta ) {
        if ( ! empty( $meta['is_theme_mode'] ) ) {
            continue;
        }
        $choices[ $slug ] = $meta['label'];
    }

    $wp_customize->add_control( 'slv_template_variant', [
        'label'   => __( '选择行业模板', 'sunlyvo-nexus' ),
        'section' => 'slv_template',
        'type'    => 'select',
        'choices' => $choices,
    ] );
} );