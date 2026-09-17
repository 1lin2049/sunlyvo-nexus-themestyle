<?php
/**
 * SunLyvo Nexus — AI 引用追踪
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 记录 AI 引擎引用事件。
 *
 * @since 1.0.0
 * @param int    $post_id 文章 ID。
 * @param string $engine  引擎名。
 * @param string $query   查询。
 */
function slv_geo_track_citation( int $post_id, string $engine, string $query = '' ): void {
    global $wpdb;
    $wpdb->insert( "{$wpdb->prefix}slv_geo_citations", [
        'post_id'     => $post_id,
        'engine'      => sanitize_key( $engine ),
        'query'       => sanitize_text_field( $query ),
        'detected_at' => current_time( 'mysql' ),
    ] );
}

/**
 * 获取某文章的引用统计。
 *
 * @since 1.0.0
 * @param int $post_id 文章 ID。
 * @return array
 */
function slv_geo_get_citation_stats( int $post_id ): array {
    global $wpdb;
    return $wpdb->get_results( $wpdb->prepare(
        "SELECT engine, COUNT(*) AS count
         FROM {$wpdb->prefix}slv_geo_citations
         WHERE post_id = %d
         GROUP BY engine
         ORDER BY count DESC",
        $post_id
    ), ARRAY_A ) ?: [];
}