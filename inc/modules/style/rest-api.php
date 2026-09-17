<?php
/**
 * SunLyvo Nexus — Style REST API
 *
 * 端点：
 *   GET  /wp-json/slv/v1/styles       获取所有风格
 *   POST /wp-json/slv/v1/user/style   保存用户偏好
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'rest_api_init', static function () {

    // ── GET /slv/v1/styles
    register_rest_route( SLV_REST_NAMESPACE, '/styles', [
        'methods'             => 'GET',
        'callback'            => static function ( WP_REST_Request $req ): WP_REST_Response {
            if ( ! class_exists( 'SLV_Style_Registry' ) || ! class_exists( 'SLV_Style_Resolver' ) ) {
                return new WP_REST_Response( [ 'error' => 'style_module_not_loaded' ], 500 );
            }

            $all     = SLV_Style_Registry::all();
            $current = SLV_Style_Resolver::resolve();

            $groups = [ 'base' => [], 'industry' => [], 'aux' => [] ];
            foreach ( $all as $slug => $meta ) {
                $groups[ $meta['group'] ][ $slug ] = [
                    'slug'      => $slug,
                    'label'     => $meta['label'],
                    'css'       => $meta['css'],
                    'is_active' => $slug === $current,
                ];
            }

            return new WP_REST_Response( [
                'current' => $current,
                'groups'  => $groups,
                'all'     => array_keys( $all ),
            ], 200 );
        },
        'permission_callback' => '__return_true',
    ] );

    // ── POST /slv/v1/user/style
    register_rest_route( SLV_REST_NAMESPACE, '/user/style', [
        'methods'             => 'POST',
        'callback'            => static function ( WP_REST_Request $req ): WP_REST_Response {
            if ( ! class_exists( 'SLV_Style_Resolver' ) ) {
                return new WP_REST_Response( [ 'success' => false, 'reason' => 'style_module_not_loaded' ], 500 );
            }
            $user_id = get_current_user_id();
            if ( ! $user_id ) {
                return new WP_REST_Response( [ 'success' => false, 'reason' => 'not_logged_in' ], 401 );
            }
            $style = sanitize_key( (string) $req['style'] );
            $ok    = SLV_Style_Resolver::save_user_preference( $user_id, $style );
            return new WP_REST_Response( [ 'success' => $ok ], $ok ? 200 : 400 );
        },
        'permission_callback' => static fn() => is_user_logged_in(),
        'args'                => [
            'style' => [
                'required'          => true,
                'validate_callback' => static fn( $v ) => is_string( $v ),
            ],
        ],
    ] );
} );