<?php
/**
 * SunLyvo Nexus — 全站头部（PHP 经典模板）
 *
 * FSE 模板会自动 fallback 到这里
 *
 * @package SunLyvo_Nexus
 * @since 5.3.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current_url = home_url( add_query_arg( [], $GLOBALS['wp']->request ?? '' ) );
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

        <!-- 品牌 -->
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

        <!-- 导航 -->
        <nav class="slv-header__nav" aria-label="<?php esc_attr_e( '主导航', 'sunlyvo-nexus' ); ?>">
            <?php
            $menu_items = [
                [ '首页',   '/' ],
                [ '博客',   '/blog/' ],
                [ '百科',   '/wiki/' ],
                [ 'FAQ',    '/faq/' ],
                [ '商品',   '/products/' ],
                [ '合集',   '/collections/' ],
            ];
            $current_path = trailingslashit( wp_parse_url( $current_url, PHP_URL_PATH ) ?: '/' );
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

        <!-- 操作 -->
        <div class="slv-header__actions">
            <button type="button" class="slv-header__icon-btn" aria-label="<?php esc_attr_e( '搜索', 'sunlyvo-nexus' ); ?>" data-slv-search-toggle>
                <?php echo slv_icon( 'search', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </button>
            <button type="button" class="slv-header__icon-btn slv-theme-toggle" aria-label="<?php esc_attr_e( '切换主题', 'sunlyvo-nexus' ); ?>" data-slv-theme-toggle>
                <span class="slv-theme-toggle__icon slv-theme-toggle__icon--light">
                    <?php echo slv_icon( 'sun', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </span>
                <span class="slv-theme-toggle__icon slv-theme-toggle__icon--dark">
                    <?php echo slv_icon( 'moon', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </span>
            </button>
            <a href="<?php echo esc_url( home_url( '/cart/' ) ); ?>" class="slv-header__icon-btn" aria-label="<?php esc_attr_e( '购物车', 'sunlyvo-nexus' ); ?>">
                <?php echo slv_icon( 'cart', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <span class="slv-header__cart-count" data-slv-cart-count></span>
            </a>
            <a href="<?php echo esc_url( home_url( '/my-account/' ) ); ?>" class="slv-header__icon-btn" aria-label="<?php esc_attr_e( '我的账户', 'sunlyvo-nexus' ); ?>">
                <?php echo slv_icon( 'user', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </a>
        </div>

    </div>
</header>