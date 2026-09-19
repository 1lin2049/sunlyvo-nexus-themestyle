<?php
/**
 * SunLyvo Nexus — 百科模板片段
 *
 * @package SunLyvo_Nexus
 * @since 1.0.1
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'slv_tp_resolve_post_id' ) ) {
    /**
     * 在区块模板中稳健地解析当前文章 ID。
     */
    function slv_tp_resolve_post_id( int $post_id = 0 ): int {
        if ( $post_id > 0 ) return $post_id;

        // 1. 查询对象
        $qid = (int) get_queried_object_id();
        if ( $qid > 0 ) return $qid;

        // 2. 全局 $post
        if ( isset( $GLOBALS['post'] ) && $GLOBALS['post'] instanceof WP_Post ) {
            return (int) $GLOBALS['post']->ID;
        }

        // 3. 循环内
        $tid = (int) get_the_ID();
        if ( $tid > 0 ) return $tid;

        return 0;
    }
}

if ( ! function_exists( 'slv_tp_wiki_header' ) ) {
    function slv_tp_wiki_header( int $post_id = 0 ): void {
        $post_id = slv_tp_resolve_post_id( $post_id );
        if ( ! $post_id ) return;
        slv_render_wiki_definition( $post_id );
        slv_render_wiki_aliases( $post_id );
    }
}