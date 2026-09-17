<?php
/**
 * SunLyvo Nexus — BLUF 结构检测
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 检测内容的 BLUF 结构。
 *
 * @since 1.0.0
 * @param int $post_id 文章 ID。
 * @return array
 */
function slv_geo_check_bluf( int $post_id ): array {
    $content = (string) get_post_field( 'post_content', $post_id );
    $result  = [
        'has_bluf' => false,
        'score'    => 0,
        'issues'   => [],
    ];

    $pattern = '/<h2[^>]*>(.*?)<\/h2>\s*<p>(.*?)<\/p>/is';
    if ( ! preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER ) ) {
        return $result;
    }

    $total_h2 = count( $matches );
    $bluf_h2  = 0;

    foreach ( $matches as $match ) {
        $heading = wp_strip_all_tags( $match[1] );
        $first   = wp_strip_all_tags( $match[2] );
        $length  = mb_strlen( $first );

        if ( $length >= 30 && $length <= 80 ) {
            $bluf_h2++;
        } else {
            $result['issues'][] = sprintf(
                'H2「%s」首段长度 %d 字，建议 40-60 字',
                $heading, $length
            );
        }
    }

    $result['score']    = $total_h2 > 0 ? (int) ( $bluf_h2 / $total_h2 * 100 ) : 0;
    $result['has_bluf'] = $result['score'] >= 80;

    return $result;
}