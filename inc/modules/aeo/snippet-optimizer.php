<?php
/**
 * SunLyvo Nexus — Snippet 提取
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 提取段落型 Snippet。
 *
 * @since 1.0.0
 * @param string $content 内容。
 * @return array
 */
function slv_aeo_extract_paragraph_snippets( string $content ): array {
    $snippets = [];
    $pattern  = '/<h[23][^>]*>(.*?)<\/h[23]>\s*<p>(.*?)<\/p>/is';
    if ( ! preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER ) ) {
        return [];
    }
    foreach ( $matches as $match ) {
        $heading = wp_strip_all_tags( $match[1] );
        $para    = wp_strip_all_tags( $match[2] );
        $length  = mb_strlen( $para );
        if ( $length >= 30 && $length <= 80 ) {
            $snippets[] = [
                'question' => $heading,
                'answer'   => $para,
                'length'   => $length,
                'quality'  => ( $length >= 40 && $length <= 60 ) ? 'perfect' : 'good',
            ];
        }
    }
    return $snippets;
}

/**
 * 提取列表型 Snippet。
 *
 * @since 1.0.0
 * @param string $content 内容。
 * @return array
 */
function slv_aeo_extract_list_snippets( string $content ): array {
    $snippets = [];
    $pattern  = '/<h[23][^>]*>(.*?)<\/h[23]>\s*<(ul|ol)[^>]*>(.*?)<\/\2>/is';
    if ( ! preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER ) ) {
        return [];
    }
    foreach ( $matches as $match ) {
        $heading = wp_strip_all_tags( $match[1] );
        $list    = $match[3];
        if ( ! preg_match_all( '/<li[^>]*>(.*?)<\/li>/is', $list, $items ) ) {
            continue;
        }
        $list_items = array_map( 'wp_strip_all_tags', $items[1] );
        if ( count( $list_items ) >= 3 ) {
            $snippets[] = [
                'question' => $heading,
                'type'     => 'ol' === $match[2] ? 'ordered' : 'unordered',
                'items'    => $list_items,
                'count'    => count( $list_items ),
            ];
        }
    }
    return $snippets;
}

/**
 * 提取表格型 Snippet。
 *
 * @since 1.0.0
 * @param string $content 内容。
 * @return array
 */
function slv_aeo_extract_table_snippets( string $content ): array {
    $snippets = [];
    $pattern  = '/<h[23][^>]*>(.*?)<\/h[23]>\s*<table[^>]*>(.*?)<\/table>/is';
    if ( ! preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER ) ) {
        return [];
    }
    foreach ( $matches as $match ) {
        $snippets[] = [
            'question'  => wp_strip_all_tags( $match[1] ),
            'has_table' => true,
        ];
    }
    return $snippets;
}