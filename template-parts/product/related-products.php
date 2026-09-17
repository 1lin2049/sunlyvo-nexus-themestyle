<?php
/**
 * 商品详情页 - 相关商品列表
 *
 * @package SunLyvo_Nexus
 */

$product_id = get_the_ID();

// 获取当前商品分类
$terms = wp_get_post_terms( $product_id, 'product_cat', [ 'fields' => 'ids' ] );

$args = [
    'post_type'      => 'product',
    'posts_per_page' => 4,
    'post__not_in'   => [ $product_id ],
    'orderby'        => 'rand',
];

if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
    $args['tax_query'] = [
        [
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $terms,
        ],
    ];
}

$related_query = new WP_Query( $args );

if ( $related_query->have_posts() ) : ?>
    <div class="slv-related-products">
        <div class="slv-product-detail__container">
            <h2 class="slv-section__title"><?php esc_html_e( '相关商品', 'sunlyvo-nexus' ); ?></h2>
            <div class="slv-related-products__grid">
                <?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
                    <article class="slv-product-card">
                        <a href="<?php the_permalink(); ?>" class="slv-product-card__link">
                            <div class="slv-product-card__media">
                                <?php the_post_thumbnail( 'medium', [ 'loading' => 'lazy' ] ); ?>
                            </div>
                            <div class="slv-product-card__body">
                                <h3><?php the_title(); ?></h3>
                            </div>
                        </a>
                    </article>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
<?php
endif;

// 关键：重置主查询的 post 数据
wp_reset_postdata();