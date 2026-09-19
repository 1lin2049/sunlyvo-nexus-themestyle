<?php
/**
 * SunLyvo Nexus — 全站尾部（PHP 经典模板）
 *
 * @package SunLyvo_Nexus
 * @since 5.3.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<footer class="slv-footer" role="contentinfo">
    <div class="slv-footer__inner">
        <div class="slv-footer__cols">

            <!-- 品牌列 -->
            <div class="slv-footer__brand">
                <div class="slv-footer__brand-logo">
                    <?php
                    $logo_id = (int) get_theme_mod( 'custom_logo' );
                    if ( $logo_id ) {
                        $logo_url = wp_get_attachment_image_url( $logo_id, 'medium' );
                        if ( $logo_url ) {
                            printf(
                                '<img src="%s" alt="%s" width="28" height="28" />',
                                esc_url( $logo_url ),
                                esc_attr( get_bloginfo( 'name' ) )
                            );
                        }
                    }
                    ?>
                    <h3 class="slv-footer__brand-name"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h3>
                </div>
                <p class="slv-footer__brand-desc">内容电商 · 知识付费 · AI 代理商务</p>
                <div class="slv-footer__social">
                    <?php
                    $social = [
                        'twitter'  => 'https://twitter.com/',
                        'linkedin' => 'https://linkedin.com/',
                        'github'   => 'https://github.com/',
                        'rss'      => get_bloginfo( 'rss2_url' ),
                    ];
                    $social_icons = [
                        'twitter'  => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>',
                        'linkedin' => '<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>',
                        'github'   => '<path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/>',
                        'rss'      => '<path d="M6.503 20.752A2.25 2.25 0 114.253 18.502a2.25 2.25 0 012.25 2.25zM4.25 10.5v3.001a7.5 7.5 0 016 6h3.001c0-4.971-4.03-9-9.001-9zM4.25 4.5v3.001c6.628 0 12 5.372 12 12h3c0-8.284-6.716-15-15-15z"/>',
                    ];
                    foreach ( $social as $key => $url ) {
                        printf(
                            '<a href="%s" class="slv-footer__social-link" aria-label="%s" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">%s</svg></a>',
                            esc_url( $url ),
                            esc_attr( $key ),
                            $social_icons[ $key ]
                        );
                    }
                    ?>
                </div>
            </div>

            <!-- 产品列 -->
            <div class="slv-footer__col">
                <h4><?php esc_html_e( '产品', 'sunlyvo-nexus' ); ?></h4>
                <ul>
                    <li><a href="<?php echo esc_url( home_url( '/products/' ) ); ?>">商品</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/collections/' ) ); ?>">合集</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/wiki/' ) ); ?>">百科</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">FAQ</a></li>
                </ul>
            </div>

            <!-- 关于列 -->
            <div class="slv-footer__col">
                <h4><?php esc_html_e( '关于', 'sunlyvo-nexus' ); ?></h4>
                <ul>
                    <li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">关于我们</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">联系我们</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>">隐私政策</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>">服务条款</a></li>
                </ul>
            </div>

            <!-- 订阅列 -->
            <div class="slv-footer__col">
                <h4><?php esc_html_e( '订阅', 'sunlyvo-nexus' ); ?></h4>
                <p class="slv-footer__brand-desc" style="margin:0 0 12px;font-size:12px;">获取最新内容与产品更新</p>
                <form class="slv-footer__subscribe-form" data-slv-subscribe>
                    <input type="email" name="email" placeholder="your@email.com" required aria-label="<?php esc_attr_e( '邮箱', 'sunlyvo-nexus' ); ?>" />
                    <button type="submit" aria-label="<?php esc_attr_e( '订阅', 'sunlyvo-nexus' ); ?>">
                        <?php echo slv_icon( 'arrow-right', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </button>
                </form>
            </div>

        </div>

        <div class="slv-footer__bottom">
            <span>© <?php echo esc_html( date( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?> · 李咏燊</span>
            <div class="slv-footer__bottom-links">
                <a href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>">隐私</a>
                <a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>">条款</a>
                <a href="<?php echo esc_url( home_url( '/sitemap.xml' ) ); ?>">Sitemap</a>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>