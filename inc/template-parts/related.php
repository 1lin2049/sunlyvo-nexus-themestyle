<?php
/**
 * SunLyvo Nexus — 相关阅读模板片段
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'slv_tp_related' ) ) {
    function slv_tp_related( int $post_id, string $post_type = 'post', int $limit = 3 ): void {
        slv_render_related_posts( $post_id, $post_type, $limit );
    }
}