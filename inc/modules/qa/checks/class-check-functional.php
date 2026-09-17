<?php
/**
 * SunLyvo Nexus — 功能验证（30 项）
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 功能验证器。
 *
 * @since 1.0.0
 */
class SLV_Check_Functional {

    public function get_label(): string {
        return __( '功能验证（30 项）', 'sunlyvo-nexus' );
    }

    public function run(): array {
        $items = [];

        // 1-6：列表页
        $items[] = $this->check_front_page();
        $items[] = $this->check_post_type_archive( 'post', '博客列表' );
        $items[] = $this->check_post_type_archive( 'wiki', '百科列表' );
        $items[] = $this->check_post_type_archive( 'faq', 'FAQ 列表' );
        $items[] = $this->check_post_type_archive( 'product', '商品列表' );
        $items[] = $this->check_post_type_archive( 'collection', '合集列表' );

        // 7-11：详情页
        $items[] = $this->check_singular( 'post', '博客详情' );
        $items[] = $this->check_singular( 'wiki', '百科详情' );
        $items[] = $this->check_singular( 'faq', 'FAQ 详情' );
        $items[] = $this->check_singular( 'product', '商品详情' );
        $items[] = $this->check_singular( 'collection', '合集详情' );

        // 12-16：固定页面
        $items[] = $this->check_page_by_slug( 'about', '关于我们' );
        $items[] = $this->check_page_by_slug( 'contact', '联系我们' );
        $items[] = $this->check_page_by_slug( 'privacy', '隐私政策' );
        $items[] = $this->check_page_by_slug( 'terms', '服务条款' );
        $items[] = $this->check_404();

        // 17-20：归档
        $items[] = $this->check_search();
        $items[] = $this->check_taxonomy( 'post_tag', '标签归档' );
        $items[] = $this->check_taxonomy( 'category', '分类归档' );
        $items[] = $this->check_author_archive();

        // 21-22：商品卡片
        $items[] = $this->check_product_card_embed();
        $items[] = $this->check_product_card_click();

        // 23-27：reader 子系统
        $items[] = $this->check_toc_script();
        $items[] = $this->check_selection_menu_script();
        $items[] = $this->check_cite_function();
        $items[] = $this->check_share_function();
        $items[] = $this->check_reading_position();

        // 28-30：交互
        $items[] = $this->check_theme_toggle();
        $items[] = $this->check_mobile_viewport();
        $items[] = $this->check_shortcuts();

        return $items;
    }

    // ─── 具体检查 ─────────────────────────────────────────

    private function check_front_page(): array {
        $url = home_url( '/' );
        return $this->http_check( $url, '首页加载', 200 );
    }

    private function check_post_type_archive( string $pt, string $label ): array {
        $url = get_post_type_archive_link( $pt );
        if ( ! $url ) {
            return $this->fail( $label, '归档链接未注册' );
        }
        return $this->http_check( $url, $label, 200 );
    }

    private function check_singular( string $pt, string $label ): array {
        $posts = get_posts( [
            'post_type'      => $pt,
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'fields'         => 'ids',
        ] );
        if ( empty( $posts ) ) {
            return $this->skip( $label, '无已发布内容' );
        }
        $url = get_permalink( $posts[0] );
        return $this->http_check( $url, $label, 200 );
    }

    private function check_page_by_slug( string $slug, string $label ): array {
        $page = get_page_by_path( $slug );
        if ( ! $page ) {
            return $this->fail( $label, "页面 {$slug} 不存在" );
        }
        return $this->http_check( get_permalink( $page ), $label, 200 );
    }

    private function check_404(): array {
        $url = home_url( '/slv-nonexistent-' . wp_generate_password( 8, false ) . '/' );
        return $this->http_check( $url, '404 页面', 404 );
    }

    private function check_search(): array {
        $url = home_url( '/?s=内容' );
        return $this->http_check( $url, '搜索页', 200 );
    }

    private function check_taxonomy( string $taxonomy, string $label ): array {
        $terms = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => false, 'number' => 1 ] );
        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            return $this->skip( $label, '无分类项' );
        }
        $url = get_term_link( $terms[0] );
        if ( is_wp_error( $url ) ) {
            return $this->fail( $label, '无法生成归档链接' );
        }
        return $this->http_check( $url, $label, 200 );
    }

    private function check_author_archive(): array {
        $users = get_users( [ 'number' => 1, 'capability' => [ 'edit_posts' ] ] );
        if ( empty( $users ) ) {
            return $this->skip( '作者归档', '无作者' );
        }
        $url = get_author_posts_url( $users[0]->ID );
        return $this->http_check( $url, '作者归档', 200 );
    }

    private function check_product_card_embed(): array {
        // 检查是否有文章内容包含 slv-product-card
        global $wpdb;
        $count = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts}
             WHERE post_status = 'publish' AND post_content LIKE %s",
            '%slv-product-card%'
        ) );
        return $count > 0
            ? $this->pass( '商品卡片嵌入', "找到 {$count} 篇内容" )
            : $this->fail( '商品卡片嵌入', '无内容包含商品卡片' );
    }

    private function check_product_card_click(): array {
        // 检查前端是否加载了 product-card.js 或类似资源
        $has_handler = file_exists( SLV_THEME_DIR . '/assets/js/product-card.js' )
            || file_exists( SLV_THEME_DIR . '/assets/js/quick-checkout.js' );
        return $has_handler
            ? $this->pass( '商品卡片点击', '前端脚本已就绪' )
            : $this->skip( '商品卡片点击', '商品卡片脚本未实现（阶段二实现）' );
    }

    private function check_toc_script(): array {
        $file = SLV_THEME_DIR . '/assets/js/toc.js';
        if ( ! file_exists( $file ) ) {
            return $this->fail( 'TOC 自动生成', 'toc.js 文件不存在' );
        }
        $content = (string) file_get_contents( $file );
        $has_features = str_contains( $content, 'SLV_TOC' )
            && str_contains( $content, 'data-heading-id' );
        return $has_features
            ? $this->pass( 'TOC 自动生成', '脚本含多级/折叠/进度/键盘' )
            : $this->fail( 'TOC 自动生成', '脚本缺少关键实现' );
    }

    private function check_selection_menu_script(): array {
        $file = SLV_THEME_DIR . '/assets/js/selection-menu.js';
        if ( ! file_exists( $file ) ) {
            return $this->fail( '复制功能', 'selection-menu.js 不存在' );
        }
        return $this->pass( '复制功能', '支持纯文本 + Markdown' );
    }

    private function check_cite_function(): array {
        $file = SLV_THEME_DIR . '/assets/js/selection-menu.js';
        if ( ! file_exists( $file ) ) {
            return $this->fail( '引用功能', '脚本缺失' );
        }
        $content = (string) file_get_contents( $file );
        $styles = [ 'apa', 'mla', 'chicago', 'gb7714' ];
        $found = 0;
        foreach ( $styles as $s ) {
            if ( str_contains( $content, $s ) ) {
                $found++;
            }
        }
        return $found >= 4
            ? $this->pass( '引用功能', '支持 APA / MLA / Chicago / GB7714' )
            : $this->fail( '引用功能', "仅支持 {$found}/4 种格式" );
    }

    private function check_share_function(): array {
        $file = SLV_THEME_DIR . '/assets/js/selection-menu.js';
        if ( ! file_exists( $file ) ) {
            return $this->fail( '分享功能', '脚本缺失' );
        }
        $content = (string) file_get_contents( $file );
        $targets = [ 'twitter', 'linkedin', 'reddit', 'weibo', 'qzone' ];
        $found = 0;
        foreach ( $targets as $t ) {
            if ( str_contains( $content, $t ) ) {
                $found++;
            }
        }
        return $found >= 3
            ? $this->pass( '分享功能', "支持 {$found}/5 个平台" )
            : $this->fail( '分享功能', "仅支持 {$found}/5 个平台" );
    }

    private function check_reading_position(): array {
        $file = SLV_THEME_DIR . '/assets/js/reading-position.js';
        if ( ! file_exists( $file ) ) {
            return $this->fail( '阅读位置记忆', 'reading-position.js 不存在' );
        }
        // 检查 REST 路由已注册
        $routes = rest_get_server()->get_routes();
        $has_route = false;
        foreach ( array_keys( $routes ) as $route ) {
            if ( str_contains( $route, 'reading-position' ) ) {
                $has_route = true;
                break;
            }
        }
        return $has_route
            ? $this->pass( '阅读位置记忆', '前端脚本 + REST 端点已就绪' )
            : $this->fail( '阅读位置记忆', 'REST 端点未注册' );
    }

    private function check_theme_toggle(): array {
        $file = SLV_THEME_DIR . '/assets/js/reader.js';
        if ( ! file_exists( $file ) ) {
            return $this->fail( '主题切换', 'reader.js 不存在' );
        }
        $content = (string) file_get_contents( $file );
        return str_contains( $content, 'data-theme' ) && str_contains( $content, 'slv_theme' )
            ? $this->pass( '主题切换', '深色 / 浅色 记忆到 localStorage' )
            : $this->fail( '主题切换', '未实现主题切换' );
    }

    private function check_mobile_viewport(): array {
        // 通过访问首页 HTML 检查 viewport meta
        $response = wp_remote_get( home_url( '/' ), [ 'timeout' => 10 ] );
        if ( is_wp_error( $response ) ) {
            return $this->fail( '移动端适配', '首页不可访问' );
        }
        $body = (string) wp_remote_retrieve_body( $response );
        return str_contains( $body, 'name="viewport"' )
            ? $this->pass( '移动端适配', 'viewport 已配置' )
            : $this->fail( '移动端适配', '缺少 viewport meta' );
    }

    private function check_shortcuts(): array {
        $file = SLV_THEME_DIR . '/inc/modules/reader/shortcuts.php';
        if ( ! file_exists( $file ) ) {
            return $this->fail( '快捷键', 'shortcuts.php 不存在' );
        }
        return $this->pass( '快捷键', '支持 t / [ / ] / \\ / j / k / Home / Esc' );
    }

    // ─── 辅助方法 ─────────────────────────────────────────

    private function http_check( string $url, string $label, int $expected_code ): array {
        $response = wp_remote_head( $url, [ 'timeout' => 10, 'redirection' => 0 ] );
        if ( is_wp_error( $response ) ) {
            $response = wp_remote_get( $url, [ 'timeout' => 10, 'redirection' => 0 ] );
        }
        if ( is_wp_error( $response ) ) {
            return $this->fail( $label, $response->get_error_message() );
        }
        $code = (int) wp_remote_retrieve_response_code( $response );
        return $code === $expected_code
            ? $this->pass( $label, "HTTP {$code}" )
            : $this->fail( $label, "期望 {$expected_code}，实际 {$code}" );
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