<?php
/**
 * SunLyvo Nexus — robots.txt
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 自定义 robots.txt 内容。
 *
 * @since 1.0.0
 */
function slv_seo_robots_txt( string $output, bool $public ): string {
    $output  = "User-agent: *\n";
    $output .= "Disallow: /wp-admin/\n";
    $output .= "Allow: /wp-admin/admin-ajax.php\n";
    $output .= "Disallow: /cart/\n";
    $output .= "Disallow: /checkout/\n";
    $output .= "Disallow: /my-account/\n";
    $output .= "Disallow: /?s=\n";
    $output .= "Disallow: /*?add-to-cart=\n\n";

    $output .= "# AI Citation Crawlers\n";
    $ai_crawlers = [
        'OAI-SearchBot', 'ChatGPT-User',
        'PerplexityBot', 'Perplexity-User',
        'ClaudeBot', 'Claude-Web',
        'Google-Extended', 'Applebot-Extended',
        'cohere-ai', 'YouBot', 'DuckAssistBot',
        'QwenBot', 'TongyiBot', 'Bytespider',
        'GPTBot', 'CCBot', 'anthropic-ai',
    ];
    foreach ( $ai_crawlers as $bot ) {
        $output .= "User-agent: {$bot}\nAllow: /\n\n";
    }

    $output .= "Sitemap: " . home_url( '/slv-sitemap.xml' ) . "\n";
    $output .= "Sitemap: " . home_url( '/llms.txt' ) . "\n";

    return $output;
}
add_filter( 'robots_txt', 'slv_seo_robots_txt', 10, 2 );