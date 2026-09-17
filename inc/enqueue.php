<?php
/**
 * SunLyvo Nexus — 资源条件加载
 *
 * reader 环境禁止加载 main.css / motion.css / mobile.css。
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
 */
function slv_enqueue_assets(): void {
    $css = SLV_ASSETS_URL . '/css';
    $js  = SLV_ASSETS_URL . '/js';

    // ── 基础链（reader 与主站共用）
    wp_enqueue_style( 'slv-reset',      "{$css}/reset.css",      [], slv_asset_version( 'css/reset.css' ) );
    wp_enqueue_style( 'slv-tokens',     "{$css}/tokens.css",     [ 'slv-reset' ], slv_asset_version( 'css/tokens.css' ) );
    wp_enqueue_style( 'slv-icons',      "{$css}/icons.css",      [ 'slv-tokens' ], slv_asset_version( 'css/icons.css' ) );
    wp_enqueue_style( 'slv-components', "{$css}/components.css", [ 'slv-icons' ], slv_asset_version( 'css/components.css' ) );
    wp_enqueue_style( 'slv-a11y',       "{$css}/a11y.css",       [ 'slv-components' ], slv_asset_version( 'css/a11y.css' ) );

    if ( slv_reader_is_environment() ) {
        // ── reader 环境：隔离加载
        wp_enqueue_style( 'slv-reader',           "{$css}/reader.css",           [ 'slv-a11y' ], slv_asset_version( 'css/reader.css' ) );
        wp_enqueue_style( 'slv-reader-extras',    "{$css}/reader-extras.css",    [ 'slv-reader' ], slv_asset_version( 'css/reader-extras.css' ) );
        wp_enqueue_style( 'slv-chapter-comments', "{$css}/chapter-comments.css", [ 'slv-reader' ], slv_asset_version( 'css/chapter-comments.css' ) );

        wp_enqueue_script( 'slv-reader',           "{$js}/reader.js",           [], slv_asset_version( 'js/reader.js' ), true );
        wp_enqueue_script( 'slv-toc',              "{$js}/toc.js",              [ 'slv-reader' ], slv_asset_version( 'js/toc.js' ), true );
        wp_enqueue_script( 'slv-selection-menu',   "{$js}/selection-menu.js",   [ 'slv-reader' ], slv_asset_version( 'js/selection-menu.js' ), true );
        wp_enqueue_script( 'slv-reading-position', "{$js}/reading-position.js", [ 'slv-reader' ], slv_asset_version( 'js/reading-position.js' ), true );
        wp_enqueue_script( 'slv-share',            "{$js}/share.js",            [ 'slv-reader' ], slv_asset_version( 'js/share.js' ), true );
    } else {
        // ── 主站环境
        wp_enqueue_style( 'slv-main',           "{$css}/main.css",           [ 'slv-a11y' ], slv_asset_version( 'css/main.css' ) );
        wp_enqueue_style( 'slv-header-footer',  "{$css}/header-footer.css",  [ 'slv-main' ], slv_asset_version( 'css/header-footer.css' ) );
        wp_enqueue_style( 'slv-motion',         "{$css}/motion.css",         [ 'slv-main' ], slv_asset_version( 'css/motion.css' ) );
        wp_enqueue_style( 'slv-mobile',         "{$css}/mobile.css",         [ 'slv-main' ], slv_asset_version( 'css/mobile.css' ) );

        wp_enqueue_script( 'slv-header', "{$js}/header.js", [], slv_asset_version( 'js/header.js' ), true );
        wp_enqueue_script( 'slv-main',   "{$js}/main.js",   [ 'slv-header' ], slv_asset_version( 'js/main.js' ), true );

        // Sticky Header 修复
        $sticky_css = SLV_THEME_DIR . '/assets/css/sticky-fix.css';
        if ( file_exists( $sticky_css ) ) {
            wp_enqueue_style( 'slv-sticky-fix', "{$css}/sticky-fix.css", [ 'slv-main' ], (string) filemtime( $sticky_css ) );
        }

        // 首页专属资源
        if ( is_front_page() ) {
            $home_css = SLV_THEME_DIR . '/assets/css/home.css';
            if ( file_exists( $home_css ) ) {
                wp_enqueue_style( 'slv-home', "{$css}/home.css", [ 'slv-main' ], (string) filemtime( $home_css ) );
            }
            $home_js = SLV_THEME_DIR . '/assets/js/home.js';
            if ( file_exists( $home_js ) ) {
                wp_enqueue_script( 'slv-home', "{$js}/home.js", [ 'slv-main' ], (string) filemtime( $home_js ), true );
            }
        }

        // 商品列表页专属
        if ( is_post_type_archive( 'product' ) || is_tax( [ 'product_cat', 'product_tag' ] ) ) {
            $pl_css = SLV_THEME_DIR . '/assets/css/product-list.css';
            if ( file_exists( $pl_css ) ) {
                wp_enqueue_style( 'slv-product-list', "{$css}/product-list.css", [ 'slv-main' ], (string) filemtime( $pl_css ) );
            }
            $pl_js = SLV_THEME_DIR . '/assets/js/product-list.js';
            if ( file_exists( $pl_js ) ) {
                wp_enqueue_script( 'slv-product-list', "{$js}/product-list.js", [ 'slv-main' ], (string) filemtime( $pl_js ), true );
            }
        }

        // 商品详情页专属
        if ( is_singular( 'product' ) ) {
            $pd_css = SLV_THEME_DIR . '/assets/css/product-detail.css';
            if ( file_exists( $pd_css ) ) {
                wp_enqueue_style( 'slv-product-detail', "{$css}/product-detail.css", [ 'slv-main' ], (string) filemtime( $pd_css ) );
            }
            $pd_js = SLV_THEME_DIR . '/assets/js/product-detail.js';
            if ( file_exists( $pd_js ) ) {
                wp_enqueue_script( 'slv-product-detail', "{$js}/product-detail.js", [ 'slv-main' ], (string) filemtime( $pd_js ), true );
            }
        }

        // React 嵌页：购物车
        if ( is_page_template( 'page-templates/page-cart.php' ) ) {
            $react_dist = SLV_THEME_URL . '/assets/js/react/dist';
            wp_enqueue_script( 'slv-react-cart', "{$react_dist}/cart.js", [], SLV_VERSION, true );
            wp_enqueue_style( 'slv-react-cart-css', "{$react_dist}/cart.css", [], SLV_VERSION );
        }
        // React 嵌页：结账
        if ( is_page_template( 'page-templates/page-checkout.php' ) ) {
            $react_dist = SLV_THEME_URL . '/assets/js/react/dist';
            wp_enqueue_script( 'slv-react-checkout', "{$react_dist}/checkout.js", [], SLV_VERSION, true );
            wp_enqueue_style( 'slv-react-checkout-css', "{$react_dist}/checkout.css", [], SLV_VERSION );
        }
        // React 嵌页：用户中心
        if ( is_page_template( 'page-templates/page-account.php' ) ) {
            $react_dist = SLV_THEME_URL . '/assets/js/react/dist';
            wp_enqueue_script( 'slv-react-account', "{$react_dist}/account.js", [], SLV_VERSION, true );
            wp_enqueue_style( 'slv-react-account-css', "{$react_dist}/account.css", [], SLV_VERSION );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'slv_enqueue_assets' );

/**
 * 传递运行时参数给 JS。
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

    $config = (array) apply_filters( 'slv_frontend_config', $config );

    $handle = slv_reader_is_environment() ? 'slv-reader' : 'slv-header';
    wp_add_inline_script(
        $handle,
        'window.SLV_CONFIG = ' . wp_json_encode( $config ) . ';',
        'before'
    );
}
add_action( 'wp_enqueue_scripts', 'slv_enqueue_inline_config', 20 );