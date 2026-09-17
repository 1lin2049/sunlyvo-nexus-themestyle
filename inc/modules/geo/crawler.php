<?php
/**
 * SunLyvo Nexus — AI 爬虫管理
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * AI 爬虫 UA 映射表。
 *
 * @since 1.0.0
 * @return array
 */
function slv_geo_crawler_map(): array {
    return [
        'OAI-SearchBot', 'ChatGPT-User',
        'PerplexityBot', 'Perplexity-User',
        'ClaudeBot', 'Claude-Web',
        'Google-Extended', 'Applebot-Extended',
        'cohere-ai', 'YouBot', 'DuckAssistBot',
        'GPTBot', 'CCBot', 'anthropic-ai',
        'QwenBot', 'TongyiBot', 'Bytespider',
    ];
}

/**
 * 检测当前请求是否为 AI 爬虫。
 *
 * @since 1.0.0
 * @return string|null
 */
function slv_geo_detect_crawler(): ?string {
    $ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
    if ( '' === $ua ) {
        return null;
    }
    foreach ( slv_geo_crawler_map() as $crawler ) {
        if ( false !== stripos( $ua, $crawler ) ) {
            return $crawler;
        }
    }
    return null;
}

/**
 * 记录 AI 爬虫访问。
 *
 * @since 1.0.0
 */
function slv_geo_log_crawler(): void {
    $crawler = slv_geo_detect_crawler();
    if ( ! $crawler ) {
        return;
    }

    $url = home_url( add_query_arg( [], $GLOBALS['wp']->request ) );
    $ua  = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
    $ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

    global $wpdb;
    $table = "{$wpdb->prefix}slv_geo_crawlers";

    $existing = $wpdb->get_var( $wpdb->prepare(
        "SELECT id FROM {$table} WHERE crawler_name = %s AND url = %s",
        $crawler, $url
    ) );

    if ( $existing ) {
        $wpdb->query( $wpdb->prepare(
            "UPDATE {$table} SET hit_count = hit_count + 1, last_hit = NOW() WHERE id = %d",
            $existing
        ) );
    } else {
        $wpdb->insert( $table, [
            'crawler_name' => $crawler,
            'user_agent'   => $ua,
            'url'          => $url,
            'ip_address'   => $ip,
        ] );
    }
}
add_action( 'template_redirect', 'slv_geo_log_crawler' );