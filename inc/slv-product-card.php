<?php
/**
 * SunLyvo Nexus — 商品卡片渲染
 *
 * @package SunLyvo_Nexus
 * @since 5.1.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'slv_get_product_placeholder_url' ) ) {
    /**
     * 获取占位图 URL（站点 Logo → 站点 Icon → 主题默认）
     */
    function slv_get_product_placeholder_url(): string {
        $logo_id = (int) get_theme_mod( 'custom_logo' );
        if ( $logo_id > 0 ) {
            $url = wp_get_attachment_image_url( $logo_id, 'medium' );
            if ( $url ) return (string) $url;
        }
        $icon = get_site_icon_url( 512 );
        if ( $icon ) return (string) $icon;

        $default = get_template_directory() . '/assets/images/placeholder-product.svg';
        if ( file_exists( $default ) ) {
            return get_template_directory_uri() . '/assets/images/placeholder-product.svg';
        }
        return '';
    }
}

if ( ! function_exists( 'slv_get_product_images' ) ) {
    /**
     * 获取商品多图（主图 + 附加图）。
     */
    function slv_get_product_images( int $post_id ): array {
        $images = [];

        // 主图
        $thumb_id = (int) get_post_thumbnail_id( $post_id );
        if ( $thumb_id ) {
            $images[] = $thumb_id;
        }

        // 附加图（meta: _slv_product_gallery，逗号分隔的附件 ID）
        $gallery = (string) get_post_meta( $post_id, '_slv_product_gallery', true );
        if ( $gallery !== '' ) {
            $ids = array_filter( array_map( 'intval', explode( ',', $gallery ) ) );
            foreach ( $ids as $id ) {
                if ( $id && $id !== $thumb_id ) {
                    $images[] = $id;
                }
            }
        }

        return $images;
    }
}

if ( ! function_exists( 'slv_render_product_card' ) ) {
    /**
     * 渲染商品卡片 HTML。
     */
    function slv_render_product_card( int $post_id, bool $mini = false ): string {
        $title     = get_the_title( $post_id );
        $permalink = get_permalink( $post_id );
        $excerpt   = get_the_excerpt( $post_id );
        $price     = get_post_meta( $post_id, '_slv_price', true );

        if ( $price === '' || $price === false ) {
            $price = '0.00';
        } else {
            $price = number_format( (float) $price, 2, '.', '' );
        }

        // 多图
        $images = slv_get_product_images( $post_id );

        ob_start();
        ?>
        <article class="slv-product-card" data-product-id="<?php echo (int) $post_id; ?>">

            <a href="<?php echo esc_url( $permalink ); ?>" class="slv-product-card__media" aria-label="<?php echo esc_attr( $title ); ?>">
                <?php if ( ! empty( $images ) ) : ?>
                    <?php foreach ( $images as $i => $img_id ) : ?>
                        <?php
                        $img_html = wp_get_attachment_image( $img_id, 'medium', false, [
                            'loading' => $i === 0 ? 'eager' : 'lazy',
                            'class'   => $i > 0 ? 'slv-product-card__img-secondary' : 'slv-product-card__img-primary',
                            'alt'     => $title,
                        ] );
                        echo $img_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        ?>
                    <?php endforeach; ?>

                    <?php if ( count( $images ) > 1 ) : ?>
                        <div class="slv-product-card__dots" aria-hidden="true">
                            <?php foreach ( $images as $i => $img_id ) : ?>
                                <span class="slv-product-card__dot<?php echo $i === 0 ? ' is-active' : ''; ?>"></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                <?php else : ?>
                    <?php $placeholder = slv_get_product_placeholder_url(); ?>
                    <?php if ( $placeholder ) : ?>
                        <div class="slv-product-card__placeholder">
                            <img src="<?php echo esc_url( $placeholder ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" />
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </a>

            <div class="slv-product-card__body">
                <h3 class="slv-product-card__title">
                    <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
                </h3>

                <?php if ( ! $mini ) : ?>
                    <p class="slv-product-card__desc"><?php echo esc_html( wp_trim_words( $excerpt, 20 ) ); ?></p>
                <?php endif; ?>

                <div class="slv-product-card__footer">
                    <span class="slv-product-card__price">
                        <span class="slv-product-card__currency">¥</span>
                        <span class="slv-product-card__amount"><?php echo esc_html( $price ); ?></span>
                    </span>

                    <?php if ( ! $mini ) : ?>
                        <button type="button" class="slv-product-card__cart-btn" data-slv-quick-add="<?php echo (int) $post_id; ?>" aria-label="<?php esc_attr_e( '加入购物车', 'sunlyvo-nexus' ); ?>">
                            <?php echo slv_icon( 'cart', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </article>
        <?php
        return (string) ob_get_clean();
    }
}

/**
 * 商品列表 shortcode（继承主查询）
 */
if ( ! function_exists( 'slv_product_grid_shortcode' ) ) {
    function slv_product_grid_shortcode( $atts ): string {
        global $wp_query;

        if ( ! $wp_query->have_posts() ) {
            return '<div class="slv-product-empty">' . slv_icon( 'package', 64 ) . '<p>' . esc_html__( '暂无商品', 'sunlyvo-nexus' ) . '</p></div>';
        }

        ob_start();
        echo '<div class="slv-product-grid" data-slv-grid>';
        while ( $wp_query->have_posts() ) {
            $wp_query->the_post();
            echo slv_render_product_card( get_the_ID(), false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        echo '</div>';
        wp_reset_postdata();
        return (string) ob_get_clean();
    }
    add_shortcode( 'slv_product_grid', 'slv_product_grid_shortcode' );
}

/**
 * 推荐商品 shortcode（独立查询）
 */
if ( ! function_exists( 'slv_product_recommend_shortcode' ) ) {
    function slv_product_recommend_shortcode( $atts ): string {
        $query = new WP_Query( [
            'post_type'      => 'product',
            'posts_per_page' => 4,
            'orderby'        => 'rand',
            'post_status'    => 'publish',
            'no_found_rows'  => true,
        ] );

        if ( ! $query->have_posts() ) return '';

        ob_start();
        echo '<div class="slv-product-grid">';
        while ( $query->have_posts() ) {
            $query->the_post();
            echo slv_render_product_card( get_the_ID(), true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        echo '</div>';
        wp_reset_postdata();
        return (string) ob_get_clean();
    }
    add_shortcode( 'slv_product_recommend', 'slv_product_recommend_shortcode' );
}