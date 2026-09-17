<?php
/**
 * SunLyvo Nexus — SEO Meta 输出
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 获取 SEO Meta 数据。
 *
 * @since 1.0.0
 * @param int $post_id 文章 ID。
 * @return object|null
 */
function slv_seo_get_meta( int $post_id ): ?object {
    global $wpdb;
    $row = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}slv_seo_meta WHERE post_id = %d",
        $post_id
    ) );
    return $row ?: null;
}

/**
 * 生成页面标题。
 *
 * @since 1.0.0
 * @return string
 */
function slv_seo_generate_title(): string {
    if ( is_singular() ) {
        return get_the_title() . ' - ' . get_bloginfo( 'name' );
    }
    if ( is_archive() ) {
        return wp_strip_all_tags( get_the_archive_title() ) . ' - ' . get_bloginfo( 'name' );
    }
    if ( is_search() ) {
        return sprintf( __( '搜索：%s', 'sunlyvo-nexus' ), get_search_query() ) . ' - ' . get_bloginfo( 'name' );
    }
    if ( is_404() ) {
        return __( '页面未找到', 'sunlyvo-nexus' ) . ' - ' . get_bloginfo( 'name' );
    }
    return get_bloginfo( 'name' ) . ' - ' . get_bloginfo( 'description' );
}

/**
 * 生成 Meta 描述。
 *
 * @since 1.0.0
 * @return string
 */
function slv_seo_generate_description(): string {
    if ( is_singular() ) {
        $excerpt = get_the_excerpt();
        if ( $excerpt ) {
            return mb_substr( wp_strip_all_tags( $excerpt ), 0, 160 );
        }
        return mb_substr( wp_strip_all_tags( get_the_content() ), 0, 160 );
    }
    if ( is_archive() ) {
        return mb_substr( wp_strip_all_tags( get_the_archive_description() ), 0, 160 );
    }
    return get_bloginfo( 'description' );
}

/**
 * 生成 canonical URL。
 *
 * @since 1.0.0
 * @return string
 */
function slv_seo_generate_canonical(): string {
    if ( is_singular() ) {
        return get_permalink();
    }
    if ( is_front_page() ) {
        return home_url( '/' );
    }
    if ( is_archive() ) {
        return get_pagenum_link();
    }
    return home_url( add_query_arg( [], $GLOBALS['wp']->request ) );
}

/**
 * 输出 Meta 标签到 wp_head。
 *
 * @since 1.0.0
 */
function slv_seo_output_meta(): void {
    $post_id = is_singular() ? get_the_ID() : 0;
    $meta    = $post_id ? slv_seo_get_meta( $post_id ) : null;

    // title
    $title = ( $meta && $meta->meta_title ) ? $meta->meta_title : slv_seo_generate_title();
    echo '<title>' . esc_html( $title ) . '</title>' . "\n";

    // description
    $description = ( $meta && $meta->meta_description ) ? $meta->meta_description : slv_seo_generate_description();
    if ( $description ) {
        echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
    }

    // keywords
    if ( $meta && $meta->meta_keywords ) {
        echo '<meta name="keywords" content="' . esc_attr( $meta->meta_keywords ) . '" />' . "\n";
    }

    // robots
    $robots = ( $meta && $meta->robots ) ? $meta->robots : 'index,follow';
    echo '<meta name="robots" content="' . esc_attr( $robots ) . '" />' . "\n";

    // canonical
    $canonical = ( $meta && $meta->canonical_url ) ? $meta->canonical_url : slv_seo_generate_canonical();
    echo '<link rel="canonical" href="' . esc_url( $canonical ) . '" />' . "\n";

    // ICP（国内）
    if ( 'cn' === slv_detect_user_region() ) {
        $icp = slv_get_config( 'icp_number' );
        if ( $icp ) {
            echo '<meta name="icp" content="' . esc_attr( $icp ) . '" />' . "\n";
        }
    }
}
add_action( 'wp_head', 'slv_seo_output_meta', 1 );

/**
 * 检测用户地区（供 SEO 模块共用）。
 *
 * @since 1.0.0
 * @return string 'cn' 或 'international'
 */
function slv_detect_user_region(): string {
    $user_id = get_current_user_id();
    if ( $user_id ) {
        $region = get_user_meta( $user_id, '_slv_region', true );
        if ( $region ) {
            return $region;
        }
    }
    if ( isset( $_COOKIE['slv_region'] ) ) {
        $region = sanitize_key( $_COOKIE['slv_region'] );
        if ( in_array( $region, [ 'cn', 'international' ], true ) ) {
            return $region;
        }
    }
    $locale = get_locale();
    return ( 'zh_CN' === $locale ) ? 'cn' : 'international';
}