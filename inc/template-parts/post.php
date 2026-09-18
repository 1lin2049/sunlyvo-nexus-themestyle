<?php
/**
 * SunLyvo Nexus — 博客模板片段
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'slv_tp_post_footer' ) ) {
    function slv_tp_post_footer( int $post_id ): void {
        slv_render_author_box( $post_id );
        slv_render_related_posts( $post_id, 'post', 3 );
    }
}