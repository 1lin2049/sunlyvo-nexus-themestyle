<?php
/**
 * SunLyvo Nexus — Open Graph + Twitter Card
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 输出 OG + Twitter 标签。
 *
 * @since 1.0.0
 */
function slv_seo_output_og_twitter(): void {
    $post_id = is_singular() ? get_the_ID() : 0;
    $meta    = $post_id ? slv_seo_get_meta( $post_id ) : null;

    $title       = ( $meta && $meta->og_title ) ? $meta->og_title : slv_seo_generate_title();
    $description = ( $meta && $meta->og_description ) ? $meta->og_description : slv_seo_generate_description();
    $url         = slv_seo_generate_canonical();
    $image       = ( $meta && $meta->og_image ) ? $meta->og_image : ( $post_id ? get_the_post_thumbnail_url( $post_id, 'large' ) : '' );

    // Open Graph
    $og = [
        'og:type'        => is_singular( 'post' ) ? 'article' : 'website',
        'og:title'       => $title,
        'og:description' => $description,
        'og:url'         => $url,
        'og:site_name'   => get_bloginfo( 'name' ),
        'og:locale'      => str_replace( '-', '_', get_locale() ),
    ];
    if ( $image ) {
        $og['og:image']        = $image;
        $og['og:image:width']  = '1200';
        $og['og:image:height'] = '630';
    }
    if ( is_singular( 'post' ) ) {
        $og['article:published_time'] = get_the_date( 'c', $post_id );
        $og['article:modified_time']  = get_the_modified_date( 'c', $post_id );
    }
    foreach ( $og as $prop => $content ) {
        echo '<meta property="' . esc_attr( $prop ) . '" content="' . esc_attr( $content ) . '" />' . "\n";
    }

    // Twitter Card
    $twitter = [
        'twitter:card'        => ( $meta && $meta->twitter_card ) ? $meta->twitter_card : 'summary_large_image',
        'twitter:title'       => ( $meta && $meta->twitter_title ) ? $meta->twitter_title : $title,
        'twitter:description' => ( $meta && $meta->twitter_description ) ? $meta->twitter_description : $description,
    ];
    if ( $image ) {
        $twitter['twitter:image'] = $image;
    }
    foreach ( $twitter as $name => $content ) {
        echo '<meta name="' . esc_attr( $name ) . '" content="' . esc_attr( $content ) . '" />' . "\n";
    }
}
add_action( 'wp_head', 'slv_seo_output_og_twitter', 2 );