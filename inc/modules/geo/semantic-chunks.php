<?php
/**
 * SunLyvo Nexus — 语义分块
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 将内容按 H2/H3 拆分为语义块。
 *
 * @since 1.0.0
 * @param string $content 内容 HTML。
 * @return array
 */
function slv_geo_split_content( string $content ): array {
    if ( '' === $content ) {
        return [];
    }
    $chunks  = [];
    $pattern = '/<h([23])[^>]*>(.*?)<\/h[23]>(.*?)(?=<h[23]|$)/is';
    if ( ! preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER ) ) {
        return [];
    }
    foreach ( $matches as $match ) {
        $heading = trim( wp_strip_all_tags( $match[2] ) );
        $text    = trim( wp_strip_all_tags( $match[3] ) );
        if ( '' === $heading || '' === $text ) {
            continue;
        }
        $chunks[] = [
            'level'   => (int) $match[1],
            'heading' => $heading,
            'content' => $text,
        ];
    }
    return $chunks;
}

/**
 * 构建语义分块 ItemList Schema。
 *
 * @since 1.0.0
 * @return array
 */
function slv_geo_build_semantic_itemlist(): array {
    if ( ! is_singular() ) {
        return [];
    }
    $content = get_post_field( 'post_content', get_the_ID() );
    $chunks  = slv_geo_split_content( (string) $content );
    if ( empty( $chunks ) ) {
        return [];
    }
    $items = [];
    foreach ( $chunks as $i => $c ) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'item'     => [
                '@type'    => 'CreativeWork',
                'name'     => $c['heading'],
                'abstract' => mb_substr( $c['content'], 0, 200 ),
            ],
        ];
    }
    return [
        [
            '@type'           => 'ItemList',
            'name'            => get_the_title() . ' — 内容分块',
            'itemListElement' => $items,
        ],
    ];
}