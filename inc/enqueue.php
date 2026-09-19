<?php
/**
 * SunLyvo Nexus — 资源条件加载 v7.0
 *
 * @package SunLyvo_Nexus
 * @since 7.0.0
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

function slv_enqueue_style_if_exists( string $handle, string $file, array $deps = [] ): void {
    $path = SLV_THEME_DIR . '/assets/css/' . $file;
    if ( ! file_exists( $path ) ) {
        return;
    }
    wp_enqueue_style(
        $handle,
        SLV_ASSETS_URL . '/css/' . $file,
        $deps,
        (string) filemtime( $path )
    );
}

/**
 * 加载 Swiper.js（成熟轮播库）
 *
 * @return bool 是否已加载
 */
function slv_enqueue_swiper(): bool {
    // 已加载则跳过
    if ( wp_style_is( 'slv-swiper', 'enqueued' ) ) {
        return true;
    }

    // CDN 源（双源兜底）
    $css_url = 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css';
    $js_url  = 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js';

    // 优先使用本地文件（如存在）
    $local_css = SLV_THEME_DIR . '/assets/libs/swiper/swiper-bundle.min.css';
    $local_js  = SLV_THEME_DIR . '/assets/libs/swiper/swiper-bundle.min.js';

    if ( file_exists( $local_css ) ) {
        wp_enqueue_style( 'slv-swiper', SLV_ASSETS_URL . '/libs/swiper/swiper-bundle.min.css', [], '11' );
    } else {
        wp_enqueue_style( 'slv-swiper', $css_url, [], '11' );
    }

    if ( file_exists( $local_js ) ) {
        wp_enqueue_script( 'slv-swiper', SLV_ASSETS_URL . '/libs/swiper/swiper-bundle.min.js', [], '11', true );
    } else {
        wp_enqueue_script( 'slv-swiper', $js_url, [], '11', true );
    }

    return true;
}

function slv_enqueue_assets(): void {
    $js = SLV_ASSETS_URL . '/js';

    // 基础链
    slv_enqueue_style_if_exists( 'slv-reset',      'reset.css' );
    slv_enqueue_style_if_exists( 'slv-tokens',     '01-tokens.css' );
    slv_enqueue_style_if_exists( 'slv-base',       '02-base.css' );
    slv_enqueue_style_if_exists( 'slv-a11y',       '05-a11y.css' );
    slv_enqueue_style_if_exists( 'slv-icons',      '06-icons.css' );
    slv_enqueue_style_if_exists( 'slv-layout',     '10-layout.css' );
    slv_enqueue_style_if_exists( 'slv-components', '20-components.css' );

    if ( function_exists( 'slv_reader_is_environment' ) && slv_reader_is_environment() ) {
        slv_enqueue_style_if_exists( 'slv-reader', '60-reader.css' );
        wp_enqueue_script( 'slv-reader',           "{$js}/reader.js",           [], slv_asset_version( 'js/reader.js' ), true );
        wp_enqueue_script( 'slv-toc',              "{$js}/toc.js",              [ 'slv-reader' ], slv_asset_version( 'js/toc.js' ), true );
        wp_enqueue_script( 'slv-selection-menu',   "{$js}/selection-menu.js",   [ 'slv-reader' ], slv_asset_version( 'js/selection-menu.js' ), true );
        wp_enqueue_script( 'slv-reading-position', "{$js}/reading-position.js", [ 'slv-reader' ], slv_asset_version( 'js/reading-position.js' ), true );
        wp_enqueue_script( 'slv-share',            "{$js}/share.js",            [ 'slv-reader' ], slv_asset_version( 'js/share.js' ), true );
    } else {
        slv_enqueue_style_if_exists( 'slv-header', '30-header.css' );
        slv_enqueue_style_if_exists( 'slv-footer', '31-footer.css' );

        wp_enqueue_script( 'slv-header', "{$js}/header.js", [], slv_asset_version( 'js/header.js' ), true );
        wp_enqueue_script( 'slv-main',   "{$js}/main.js",   [ 'slv-header' ], slv_asset_version( 'js/main.js' ), true );

        // 首页
        if ( is_front_page() ) {
            slv_enqueue_style_if_exists( 'slv-home', '32-home.css' );
        }

        // 商品列表
        if ( is_post_type_archive( 'product' ) || is_tax( [ 'product_cat', 'product_tag' ] ) ) {
            slv_enqueue_swiper();
            slv_enqueue_style_if_exists( 'slv-product-list', '40-product-list.css', [ 'slv-swiper' ] );
            if ( file_exists( SLV_THEME_DIR . '/assets/js/product-list.js' ) ) {
                wp_enqueue_script( 'slv-product-list', "{$js}/product-list.js", [ 'slv-main', 'slv-swiper' ], slv_asset_version( 'js/product-list.js' ), true );
            }
        }

        // 商品详情
        if ( is_singular( 'product' ) ) {
            slv_enqueue_swiper();
            slv_enqueue_style_if_exists( 'slv-product-detail', '40-product-detail.css', [ 'slv-swiper' ] );
            if ( file_exists( SLV_THEME_DIR . '/assets/js/product-detail.js' ) ) {
                wp_enqueue_script( 'slv-product-detail', "{$js}/product-detail.js", [ 'slv-main', 'slv-swiper' ], slv_asset_version( 'js/product-detail.js' ), true );
            }
        }

        // 博客
        if ( is_singular( 'post' ) || is_home() || is_category() || is_tag() || is_author() || is_date() ) {
            slv_enqueue_style_if_exists( 'slv-blog', '41-blog.css' );
        }

        // FAQ
        if ( is_singular( 'faq' ) || is_post_type_archive( 'faq' ) || is_tax( 'faq_cat' ) ) {
            slv_enqueue_style_if_exists( 'slv-faq', '42-faq.css' );
        }

        // 百科
        if ( is_singular( 'wiki' ) || is_post_type_archive( 'wiki' ) || is_tax( 'wiki_cat' ) ) {
            slv_enqueue_style_if_exists( 'slv-wiki', '43-wiki.css' );
        }

        // 合集
        if ( is_singular( 'collection' ) || is_post_type_archive( 'collection' ) || is_tax( 'collection_cat' ) ) {
            slv_enqueue_style_if_exists( 'slv-collection', '44-collection.css' );
        }

        // 通用单页
        if ( is_singular() ) {
            slv_enqueue_style_if_exists( 'slv-single', '50-single.css' );
        }

        // 深色模式
        slv_enqueue_style_if_exists( 'slv-dark', '70-dark.css' );
    }
}
add_action( 'wp_enqueue_scripts', 'slv_enqueue_assets', 20 );

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
        'isReader' => function_exists( 'slv_reader_is_environment' ) ? slv_reader_is_environment() : false,
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

    $handle = ( function_exists( 'slv_reader_is_environment' ) && slv_reader_is_environment() ) ? 'slv-reader' : 'slv-header';
    wp_add_inline_script( $handle, 'window.SLV_CONFIG = ' . wp_json_encode( $config ) . ';', 'before' );
}
add_action( 'wp_enqueue_scripts', 'slv_enqueue_inline_config', 30 );