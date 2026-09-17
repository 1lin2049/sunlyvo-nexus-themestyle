<?php
/**
 * SunLyvo Nexus — 基础 Schema（实体层 + 语义层）
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 构建全站实体层 @graph。
 *
 * @since 1.0.0
 * @return array
 */
function slv_seo_build_entity_graph(): array {
    $graph = [];

    // Organization
    $graph[] = [
        '@type' => 'Organization',
        '@id'   => network_home_url( '/#organization' ),
        'name'  => get_bloginfo( 'name' ),
        'url'   => network_home_url(),
        'logo'  => [
            '@type' => 'ImageObject',
            'url'   => slv_seo_get_logo_url(),
        ],
        'sameAs' => slv_seo_get_social_profiles(),
    ];

    // WebSite
    $graph[] = [
        '@type'      => 'WebSite',
        '@id'        => home_url( '/#website' ),
        'url'        => home_url(),
        'name'       => get_bloginfo( 'name' ),
        'publisher'  => [ '@id' => network_home_url( '/#organization' ) ],
        'inLanguage' => get_locale(),
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => [
                '@type'       => 'EntryPoint',
                'urlTemplate' => home_url( '/?s={search_term_string}' ),
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ];

    // Person（当前文章作者）
    if ( is_singular() ) {
        $author_id = (int) get_post_field( 'post_author', get_the_ID() );
        if ( $author_id ) {
            $graph[] = slv_seo_build_author_schema( $author_id );
        }
    }

    // LocalBusiness（城市站）
    $city = slv_get_current_city();
    if ( $city ) {
        $graph[] = slv_seo_build_local_business_schema( $city );
    }

    return $graph;
}

/**
 * 构建作者 Person Schema。
 *
 * @since 1.0.0
 * @param int $author_id 作者 ID。
 * @return array
 */
function slv_seo_build_author_schema( int $author_id ): array {
    $user = get_userdata( $author_id );
    return [
        '@type'    => 'Person',
        '@id'      => get_author_posts_url( $author_id ) . '#person',
        'name'     => $user ? $user->display_name : '',
        'url'      => get_author_posts_url( $author_id ),
        'sameAs'   => array_filter( [
            $user ? get_user_meta( $author_id, '_slv_twitter', true ) : '',
            $user ? get_user_meta( $author_id, '_slv_linkedin', true ) : '',
        ] ),
    ];
}

/**
 * 构建 LocalBusiness Schema。
 *
 * @since 1.0.0
 * @param object $city 城市配置。
 * @return array
 */
function slv_seo_build_local_business_schema( object $city ): array {
    return [
        '@type'   => 'LocalBusiness',
        '@id'     => home_url( '/#localbusiness' ),
        'name'    => $city->city_name ?? get_bloginfo( 'name' ),
        'url'     => home_url(),
        'address' => [
            '@type'          => 'PostalAddress',
            'addressCountry' => $city->country_code ?? '',
            'addressLocality'=> $city->city_name ?? '',
        ],
        'geo' => [
            '@type'     => 'GeoCoordinates',
            'latitude'  => (float) ( $city->geo_lat ?? 0 ),
            'longitude' => (float) ( $city->geo_lng ?? 0 ),
        ],
    ];
}

/**
 * 构建当前页面的语义层 Schema。
 *
 * @since 1.0.0
 * @return array|null
 */
function slv_seo_build_main_schema(): ?array {
    if ( ! is_singular() ) {
        return null;
    }

    $post_id = get_the_ID();
    $post_type = get_post_type( $post_id );

    switch ( $post_type ) {
        case 'post':
            return slv_seo_build_article_schema( $post_id );
        case 'wiki':
            return slv_seo_build_defined_term_schema( $post_id );
        case 'faq':
            return slv_seo_build_faqpage_schema( $post_id );
        case 'product':
            return slv_seo_build_product_schema( $post_id );
        case 'collection':
            return slv_seo_build_collection_schema( $post_id );
    }

    return null;
}

/**
 * Article Schema。
 *
 * @since 1.0.0
 */
function slv_seo_build_article_schema( int $post_id ): array {
    $author_id = (int) get_post_field( 'post_author', $post_id );
    return [
        '@type'            => 'Article',
        '@id'              => get_permalink( $post_id ) . '#article',
        'headline'         => get_the_title( $post_id ),
        'description'      => wp_strip_all_tags( get_the_excerpt( $post_id ) ),
        'datePublished'    => get_the_date( 'c', $post_id ),
        'dateModified'     => get_the_modified_date( 'c', $post_id ),
        'author'           => [ '@id' => get_author_posts_url( $author_id ) . '#person' ],
        'publisher'        => [ '@id' => network_home_url( '/#organization' ) ],
        'mainEntityOfPage' => [ '@id' => get_permalink( $post_id ) . '#webpage' ],
        'image'            => get_the_post_thumbnail_url( $post_id, 'large' ) ?: '',
        'inLanguage'       => get_locale(),
    ];
}

/**
 * DefinedTerm Schema（百科）。
 *
 * @since 1.0.0
 */
function slv_seo_build_defined_term_schema( int $post_id ): array {
    return [
        '@type'       => 'DefinedTerm',
        '@id'         => get_permalink( $post_id ) . '#definedterm',
        'name'        => get_the_title( $post_id ),
        'description' => get_post_meta( $post_id, '_slv_wiki_definition', true ) ?: wp_strip_all_tags( get_the_excerpt( $post_id ) ),
        'inDefinedTermSet' => [
            '@type' => 'DefinedTermSet',
            'name'  => 'SunLyvo Nexus 百科',
            'url'   => home_url( '/wiki/' ),
        ],
        'sameAs'      => get_post_meta( $post_id, '_slv_wiki_aliases', true ) ?: '',
    ];
}

/**
 * FAQPage Schema（由 AEO 模块提供）。
 * 此处保留空壳供 @graph 调用，实际由 aeo/faq-schema.php 覆盖。
 *
 * @since 1.0.0
 */
function slv_seo_build_faqpage_schema( int $post_id ): array {
    return function_exists( 'slv_aeo_build_faqpage_schema' )
        ? slv_aeo_build_faqpage_schema( $post_id )
        : [ '@type' => 'FAQPage', '@id' => get_permalink( $post_id ) . '#faqpage' ];
}

/**
 * Product Schema。
 *
 * @since 1.0.0
 */
function slv_seo_build_product_schema( int $post_id ): array {
    $sku   = get_post_meta( $post_id, '_slv_sku', true );
    $price = (float) get_post_meta( $post_id, '_slv_price', true );

    return [
        '@type'       => 'Product',
        '@id'         => get_permalink( $post_id ) . '#product',
        'name'        => get_the_title( $post_id ),
        'description' => wp_strip_all_tags( get_the_excerpt( $post_id ) ),
        'sku'         => $sku,
        'image'       => get_the_post_thumbnail_url( $post_id, 'large' ) ?: '',
        'offers'      => [
            '@type'         => 'Offer',
            'price'         => number_format( $price, 2, '.', '' ),
            'priceCurrency' => slv_get_config( 'default_currency', 'USD' ),
            'availability'  => 'https://schema.org/InStock',
            'url'           => get_permalink( $post_id ),
        ],
    ];
}

/**
 * Collection Schema（复用 Book 类型）。
 *
 * @since 1.0.0
 */
function slv_seo_build_collection_schema( int $post_id ): array {
    return [
        '@type'       => 'Book',
        '@id'         => get_permalink( $post_id ) . '#book',
        'name'        => get_the_title( $post_id ),
        'description' => wp_strip_all_tags( get_the_excerpt( $post_id ) ),
        'author'      => [ '@id' => get_author_posts_url( (int) get_post_field( 'post_author', $post_id ) ) . '#person' ],
        'inLanguage'  => get_locale(),
    ];
}

/**
 * 统一输出 @graph 到 wp_head。
 *
 * @since 1.0.0
 */
function slv_seo_output_jsonld_graph(): void {
    $graph = slv_seo_build_entity_graph();

    $main = slv_seo_build_main_schema();
    if ( $main ) {
        $graph[] = $main;
    }

    // 面包屑
    $breadcrumb = slv_seo_build_breadcrumb_schema();
    if ( $breadcrumb ) {
        $graph[] = $breadcrumb;
    }

    // GEO 增强（语义分块 + Speakable）
    if ( function_exists( 'slv_geo_build_enhanced_graph' ) ) {
        $graph = array_merge( $graph, slv_geo_build_enhanced_graph() );
    }

    // AEO 增强（HowTo / QAPage / Speakable）
    if ( function_exists( 'slv_aeo_build_additional_schemas' ) ) {
        $graph = array_merge( $graph, slv_aeo_build_additional_schemas() );
    }

    echo '<script type="application/ld+json">'
        . wp_json_encode( [ '@context' => 'https://schema.org', '@graph' => $graph ],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
        . '</script>' . "\n";
}
add_action( 'wp_head', 'slv_seo_output_jsonld_graph', 5 );

/**
 * 获取 Logo URL。
 *
 * @since 1.0.0
 */
function slv_seo_get_logo_url(): string {
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    if ( $custom_logo_id ) {
        return (string) wp_get_attachment_image_url( $custom_logo_id, 'full' );
    }
    return SLV_ASSETS_URL . '/images/logo.svg';
}

/**
 * 获取社交档案链接。
 *
 * @since 1.0.0
 */
function slv_seo_get_social_profiles(): array {
    $profiles = [];
    foreach ( [ 'twitter', 'linkedin', 'facebook', 'github', 'weibo', 'wechat' ] as $key ) {
        $url = slv_get_config( "social_{$key}", '' );
        if ( $url ) {
            $profiles[] = $url;
        }
    }
    return $profiles;
}

/**
 * 获取当前城市配置。
 *
 * @since 1.0.0
 */
function slv_get_current_city(): ?object {
    if ( ! is_multisite() ) {
        return null;
    }
    global $wpdb;
    $blog_id = get_current_blog_id();
    $row = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM {$wpdb->base_prefix}slv_city_config WHERE blog_id = %d AND is_active = 1",
        $blog_id
    ) );
    return $row ?: null;
}