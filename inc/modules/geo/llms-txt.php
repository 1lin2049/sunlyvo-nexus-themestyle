<?php
/**
 * SunLyvo Nexus — llms.txt
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 注册 llms.txt 重写规则。
 *
 * @since 1.0.0
 */
function slv_geo_register_llms_rewrite(): void {
    add_rewrite_rule( '^llms\.txt$', 'index.php?slv_llms=1', 'top' );
    add_rewrite_rule( '^llms-full\.txt$', 'index.php?slv_llms_full=1', 'top' );
}
add_action( 'init', 'slv_geo_register_llms_rewrite' );

/**
 * 注册 query var。
 *
 * @since 1.0.0
 */
function slv_geo_register_llms_query_vars( array $vars ): array {
    $vars[] = 'slv_llms';
    $vars[] = 'slv_llms_full';
    return $vars;
}
add_filter( 'query_vars', 'slv_geo_register_llms_query_vars' );

/**
 * 输出 llms.txt。
 *
 * @since 1.0.0
 */
function slv_geo_render_llms(): void {
    $is_full = (bool) get_query_var( 'slv_llms_full' );
    if ( ! get_query_var( 'slv_llms' ) && ! $is_full ) {
        return;
    }

    header( 'Content-Type: text/plain; charset=utf-8' );
    echo $is_full ? slv_geo_generate_llms_full() : slv_geo_generate_llms();
    exit;
}
add_action( 'template_redirect', 'slv_geo_render_llms' );

/**
 * 生成 llms.txt。
 *
 * @since 1.0.0
 * @return string
 */
function slv_geo_generate_llms(): string {
    $region = slv_detect_user_region();
    $out    = "# " . get_bloginfo( 'name' ) . "\n\n";
    $out   .= "> " . get_bloginfo( 'description' ) . "\n\n";

    $out .= ( 'cn' === $region ? "## 核心页面\n\n" : "## Core Pages\n\n" );
    $out .= "- [" . ( 'cn' === $region ? '首页' : 'Home' ) . "](" . home_url() . ")\n";
    $out .= "- [" . ( 'cn' === $region ? '关于我们' : 'About' ) . "](" . home_url( '/about/' ) . ")\n";
    $out .= "- [" . ( 'cn' === $region ? '联系我们' : 'Contact' ) . "](" . home_url( '/contact/' ) . ")\n\n";

    $types = [
        'post'       => 'cn' === $region ? '博客' : 'Blog',
        'wiki'       => 'cn' === $region ? '百科' : 'Wiki',
        'faq'        => 'FAQ',
        'product'    => 'cn' === $region ? '商品' : 'Products',
        'collection' => 'cn' === $region ? '合集' : 'Collections',
    ];

    foreach ( $types as $pt => $label ) {
        $posts = get_posts( [
            'post_type'      => $pt,
            'posts_per_page' => 20,
            'orderby'        => 'modified',
            'order'          => 'DESC',
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ] );
        if ( empty( $posts ) ) {
            continue;
        }

        $out .= "## {$label}\n\n";
        foreach ( $posts as $id ) {
            $excerpt = wp_strip_all_tags( get_the_excerpt( $id ) );
            $excerpt = mb_substr( $excerpt, 0, 100 );
            $out .= "- [" . get_the_title( $id ) . "](" . get_permalink( $id ) . "): {$excerpt}\n";
        }
        $out .= "\n";
    }

    $out .= "## " . ( 'cn' === $region ? '结构化数据' : 'Structured Data' ) . "\n\n";
    $out .= "- Schema: Organization, WebSite, Article, Product, FAQPage, DefinedTerm, BreadcrumbList, HowTo, QAPage\n";
    $out .= "- Sitemap: " . home_url( '/slv-sitemap.xml' ) . "\n";
    $out .= "- robots.txt: " . home_url( '/robots.txt' ) . "\n";

    return $out;
}

/**
 * 生成 llms-full.txt（包含完整正文）。
 *
 * @since 1.0.0
 * @return string
 */
function slv_geo_generate_llms_full(): string {
    $out = slv_geo_generate_llms();
    $out .= "\n\n# Full Content\n\n";

    $posts = get_posts( [
        'post_type'      => [ 'post', 'wiki', 'faq' ],
        'posts_per_page' => 50,
        'orderby'        => 'modified',
        'order'          => 'DESC',
    ] );

    foreach ( $posts as $p ) {
        $out .= "## " . $p->post_title . "\n\n";
        $out .= "URL: " . get_permalink( $p->ID ) . "\n";
        $out .= "Modified: " . get_the_modified_date( 'c', $p->ID ) . "\n\n";
        $out .= wp_strip_all_tags( $p->post_content ) . "\n\n---\n\n";
    }

    return $out;
}