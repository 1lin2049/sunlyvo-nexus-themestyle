<?php
/**
 * SunLyvo Nexus — 分类法注册
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 注册所有自定义分类法。
 *
 * @since 1.0.0
 */
function slv_register_taxonomies(): void {

    // ─── product_cat（商品分类，层级）──────────────────────
    register_taxonomy( 'product_cat', [ 'product' ], [
        'labels' => [
            'name'          => __( '商品分类', 'sunlyvo-nexus' ),
            'singular_name' => __( '商品分类', 'sunlyvo-nexus' ),
            'menu_name'     => __( '商品分类', 'sunlyvo-nexus' ),
        ],
        'public'            => true,
        'show_in_rest'      => true,
        'rest_base'         => 'product_categories',
        'hierarchical'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'product-category', 'with_front' => false ],
    ] );

    // ─── collection_cat（合集分类，层级）───────────────────
    register_taxonomy( 'collection_cat', [ 'collection' ], [
        'labels' => [
            'name'          => __( '合集分类', 'sunlyvo-nexus' ),
            'singular_name' => __( '合集分类', 'sunlyvo-nexus' ),
            'menu_name'     => __( '合集分类', 'sunlyvo-nexus' ),
        ],
        'public'            => true,
        'show_in_rest'      => true,
        'rest_base'         => 'collection_categories',
        'hierarchical'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'collection-category', 'with_front' => false ],
    ] );

    // ─── wiki_cat（百科分类，层级）─────────────────────────
    register_taxonomy( 'wiki_cat', [ 'wiki' ], [
        'labels' => [
            'name'          => __( '百科分类', 'sunlyvo-nexus' ),
            'singular_name' => __( '百科分类', 'sunlyvo-nexus' ),
            'menu_name'     => __( '百科分类', 'sunlyvo-nexus' ),
        ],
        'public'            => true,
        'show_in_rest'      => true,
        'rest_base'         => 'wiki_categories',
        'hierarchical'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'wiki-category', 'with_front' => false ],
    ] );

    // ─── faq_cat（FAQ 分类，层级）──────────────────────────
    register_taxonomy( 'faq_cat', [ 'faq' ], [
        'labels' => [
            'name'          => __( 'FAQ 分类', 'sunlyvo-nexus' ),
            'singular_name' => __( 'FAQ 分类', 'sunlyvo-nexus' ),
            'menu_name'     => __( 'FAQ 分类', 'sunlyvo-nexus' ),
        ],
        'public'            => true,
        'show_in_rest'      => true,
        'rest_base'         => 'faq_categories',
        'hierarchical'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'faq-category', 'with_front' => false ],
    ] );

    // ─── doc_category（文库分类，层级）─────────────────────
    register_taxonomy( 'doc_category', [ 'document' ], [
        'labels' => [
            'name'          => __( '文库分类', 'sunlyvo-nexus' ),
            'singular_name' => __( '文库分类', 'sunlyvo-nexus' ),
            'menu_name'     => __( '文库分类', 'sunlyvo-nexus' ),
        ],
        'public'            => true,
        'show_in_rest'      => true,
        'rest_base'         => 'doc_categories',
        'hierarchical'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'doc-category', 'with_front' => false ],
    ] );

    // ─── forum_board（论坛板块，层级）──────────────────────
    register_taxonomy( 'forum_board', [ 'topic' ], [
        'labels' => [
            'name'          => __( '论坛板块', 'sunlyvo-nexus' ),
            'singular_name' => __( '论坛板块', 'sunlyvo-nexus' ),
            'menu_name'     => __( '论坛板块', 'sunlyvo-nexus' ),
        ],
        'public'            => true,
        'show_in_rest'      => true,
        'rest_base'         => 'forum_boards',
        'hierarchical'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'forum', 'with_front' => false ],
    ] );

    // ─── group_cat（圈子分类，层级）────────────────────────
    register_taxonomy( 'group_cat', [ 'group' ], [
        'labels' => [
            'name'          => __( '圈子分类', 'sunlyvo-nexus' ),
            'singular_name' => __( '圈子分类', 'sunlyvo-nexus' ),
            'menu_name'     => __( '圈子分类', 'sunlyvo-nexus' ),
        ],
        'public'            => true,
        'show_in_rest'      => true,
        'rest_base'         => 'group_categories',
        'hierarchical'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'group-category', 'with_front' => false ],
    ] );

    // ─── slv_language（语言分类法，多语言）─────────────────
    register_taxonomy( 'slv_language', [ 'post', 'page', 'product', 'collection', 'wiki', 'faq', 'document', 'video' ], [
        'labels' => [
            'name'          => __( '语言', 'sunlyvo-nexus' ),
            'singular_name' => __( '语言', 'sunlyvo-nexus' ),
            'menu_name'     => __( '语言', 'sunlyvo-nexus' ),
        ],
        'public'            => true,
        'show_in_rest'      => true,
        'rest_base'         => 'languages',
        'hierarchical'      => false,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'language', 'with_front' => false ],
    ] );
}
add_action( 'init', 'slv_register_taxonomies', 4 );

/**
 * 为产品/合集等内容类型启用默认文章分类支持。
 *
 * @since 1.0.0
 */
function slv_register_default_taxonomies_for_cpt(): void {
    register_taxonomy_for_object_type( 'category', 'post' );
    register_taxonomy_for_object_type( 'post_tag', 'post' );
    register_taxonomy_for_object_type( 'post_tag', 'product' );
    register_taxonomy_for_object_type( 'post_tag', 'wiki' );
    register_taxonomy_for_object_type( 'post_tag', 'document' );
    register_taxonomy_for_object_type( 'post_tag', 'video' );
    register_taxonomy_for_object_type( 'post_tag', 'topic' );
}
add_action( 'init', 'slv_register_default_taxonomies_for_cpt', 5 );