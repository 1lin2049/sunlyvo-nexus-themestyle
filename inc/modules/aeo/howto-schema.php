<?php
/**
 * SunLyvo Nexus — HowTo Schema
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 从内容自动识别 HowTo 结构。
 *
 * @since 1.0.0
 * @param int $post_id 文章 ID。
 * @return array|null
 */
function slv_aeo_build_howto_schema( int $post_id ): ?array {
    $content = (string) get_post_field( 'post_content', $post_id );
    $pattern = '/<h[23][^>]*>(.*?)<\/h[23]>\s*<(ul|ol)[^>]*>(.*?)<\/\2>/is';

    if ( ! preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER ) ) {
        return null;
    }

    $steps = [];
    foreach ( $matches as $match ) {
        $heading = trim( wp_strip_all_tags( $match[1] ) );
        $list    = $match[3];
        if ( ! preg_match_all( '/<li[^>]*>(.*?)<\/li>/is', $list, $items ) ) {
            continue;
        }
        $list_items = array_map( 'wp_strip_all_tags', $items[1] );
        if ( count( $list_items ) < 3 ) {
            continue;
        }
        $steps[] = [
            '@type'    => 'HowToStep',
            'name'     => $heading,
            'text'     => implode( '；', $list_items ),
        ];
    }

    if ( count( $steps ) < 2 ) {
        return null;
    }

    return [
        '@type' => 'HowTo',
        '@id'   => get_permalink( $post_id ) . '#howto',
        'name'  => get_the_title( $post_id ),
        'step'  => $steps,
    ];
}