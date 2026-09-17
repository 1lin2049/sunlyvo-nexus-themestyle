<?php
/**
 * SunLyvo Nexus — AEO 评分
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 计算 AEO 得分（0-100）。
 *
 * @since 1.0.0
 * @param int $post_id 文章 ID。
 * @return array
 */
function slv_aeo_calculate_score( int $post_id ): array {
    $score   = 0;
    $checks  = [];
    $content = (string) get_post_field( 'post_content', $post_id );

    // 段落 Snippet（30）
    $paras = slv_aeo_extract_paragraph_snippets( $content );
    $paras_n = count( $paras );
    if ( $paras_n >= 3 ) {
        $score += 30;
        $checks['paragraph_snippets'] = 'pass';
    } elseif ( $paras_n >= 1 ) {
        $score += 15;
        $checks['paragraph_snippets'] = 'warn';
    } else {
        $checks['paragraph_snippets'] = 'fail';
    }

    // 列表 Snippet（25）
    $lists_n = count( slv_aeo_extract_list_snippets( $content ) );
    if ( $lists_n >= 2 ) {
        $score += 25;
        $checks['list_snippets'] = 'pass';
    } elseif ( $lists_n >= 1 ) {
        $score += 12;
        $checks['list_snippets'] = 'warn';
    } else {
        $checks['list_snippets'] = 'fail';
    }

    // FAQ（25）
    $faqs_n = count( slv_aeo_get_faqs( $post_id ) );
    if ( $faqs_n >= 3 ) {
        $score += 25;
        $checks['faq_schema'] = 'pass';
    } elseif ( $faqs_n >= 1 ) {
        $score += 12;
        $checks['faq_schema'] = 'warn';
    } else {
        $checks['faq_schema'] = 'fail';
    }

    // Speakable（20）
    $checks['speakable'] = preg_match( '/slv-bluf-summary|slv-faq-answer/', $content ) ? 'pass' : 'fail';
    if ( 'pass' === $checks['speakable'] ) {
        $score += 20;
    }

    return [
        'score'  => $score,
        'checks' => $checks,
        'level'  => $score >= 80 ? 'good' : ( $score >= 60 ? 'ok' : 'bad' ),
    ];
}