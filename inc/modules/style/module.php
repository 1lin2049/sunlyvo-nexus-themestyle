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

$slv_style_dir = __DIR__;
foreach ( [ 'class-style-registry.php', 'class-style-resolver.php', 'class-style-switcher.php' ] as $f ) {
    if ( file_exists( $slv_style_dir . '/' . $f ) ) {
        require_once $slv_style_dir . '/' . $f;
    }
}

// ── 输出 data-style 到 html 元素
add_action( 'wp_head', static function () {
    $style = SLV_Style_Resolver::resolve();
    echo '<script>(function(){document.documentElement.dataset.style=' . wp_json_encode( $style ) . ';})();</script>' . "\n";
}, 1 );

// ── body_class 加风格类
add_filter( 'body_class', static function ( array $classes ): array {
    $classes[] = 'slv-style-' . SLV_Style_Resolver::resolve();
    return $classes;
} );

// ── 加载对应风格 CSS
add_action( 'wp_enqueue_scripts', static function () {
    $style = SLV_Style_Resolver::resolve();
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

// ── REST：保存用户偏好
add_action( 'rest_api_init', static function () {
    register_rest_route( SLV_REST_NAMESPACE, '/user/style', [
        'methods'             => 'POST',
        'callback'            => static function ( WP_REST_Request $req ) {
            $user_id = get_current_user_id();
            if ( ! $user_id ) {
                return new WP_REST_Response( [ 'success' => false, 'reason' => 'not_logged_in' ], 401 );
            }
            $style = sanitize_key( (string) $req['style'] );
            $ok = SLV_Style_Resolver::save_user_preference( $user_id, $style );
            return new WP_REST_Response( [ 'success' => $ok ], $ok ? 200 : 400 );
        },
        'permission_callback' => static fn() => is_user_logged_in(),
    ] );
} );

// ── Customizer
add_action( 'customize_register', static function ( $wp_customize ) {
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