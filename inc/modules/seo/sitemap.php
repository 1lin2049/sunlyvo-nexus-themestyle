<?php
/**
 * SunLyvo Nexus — Sitemap
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 注册 Sitemap 重写规则。
 *
 * @since 1.0.0
 */
function slv_seo_register_sitemap_rewrite(): void {
    add_rewrite_rule( '^slv-sitemap\.xml$', 'index.php?slv_sitemap=1', 'top' );
}
add_action( 'init', 'slv_seo_register_sitemap_rewrite' );

/**
 * 注册 query var。
 *
 * @since 1.0.0
 */
function slv_seo_register_sitemap_query_var( array $vars ): array {
    $vars[] = 'slv_sitemap';
    return $vars;
}
add_filter( 'query_vars', 'slv_seo_register_sitemap_query_var' );

/**
 * 输出 Sitemap。
 *
 * @since 1.0.0
 */
function slv_seo_render_sitemap(): void {
    if ( ! get_query_var( 'slv_sitemap' ) ) {
        return;
    }

    header( 'Content-Type: application/xml; charset=utf-8' );
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    $post_types = [ 'post', 'page', 'product', 'collection', 'wiki', 'faq', 'document', 'video', 'course' ];

    foreach ( $post_types as $pt ) {
        $posts = get_posts( [
            'post_type'      => $pt,
            'posts_per_page' => 500,
            'post_status'    => 'publish',
            'orderby'        => 'modified',
            'order'          => 'DESC',
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ] );

        foreach ( $posts as $post_id ) {
            echo "\t<url>\n";
            echo "\t\t<loc>" . esc_url( get_permalink( $post_id ) ) . "</loc>\n";
            echo "\t\t<lastmod>" . esc_html( get_the_modified_date( 'c', $post_id ) ) . "</lastmod>\n";
            echo "\t\t<changefreq>weekly</changefreq>\n";
            echo "\t\t<priority>0.6</priority>\n";
            echo "\t</url>\n";
        }
    }

    echo '</urlset>';
    exit;
}
add_action( 'template_redirect', 'slv_seo_render_sitemap' );