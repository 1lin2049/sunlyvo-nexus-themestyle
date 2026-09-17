<?php
/**
 * SunLyvo Nexus — Speakable Schema
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 构建 Speakable Schema（语音搜索）。
 *
 * @since 1.0.0
 * @param int $post_id 文章 ID。
 * @return array
 */
function slv_aeo_build_speakable_schema( int $post_id ): array {
    return [
        '@type'     => 'WebPage',
        '@id'       => get_permalink( $post_id ) . '#speakable',
        'url'       => get_permalink( $post_id ),
        'speakable' => [
            '@type'       => 'SpeakableSpecification',
            'cssSelector' => [
                '.slv-bluf-summary',
                '.slv-faq-answer',
                '.slv-article-summary',
                'h1',
            ],
        ],
    ];
}