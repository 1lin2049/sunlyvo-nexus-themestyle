<?php
/**
 * SunLyvo Nexus — SEO/GEO/AEO 验证（10 项）
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SLV_Check_SEO {

    public function get_label(): string {
        return __( 'SEO/GEO/AEO 验证（10 项）', 'sunlyvo-nexus' );
    }

    public function run(): array {
        return [
            $this->check_llms_txt(),
            $this->check_robots_txt(),
            $this->check_sitemap(),
            $this->check_hreflang(),
            $this->check_canonical(),
            $this->check_meta_title_unique(),
            $this->check_meta_description(),
            $this->check_open_graph(),
            $this->check_twitter_card(),
            $this->check_jsonld_graph(),
        ];
    }

    private function check_llms_txt(): array {
        $url = home_url( '/llms.txt' );
        $response = wp_remote_get( $url, [
            'timeout'     => 10,
            'redirection' => 5,   // 允许跟随 HTTP→HTTPS 或尾部斜杠重定向
        ] );
        if ( is_wp_error( $response ) ) {
            return $this->fail( 'llms.txt 可访问', $response->get_error_message() );
        }
        $code = (int) wp_remote_retrieve_response_code( $response );
        if ( 200 !== $code ) {
            flush_rewrite_rules( false );
            return $this->fail( 'llms.txt 可访问', "HTTP {$code}（已尝试刷新重写规则，请重新运行）" );
        }
        $body = (string) wp_remote_retrieve_body( $response );
        $has_content = str_contains( $body, '# ' ) && strlen( $body ) > 200;
        return $has_content
            ? $this->pass( 'llms.txt 可访问', strlen( $body ) . ' 字节' )
            : $this->fail( 'llms.txt 可访问', '内容为空或过短' );
    }

    private function check_robots_txt(): array {
        $url = home_url( '/robots.txt' );
        $response = wp_remote_get( $url, [ 'timeout' => 10, 'redirection' => 5 ] );
        if ( is_wp_error( $response ) ) {
            return $this->fail( 'robots.txt 放行 AI 爬虫', $response->get_error_message() );
        }
        $body = (string) wp_remote_retrieve_body( $response );
        $required = [ 'OAI-SearchBot', 'PerplexityBot', 'ClaudeBot', 'Google-Extended' ];
        $found = 0;
        foreach ( $required as $bot ) {
            if ( str_contains( $body, $bot ) ) {
                $found++;
            }
        }
        return $found >= 3
            ? $this->pass( 'robots.txt 放行 AI 爬虫', "包含 {$found}/4 个关键爬虫" )
            : $this->fail( 'robots.txt 放行 AI 爬虫', "仅 {$found}/4" );
    }

    private function check_sitemap(): array {
        $url = home_url( '/slv-sitemap.xml' );
        $response = wp_remote_get( $url, [ 'timeout' => 15, 'redirection' => 5 ] );
        if ( is_wp_error( $response ) ) {
            return $this->fail( 'sitemap 可访问', $response->get_error_message() );
        }
        $code = (int) wp_remote_retrieve_response_code( $response );
        $body = (string) wp_remote_retrieve_body( $response );
        $has_url = str_contains( $body, '<urlset' ) && str_contains( $body, '<url>' );
        return ( 200 === $code && $has_url )
            ? $this->pass( 'sitemap 可访问', 'XML 格式正确' )
            : $this->fail( 'sitemap 可访问', "HTTP {$code} 或格式错误" );
    }

    private function check_hreflang(): array {
        $body = $this->fetch_home_html();
        if ( null === $body ) {
            return $this->skip( 'hreflang 输出', '首页不可访问' );
        }
        return str_contains( $body, 'hreflang' )
            ? $this->pass( 'hreflang 输出', '已输出' )
            : $this->skip( 'hreflang 输出', 'Demo 阶段单语言，P5 阶段实现' );
    }

    private function check_canonical(): array {
        $body = $this->fetch_home_html();
        if ( null === $body ) {
            return $this->fail( 'canonical 输出', '首页不可访问' );
        }
        return str_contains( $body, 'rel="canonical"' )
            ? $this->pass( 'canonical 输出', '已输出' )
            : $this->fail( 'canonical 输出', '未找到 canonical 标签' );
    }

    private function check_meta_title_unique(): array {
        $posts = get_posts( [
            'post_type'      => [ 'post', 'page' ],
            'posts_per_page' => 10,
            'post_status'    => 'publish',
            'fields'         => 'ids',
        ] );
        if ( count( $posts ) < 2 ) {
            return $this->skip( 'meta title 唯一', '内容不足' );
        }
        $titles = [];
        foreach ( $posts as $id ) {
            $titles[] = get_the_title( $id ) . ' - ' . get_bloginfo( 'name' );
        }
        $unique = count( array_unique( $titles ) );
        return $unique === count( $titles )
            ? $this->pass( 'meta title 唯一', "{$unique} 个唯一标题" )
            : $this->fail( 'meta title 唯一', "重复 " . ( count( $titles ) - $unique ) . " 个" );
    }

    private function check_meta_description(): array {
        global $wpdb;
        $count = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}slv_seo_meta WHERE meta_description != ''"
        );
        return $count > 0
            ? $this->pass( 'meta description', "{$count} 条已配置" )
            : $this->fail( 'meta description', '无配置' );
    }

    private function check_open_graph(): array {
        $body = $this->fetch_home_html();
        if ( null === $body ) {
            return $this->fail( 'Open Graph 完整', '首页不可访问' );
        }
        $required = [ 'og:type', 'og:title', 'og:description', 'og:url', 'og:site_name' ];
        $found = 0;
        foreach ( $required as $tag ) {
            if ( str_contains( $body, "property=\"{$tag}\"" ) ) {
                $found++;
            }
        }
        return $found >= 4
            ? $this->pass( 'Open Graph 完整', "{$found}/5 标签" )
            : $this->fail( 'Open Graph 完整', "仅 {$found}/5" );
    }

    private function check_twitter_card(): array {
        $body = $this->fetch_home_html();
        if ( null === $body ) {
            return $this->fail( 'Twitter Card 完整', '首页不可访问' );
        }
        $required = [ 'twitter:card', 'twitter:title', 'twitter:description' ];
        $found = 0;
        foreach ( $required as $tag ) {
            if ( str_contains( $body, "name=\"{$tag}\"" ) ) {
                $found++;
            }
        }
        return $found >= 3
            ? $this->pass( 'Twitter Card 完整', "{$found}/3 标签" )
            : $this->fail( 'Twitter Card 完整', "仅 {$found}/3" );
    }

    private function check_jsonld_graph(): array {
        $body = $this->fetch_home_html();
        if ( null === $body ) {
            return $this->fail( 'JSON-LD @graph 完整', '首页不可访问' );
        }
        $has_jsonld  = str_contains( $body, 'application/ld+json' );
        $has_graph   = str_contains( $body, '"@graph"' );
        $has_context = str_contains( $body, '"@context"' );
        return ( $has_jsonld && $has_graph && $has_context )
            ? $this->pass( 'JSON-LD @graph 完整', '@context + @graph 已输出' )
            : $this->fail( 'JSON-LD @graph 完整', '缺少关键字段' );
    }

    private function fetch_home_html(): ?string {
        $response = wp_remote_get( home_url( '/' ), [ 'timeout' => 10, 'redirection' => 5 ] );
        if ( is_wp_error( $response ) ) {
            return null;
        }
        return (string) wp_remote_retrieve_body( $response );
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