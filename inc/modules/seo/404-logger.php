<?php
/**
 * SunLyvo Nexus — 404 日志
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 记录 404。
 *
 * @since 1.0.0
 */
function slv_seo_log_404(): void {
    if ( ! is_404() ) {
        return;
    }

    $url     = home_url( add_query_arg( [], $GLOBALS['wp']->request ) );
    $referer = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
    $ip      = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

    global $wpdb;
    $table = "{$wpdb->prefix}slv_seo_404_log";

    $existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE url = %s", $url ) );
    if ( $existing ) {
        $wpdb->query( $wpdb->prepare(
            "UPDATE {$table} SET hit_count = hit_count + 1, last_hit = NOW(), referer = %s WHERE id = %d",
            $referer, $existing
        ) );
    } else {
        $wpdb->insert( $table, [
            'url'        => $url,
            'referer'    => $referer,
            'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '',
            'ip_address' => $ip,
        ] );
    }
}
add_action( 'template_redirect', 'slv_seo_log_404' );