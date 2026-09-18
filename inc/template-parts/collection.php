<?php
/**
 * SunLyvo Nexus — 合集模板片段
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'slv_tp_collection_body' ) ) {
    function slv_tp_collection_body( int $post_id ): void {
        slv_render_collection_cta( $post_id );
        slv_render_collection_chapters( $post_id );
    }
}