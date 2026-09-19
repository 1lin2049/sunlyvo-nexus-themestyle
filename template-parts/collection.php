<?php
/**
 * SunLyvo Nexus — 合集模板片段
 *
 * @package SunLyvo_Nexus
 * @since 1.0.1
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'slv_tp_collection_body' ) ) {
    function slv_tp_collection_body( int $post_id = 0 ): void {
        $post_id = slv_tp_resolve_post_id( $post_id );
        if ( ! $post_id ) return;
        slv_render_collection_chapters( $post_id );
    }
}

if ( ! function_exists( 'slv_tp_collection_cta' ) ) {
    function slv_tp_collection_cta( int $post_id = 0 ): void {
        $post_id = slv_tp_resolve_post_id( $post_id );
        if ( ! $post_id ) return;
        slv_render_collection_cta( $post_id );
    }
}