<?php
/**
 * SunLyvo Nexus — BreadcrumbList Schema
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 构建 BreadcrumbList Schema。
 *
 * @since 1.0.0
 * @return array|null
 */
function slv_seo_build_breadcrumb_schema(): ?array {
    if ( is_front_page() ) {
        return null;
    }

    $items    = [];
    $position = 1;

    $items[] = [
        '@type'    => 'ListItem',
        'position' => $position++,
        'name'     => __( '首页', 'sunlyvo-nexus' ),
        'item'     => home_url( '/' ),
    ];

    if ( is_singular() ) {
        $post_type = get_post_type();
        if ( ! in_array( $post_type, [ 'post', 'page' ], true ) ) {
            $obj = get_post_type_object( $post_type );
            $archive = get_post_type_archive_link( $post_type );
            if ( $obj && $archive ) {
                $items[] = [
                    '@type'    => 'ListItem',
                    'position' => $position++,
                    'name'     => $obj->labels->name,
                    'item'     => $archive,
                ];
            }
        } elseif ( 'post' === $post_type ) {
            $cats = get_the_category();
            if ( ! empty( $cats ) ) {
                $items[] = [
                    '@type'    => 'ListItem',
                    'position' => $position++,
                    'name'     => $cats[0]->name,
                    'item'     => get_category_link( $cats[0]->term_id ),
                ];
            }
        }

        $items[] = [
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        ];
    } elseif ( is_archive() ) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => wp_strip_all_tags( get_the_archive_title() ),
            'item'     => get_pagenum_link(),
        ];
    } elseif ( is_search() ) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $position,
            'name'     => sprintf( __( '搜索：%s', 'sunlyvo-nexus' ), get_search_query() ),
            'item'     => get_search_link(),
        ];
    }

    if ( count( $items ) < 2 ) {
        return null;
    }

    return [
        '@type'           => 'BreadcrumbList',
        '@id'             => slv_seo_generate_canonical() . '#breadcrumb',
        'itemListElement' => $items,
    ];
}