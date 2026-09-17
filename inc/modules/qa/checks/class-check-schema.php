<?php
/**
 * SunLyvo Nexus — Schema 验证（5 项）
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Schema 验证器。
 *
 * @since 1.0.0
 */
class SLV_Check_Schema {

    public function get_label(): string {
        return __( 'Schema 验证（5 项）', 'sunlyvo-nexus' );
    }

    public function run(): array {
        return [
            $this->check_article_schema(),
            $this->check_defined_term_schema(),
            $this->check_faqpage_schema(),
            $this->check_product_schema(),
            $this->check_breadcrumb_schema(),
        ];
    }

    private function check_article_schema(): array {
        return $this->check_graph_has_type( 'Article', 'Article Schema' );
    }

    private function check_defined_term_schema(): array {
        return $this->check_graph_has_type( 'DefinedTerm', 'DefinedTerm Schema' );
    }

    private function check_faqpage_schema(): array {
        return $this->check_graph_has_type( 'FAQPage', 'FAQPage Schema' );
    }

    private function check_product_schema(): array {
        return $this->check_graph_has_type( 'Product', 'Product Schema' );
    }

    private function check_breadcrumb_schema(): array {
        $items = get_posts( [
            'post_type'      => 'post',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'fields'         => 'ids',
        ] );
        if ( empty( $items ) ) {
            return $this->skip( 'BreadcrumbList Schema', '无文章' );
        }
        $this->simulate_singular( (int) $items[0] );
        $breadcrumb = function_exists( 'slv_seo_build_breadcrumb_schema' )
            ? slv_seo_build_breadcrumb_schema()
            : null;
        return $breadcrumb && ! empty( $breadcrumb['itemListElement'] )
            ? $this->pass( 'BreadcrumbList Schema', count( $breadcrumb['itemListElement'] ) . ' 项' )
            : $this->fail( 'BreadcrumbList Schema', '未输出或结构错误' );
    }

    // ─── 辅助方法 ─────────────────────────────────────────

    private function check_graph_has_type( string $type, string $label ): array {
        $post_type_map = [
            'Article'     => 'post',
            'DefinedTerm' => 'wiki',
            'FAQPage'     => 'faq',
            'Product'     => 'product',
        ];
        $pt = $post_type_map[ $type ] ?? 'post';

        $items = get_posts( [
            'post_type'      => $pt,
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'fields'         => 'ids',
        ] );
        if ( empty( $items ) ) {
            return $this->skip( $label, "无 {$pt} 内容" );
        }

        $this->simulate_singular( (int) $items[0] );

        $main = function_exists( 'slv_seo_build_main_schema' )
            ? slv_seo_build_main_schema()
            : null;

        if ( ! $main ) {
            return $this->fail( $label, '主 Schema 未生成' );
        }

        $actual_type = $main['@type'] ?? '';
        return $actual_type === $type
            ? $this->pass( $label, "类型 {$actual_type} 正确" )
            : $this->fail( $label, "期望 {$type}，实际 {$actual_type}" );
    }

    private function simulate_singular( int $post_id ): void {
        // 通过 WordPress 主循环模拟单个文章上下文
        global $wp_query;
        $original = $wp_query;
        $wp_query = new WP_Query( [ 'p' => $post_id, 'post_type' => get_post_type( $post_id ) ] );
        if ( $wp_query->have_posts() ) {
            $wp_query->the_post();
        }
    }

    private function pass( string $label, string $value ): array {
        return [ 'label' => $label, 'status' => 'pass', 'value' => $value ];
    }
    private function fail( string $label, string $value ): array {
        return [ 'label' => $label, 'status' => 'fail', 'value' => $value ];
    }
    private function skip( string $label, string $value ): array {
        return [ 'label' => $label, 'status' => 'skip', 'value' => $value ];
    }
}