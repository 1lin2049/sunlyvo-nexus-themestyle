<?php
/**
 * SunLyvo Nexus — 资源条件加载
 *
 * reader 环境禁止加载 main.css / motion.css / mobile.css。
 * 使用文件版本号，避免缓存问题。
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 获取资源版本号（文件修改时间）。
 *
 * @since 1.0.0
 * @param string $relative 相对 assets/ 的路径。
 * @return string
 */
function slv_asset_version( string $relative ): string {
    $path = SLV_THEME_DIR . '/assets/' . ltrim( $relative, '/' );
    if ( file_exists( $path ) ) {
        return (string) filemtime( $path );
    }
    return SLV_VERSION;
}

/**
 * 加载前端资源。
 *
 * @since 1.0.0
 */
function slv_enqueue_assets(): void {
    wp_enqueue_style( 'slv-icons', "{$css}/icons.css", [ 'slv-tokens' ], slv_asset_version( 'css/icons.css' ) );
    $css = SLV_ASSETS_URL . '/css';
    $js  = SLV_ASSETS_URL . '/js';

    // ── 基础链（reader 与主站共用）
    wp_enqueue_style( 'slv-reset',      "{$css}/reset.css",      [], slv_asset_version( 'css/reset.css' ) );
    wp_enqueue_style( 'slv-tokens',     "{$css}/tokens.css",     [ 'slv-reset' ], slv_asset_version( 'css/tokens.css' ) );
    wp_enqueue_style( 'slv-components', "{$css}/components.css", [ 'slv-tokens' ], slv_asset_version( 'css/components.css' ) );
    wp_enqueue_style( 'slv-a11y',       "{$css}/a11y.css",       [ 'slv-components' ], slv_asset_version( 'css/a11y.css' ) );

    if ( slv_reader_is_environment() ) {
        // ── reader 环境：隔离加载
        wp_enqueue_style( 'slv-reader',        "{$css}/reader.css",        [ 'slv-a11y' ], slv_asset_version( 'css/reader.css' ) );
        wp_enqueue_style( 'slv-reader-extras', "{$css}/reader-extras.css", [ 'slv-reader' ], slv_asset_version( 'css/reader-extras.css' ) );
        wp_enqueue_style( 'slv-chapter-comments', "{$css}/chapter-comments.css", [ 'slv-reader' ], slv_asset_version( 'css/chapter-comments.css' ) );

        wp_enqueue_script( 'slv-reader',           "{$js}/reader.js",           [], slv_asset_version( 'js/reader.js' ), true );
        wp_enqueue_script( 'slv-toc',              "{$js}/toc.js",              [ 'slv-reader' ], slv_asset_version( 'js/toc.js' ), true );
        wp_enqueue_script( 'slv-selection-menu',   "{$js}/selection-menu.js",   [ 'slv-reader' ], slv_asset_version( 'js/selection-menu.js' ), true );
        wp_enqueue_script( 'slv-reading-position', "{$js}/reading-position.js", [ 'slv-reader' ], slv_asset_version( 'js/reading-position.js' ), true );
        wp_enqueue_script( 'slv-share',            "{$js}/share.js",            [ 'slv-reader' ], slv_asset_version( 'js/share.js' ), true );
    } else {
        // ── 主站环境
        wp_enqueue_style( 'slv-main',   "{$css}/main.css",   [ 'slv-a11y' ], slv_asset_version( 'css/main.css' ) );
        wp_enqueue_style( 'slv-motion', "{$css}/motion.css", [ 'slv-main' ], slv_asset_version( 'css/motion.css' ) );
        wp_enqueue_style( 'slv-mobile', "{$css}/mobile.css", [ 'slv-main' ], slv_asset_version( 'css/mobile.css' ) );
        wp_enqueue_script( 'slv-main',  "{$js}/main.js", [], slv_asset_version( 'js/main.js' ), true );
    }
}
add_action( 'wp_enqueue_scripts', 'slv_enqueue_assets' );

/**
 * 传递运行时参数给 JS（挂载到第一个 reader 脚本）。
 *
 * @since 1.0.0
 */
function slv_enqueue_inline_config(): void {
    $post_id = (int) ( get_the_ID() ?: 0 );

    $config = [
        'restUrl'   => esc_url_raw( rest_url( SLV_REST_NAMESPACE ) ),
        'nonce'     => wp_create_nonce( 'wp_rest' ),
        'homeUrl'   => esc_url_raw( home_url() ),
        'postId'    => $post_id,
        'userId'    => (int) get_current_user_id(),
        'locale'    => get_locale(),
        'isReader'  => slv_reader_is_environment(),
        'postMeta'  => [
            'title'       => $post_id ? get_the_title( $post_id ) : get_bloginfo( 'name' ),
            'url'         => $post_id ? get_permalink( $post_id ) : home_url(),
            'author'      => $post_id ? get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $post_id ) ) : '',
            'publishedAt' => $post_id ? get_the_date( 'c', $post_id ) : '',
        ],
    ];

    /**
     * 允许其它模块向 SLV_CONFIG 追加配置。
     *
     * @since 1.0.0
     */
    $config = (array) apply_filters( 'slv_frontend_config', $config );

    $handle = slv_reader_is_environment() ? 'slv-reader' : 'slv-main';
    wp_add_inline_script(
        $handle,
        'window.SLV_CONFIG = ' . wp_json_encode( $config ) . ';',
        'before'
    );
}
add_action( 'wp_enqueue_scripts', 'slv_enqueue_inline_config', 20 );