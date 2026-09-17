<?php
/**
 * SunLyvo Nexus — 阅读位置记忆 REST API
 *
 * 端点：
 *   GET  /wp-json/slv/v1/reading-position/{post_id}
 *   POST /wp-json/slv/v1/reading-position/{post_id}
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 注册阅读位置 REST 路由。
 *
 * @since 1.0.0
 */
function slv_reader_register_position_routes(): void {
    register_rest_route( SLV_REST_NAMESPACE, '/reading-position/(?P<post_id>\d+)', [
        [
            'methods'             => 'GET',
            'callback'            => 'slv_reader_get_position',
            'permission_callback' => '__return_true',
            'args'                => [
                'post_id' => [ 'validate_callback' => 'is_numeric' ],
            ],
        ],
        [
            'methods'             => 'POST',
            'callback'            => 'slv_reader_save_position',
            'permission_callback' => '__return_true',
            'args'                => [
                'post_id'        => [ 'validate_callback' => 'is_numeric' ],
                'scroll_position'=> [ 'validate_callback' => 'is_numeric' ],
                'scroll_percent' => [ 'validate_callback' => 'is_numeric' ],
            ],
        ],
    ] );
}
add_action( 'rest_api_init', 'slv_reader_register_position_routes' );

/**
 * 读取阅读位置。
 *
 * @since 1.0.0
 * @param WP_REST_Request $request 请求对象。
 * @return WP_REST_Response
 */
function slv_reader_get_position( WP_REST_Request $request ): WP_REST_Response {
    $post_id = (int) $request['post_id'];
    $user_id = get_current_user_id();

    if ( ! $user_id ) {
        return new WP_REST_Response( [ 'position' => null, 'reason' => 'not_logged_in' ], 200 );
    }

    global $wpdb;
    $row = $wpdb->get_row( $wpdb->prepare(
        "SELECT scroll_position, scroll_percent, last_read_at
         FROM {$wpdb->prefix}slv_reading_position
         WHERE user_id = %d AND post_id = %d",
        $user_id, $post_id
    ) );

    if ( ! $row ) {
        return new WP_REST_Response( [ 'position' => null ], 200 );
    }

    return new WP_REST_Response( [
        'position' => [
            'scroll_position' => (int) $row->scroll_position,
            'scroll_percent'  => (float) $row->scroll_percent,
            'last_read_at'    => $row->last_read_at,
        ],
    ], 200 );
}

/**
 * 保存阅读位置。
 *
 * @since 1.0.0
 * @param WP_REST_Request $request 请求对象。
 * @return WP_REST_Response
 */
function slv_reader_save_position( WP_REST_Request $request ): WP_REST_Response {
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        return new WP_REST_Response( [ 'success' => false, 'reason' => 'not_logged_in' ], 200 );
    }

    $post_id   = (int) $request['post_id'];
    $position  = (int) $request['scroll_position'];
    $percent   = (float) $request['scroll_percent'];

    if ( $post_id <= 0 || $percent < 0 || $percent > 100 ) {
        return new WP_REST_Response( [ 'success' => false, 'reason' => 'invalid_params' ], 400 );
    }

    global $wpdb;
    $wpdb->replace( "{$wpdb->prefix}slv_reading_position", [
        'user_id'         => $user_id,
        'post_id'         => $post_id,
        'scroll_position' => $position,
        'scroll_percent'  => $percent,
        'last_read_at'    => current_time( 'mysql' ),
    ] );

    return new WP_REST_Response( [ 'success' => true ], 200 );
}

/**
 * 获取登录用户的"继续阅读"列表。
 *
 * @since 1.0.0
 * @param int $limit 数量。
 * @return array
 */
function slv_reader_get_continue_reading( int $limit = 5 ): array {
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        return [];
    }

    global $wpdb;
    $rows = $wpdb->get_results( $wpdb->prepare(
        "SELECT post_id, scroll_percent, last_read_at
         FROM {$wpdb->prefix}slv_reading_position
         WHERE user_id = %d AND scroll_percent BETWEEN 5 AND 99
         ORDER BY last_read_at DESC
         LIMIT %d",
        $user_id, $limit
    ) );

    $items = [];
    foreach ( (array) $rows as $row ) {
        $post = get_post( (int) $row->post_id );
        if ( ! $post || 'publish' !== $post->post_status ) {
            continue;
        }
        $items[] = [
            'post_id'        => (int) $row->post_id,
            'title'          => get_the_title( $post ),
            'permalink'      => get_permalink( $post ),
            'scroll_percent' => (float) $row->scroll_percent,
            'last_read_at'   => $row->last_read_at,
        ];
    }

    return $items;
}