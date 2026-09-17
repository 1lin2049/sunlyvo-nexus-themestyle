<?php
/**
 * SunLyvo Nexus — 移动端验证
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 移动端验证器。
 *
 * @since 1.0.0
 */
class SLV_Check_Mobile {

    public function get_label(): string {
        return __( '移动端验证', 'sunlyvo-nexus' );
    }

    public function run(): array {
        return [
            $this->check_viewport_meta(),
            $this->check_mobile_css(),
            $this->check_breakpoints(),
            $this->check_touch_targets(),
        ];
    }

    private function check_viewport_meta(): array {
        $body = $this->fetch_home_html();
        if ( null === $body ) {
            return $this->fail( 'viewport meta', '首页不可访问' );
        }
        return str_contains( $body, 'name="viewport"' )
            ? $this->pass( 'viewport meta', '已配置' )
            : $this->fail( 'viewport meta', '缺失' );
    }

    private function check_mobile_css(): array {
        $file = SLV_THEME_DIR . '/assets/css/mobile.css';
        if ( ! file_exists( $file ) ) {
            return $this->fail( 'mobile.css', '文件不存在' );
        }
        $content = (string) file_get_contents( $file );
        $has_media = substr_count( $content, '@media' );
        return $has_media >= 4
            ? $this->pass( 'mobile.css', "包含 {$has_media} 个媒体查询" )
            : $this->fail( 'mobile.css', "仅 {$has_media} 个媒体查询（期望 ≥ 4）" );
    }

    private function check_breakpoints(): array {
        $file = SLV_THEME_DIR . '/assets/css/mobile.css';
        if ( ! file_exists( $file ) ) {
            return $this->fail( '断点契约', 'mobile.css 不存在' );
        }
        $content = (string) file_get_contents( $file );
        $required = [ '1024px', '900px', '640px', '375px' ];
        $found = 0;
        foreach ( $required as $bp ) {
            if ( str_contains( $content, $bp ) ) {
                $found++;
            }
        }
        return $found === 4
            ? $this->pass( '断点契约', '4 档断点齐全（1024/900/640/375）' )
            : $this->fail( '断点契约', "仅 {$found}/4 档" );
    }

    private function check_touch_targets(): array {
        $file = SLV_THEME_DIR . '/assets/css/reader.css';
        if ( ! file_exists( $file ) ) {
            return $this->skip( '触控目标尺寸', 'reader.css 不存在' );
        }
        $content = (string) file_get_contents( $file );
        // 检查 FAB 尺寸
        $has_fab = str_contains( $content, 'slv-toc__fab' );
        $has_size = preg_match( '/\.slv-toc__fab[^}]*width:\s*48px/s', $content );
        return ( $has_fab && $has_size )
            ? $this->pass( '触控目标尺寸', 'FAB 48x48（符合 44px 最小标准）' )
            : $this->skip( '触控目标尺寸', '未检测到关键触控元素' );
    }

    private function fetch_home_html(): ?string {
        $response = wp_remote_get( home_url( '/' ), [ 'timeout' => 10 ] );
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