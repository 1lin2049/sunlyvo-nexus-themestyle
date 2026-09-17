<?php
/**
 * SunLyvo Nexus — Core Web Vitals 优化
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 预连接关键域名。
 *
 * @since 1.0.0
 */
function slv_seo_preconnect(): void {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com" />' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />' . "\n";

    if ( is_singular() && has_post_thumbnail() ) {
        $image = get_the_post_thumbnail_url( null, 'large' );
        if ( $image ) {
            echo '<link rel="preload" as="image" href="' . esc_url( $image ) . '" fetchpriority="high" />' . "\n";
        }
    }
}
add_action( 'wp_head', 'slv_seo_preconnect', 1 );

/**
 * 移除无用 meta。
 *
 * @since 1.0.0
 */
function slv_seo_clean_head(): void {
    remove_action( 'wp_head', 'wp_generator' );
    remove_action( 'wp_head', 'rsd_link' );
    remove_action( 'wp_head', 'wlwmanifest_link' );
    remove_action( 'wp_head', 'wp_shortlink_wp_head' );
    remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
}
add_action( 'init', 'slv_seo_clean_head' );

/**
 * 首图不懒加载。
 *
 * @since 1.0.0
 */
add_filter( 'wp_get_attachment_image_attributes', function( array $attr, $attachment ): array {
    if ( ! isset( $attr['loading'] ) ) {
        $attr['loading'] = 'lazy';
    }
    if ( ! isset( $attr['decoding'] ) ) {
        $attr['decoding'] = 'async';
    }
    if ( did_action( 'loop_start' ) && ! did_action( 'loop_end' ) ) {
        $attr['loading']       = 'eager';
        $attr['fetchpriority'] = 'high';
    }
    return $attr;
}, 15, 2 );