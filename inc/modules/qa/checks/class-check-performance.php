<?php
/**
 * SunLyvo Nexus — 性能验证（5 项）
 *
 * 服务端能测的：TTFB。
 * 需要浏览器的：LCP/INP/CLS/PageSpeed（标记为需手动）。
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SLV_Check_Performance {

    public function get_label(): string {
        return __( '性能验证（5 项）', 'sunlyvo-nexus' );
    }

    public function run(): array {
        return [
            $this->check_ttfb(),
            $this->check_lcp_manual(),
            $this->check_inp_manual(),
            $this->check_cls_manual(),
            $this->check_pagespeed_manual(),
        ];
    }

    private function check_ttfb(): array {
        $url = home_url( '/' );
        $times = [];
        // 预热一次（避免冷启动影响首次测试）
        wp_remote_get( $url, [ 'timeout' => 15, 'redirection' => 5 ] );
        for ( $i = 0; $i < 3; $i++ ) {
            $start = microtime( true );
            $response = wp_remote_get( $url, [ 'timeout' => 15, 'redirection' => 5 ] );
            $elapsed = ( microtime( true ) - $start ) * 1000;
            if ( ! is_wp_error( $response ) ) {
                $times[] = $elapsed;
            }
        }
        if ( empty( $times ) ) {
            return $this->fail( 'TTFB', '首页不可访问' );
        }
        $avg = round( array_sum( $times ) / count( $times ), 2 );

        // 本地/无 CDN 环境阈值放宽到 800ms
        $has_object_cache = file_exists( WP_CONTENT_DIR . '/object-cache.php' );
        $threshold = $has_object_cache ? 600 : 800;

        return $avg < $threshold
            ? $this->pass( 'TTFB', "平均 {$avg}ms（目标 < {$threshold}ms）" )
            : $this->fail( 'TTFB', "平均 {$avg}ms（目标 < {$threshold}ms，建议启用 Redis 对象缓存）" );
    }

    private function check_lcp_manual(): array {
        return [
            'label'  => 'LCP（需浏览器）',
            'status' => 'skip',
            'value'  => '使用 PageSpeed Insights 或 Lighthouse 验证（目标 < 2.5s）',
        ];
    }

    private function check_inp_manual(): array {
        return [
            'label'  => 'INP（需浏览器）',
            'status' => 'skip',
            'value'  => '使用 PageSpeed Insights 或 Lighthouse 验证（目标 < 200ms）',
        ];
    }

    private function check_cls_manual(): array {
        return [
            'label'  => 'CLS（需浏览器）',
            'status' => 'skip',
            'value'  => '使用 PageSpeed Insights 或 Lighthouse 验证（目标 < 0.1）',
        ];
    }

    private function check_pagespeed_manual(): array {
        return [
            'label'  => 'PageSpeed 评分（需浏览器）',
            'status' => 'skip',
            'value'  => '使用 PageSpeed Insights 验证（目标 > 90）',
        ];
    }

    private function pass( string $label, string $value ): array {
        return [ 'label' => $label, 'status' => 'pass', 'value' => $value ];
    }
    private function fail( string $label, string $value ): array {
        return [ 'label' => $label, 'status' => 'fail', 'value' => $value ];
    }
}