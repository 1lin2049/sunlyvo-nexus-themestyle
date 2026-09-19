<?php
/**
 * SunLyvo Nexus — 博客模板片段
 *
 * @package SunLyvo_Nexus
 * @since 1.0.1
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'slv_tp_post_footer' ) ) {
    function slv_tp_post_footer( int $post_id = 0 ): void {
        $post_id = slv_tp_resolve_post_id( $post_id );
        if ( ! $post_id ) return;
        slv_render_author_box( $post_id );
        slv_render_related_posts( $post_id, 'post', 3 );
    }
}