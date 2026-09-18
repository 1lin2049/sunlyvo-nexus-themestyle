<?php
/**
 * SunLyvo Nexus — 模板 REST API
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'rest_api_init', static function () {

    // GET /templates
    register_rest_route( SLV_REST_NAMESPACE, '/templates', [
        'methods'             => 'GET',
        'callback'            => static function (): WP_REST_Response {
            if ( ! class_exists( 'SLV_Template_Registry' ) || ! class_exists( 'SLV_Template_Resolver' ) ) {
                return new WP_REST_Response( [ 'error' => 'template_module_not_loaded' ], 500 );
            }
            $all     = SLV_Template_Registry::all();
            $current = SLV_Template_Resolver::resolve();
            $groups  = [ 'base' => [], 'industry' => [], 'aux' => [] ];
            foreach ( $all as $slug => $meta ) {
                $group = $meta['group'];
                if ( ! isset( $groups[ $group ] ) ) {
                    $groups[ $group ] = [];
                }
                $groups[ $group ][ $slug ] = [
                    'slug'             => $slug,
                    'label'            => $meta['label'],
                    'description'      => $meta['description'],
                    'user_switchable'  => $meta['user_switchable'],
                    'admin_switchable' => $meta['admin_switchable'],
                    'is_active'        => $slug === $current,
                ];
            }
            return new WP_REST_Response( [
                'current_template' => $current,
                'current_theme'    => SLV_Template_Resolver::resolve_theme_mode(),
                'groups'           => $groups,
                'all'              => array_keys( $all ),
            ], 200 );
        },
        'permission_callback' => '__return_true',
    ] );

    // POST /user/theme（用户级深色模式）
    register_rest_route( SLV_REST_NAMESPACE, '/user/theme', [
        'methods'             => 'POST',
        'callback'            => static function ( WP_REST_Request $req ): WP_REST_Response {
            if ( ! class_exists( 'SLV_Template_Resolver' ) ) {
                return new WP_REST_Response( [ 'success' => false ], 500 );
            }
            $user_id = get_current_user_id();
            if ( ! $user_id ) {
                return new WP_REST_Response( [ 'success' => false, 'reason' => 'not_logged_in' ], 401 );
            }
            $mode = sanitize_key( (string) $req['mode'] );
            $ok   = SLV_Template_Resolver::save_user_theme_mode( $user_id, $mode );
            return new WP_REST_Response( [ 'success' => $ok ], $ok ? 200 : 400 );
        },
        'permission_callback' => static fn() => is_user_logged_in(),
        'args'                => [
            'mode' => [ 'required' => true, 'validate_callback' => static fn( $v ) => is_string( $v ) ],
        ],
    ] );

    // POST /site/template（管理员设置全站默认）
    register_rest_route( SLV_REST_NAMESPACE, '/site/template', [
        'methods'             => 'POST',
        'callback'            => static function ( WP_REST_Request $req ): WP_REST_Response {
            if ( ! current_user_can( 'manage_options' ) ) {
                return new WP_REST_Response( [ 'success' => false ], 403 );
            }
            $slug = sanitize_key( (string) $req['template'] );
            if ( ! class_exists( 'SLV_Template_Registry' )
                 || ! in_array( $slug, SLV_Template_Registry::admin_switchable(), true ) ) {
                return new WP_REST_Response( [ 'success' => false ], 400 );
            }
            update_option( 'slv_site_template', $slug );
            return new WP_REST_Response( [ 'success' => true ], 200 );
        },
        'permission_callback' => static fn() => current_user_can( 'manage_options' ),
        'args'                => [
            'template' => [ 'required' => true, 'validate_callback' => static fn( $v ) => is_string( $v ) ],
        ],
    ] );

    // POST /store/{id}/template
    register_rest_route( SLV_REST_NAMESPACE, '/store/(?P<id>\d+)/template', [
        'methods'             => 'POST',
        'callback'            => static function ( WP_REST_Request $req ): WP_REST_Response {
            $store_id = (int) $req['id'];
            if ( get_current_user_id() !== $store_id && ! current_user_can( 'manage_options' ) ) {
                return new WP_REST_Response( [ 'success' => false ], 403 );
            }
            if ( ! get_option( 'slv_allow_store_template', false ) ) {
                return new WP_REST_Response( [ 'success' => false, 'reason' => 'not_allowed' ], 403 );
            }
            $slug    = sanitize_key( (string) $req['template'] );
            $allowed = (array) get_option( 'slv_store_allowed_templates', [] );
            if ( ! empty( $allowed ) && ! in_array( $slug, $allowed, true ) ) {
                return new WP_REST_Response( [ 'success' => false, 'reason' => 'not_in_allowed_list' ], 400 );
            }
            update_user_meta( $store_id, '_slv_store_template', $slug );
            return new WP_REST_Response( [ 'success' => true ], 200 );
        },
        'permission_callback' => static fn() => is_user_logged_in(),
    ] );

} );