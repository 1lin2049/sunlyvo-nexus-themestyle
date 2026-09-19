<?php
/**
 * SunLyvo Nexus — 商品卡片（Swiper 集成）
 *
 * @package SunLyvo_Nexus
 * @since 7.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'slv_get_product_placeholder_url' ) ) {
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
    function slv_get_product_images( int $post_id ): array {
        $images = [];
        $thumb_id = (int) get_post_thumbnail_id( $post_id );
        if ( $thumb_id ) $images[] = $thumb_id;

        $gallery = (string) get_post_meta( $post_id, '_slv_product_gallery', true );
        if ( $gallery !== '' ) {
            foreach ( array_filter( array_map( 'intval', explode( ',', $gallery ) ) ) as $id ) {
                if ( $id && $id !== $thumb_id ) $images[] = $id;
            }
        }
        return $images;
    }
}

if ( ! function_exists( 'slv_render_product_card' ) ) {
    function slv_render_product_card( int $post_id, bool $mini = false ): string {
        $title     = get_the_title( $post_id );
        $permalink = get_permalink( $post_id );
        $excerpt   = get_the_excerpt( $post_id );
        $price     = get_post_meta( $post_id, '_slv_price', true );
        $price     = ( $price === '' || $price === false ) ? '0.00' : number_format( (float) $price, 2, '.', '' );

        $images = slv_get_product_images( $post_id );
        $image_count = count( $images );
        $has_multiple = $image_count > 1;

        // Swiper 唯一 ID
        $swiper_id = 'slv-swiper-' . $post_id . '-' . wp_rand( 1000, 9999 );

        ob_start();
        ?>
        <article class="slv-product-card<?php echo $has_multiple ? ' has-multiple-images' : ''; ?>"
                 data-product-id="<?php echo (int) $post_id; ?>">

            <div class="slv-product-card__media-wrap">

                <?php if ( $image_count > 0 ) : ?>
                    <!-- Swiper 容器 -->
                    <div class="swiper slv-product-card__swiper" id="<?php echo esc_attr( $swiper_id ); ?>">
                        <div class="swiper-wrapper">
                            <?php foreach ( $images as $i => $img_id ) : ?>
                                <?php
                                $img_url = wp_get_attachment_image_url( $img_id, 'large' );
                                if ( ! $img_url ) continue;
                                ?>
                                <div class="swiper-slide slv-product-card__slide">
                                    <a href="<?php echo esc_url( $permalink ); ?>" class="slv-product-card__slide-link" aria-label="<?php echo esc_attr( $title ); ?>">
                                        <img src="<?php echo esc_url( $img_url ); ?>"
                                             alt="<?php echo esc_attr( $title ); ?>"
                                             loading="<?php echo $i === 0 ? 'eager' : 'lazy'; ?>" />
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if ( $has_multiple ) : ?>
                            <div class="swiper-pagination slv-product-card__pagination"></div>
                            <button type="button" class="swiper-button-prev slv-product-card__nav slv-product-card__nav--prev" aria-label="上一张"></button>
                            <button type="button" class="swiper-button-next slv-product-card__nav slv-product-card__nav--next" aria-label="下一张"></button>
                        <?php endif; ?>
                    </div>

                <?php else : ?>
                    <?php $placeholder = slv_get_product_placeholder_url(); ?>
                    <a href="<?php echo esc_url( $permalink ); ?>" class="slv-product-card__media" aria-label="<?php echo esc_attr( $title ); ?>">
                        <?php if ( $placeholder ) : ?>
                            <div class="slv-product-card__placeholder">
                                <img src="<?php echo esc_url( $placeholder ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" />
                            </div>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>

            </div>

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
                        <button type="button" class="slv-product-card__cart-btn" data-slv-quick-add="<?php echo (int) $post_id; ?>" aria-label="加入购物车">
                            <?php slv_icon_e( 'cart', 16 ); ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </article>
        <?php
        return (string) ob_get_clean();
    }
}