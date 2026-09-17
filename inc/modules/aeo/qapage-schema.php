<?php
/**
 * SunLyvo Nexus — QAPage Schema
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 为 FAQ CPT 构建 QAPage Schema。
 *
 * @since 1.0.0
 * @param int $post_id 文章 ID。
 * @return array|null
 */
function slv_aeo_build_qapage_schema( int $post_id ): ?array {
    if ( 'faq' !== get_post_type( $post_id ) ) {
        return null;
    }

    $content = (string) get_post_field( 'post_content', $post_id );
    $author  = get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $post_id ) );

    return [
        '@type' => 'QAPage',
        '@id'   => get_permalink( $post_id ) . '#qapage',
        'mainEntity' => [
            '@type'       => 'Question',
            'name'        => get_the_title( $post_id ),
            'text'        => wp_strip_all_tags( $content ),
            'answerCount' => 1,
            'acceptedAnswer' => [
                '@type'       => 'Answer',
                'text'        => wp_strip_all_tags( $content ),
                'dateCreated' => get_the_date( 'c', $post_id ),
                'author'      => [
                    '@type' => 'Person',
                    'name'  => $author,
                ],
            ],
        ],
    ];
}