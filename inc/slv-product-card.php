<?php
/**
 * SunLyvo Nexus — 商品卡片渲染 v2（内联 SVG，不依赖外部函数）
 *
 * @package SunLyvo_Nexus
 * @since 5.5.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* 占位图 URL */
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

/* 商品多图 */
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

/* 内联 SVG（不依赖 slv_icon 函数，确保图标必定输出） */
if ( ! function_exists( 'slv_svg_inline' ) ) {
    function slv_svg_inline( string $name, int $size = 16, string $class = '' ): string {
        $paths = [
            'cart'    => '<circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>',
            'package' => '<path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/>',
            'check'   => '<polyline points="20 6 9 17 4 12"/>',
            'x'       => '<path d="M18 6 6 18M6 6l12 12"/>',
            'chevron-down'  => '<path d="m6 9 6 6 6-6"/>',
            'chevron-right' => '<path d="m9 18 6-6-6-6"/>',
            'search'  => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
            'sun'     => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>',
            'moon'    => '<path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/>',
            'user'    => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
            'grid'    => '<rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/>',
            'list'    => '<line x1="8" x2="21" y1="6" y2="6"/><line x1="8" x2="21" y1="12" y2="12"/><line x1="8" x2="21" y1="18" y2="18"/><line x1="3" x2="3.01" y1="6" y2="6"/><line x1="3" x2="3.01" y1="12" y2="12"/><line x1="3" x2="3.01" y1="18" y2="18"/>',
        ];
        if ( ! isset( $paths[ $name ] ) ) {
            return '';
        }
        $class_attr = $class ? ' class="' . esc_attr( $class ) . '"' : '';
        return sprintf(
            '<svg%s width="%d" height="%d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%s</svg>',
            $class_attr,
            $size,
            $size,
            $paths[ $name ]
        );
    }
}

/* 商品卡片 */
if ( ! function_exists( 'slv_render_product_card' ) ) {
    function slv_render_product_card( int $post_id, bool $mini = false ): string {
        $title     = get_the_title( $post_id );
        $permalink = get_permalink( $post_id );
        $excerpt   = get_the_excerpt( $post_id );
        $price     = get_post_meta( $post_id, '_slv_price', true );
        $price     = ( $price === '' || $price === false ) ? '0.00' : number_format( (float) $price, 2, '.', '' );

        $images = slv_get_product_images( $post_id );

        ob_start();
        ?>
        <article class="slv-product-card" data-product-id="<?php echo (int) $post_id; ?>">
            <a href="<?php echo esc_url( $permalink ); ?>" class="slv-product-card__media" aria-label="<?php echo esc_attr( $title ); ?>">
                <?php if ( ! empty( $images ) ) : ?>
                    <?php foreach ( $images as $i => $img_id ) : ?>
                        <?php echo wp_get_attachment_image( $img_id, 'medium', false, [
                            'loading' => $i === 0 ? 'eager' : 'lazy',
                            'class'   => $i > 0 ? 'slv-product-card__img-secondary' : 'slv-product-card__img-primary',
                            'alt'     => $title,
                        ] ); ?>
                    <?php endforeach; ?>
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
                        <button type="button" class="slv-product-card__cart-btn" data-slv-quick-add="<?php echo (int) $post_id; ?>" aria-label="加入购物车">
                            <?php echo slv_svg_inline( 'cart', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </article>
        <?php
        return (string) ob_get_clean();
    }
}