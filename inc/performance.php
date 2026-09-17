<?php
/**
 * SunLyvo Nexus — 性能优化模块
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 移除 emoji 脚本（减小请求体积）。
 *
 * @since 1.0.0
 */
function slv_disable_emojis(): void {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
}
add_action( 'init', 'slv_disable_emojis' );

/**
 * 移除 oEmbed 自动发现（可减少第三方请求）。
 *
 * @since 1.0.0
 */
function slv_disable_embeds(): void {
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );
}
add_action( 'init', 'slv_disable_embeds' );