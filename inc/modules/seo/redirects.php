<?php
/**
 * SunLyvo Nexus — 重定向
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 处理重定向规则。
 *
 * @since 1.0.0
 */
function slv_seo_handle_redirect(): void {
    if ( is_admin() || is_404() === false && is_singular() ) {
        return;
    }

    $current_url = home_url( add_query_arg( [], $GLOBALS['wp']->request ) );

    global $wpdb;
    $table = "{$wpdb->prefix}slv_seo_redirects";

    $redirect = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM {$table} WHERE source_url = %s AND is_active = 1",
        $current_url
    ) );

    if ( ! $redirect ) {
        return;
    }

    $wpdb->query( $wpdb->prepare( "UPDATE {$table} SET hit_count = hit_count + 1 WHERE id = %d", $redirect->id ) );

    $code = in_array( (int) $redirect->redirect_type, [ 301, 302, 307, 308 ], true ) ? (int) $redirect->redirect_type : 301;
    wp_safe_redirect( $redirect->target_url, $code );
    exit;
}
add_action( 'template_redirect', 'slv_seo_handle_redirect', 1 );