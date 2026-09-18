<?php
/**
 * SunLyvo Nexus — 商品列表数量动态填充
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_footer', static function () {
    // 仅在商品归档/分类/标签页注入
    if ( ! is_post_type_archive( 'product' ) && ! is_tax( [ 'product_cat', 'product_tag' ] ) ) {
        return;
    }

    // 当前查询的总数
    global $wp_query;
    $total = (int) $wp_query->found_posts;

    // 或使用独立的 count 查询（更准确）
    if ( $total === 0 ) {
        $counts = wp_count_posts( 'product' );
        $total  = (int) ( $counts->publish ?? 0 );
    }
    ?>
    <script>
    (function() {
        var total = <?php echo wp_json_encode( $total ); ?>;
        document.querySelectorAll('[data-slv-total]').forEach(function(el) {
            el.textContent = total;
        });
    })();
    </script>
    <?php
}, 50 );