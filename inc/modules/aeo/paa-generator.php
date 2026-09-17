<?php
/**
 * SunLyvo Nexus — PAA 问题生成
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 生成 People Also Ask 问题。
 *
 * @since 1.0.0
 * @param int $post_id 文章 ID。
 * @return array
 */
function slv_aeo_generate_paa_questions( int $post_id ): array {
    $post      = get_post( $post_id );
    if ( ! $post ) {
        return [];
    }
    $questions = [];
    $title     = $post->post_title;

    if ( preg_match( '/^什么是(.+)$/u', $title, $m ) ) {
        $questions[] = "{$m[1]}是什么？";
        $questions[] = "{$m[1]}有什么用？";
        $questions[] = "{$m[1]}怎么用？";
    }
    if ( preg_match( '/^如何(.+)$/u', $title, $m ) ) {
        $questions[] = "如何{$m[1]}？";
        $questions[] = "{$m[1]}的步骤是什么？";
        $questions[] = "{$m[1]}需要什么工具？";
    }

    foreach ( slv_aeo_get_faqs( $post_id ) as $faq ) {
        if ( ! empty( $faq['question'] ) ) {
            $questions[] = $faq['question'];
        }
    }

    return array_slice( array_values( array_unique( $questions ) ), 0, 10 );
}