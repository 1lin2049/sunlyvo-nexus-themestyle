<?php
/**
 * SunLyvo Nexus — GEO 增强 @graph
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 构建 GEO 增强的 @graph 节点。
 *
 * @since 1.0.0
 * @return array
 */
function slv_geo_build_enhanced_graph(): array {
    if ( ! is_singular() ) {
        return [];
    }

    $nodes = [];

    // 语义分块 ItemList
    $itemlist = slv_geo_build_semantic_itemlist();
    if ( ! empty( $itemlist ) ) {
        $nodes = array_merge( $nodes, $itemlist );
    }

    // Speakable
    $nodes[] = [
        '@type'      => 'WebPage',
        '@id'        => get_permalink() . '#webpage',
        'url'        => get_permalink(),
        'speakable'  => [
            '@type'       => 'SpeakableSpecification',
            'cssSelector' => [ '.slv-bluf-summary', '.slv-faq-answer', 'h1' ],
        ],
    ];

    return $nodes;
}