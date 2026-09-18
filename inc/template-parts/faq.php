<?php
/**
 * SunLyvo Nexus — FAQ 模板片段
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'slv_tp_faq_body' ) ) {
    function slv_tp_faq_body( int $post_id ): void {
        slv_render_faq_accordion( $post_id );
    }
}