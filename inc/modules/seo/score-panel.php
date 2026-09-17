<?php
/**
 * SunLyvo Nexus — SEO 评分
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 计算 SEO 得分（0-100）。
 *
 * @since 1.0.0
 * @param int $post_id 文章 ID。
 * @return array
 */
function slv_seo_calculate_score( int $post_id ): array {
    $score  = 0;
    $checks = [];

    $post    = get_post( $post_id );
    $meta    = slv_seo_get_meta( $post_id );
    $content = (string) $post->post_content;

    // 标题长度（20）
    $len = mb_strlen( $post->post_title );
    if ( $len >= 20 && $len <= 60 ) {
        $score += 20;
        $checks['title_length'] = 'pass';
    } else {
        $checks['title_length'] = 'fail';
    }

    // Meta 描述（20）
    $desc = $meta ? mb_strlen( (string) $meta->meta_description ) : 0;
    if ( $desc >= 120 && $desc <= 160 ) {
        $score += 20;
        $checks['meta_description'] = 'pass';
    } else {
        $checks['meta_description'] = 'fail';
    }

    // 内容长度（20）
    $word_count = str_word_count( wp_strip_all_tags( $content ) );
    if ( $word_count >= 800 ) {
        $score += 20;
        $checks['content_length'] = 'pass';
    } elseif ( $word_count >= 300 ) {
        $score += 10;
        $checks['content_length'] = 'warn';
    } else {
        $checks['content_length'] = 'fail';
    }

    // 特色图（10）
    $checks['featured_image'] = has_post_thumbnail( $post_id ) ? 'pass' : 'fail';
    if ( 'pass' === $checks['featured_image'] ) {
        $score += 10;
    }

    // 内链（10）
    $internal = substr_count( $content, home_url() );
    $checks['internal_links'] = $internal >= 3 ? 'pass' : 'fail';
    if ( 'pass' === $checks['internal_links'] ) {
        $score += 10;
    }

    // 标题层级（10）
    $checks['heading_structure'] = preg_match( '/<h[2-3][^>]*>/', $content ) ? 'pass' : 'fail';
    if ( 'pass' === $checks['heading_structure'] ) {
        $score += 10;
    }

    // URL（10）
    $slug = $post->post_name;
    $checks['url_structure'] = ( ! preg_match( '/[0-9]{4,}/', $slug ) && strlen( $slug ) <= 60 ) ? 'pass' : 'fail';
    if ( 'pass' === $checks['url_structure'] ) {
        $score += 10;
    }

    return [
        'score'  => $score,
        'checks' => $checks,
        'level'  => $score >= 80 ? 'good' : ( $score >= 60 ? 'ok' : 'bad' ),
    ];
}