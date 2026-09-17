<?php
/**
 * SunLyvo Nexus — CPT 注册
 *
 * 注册 18+ 种自定义内容类型。
 * 严格遵循：slv_ 前缀、sunlyvo-nexus 文本域、REST 支持、能力映射。
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 注册所有自定义内容类型。
 *
 * @since 1.0.0
 */
function slv_register_post_types(): void {

    // ─── 1. product（商品）─────────────────────────────────
    register_post_type( 'product', [
        'labels' => [
            'name'          => __( '商品', 'sunlyvo-nexus' ),
            'singular_name' => __( '商品', 'sunlyvo-nexus' ),
            'add_new'       => __( '新建商品', 'sunlyvo-nexus' ),
            'add_new_item'  => __( '新建商品', 'sunlyvo-nexus' ),
            'edit_item'     => __( '编辑商品', 'sunlyvo-nexus' ),
            'new_item'      => __( '新商品', 'sunlyvo-nexus' ),
            'view_item'     => __( '查看商品', 'sunlyvo-nexus' ),
            'search_items'  => __( '搜索商品', 'sunlyvo-nexus' ),
            'not_found'     => __( '未找到商品', 'sunlyvo-nexus' ),
            'menu_name'     => __( '商品', 'sunlyvo-nexus' ),
        ],
        'public'             => true,
        'show_in_rest'       => true,
        'rest_base'          => 'products',
        'has_archive'        => true,
        'menu_icon'          => 'dashicons-cart',
        'supports'           => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions', 'author' ],
        'rewrite'            => [ 'slug' => 'products', 'with_front' => false ],
        'capability_type'    => 'post',
        'map_meta_cap'       => true,
        'taxonomies'         => [ 'product_cat', 'post_tag' ],
        'menu_position'      => 20,
    ] );

    // ─── 2. collection（合集）──────────────────────────────
    register_post_type( 'collection', [
        'labels' => [
            'name'          => __( '合集', 'sunlyvo-nexus' ),
            'singular_name' => __( '合集', 'sunlyvo-nexus' ),
            'add_new_item'  => __( '新建合集', 'sunlyvo-nexus' ),
            'edit_item'     => __( '编辑合集', 'sunlyvo-nexus' ),
            'menu_name'     => __( '合集', 'sunlyvo-nexus' ),
        ],
        'public'          => true,
        'show_in_rest'    => true,
        'rest_base'       => 'collections',
        'has_archive'     => true,
        'menu_icon'       => 'dashicons-book-alt',
        'supports'        => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions', 'author' ],
        'rewrite'         => [ 'slug' => 'collections', 'with_front' => false ],
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'taxonomies'      => [ 'collection_cat' ],
        'menu_position'   => 21,
    ] );

    // ─── 3. chapter（章节，合集内）─────────────────────────
    register_post_type( 'chapter', [
        'labels' => [
            'name'          => __( '章节', 'sunlyvo-nexus' ),
            'singular_name' => __( '章节', 'sunlyvo-nexus' ),
            'add_new_item'  => __( '新建章节', 'sunlyvo-nexus' ),
            'edit_item'     => __( '编辑章节', 'sunlyvo-nexus' ),
            'menu_name'     => __( '章节', 'sunlyvo-nexus' ),
        ],
        'public'             => true,
        'show_in_rest'       => true,
        'rest_base'          => 'chapters',
        'has_archive'        => false,
        'publicly_queryable' => true,
        'menu_icon'          => 'dashicons-media-document',
        'supports'           => [ 'title', 'editor', 'thumbnail', 'custom-fields', 'revisions', 'author', 'excerpt' ],
        'rewrite'            => [ 'slug' => 'chapter', 'with_front' => false ],
        'capability_type'    => 'post',
        'map_meta_cap'       => true,
        'menu_position'      => 22,
    ] );

    // ─── 4. wiki（百科词条）────────────────────────────────
    register_post_type( 'wiki', [
        'labels' => [
            'name'          => __( '百科', 'sunlyvo-nexus' ),
            'singular_name' => __( '百科词条', 'sunlyvo-nexus' ),
            'add_new_item'  => __( '新建词条', 'sunlyvo-nexus' ),
            'edit_item'     => __( '编辑词条', 'sunlyvo-nexus' ),
            'menu_name'     => __( '百科', 'sunlyvo-nexus' ),
        ],
        'public'          => true,
        'show_in_rest'    => true,
        'rest_base'       => 'wiki',
        'has_archive'     => true,
        'menu_icon'       => 'dashicons-book',
        'supports'        => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions', 'author' ],
        'rewrite'         => [ 'slug' => 'wiki', 'with_front' => false ],
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'taxonomies'      => [ 'wiki_cat', 'post_tag' ],
        'menu_position'   => 23,
    ] );

    // ─── 5. faq（常见问题）─────────────────────────────────
    register_post_type( 'faq', [
        'labels' => [
            'name'          => __( 'FAQ', 'sunlyvo-nexus' ),
            'singular_name' => __( 'FAQ', 'sunlyvo-nexus' ),
            'add_new_item'  => __( '新建 FAQ', 'sunlyvo-nexus' ),
            'edit_item'     => __( '编辑 FAQ', 'sunlyvo-nexus' ),
            'menu_name'     => __( 'FAQ', 'sunlyvo-nexus' ),
        ],
        'public'          => true,
        'show_in_rest'    => true,
        'rest_base'       => 'faq',
        'has_archive'     => true,
        'menu_icon'       => 'dashicons-editor-help',
        'supports'        => [ 'title', 'editor', 'excerpt', 'custom-fields', 'revisions', 'author' ],
        'rewrite'         => [ 'slug' => 'faq', 'with_front' => false ],
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'taxonomies'      => [ 'faq_cat' ],
        'menu_position'   => 24,
    ] );

    // ─── 6. document（文库）───────────────────────────────
    register_post_type( 'document', [
        'labels' => [
            'name'          => __( '文库', 'sunlyvo-nexus' ),
            'singular_name' => __( '文档', 'sunlyvo-nexus' ),
            'add_new_item'  => __( '新建文档', 'sunlyvo-nexus' ),
            'edit_item'     => __( '编辑文档', 'sunlyvo-nexus' ),
            'menu_name'     => __( '文库', 'sunlyvo-nexus' ),
        ],
        'public'          => true,
        'show_in_rest'    => true,
        'rest_base'       => 'documents',
        'has_archive'     => true,
        'menu_icon'       => 'dashicons-media-default',
        'supports'        => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions', 'author' ],
        'rewrite'         => [ 'slug' => 'documents', 'with_front' => false ],
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'taxonomies'      => [ 'doc_category', 'post_tag' ],
        'menu_position'   => 25,
    ] );

    // ─── 7. video（视频）──────────────────────────────────
    register_post_type( 'video', [
        'labels' => [
            'name'          => __( '视频', 'sunlyvo-nexus' ),
            'singular_name' => __( '视频', 'sunlyvo-nexus' ),
            'add_new_item'  => __( '新建视频', 'sunlyvo-nexus' ),
            'edit_item'     => __( '编辑视频', 'sunlyvo-nexus' ),
            'menu_name'     => __( '视频', 'sunlyvo-nexus' ),
        ],
        'public'          => true,
        'show_in_rest'    => true,
        'rest_base'       => 'videos',
        'has_archive'     => true,
        'menu_icon'       => 'dashicons-video-alt3',
        'supports'        => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions', 'author' ],
        'rewrite'         => [ 'slug' => 'videos', 'with_front' => false ],
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'taxonomies'      => [ 'post_tag' ],
        'menu_position'   => 26,
    ] );

    // ─── 8. audio（音频）──────────────────────────────────
    register_post_type( 'audio', [
        'labels' => [
            'name'          => __( '音频', 'sunlyvo-nexus' ),
            'singular_name' => __( '音频', 'sunlyvo-nexus' ),
            'menu_name'     => __( '音频', 'sunlyvo-nexus' ),
        ],
        'public'          => true,
        'show_in_rest'    => true,
        'rest_base'       => 'audios',
        'has_archive'     => true,
        'menu_icon'       => 'dashicons-format-audio',
        'supports'        => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions', 'author' ],
        'rewrite'         => [ 'slug' => 'audios', 'with_front' => false ],
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'taxonomies'      => [ 'post_tag' ],
        'menu_position'   => 27,
    ] );

    // ─── 9. course（课程）─────────────────────────────────
    register_post_type( 'course', [
        'labels' => [
            'name'          => __( '课程', 'sunlyvo-nexus' ),
            'singular_name' => __( '课程', 'sunlyvo-nexus' ),
            'menu_name'     => __( '课程', 'sunlyvo-nexus' ),
        ],
        'public'          => true,
        'show_in_rest'    => true,
        'rest_base'       => 'courses',
        'has_archive'     => true,
        'menu_icon'       => 'dashicons-welcome-learn-more',
        'supports'        => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions', 'author' ],
        'rewrite'         => [ 'slug' => 'courses', 'with_front' => false ],
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'taxonomies'      => [ 'post_tag' ],
        'menu_position'   => 28,
    ] );

    // ─── 10. live（直播）──────────────────────────────────
    register_post_type( 'live', [
        'labels' => [
            'name'          => __( '直播', 'sunlyvo-nexus' ),
            'singular_name' => __( '直播', 'sunlyvo-nexus' ),
            'menu_name'     => __( '直播', 'sunlyvo-nexus' ),
        ],
        'public'          => true,
        'show_in_rest'    => true,
        'rest_base'       => 'lives',
        'has_archive'     => true,
        'menu_icon'       => 'dashicons-video-alt2',
        'supports'        => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions', 'author' ],
        'rewrite'         => [ 'slug' => 'lives', 'with_front' => false ],
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'menu_position'   => 29,
    ] );

    // ─── 11. topic（论坛话题）─────────────────────────────
    register_post_type( 'topic', [
        'labels' => [
            'name'          => __( '话题', 'sunlyvo-nexus' ),
            'singular_name' => __( '话题', 'sunlyvo-nexus' ),
            'menu_name'     => __( '论坛', 'sunlyvo-nexus' ),
        ],
        'public'          => true,
        'show_in_rest'    => true,
        'rest_base'       => 'topics',
        'has_archive'     => true,
        'menu_icon'       => 'dashicons-format-chat',
        'supports'        => [ 'title', 'editor', 'custom-fields', 'revisions', 'author' ],
        'rewrite'         => [ 'slug' => 'topics', 'with_front' => false ],
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'taxonomies'      => [ 'forum_board', 'post_tag' ],
        'menu_position'   => 30,
    ] );

    // ─── 12. group（圈子）─────────────────────────────────
    register_post_type( 'group', [
        'labels' => [
            'name'          => __( '圈子', 'sunlyvo-nexus' ),
            'singular_name' => __( '圈子', 'sunlyvo-nexus' ),
            'menu_name'     => __( '圈子', 'sunlyvo-nexus' ),
        ],
        'public'          => true,
        'show_in_rest'    => true,
        'rest_base'       => 'groups',
        'has_archive'     => true,
        'menu_icon'       => 'dashicons-groups',
        'supports'        => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions', 'author' ],
        'rewrite'         => [ 'slug' => 'groups', 'with_front' => false ],
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'taxonomies'      => [ 'group_cat' ],
        'menu_position'   => 31,
    ] );

    // ─── 13. moment（朋友圈动态）──────────────────────────
    register_post_type( 'moment', [
        'labels' => [
            'name'          => __( '动态', 'sunlyvo-nexus' ),
            'singular_name' => __( '动态', 'sunlyvo-nexus' ),
            'menu_name'     => __( '朋友圈', 'sunlyvo-nexus' ),
        ],
        'public'          => true,
        'show_in_rest'    => true,
        'rest_base'       => 'moments',
        'has_archive'     => true,
        'menu_icon'       => 'dashicons-format-status',
        'supports'        => [ 'title', 'editor', 'thumbnail', 'custom-fields', 'author' ],
        'rewrite'         => [ 'slug' => 'moments', 'with_front' => false ],
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'menu_position'   => 32,
    ] );

    // ─── 14. gallery（图集）───────────────────────────────
    register_post_type( 'gallery', [
        'labels' => [
            'name'          => __( '图集', 'sunlyvo-nexus' ),
            'singular_name' => __( '图集', 'sunlyvo-nexus' ),
            'menu_name'     => __( '图集', 'sunlyvo-nexus' ),
        ],
        'public'          => true,
        'show_in_rest'    => true,
        'rest_base'       => 'galleries',
        'has_archive'     => true,
        'menu_icon'       => 'dashicons-format-gallery',
        'supports'        => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions', 'author' ],
        'rewrite'         => [ 'slug' => 'galleries', 'with_front' => false ],
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'menu_position'   => 33,
    ] );

    // ─── 15. service（服务）───────────────────────────────
    register_post_type( 'service', [
        'labels' => [
            'name'          => __( '服务', 'sunlyvo-nexus' ),
            'singular_name' => __( '服务', 'sunlyvo-nexus' ),
            'menu_name'     => __( '服务', 'sunlyvo-nexus' ),
        ],
        'public'          => true,
        'show_in_rest'    => true,
        'rest_base'       => 'services',
        'has_archive'     => true,
        'menu_icon'       => 'dashicons-hammer',
        'supports'        => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions', 'author' ],
        'rewrite'         => [ 'slug' => 'services', 'with_front' => false ],
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'menu_position'   => 34,
    ] );

    // ─── 16. company（企业）───────────────────────────────
    register_post_type( 'company', [
        'labels' => [
            'name'          => __( '企业', 'sunlyvo-nexus' ),
            'singular_name' => __( '企业', 'sunlyvo-nexus' ),
            'menu_name'     => __( '企业', 'sunlyvo-nexus' ),
        ],
        'public'             => false,
        'show_ui'            => true,
        'show_in_rest'       => true,
        'rest_base'          => 'companies',
        'has_archive'        => false,
        'menu_icon'          => 'dashicons-building',
        'supports'           => [ 'title', 'custom-fields' ],
        'capability_type'    => 'post',
        'map_meta_cap'       => true,
        'menu_position'      => 35,
    ] );

    // ─── 17. inquiry（询盘）───────────────────────────────
    register_post_type( 'inquiry', [
        'labels' => [
            'name'          => __( '询盘', 'sunlyvo-nexus' ),
            'singular_name' => __( '询盘', 'sunlyvo-nexus' ),
            'menu_name'     => __( '询盘', 'sunlyvo-nexus' ),
        ],
        'public'             => false,
        'show_ui'            => true,
        'show_in_rest'       => true,
        'rest_base'          => 'inquiries',
        'has_archive'        => false,
        'menu_icon'          => 'dashicons-email-alt',
        'supports'           => [ 'title', 'editor', 'custom-fields' ],
        'capability_type'    => 'post',
        'map_meta_cap'       => true,
        'menu_position'      => 36,
    ] );

    // ─── 18. campaign（训练营）────────────────────────────
    register_post_type( 'campaign', [
        'labels' => [
            'name'          => __( '训练营', 'sunlyvo-nexus' ),
            'singular_name' => __( '训练营', 'sunlyvo-nexus' ),
            'menu_name'     => __( '训练营', 'sunlyvo-nexus' ),
        ],
        'public'          => true,
        'show_in_rest'    => true,
        'rest_base'       => 'campaigns',
        'has_archive'     => true,
        'menu_icon'       => 'dashicons-megaphone',
        'supports'        => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions', 'author' ],
        'rewrite'         => [ 'slug' => 'campaigns', 'with_front' => false ],
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'menu_position'   => 37,
    ] );
}
add_action( 'init', 'slv_register_post_types', 5 );

/**
 * 注册产品/合集等内容类型与默认文章类型的关联。
 *
 * @since 1.0.0
 */
function slv_register_post_meta(): void {
    $meta_fields = [
        // 商品
        'product'    => [ '_slv_wholesale_price', '_slv_moq', '_slv_hs_code', '_slv_sku', '_slv_lead_time', '_slv_vendor_id' ],
        // 合集
        'collection' => [ '_slv_collection_type', '_slv_collection_price', '_slv_is_free', '_slv_member_only', '_slv_total_items' ],
        // 章节
        'chapter'    => [ '_slv_collection_id', '_slv_chapter_order', '_slv_is_free_preview' ],
        // 百科
        'wiki'       => [ '_slv_wiki_definition', '_slv_wiki_aliases', '_slv_wiki_infobox' ],
        // FAQ
        'faq'        => [ '_slv_faq_question', '_slv_faq_answer_short' ],
        // 视频
        'video'      => [ '_slv_mux_asset_id', '_slv_mux_playback_id', '_slv_duration' ],
        // 直播
        'live'       => [ '_slv_mux_live_id', '_slv_stream_key' ],
        // 课程
        'course'     => [ '_slv_course_duration', '_slv_lesson_count' ],
        // 企业
        'company'    => [ '_slv_company_vat', '_slv_company_credit_limit', '_slv_company_status' ],
        // 训练营
        'campaign'   => [ '_slv_campaign_start', '_slv_campaign_end', '_slv_campaign_capacity' ],
        // 询盘
        'inquiry'    => [ '_slv_inquiry_product_id', '_slv_inquiry_company', '_slv_inquiry_status' ],
        // 通用 SEO
    ];

    foreach ( $meta_fields as $post_type => $keys ) {
        foreach ( $keys as $key ) {
            register_post_meta( $post_type, $key, [
                'type'              => 'string',
                'single'            => true,
                'show_in_rest'      => true,
                'sanitize_callback' => 'sanitize_text_field',
                'auth_callback'     => function() {
                    return current_user_can( 'edit_posts' );
                },
            ] );
        }
    }
}
add_action( 'init', 'slv_register_post_meta', 6 );

/**
 * 激活主题时刷新 rewrite 规则。
 *
 * @since 1.0.0
 */
function slv_flush_rewrite_on_activation(): void {
    slv_register_post_types();
    slv_register_taxonomies();
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'slv_flush_rewrite_on_activation' );