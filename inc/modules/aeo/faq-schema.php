<?php
/**
 * SunLyvo Nexus — FAQPage Schema
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 获取某文章的 FAQ 数据。
 *
 * @since 1.0.0
 * @param int $post_id 文章 ID。
 * @return array
 */
function slv_aeo_get_faqs( int $post_id ): array {
    global $wpdb;
    return $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}slv_aeo_faqs
         WHERE post_id = %d
         ORDER BY sort_order ASC",
        $post_id
    ), ARRAY_A ) ?: [];
}

/**
 * 构建 FAQPage Schema。
 *
 * @since 1.0.0
 * @param int $post_id 文章 ID。
 * @return array
 */
function slv_aeo_build_faqpage_schema( int $post_id ): array {
    $faqs = slv_aeo_get_faqs( $post_id );

    $schema = [
        '@type' => 'FAQPage',
        '@id'   => get_permalink( $post_id ) . '#faqpage',
    ];

    if ( empty( $faqs ) ) {
        $schema['mainEntity'] = [
            [
                '@type' => 'Question',
                'name'  => get_the_title( $post_id ),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => wp_strip_all_tags( get_the_content( null, false, $post_id ) ),
                ],
            ],
        ];
        return $schema;
    }

    $items = [];
    foreach ( $faqs as $faq ) {
        $items[] = [
            '@type' => 'Question',
            'name'  => $faq['question'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => $faq['answer_short'] ?: wp_strip_all_tags( $faq['answer'] ),
            ],
        ];
    }
    $schema['mainEntity'] = $items;

    return $schema;
}

/**
 * 汇总 AEO 额外 Schema 供 @graph 使用。
 *
 * @since 1.0.0
 * @return array
 */
function slv_aeo_build_additional_schemas(): array {
    if ( ! is_singular() ) {
        return [];
    }
    $post_id = (int) get_the_ID();
    $schemas = [];

    // HowTo
    $howto = slv_aeo_build_howto_schema( $post_id );
    if ( $howto ) {
        $schemas[] = $howto;
    }

    // QAPage
    $qapage = slv_aeo_build_qapage_schema( $post_id );
    if ( $qapage ) {
        $schemas[] = $qapage;
    }

    // Speakable
    $schemas[] = slv_aeo_build_speakable_schema( $post_id );

    return $schemas;
}