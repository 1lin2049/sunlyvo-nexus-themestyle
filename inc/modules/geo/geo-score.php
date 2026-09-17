<?php
/**
 * SunLyvo Nexus — GEO 评分
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 计算 GEO 五维评分。
 *
 * @since 1.0.0
 * @param int $post_id 文章 ID。
 * @return array
 */
function slv_geo_calculate_score( int $post_id ): array {
    $content = (string) get_post_field( 'post_content', $post_id );

    $scores = [
        'structure' => slv_geo_score_structure( $post_id, $content ),
        'entity'    => slv_geo_score_entity( $content ),
        'fact'      => slv_geo_score_fact( $content ),
        'schema'    => slv_geo_score_schema( $post_id ),
        'freshness' => slv_geo_score_freshness( $post_id ),
    ];

    $overall = (int) round( array_sum( $scores ) / count( $scores ) );

    global $wpdb;
    $wpdb->replace( "{$wpdb->prefix}slv_geo_scores", [
        'post_id'         => $post_id,
        'structure_score' => $scores['structure'],
        'entity_score'    => $scores['entity'],
        'fact_score'      => $scores['fact'],
        'schema_score'    => $scores['schema'],
        'freshness_score' => $scores['freshness'],
        'overall_score'   => $overall,
    ] );

    return [ 'scores' => $scores, 'overall' => $overall ];
}

function slv_geo_score_structure( int $post_id, string $content ): int {
    $score = 0;
    $bluf  = slv_geo_check_bluf( $post_id );
    $score += (int) ( $bluf['score'] * 0.4 );

    $h2_count = substr_count( $content, '<h2' );
    if ( $h2_count >= 3 ) {
        $score += 30;
    } elseif ( $h2_count >= 1 ) {
        $score += 15;
    }

    if ( preg_match( '/<(ul|ol)/', $content ) ) {
        $score += 15;
    }
    if ( preg_match( '/<table/', $content ) ) {
        $score += 15;
    }

    return min( 100, $score );
}

function slv_geo_score_entity( string $content ): int {
    $score = 0;
    $entities = preg_match_all( '/\b[A-Z][a-z]+(?:\s+[A-Z][a-z]+)+\b/', $content );
    if ( $entities >= 10 ) {
        $score += 50;
    } elseif ( $entities >= 5 ) {
        $score += 30;
    } elseif ( $entities >= 2 ) {
        $score += 15;
    }

    $links = substr_count( $content, '<a ' );
    if ( $links >= 5 ) {
        $score += 30;
    } elseif ( $links >= 2 ) {
        $score += 15;
    }

    if ( preg_match( '/<blockquote|<cite/', $content ) ) {
        $score += 20;
    }

    return min( 100, $score );
}

function slv_geo_score_fact( string $content ): int {
    $score = 0;
    $facts = preg_match_all( '/\d+(?:\.\d+)?\s*(?:%|元|美元|USD|CNY|年|月|日|个|件|次|GB|MB|KB|px|秒|分钟|小时)/', $content );
    if ( $facts >= 10 ) {
        $score += 50;
    } elseif ( $facts >= 5 ) {
        $score += 30;
    } elseif ( $facts >= 2 ) {
        $score += 15;
    }

    if ( preg_match( '/\d{4}年\d{1,2}月/', $content ) ) {
        $score += 25;
    }
    if ( preg_match( '/来源[:：]|参考[:：]|根据/', $content ) ) {
        $score += 25;
    }

    return min( 100, $score );
}

function slv_geo_score_schema( int $post_id ): int {
    $score = 0;
    $type  = (string) get_post_meta( $post_id, '_slv_schema_type', true );
    if ( $type ) {
        $score += 50;
    }
    $main = slv_seo_build_main_schema();
    if ( $main && ! empty( $main['@type'] ) ) {
        $score += 50;
    }
    return min( 100, $score );
}

function slv_geo_score_freshness( int $post_id ): int {
    $modified   = (int) get_post_modified_time( 'U', false, $post_id );
    $days_since = ( time() - $modified ) / DAY_IN_SECONDS;
    if ( $days_since <= 30 ) {
        return 100;
    }
    if ( $days_since <= 90 ) {
        return 80;
    }
    if ( $days_since <= 180 ) {
        return 60;
    }
    if ( $days_since <= 365 ) {
        return 40;
    }
    return 20;
}