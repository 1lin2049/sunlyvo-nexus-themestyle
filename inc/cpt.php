<?php
/**
 * SunLyvo Nexus — CPT 注册
 *
 * 注册 18+ 种自定义内容类型。
 * 严格遵循：slv_ 前缀、sunlyvo-nexus 文本域、REST 支持、能力映射。
 *
 * 每个 CPT 必须包含完整的 13 个 labels，否则后台菜单会显示"写文章"。
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 生成标准 labels 数组。
 *
 * @since 1.0.0
 * @param string $singular 单数名称（如"商品"）。
 * @param string $plural   复数名称（如"商品"）。
 * @return array
 */
function slv_build_labels( string $singular, string $plural ): array {
    return [
        'name'                  => $plural,
        'singular_name'         => $singular,
        'menu_name'             => $plural,
        'all_items'             => sprintf( '全部%s', $plural ),
        'add_new'               => '新建',
        'add_new_item'          => sprintf( '新建%s', $singular ),
        'edit_item'             => sprintf( '编辑%s', $singular ),
        'new_item'              => sprintf( '新%s', $singular ),
        'view_item'             => sprintf( '查看%s', $singular ),
        'view_items'            => sprintf( '查看%s', $plural ),
        'search_items'          => sprintf( '搜索%s', $plural ),
        'not_found'             => sprintf( '未找到%s', $plural ),
        'not_found_in_trash'    => sprintf( '回收站中未找到%s', $plural ),
        'archives'              => sprintf( '%s归档', $plural ),
        'attributes'            => sprintf( '%s属性', $plural ),
        'insert_into_item'      => sprintf( '插入到%s', $singular ),
        'uploaded_to_this_item' => sprintf( '上传到此%s', $singular ),
        'featured_image'        => '特色图片',
        'set_featured_image'    => '设置特色图片',
        'remove_featured_image' => '移除特色图片',
        'use_featured_image'    => '使用特色图片',
        'filter_items_list'     => sprintf( '筛选%s列表', $plural ),
        'items_list_navigation' => sprintf( '%s列表导航', $plural ),
        'items_list'            => sprintf( '%s列表', $plural ),
    ];
}

/**
 * 注册所有自定义内容类型。
 *
 * @since 1.0.0
 */
function slv_register_post_types(): void {

    // ─── 1. product（商品）
    register_post_type( 'product', [
        'labels'             => slv_build_labels( '商品', '商品' ),
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

    // ─── 2. collection（合集）
    register_post_type( 'collection', [
        'labels'          => slv_build_labels( '合集', '合集' ),
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

    // ─── 3. chapter（章节）
    register_post_type( 'chapter', [
        'labels'             => slv_build_labels( '章节', '章节' ),
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

    // ─── 4. wiki（百科词条）
    register_post_type( 'wiki', [
        'labels'          => slv_build_labels( '词条', '百科' ),
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

    // ─── 5. faq（常见问题）
    register_post_type( 'faq', [
        'labels'          => slv_build_labels( 'FAQ', 'FAQ' ),
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

    // ─── 6. document（文库）
    register_post_type( 'document', [
        'labels'          => slv_build_labels( '文档', '文库' ),
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

    // ─── 7. video（视频）
    register_post_type( 'video', [
        'labels'          => slv_build_labels( '视频', '视频' ),
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

    // ─── 8. audio（音频）
    register_post_type( 'audio', [
        'labels'          => slv_build_labels( '音频', '音频' ),
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

    // ─── 9. course（课程）
    register_post_type( 'course', [
        'labels'          => slv_build_labels( '课程', '课程' ),
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

    // ─── 10. live（直播）
    register_post_type( 'live', [
        'labels'          => slv_build_labels( '直播', '直播' ),
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

    // ─── 11. topic（论坛话题）
    register_post_type( 'topic', [
        'labels'          => slv_build_labels( '话题', '论坛' ),
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

    // ─── 12. group（圈子）
    register_post_type( 'group', [
        'labels'          => slv_build_labels( '圈子', '圈子' ),
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

    // ─── 13. moment（朋友圈动态）
    register_post_type( 'moment', [
        'labels'          => slv_build_labels( '动态', '朋友圈' ),
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

    // ─── 14. gallery（图集）
    register_post_type( 'gallery', [
        'labels'          => slv_build_labels( '图集', '图集' ),
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

    // ─── 15. service（服务）
    register_post_type( 'service', [
        'labels'          => slv_build_labels( '服务', '服务' ),
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

    // ─── 16. company（企业）
    register_post_type( 'company', [
        'labels'          => slv_build_labels( '企业', '企业' ),
        'public'          => false,
        'show_ui'         => true,
        'show_in_rest'    => true,
        'rest_base'       => 'companies',
        'has_archive'     => false,
        'menu_icon'       => 'dashicons-building',
        'supports'        => [ 'title', 'custom-fields' ],
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'menu_position'   => 35,
    ] );

    // ─── 17. inquiry（询盘）
    register_post_type( 'inquiry', [
        'labels'          => slv_build_labels( '询盘', '询盘' ),
        'public'          => false,
        'show_ui'         => true,
        'show_in_rest'    => true,
        'rest_base'       => 'inquiries',
        'has_archive'     => false,
        'menu_icon'       => 'dashicons-email-alt',
        'supports'        => [ 'title', 'editor', 'custom-fields' ],
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'menu_position'   => 36,
    ] );

    // ─── 18. campaign（训练营）
    register_post_type( 'campaign', [
        'labels'          => slv_build_labels( '训练营', '训练营' ),
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
        'product'    => [ '_slv_wholesale_price', '_slv_moq', '_slv_hs_code', '_slv_sku', '_slv_lead_time', '_slv_vendor_id' ],
        'collection' => [ '_slv_collection_type', '_slv_collection_price', '_slv_is_free', '_slv_member_only', '_slv_total_items' ],
        'chapter'    => [ '_slv_collection_id', '_slv_chapter_order', '_slv_is_free_preview' ],
        'wiki'       => [ '_slv_wiki_definition', '_slv_wiki_aliases', '_slv_wiki_infobox' ],
        'faq'        => [ '_slv_faq_question', '_slv_faq_answer_short' ],
        'video'      => [ '_slv_mux_asset_id', '_slv_mux_playback_id', '_slv_duration' ],
        'live'       => [ '_slv_mux_live_id', '_slv_stream_key' ],
        'course'     => [ '_slv_course_duration', '_slv_lesson_count' ],
        'company'    => [ '_slv_company_vat', '_slv_company_credit_limit', '_slv_company_status' ],
        'campaign'   => [ '_slv_campaign_start', '_slv_campaign_end', '_slv_campaign_capacity' ],
        'inquiry'    => [ '_slv_inquiry_product_id', '_slv_inquiry_company', '_slv_inquiry_status' ],
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
    if ( function_exists( 'slv_register_taxonomies' ) ) {
        slv_register_taxonomies();
    }
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'slv_flush_rewrite_on_activation' );