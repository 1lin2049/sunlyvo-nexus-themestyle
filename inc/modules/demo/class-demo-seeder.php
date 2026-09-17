<?php
/**
 * SunLyvo Nexus — Demo 种子生成器
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 演示内容种子生成器。
 *
 * @since 1.0.0
 */
class SLV_Demo_Seeder {

    private bool $force;
    private int $author_id;

    public function __construct( bool $force = false ) {
        $this->force = $force;

        // 使用管理员作为作者
        $admins = get_users( [ 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ] );
        $this->author_id = $admins ? (int) $admins[0] : 1;
    }

    /**
     * 执行种子。
     *
     * @since 1.0.0
     * @return array
     */
    public function run(): array {
        return [
            'posts'       => $this->seed_posts( slv_demo_posts(), 'post' ),
            'wiki'        => $this->seed_posts( slv_demo_wiki(), 'wiki' ),
            'faq'         => $this->seed_posts( slv_demo_faq(), 'faq' ),
            'products'    => $this->seed_posts( slv_demo_products(), 'product' ),
            'collections' => $this->seed_collections( slv_demo_collections() ),
            'pages'       => $this->seed_pages( slv_demo_pages() ),
        ];
    }

    /**
     * 批量生产文章类内容。
     *
     * @since 1.0.0
     */
    private function seed_posts( array $items, string $post_type ): int {
        $count = 0;
        foreach ( $items as $item ) {
            if ( $this->seed_single_post( $item, $post_type ) ) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * 生产单篇文章。
     *
     * @since 1.0.0
     */
    private function seed_single_post( array $item, string $post_type ): bool {
        $demo_key = $item['demo_key'] ?? '';

        // 幂等：按 demo_key 去重
        if ( ! $this->force && $demo_key ) {
            $existing = get_posts( [
                'post_type'      => $post_type,
                'meta_key'       => '_slv_demo_key',
                'meta_value'     => $demo_key,
                'posts_per_page' => 1,
                'fields'         => 'ids',
                'post_status'    => 'any',
            ] );
            if ( ! empty( $existing ) ) {
                return false;
            }
        }

        $post_id = wp_insert_post( [
            'post_type'    => $post_type,
            'post_status'  => 'publish',
            'post_title'   => $item['title'] ?? '',
            'post_name'    => $item['slug'] ?? '',
            'post_content' => $item['content'] ?? '',
            'post_excerpt' => $item['excerpt'] ?? '',
            'post_author'  => $this->author_id,
        ], true );

        if ( is_wp_error( $post_id ) ) {
            return false;
        }

        // 元数据
        if ( ! empty( $item['meta'] ) && is_array( $item['meta'] ) ) {
            foreach ( $item['meta'] as $key => $value ) {
                update_post_meta( $post_id, $key, $value );
            }
        }
        if ( $demo_key ) {
            update_post_meta( $post_id, '_slv_demo_key', $demo_key );
        }

        // 分类
        if ( ! empty( $item['categories'] ) ) {
            wp_set_post_terms( $post_id, $item['categories'], 'category', false );
        }
        if ( ! empty( $item['tags'] ) ) {
            wp_set_post_terms( $post_id, $item['tags'], 'post_tag', false );
        }
        if ( ! empty( $item['product_cat'] ) ) {
            wp_set_post_terms( $post_id, $item['product_cat'], 'product_cat', false );
        }
        if ( ! empty( $item['wiki_cat'] ) ) {
            wp_set_post_terms( $post_id, $item['wiki_cat'], 'wiki_cat', false );
        }
        if ( ! empty( $item['faq_cat'] ) ) {
            wp_set_post_terms( $post_id, $item['faq_cat'], 'faq_cat', false );
        }

        // FAQ 数据（AEO）
        if ( 'faq' === $post_type && ! empty( $item['faq_qa'] ) ) {
            global $wpdb;
            $wpdb->delete( "{$wpdb->prefix}slv_aeo_faqs", [ 'post_id' => $post_id ] );
            foreach ( $item['faq_qa'] as $i => $qa ) {
                $wpdb->insert( "{$wpdb->prefix}slv_aeo_faqs", [
                    'post_id'      => $post_id,
                    'question'     => $qa['q'],
                    'answer'       => $qa['a'],
                    'answer_short' => mb_substr( wp_strip_all_tags( $qa['a'] ), 0, 200 ),
                    'sort_order'   => $i,
                ] );
            }
        }

        // SEO 元数据
        $this->seed_seo_meta( $post_id, $item );

        // 商品元数据
        if ( 'product' === $post_type ) {
            $this->seed_product_meta( $post_id, $item );
        }

        return true;
    }

    /**
     * 生产 SEO 元数据。
     *
     * @since 1.0.0
     */
    private function seed_seo_meta( int $post_id, array $item ): void {
        if ( empty( $item['seo'] ) ) {
            return;
        }
        global $wpdb;
        $wpdb->replace( "{$wpdb->prefix}slv_seo_meta", [
            'post_id'          => $post_id,
            'meta_title'       => $item['seo']['title'] ?? '',
            'meta_description' => $item['seo']['description'] ?? '',
            'meta_keywords'    => $item['seo']['keywords'] ?? '',
            'og_title'         => $item['seo']['title'] ?? '',
            'og_description'   => $item['seo']['description'] ?? '',
            'canonical_url'    => get_permalink( $post_id ),
            'robots'           => 'index,follow',
            'priority'         => '0.8',
            'changefreq'       => 'weekly',
        ] );
    }

    /**
     * 生产商品元数据。
     *
     * @since 1.0.0
     */
    private function seed_product_meta( int $post_id, array $item ): void {
        $fields = [ '_slv_sku', '_slv_price', '_slv_wholesale_price', '_slv_moq', '_slv_hs_code', '_slv_lead_time' ];
        foreach ( $fields as $key ) {
            if ( isset( $item['product'][ $key ] ) ) {
                update_post_meta( $post_id, $key, $item['product'][ $key ] );
            }
        }
        // 同步到 products 表（可选）
    }

    /**
     * 生产合集（含章节）。
     *
     * @since 1.0.0
     */
    private function seed_collections( array $items ): int {
        $count = 0;
        foreach ( $items as $item ) {
            $demo_key = $item['demo_key'] ?? '';
            if ( ! $this->force && $demo_key ) {
                $existing = get_posts( [
                    'post_type'      => 'collection',
                    'meta_key'       => '_slv_demo_key',
                    'meta_value'     => $demo_key,
                    'posts_per_page' => 1,
                    'fields'         => 'ids',
                    'post_status'    => 'any',
                ] );
                if ( ! empty( $existing ) ) {
                    continue;
                }
            }

            $collection_id = wp_insert_post( [
                'post_type'    => 'collection',
                'post_status'  => 'publish',
                'post_title'   => $item['title'] ?? '',
                'post_name'    => $item['slug'] ?? '',
                'post_content' => $item['content'] ?? '',
                'post_excerpt' => $item['excerpt'] ?? '',
                'post_author'  => $this->author_id,
            ], true );

            if ( is_wp_error( $collection_id ) ) {
                continue;
            }

            update_post_meta( $collection_id, '_slv_demo_key', $demo_key );
            update_post_meta( $collection_id, '_slv_collection_type', $item['collection_type'] ?? 'article_series' );
            update_post_meta( $collection_id, '_slv_is_free', $item['is_free'] ?? '0' );
            update_post_meta( $collection_id, '_slv_member_only', $item['member_only'] ?? '0' );

            if ( ! empty( $item['collection_cat'] ) ) {
                wp_set_post_terms( $collection_id, $item['collection_cat'], 'collection_cat', false );
            }

            $this->seed_seo_meta( $collection_id, $item );

            // 章节
            if ( ! empty( $item['chapters'] ) ) {
                $order = 0;
                foreach ( $item['chapters'] as $chapter ) {
                    $order++;
                    $chapter_id = wp_insert_post( [
                        'post_type'    => 'chapter',
                        'post_status'  => 'publish',
                        'post_title'   => $chapter['title'],
                        'post_content' => $chapter['content'],
                        'post_excerpt' => $chapter['excerpt'] ?? '',
                        'post_author'  => $this->author_id,
                    ] );
                    if ( ! is_wp_error( $chapter_id ) ) {
                        update_post_meta( $chapter_id, '_slv_collection_id', $collection_id );
                        update_post_meta( $chapter_id, '_slv_chapter_order', $order );
                        update_post_meta( $chapter_id, '_slv_is_free_preview', $chapter['is_free_preview'] ?? '0' );
                    }
                }
            }

            $count++;
        }
        return $count;
    }

    /**
     * 生产页面。
     *
     * @since 1.0.0
     */
    private function seed_pages( array $items ): int {
        $count = 0;
        foreach ( $items as $item ) {
            $demo_key = $item['demo_key'] ?? '';
            if ( ! $this->force && $demo_key ) {
                $existing = get_posts( [
                    'post_type'      => 'page',
                    'meta_key'       => '_slv_demo_key',
                    'meta_value'     => $demo_key,
                    'posts_per_page' => 1,
                    'fields'         => 'ids',
                    'post_status'    => 'any',
                ] );
                if ( ! empty( $existing ) ) {
                    continue;
                }
            }

            $post_id = wp_insert_post( [
                'post_type'    => 'page',
                'post_status'  => 'publish',
                'post_title'   => $item['title'] ?? '',
                'post_name'    => $item['slug'] ?? '',
                'post_content' => $item['content'] ?? '',
                'post_author'  => $this->author_id,
            ], true );

            if ( is_wp_error( $post_id ) ) {
                continue;
            }

            update_post_meta( $post_id, '_slv_demo_key', $demo_key );
            $this->seed_seo_meta( $post_id, $item );
            $count++;
        }
        return $count;
    }
}