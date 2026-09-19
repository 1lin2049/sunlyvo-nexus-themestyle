<?php
/**
 * SunLyvo Nexus — 商品详情（PHP 经典模板）
 *
 * @package SunLyvo_Nexus
 * @since 5.3.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

while ( have_posts() ) :
    the_post();

    $post_id = get_the_ID();
    $price   = get_post_meta( $post_id, '_slv_price', true );
    $price   = ( $price === '' || $price === false ) ? '0.00' : number_format( (float) $price, 2, '.', '' );
    $sku     = (string) get_post_meta( $post_id, '_slv_sku', true );
    $stock   = (int) get_post_meta( $post_id, '_slv_stock', true );

    $images = function_exists( 'slv_get_product_images' ) ? slv_get_product_images( $post_id ) : [];
?>

<main class="slv-product-detail">

    <div class="slv-product-detail__breadcrumb">
        <nav class="slv-breadcrumb" aria-label="面包屑">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">首页</a>
            <span aria-hidden="true">/</span>
            <a href="<?php echo esc_url( home_url( '/products/' ) ); ?>">商品</a>
            <span aria-hidden="true">/</span>
            <span><?php the_title(); ?></span>
        </nav>
    </div>

    <div class="slv-product-detail__grid">

        <!-- 图册 -->
        <div class="slv-product-gallery" data-slv-gallery>
            <div class="slv-product-gallery__main" data-slv-gallery-main>
                <?php if ( ! empty( $images ) ) : ?>
                    <?php echo wp_get_attachment_image( $images[0], 'large', false, [ 'alt' => get_the_title() ] ); ?>
                <?php else : ?>
                    <?php
                    $placeholder = function_exists( 'slv_get_product_placeholder_url' ) ? slv_get_product_placeholder_url() : '';
                    if ( $placeholder ) :
                    ?>
                        <div class="slv-product-card__placeholder">
                            <img src="<?php echo esc_url( $placeholder ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <?php if ( count( $images ) > 1 ) : ?>
                <div class="slv-product-gallery__thumbs" data-slv-gallery-thumbs>
                    <?php foreach ( $images as $i => $img_id ) : ?>
                        <?php
                        $thumb_url = wp_get_attachment_image_url( $img_id, 'thumbnail' );
                        $full_url  = wp_get_attachment_image_url( $img_id, 'large' );
                        if ( ! $thumb_url || ! $full_url ) continue;
                        ?>
                        <button type="button"
                                class="slv-product-gallery__thumb<?php echo $i === 0 ? ' is-active' : ''; ?>"
                                data-slv-thumb
                                data-full-url="<?php echo esc_url( $full_url ); ?>">
                            <img src="<?php echo esc_url( $thumb_url ); ?>" alt="" />
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- 信息 -->
        <div class="slv-product-info">
            <div class="slv-product-info__cats">
                <?php
                $cats = get_the_terms( $post_id, 'product_cat' );
                if ( $cats && ! is_wp_error( $cats ) ) {
                    $links = [];
                    foreach ( $cats as $cat ) {
                        $links[] = sprintf(
                            '<a href="%s">%s</a>',
                            esc_url( get_term_link( $cat ) ),
                            esc_html( $cat->name )
                        );
                    }
                    echo implode( ' · ', $links ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                }
                ?>
            </div>

            <h1 class="slv-product-info__title"><?php the_title(); ?></h1>

            <?php if ( has_excerpt() ) : ?>
                <p class="slv-product-info__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
            <?php endif; ?>

            <div class="slv-product-info__price-wrap">
                <span class="slv-product-info__price">
                    <span class="slv-product-info__currency">¥</span>
                    <span data-slv-product-price><?php echo esc_html( $price ); ?></span>
                </span>
            </div>

            <div class="slv-product-info__specs">
                <div class="slv-product-info__spec">
                    <span class="slv-product-info__spec-label">SKU</span>
                    <span class="slv-product-info__spec-value"><?php echo esc_html( $sku !== '' ? $sku : '—' ); ?></span>
                </div>
                <div class="slv-product-info__spec">
                    <span class="slv-product-info__spec-label">库存</span>
                    <span class="slv-product-info__spec-value">
                        <?php echo $stock > 0 ? '有货' : '暂时无货'; ?>
                    </span>
                </div>
            </div>

            <div class="slv-product-info__actions">
                <button type="button" class="slv-button slv-button--secondary" data-slv-product-cart="<?php echo (int) $post_id; ?>">
                    加入购物车
                </button>
                <button type="button" class="slv-button" data-slv-product-buy="<?php echo (int) $post_id; ?>">
                    立即购买
                </button>
            </div>

            <div class="slv-product-info__perks">
                <div class="slv-product-info__perk">
                    <?php echo slv_icon( 'check', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <span>正品保障 · 假一赔十</span>
                </div>
                <div class="slv-product-info__perk">
                    <?php echo slv_icon( 'check', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <span>全球配送 · 7 天无理由退货</span>
                </div>
                <div class="slv-product-info__perk">
                    <?php echo slv_icon( 'check', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <span>安全支付 · Stripe / 微信 / 支付宝</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Tabs -->
    <div class="slv-product-tabs" data-slv-product-tabs>
        <div class="slv-product-tabs__nav">
            <button type="button" class="slv-product-tabs__tab is-active" data-slv-tab="desc">商品描述</button>
            <button type="button" class="slv-product-tabs__tab" data-slv-tab="reviews">用户评价</button>
        </div>

        <div class="slv-product-tabs__panel is-active" data-slv-panel="desc">
            <div class="slv-prose">
                <?php the_content(); ?>
            </div>
        </div>
        <div class="slv-product-tabs__panel" data-slv-panel="reviews">
            <div class="slv-prose">
                <p>暂无评价。</p>
            </div>
        </div>
    </div>

    <!-- 相关商品 -->
    <?php
    $related = new WP_Query( [
        'post_type'      => 'product',
        'posts_per_page' => 4,
        'post__not_in'   => [ $post_id ],
        'orderby'        => 'rand',
        'post_status'    => 'publish',
        'no_found_rows'  => true,
    ] );
    if ( $related->have_posts() ) :
    ?>
        <section class="slv-product-related">
            <h2 class="slv-product-related__title">相关商品</h2>
            <div class="slv-product-grid">
                <?php while ( $related->have_posts() ) : $related->the_post(); ?>
                    <?php echo slv_render_product_card( get_the_ID(), true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </section>
    <?php endif; ?>

</main>

<?php
endwhile;

get_footer();