<?php
/**
 * SunLyvo Nexus — 资源条件加载
 *
 * @package SunLyvo_Nexus
 * @since 5.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function slv_asset_version( string $relative ): string {
    $path = SLV_THEME_DIR . '/assets/' . ltrim( $relative, '/' );
    if ( file_exists( $path ) ) {
        return (string) filemtime( $path );
    }
    return SLV_VERSION;
}

function slv_enqueue_assets(): void {
    $css = SLV_ASSETS_URL . '/css';
    $js  = SLV_ASSETS_URL . '/js';

    // ── 基础链（所有环境共用）
    wp_enqueue_style( 'slv-reset',      "{$css}/reset.css",       [], slv_asset_version( 'css/reset.css' ) );
    wp_enqueue_style( 'slv-tokens',     "{$css}/01-tokens.css",   [ 'slv-reset' ], slv_asset_version( 'css/01-tokens.css' ) );
    wp_enqueue_style( 'slv-base',       "{$css}/02-base.css",     [ 'slv-tokens' ], slv_asset_version( 'css/02-base.css' ) );
    wp_enqueue_style( 'slv-a11y',       "{$css}/05-a11y.css",     [ 'slv-base' ], slv_asset_version( 'css/05-a11y.css' ) );
    wp_enqueue_style( 'slv-icons',      "{$css}/06-icons.css",    [ 'slv-a11y' ], slv_asset_version( 'css/06-icons.css' ) );
    wp_enqueue_style( 'slv-layout',     "{$css}/10-layout.css",   [ 'slv-icons' ], slv_asset_version( 'css/10-layout.css' ) );
    wp_enqueue_style( 'slv-components', "{$css}/20-components.css", [ 'slv-layout' ], slv_asset_version( 'css/20-components.css' ) );

    if ( slv_reader_is_environment() ) {
        // ── Reader 环境（隔离）
        wp_enqueue_style( 'slv-reader',           "{$css}/60-reader.css",           [ 'slv-components' ], slv_asset_version( 'css/60-reader.css' ) );

        wp_enqueue_script( 'slv-reader',           "{$js}/reader.js",           [], slv_asset_version( 'js/reader.js' ), true );
        wp_enqueue_script( 'slv-toc',              "{$js}/toc.js",              [ 'slv-reader' ], slv_asset_version( 'js/toc.js' ), true );
        wp_enqueue_script( 'slv-selection-menu',   "{$js}/selection-menu.js",   [ 'slv-reader' ], slv_asset_version( 'js/selection-menu.js' ), true );
        wp_enqueue_script( 'slv-reading-position', "{$js}/reading-position.js", [ 'slv-reader' ], slv_asset_version( 'js/reading-position.js' ), true );
        wp_enqueue_script( 'slv-share',            "{$js}/share.js",            [ 'slv-reader' ], slv_asset_version( 'js/share.js' ), true );
    } else {
        // ── 主站环境
        wp_enqueue_style( 'slv-header', "{$css}/30-header.css", [ 'slv-components' ], slv_asset_version( 'css/30-header.css' ) );
        wp_enqueue_style( 'slv-footer', "{$css}/31-footer.css", [ 'slv-header' ], slv_asset_version( 'css/31-footer.css' ) );

        wp_enqueue_script( 'slv-header', "{$js}/header.js", [], slv_asset_version( 'js/header.js' ), true );
        wp_enqueue_script( 'slv-main',   "{$js}/main.js",   [ 'slv-header' ], slv_asset_version( 'js/main.js' ), true );

        // 首页
        if ( is_front_page() ) {
            $f = SLV_THEME_DIR . '/assets/css/32-home.css';
            if ( file_exists( $f ) ) {
                wp_enqueue_style( 'slv-home', "{$css}/32-home.css", [ 'slv-footer' ], (string) filemtime( $f ) );
            }
            $fjs = SLV_THEME_DIR . '/assets/js/home.js';
            if ( file_exists( $fjs ) ) {
                wp_enqueue_script( 'slv-home', "{$js}/home.js", [ 'slv-main' ], (string) filemtime( $fjs ), true );
            }
        }

        // 商品列表
        if ( is_post_type_archive( 'product' ) || is_tax( [ 'product_cat', 'product_tag' ] ) ) {
            $f = SLV_THEME_DIR . '/assets/css/40-product-list.css';
            if ( file_exists( $f ) ) {
                wp_enqueue_style( 'slv-product-list', "{$css}/40-product-list.css", [ 'slv-footer' ], (string) filemtime( $f ) );
            }
            $fjs = SLV_THEME_DIR . '/assets/js/product-list.js';
            if ( file_exists( $fjs ) ) {
                wp_enqueue_script( 'slv-product-list', "{$js}/product-list.js", [ 'slv-main' ], (string) filemtime( $fjs ), true );
            }
        }

        // 商品详情
        if ( is_singular( 'product' ) ) {
            $f = SLV_THEME_DIR . '/assets/css/40-product-detail.css';
            if ( file_exists( $f ) ) {
                wp_enqueue_style( 'slv-product-detail', "{$css}/40-product-detail.css", [ 'slv-footer' ], (string) filemtime( $f ) );
            }
            $fjs = SLV_THEME_DIR . '/assets/js/product-detail.js';
            if ( file_exists( $fjs ) ) {
                wp_enqueue_script( 'slv-product-detail', "{$js}/product-detail.js", [ 'slv-main' ], (string) filemtime( $fjs ), true );
            }
        }

        // 博客
        if ( is_singular( 'post' ) || is_home() || is_category() || is_tag() || is_author() || is_date() ) {
            wp_enqueue_style( 'slv-blog', "{$css}/41-blog.css", [ 'slv-footer' ], slv_asset_version( 'css/41-blog.css' ) );
        }

        // FAQ
        if ( is_singular( 'faq' ) || is_post_type_archive( 'faq' ) || is_tax( 'faq_cat' ) ) {
            wp_enqueue_style( 'slv-faq', "{$css}/42-faq.css", [ 'slv-footer' ], slv_asset_version( 'css/42-faq.css' ) );
        }

        // 百科
        if ( is_singular( 'wiki' ) || is_post_type_archive( 'wiki' ) || is_tax( 'wiki_cat' ) ) {
            wp_enqueue_style( 'slv-wiki', "{$css}/43-wiki.css", [ 'slv-footer' ], slv_asset_version( 'css/43-wiki.css' ) );
        }

        // 合集
        if ( is_singular( 'collection' ) || is_post_type_archive( 'collection' ) || is_tax( 'collection_cat' ) ) {
            wp_enqueue_style( 'slv-collection', "{$css}/44-collection.css", [ 'slv-footer' ], slv_asset_version( 'css/44-collection.css' ) );
        }

        // 通用单页
        if ( is_singular() ) {
            wp_enqueue_style( 'slv-single', "{$css}/50-single.css", [ 'slv-footer' ], slv_asset_version( 'css/50-single.css' ) );
        }

        // 深色模式（Token 覆盖，全站）
        wp_enqueue_style( 'slv-dark', "{$css}/70-dark.css", [ 'slv-components' ], slv_asset_version( 'css/70-dark.css' ) );
    }
}
add_action( 'wp_enqueue_scripts', 'slv_enqueue_assets' );

/**
 * 运行时配置
 */
function slv_enqueue_inline_config(): void {
    $post_id = (int) ( get_the_ID() ?: 0 );

    $config = [
        'restUrl'  => esc_url_raw( rest_url( SLV_REST_NAMESPACE ) ),
        'nonce'    => wp_create_nonce( 'wp_rest' ),
        'homeUrl'  => esc_url_raw( home_url() ),
        'postId'   => $post_id,
        'userId'   => (int) get_current_user_id(),
        'locale'   => get_locale(),
        'isReader' => slv_reader_is_environment(),
        'template' => function_exists( 'slv_get_current_template' ) ? slv_get_current_template() : 'brand',
        'postMeta' => [
            'title'       => $post_id ? get_the_title( $post_id ) : get_bloginfo( 'name' ),
            'url'         => $post_id ? get_permalink( $post_id ) : home_url(),
            'author'      => $post_id ? get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $post_id ) ) : '',
            'publishedAt' => $post_id ? get_the_date( 'c', $post_id ) : '',
        ],
        'adminUrl'  => esc_url_raw( get_option( 'slv_admin_url', home_url( '/app/' ) ) ),
        'adminMode' => get_option( 'slv_admin_deploy_mode', 'subdir' ),
    ];

    $config = (array) apply_filters( 'slv_frontend_config', $config );

    $handle = slv_reader_is_environment() ? 'slv-reader' : 'slv-header';
    wp_add_inline_script( $handle, 'window.SLV_CONFIG = ' . wp_json_encode( $config ) . ';', 'before' );
}
add_action( 'wp_enqueue_scripts', 'slv_enqueue_inline_config', 30 );