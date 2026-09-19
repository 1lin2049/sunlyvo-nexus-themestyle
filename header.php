<?php
/**
 * SunLyvo Nexus — 全站头部
 *
 * @package SunLyvo_Nexus
 * @since 6.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current_path = trailingslashit( wp_parse_url( $_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH ) ?: '/' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> data-theme="light">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="slv-header" role="banner">
    <div class="slv-header__inner">

        <div class="slv-header__brand">
            <?php
            $logo_id = (int) get_theme_mod( 'custom_logo' );
            if ( $logo_id ) {
                $logo_url = wp_get_attachment_image_url( $logo_id, 'medium' );
                if ( $logo_url ) {
                    printf(
                        '<a href="%s" class="slv-header__logo-link" aria-label="%s"><img src="%s" alt="%s" width="32" height="32" /></a>',
                        esc_url( home_url( '/' ) ),
                        esc_attr( get_bloginfo( 'name' ) ),
                        esc_url( $logo_url ),
                        esc_attr( get_bloginfo( 'name' ) )
                    );
                }
            }
            ?>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="slv-header__title">
                <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
            </a>
        </div>

        <nav class="slv-header__nav" aria-label="<?php esc_attr_e( '主导航', 'sunlyvo-nexus' ); ?>">
            <?php
            $menu_items = [
                [ '首页', '/' ],
                [ '博客', '/blog/' ],
                [ '百科', '/wiki/' ],
                [ 'FAQ',  '/faq/' ],
                [ '商品', '/products/' ],
                [ '合集', '/collections/' ],
            ];
            echo '<ul>';
            foreach ( $menu_items as $item ) {
                $label = $item[0];
                $url   = $item[1];
                $path  = trailingslashit( wp_parse_url( $url, PHP_URL_PATH ) ?: '/' );
                $is_current = ( $path === $current_path ) || ( $path !== '/' && strpos( $current_path, $path ) === 0 );
                printf(
                    '<li class="%s"><a href="%s">%s</a></li>',
                    $is_current ? 'is-current' : '',
                    esc_url( home_url( $url ) ),
                    esc_html( $label )
                );
            }
            echo '</ul>';
            ?>
        </nav>

        <div class="slv-header__actions">
            <button type="button" class="slv-header__icon-btn" aria-label="搜索" data-slv-search-toggle>
                <?php slv_icon_e( 'search', 18 ); ?>
            </button>
            <button type="button" class="slv-header__icon-btn slv-theme-toggle" aria-label="切换主题" data-slv-theme-toggle>
                <span class="slv-theme-toggle__icon slv-theme-toggle__icon--light">
                    <?php slv_icon_e( 'sun', 18 ); ?>
                </span>
                <span class="slv-theme-toggle__icon slv-theme-toggle__icon--dark">
                    <?php slv_icon_e( 'moon', 18 ); ?>
                </span>
            </button>
            <a href="<?php echo esc_url( home_url( '/cart/' ) ); ?>" class="slv-header__icon-btn" aria-label="购物车">
                <?php slv_icon_e( 'cart', 18 ); ?>
                <span class="slv-header__cart-count" data-slv-cart-count></span>
            </a>
            <a href="<?php echo esc_url( home_url( '/my-account/' ) ); ?>" class="slv-header__icon-btn" aria-label="我的账户">
                <?php slv_icon_e( 'user', 18 ); ?>
            </a>
        </div>

    </div>
</header>