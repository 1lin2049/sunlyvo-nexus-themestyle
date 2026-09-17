<?php
/**
 * SunLyvo Nexus — Style REST API
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'rest_api_init', static function () {

    // GET /styles — 获取所有风格
    register_rest_route( SLV_REST_NAMESPACE, '/styles', [
        'methods'             => 'GET',
        'callback'            => static function (): WP_REST_Response {
            if ( ! class_exists( 'SLV_Style_Registry' ) || ! class_exists( 'SLV_Style_Resolver' ) ) {
                return new WP_REST_Response( [ 'error' => 'style_module_not_loaded' ], 500 );
            }
            $all = SLV_Style_Registry::all();
            $groups = [ 'base' => [], 'industry' => [], 'aux' => [] ];
            foreach ( $all as $slug => $meta ) {
                $groups[ $meta['group'] ][ $slug ] = [
                    'slug'             => $slug,
                    'label'            => $meta['label'],
                    'css'              => $meta['css'],
                    'user_switchable'  => ! empty( $meta['user_switchable'] ),
                    'admin_switchable' => ! empty( $meta['admin_switchable'] ),
                ];
            }
            return new WP_REST_Response( [
                'current_style' => SLV_Style_Resolver::resolve(),
                'current_theme' => SLV_Style_Resolver::resolve_theme_mode(),
                'groups'        => $groups,
                'all'           => array_keys( $all ),
            ], 200 );
        },
        'permission_callback' => '__return_true',
    ] );

    // POST /user/theme — 用户保存深色模式偏好（唯一用户级）
    register_rest_route( SLV_REST_NAMESPACE, '/user/theme', [
        'methods'             => 'POST',
        'callback'            => static function ( WP_REST_Request $req ): WP_REST_Response {
            if ( ! class_exists( 'SLV_Style_Resolver' ) ) {
                return new WP_REST_Response( [ 'success' => false ], 500 );
            }
            $user_id = get_current_user_id();
            if ( ! $user_id ) {
                return new WP_REST_Response( [ 'success' => false, 'reason' => 'not_logged_in' ], 401 );
            }
            $mode = sanitize_key( (string) $req['mode'] );
            $ok   = SLV_Style_Resolver::save_user_theme_mode( $user_id, $mode );
            return new WP_REST_Response( [ 'success' => $ok ], $ok ? 200 : 400 );
        },
        'permission_callback' => static fn() => is_user_logged_in(),
        'args'                => [
            'mode' => [ 'required' => true, 'validate_callback' => static fn( $v ) => is_string( $v ) ],
        ],
    ] );

    // POST /site/style — 管理员设置全站默认风格
    register_rest_route( SLV_REST_NAMESPACE, '/site/style', [
        'methods'             => 'POST',
        'callback'            => static function ( WP_REST_Request $req ): WP_REST_Response {
            if ( ! current_user_can( 'manage_options' ) ) {
                return new WP_REST_Response( [ 'success' => false ], 403 );
            }
            $style = sanitize_key( (string) $req['style'] );
            if ( ! class_exists( 'SLV_Style_Registry' )
                 || ! in_array( $style, SLV_Style_Registry::admin_switchable(), true ) ) {
                return new WP_REST_Response( [ 'success' => false ], 400 );
            }
            update_option( 'slv_site_style', $style );
            return new WP_REST_Response( [ 'success' => true ], 200 );
        },
        'permission_callback' => static fn() => current_user_can( 'manage_options' ),
        'args'                => [
            'style' => [ 'required' => true, 'validate_callback' => static fn( $v ) => is_string( $v ) ],
        ],
    ] );

    // POST /page-type/style — 管理员设置按页面类型的风格
    register_rest_route( SLV_REST_NAMESPACE, '/page-type/style', [
        'methods'             => 'POST',
        'callback'            => static function ( WP_REST_Request $req ): WP_REST_Response {
            if ( ! current_user_can( 'manage_options' ) ) {
                return new WP_REST_Response( [ 'success' => false ], 403 );
            }
            $mapping = (array) $req['mapping'];
            $clean   = [];
            $valid   = SLV_Style_Registry::admin_switchable();
            foreach ( [ 'blog', 'shop', 'knowledge' ] as $key ) {
                if ( isset( $mapping[ $key ] ) && in_array( $mapping[ $key ], $valid, true ) ) {
                    $clean[ $key ] = $mapping[ $key ];
                }
            }
            update_option( 'slv_page_type_styles', $clean );
            return new WP_REST_Response( [ 'success' => true ], 200 );
        },
        'permission_callback' => static fn() => current_user_can( 'manage_options' ),
    ] );

    // POST /store/style-policy — 管理员设置商户风格策略
    register_rest_route( SLV_REST_NAMESPACE, '/store/style-policy', [
        'methods'             => 'POST',
        'callback'            => static function ( WP_REST_Request $req ): WP_REST_Response {
            if ( ! current_user_can( 'manage_options' ) ) {
                return new WP_REST_Response( [ 'success' => false ], 403 );
            }
            $allow   = ! empty( $req['allow_store_style'] );
            $allowed = (array) $req['allowed_styles'];
            $valid   = SLV_Style_Registry::admin_switchable();
            $allowed = array_values( array_intersect( $allowed, $valid ) );
            update_option( 'slv_allow_store_style', $allow );
            update_option( 'slv_store_allowed_styles', $allowed );
            return new WP_REST_Response( [ 'success' => true ], 200 );
        },
        'permission_callback' => static fn() => current_user_can( 'manage_options' ),
    ] );

    // POST /store/{id}/style — 商户设置自己的店铺风格
    register_rest_route( SLV_REST_NAMESPACE, '/store/(?P<id>\d+)/style', [
        'methods'             => 'POST',
        'callback'            => static function ( WP_REST_Request $req ): WP_REST_Response {
            $store_id = (int) $req['id'];
            if ( get_current_user_id() !== $store_id && ! current_user_can( 'manage_options' ) ) {
                return new WP_REST_Response( [ 'success' => false ], 403 );
            }
            if ( ! get_option( 'slv_allow_store_style', false ) ) {
                return new WP_REST_Response( [ 'success' => false, 'reason' => 'not_allowed' ], 403 );
            }
            $style   = sanitize_key( (string) $req['style'] );
            $allowed = (array) get_option( 'slv_store_allowed_styles', [] );
            if ( ! empty( $allowed ) && ! in_array( $style, $allowed, true ) ) {
                return new WP_REST_Response( [ 'success' => false, 'reason' => 'not_in_allowed_list' ], 400 );
            }
            update_user_meta( $store_id, '_slv_store_style', $style );
            return new WP_REST_Response( [ 'success' => true ], 200 );
        },
        'permission_callback' => static fn() => is_user_logged_in(),
    ] );
} );