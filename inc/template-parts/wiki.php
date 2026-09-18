<?php
/**
 * SunLyvo Nexus — 百科模板片段
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'slv_tp_wiki_header' ) ) {
    function slv_tp_wiki_header( int $post_id ): void {
        slv_render_wiki_definition( $post_id );
        slv_render_wiki_aliases( $post_id );
    }
}