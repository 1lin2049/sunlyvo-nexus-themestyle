<?php
/**
 * SunLyvo Nexus — 分享 / 短链 / 二维码
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 注册分享相关 REST 路由。
 *
 * @since 1.0.0
 */
function slv_reader_register_share_routes(): void {
    register_rest_route( SLV_REST_NAMESPACE, '/shortlink', [
        'methods'             => 'POST',
        'callback'            => 'slv_reader_create_shortlink',
        'permission_callback' => '__return_true',
        'args'                => [
            'url' => [ 'required' => true, 'validate_callback' => 'esc_url_raw' ],
        ],
    ] );

    register_rest_route( SLV_REST_NAMESPACE, '/share-track', [
        'methods'             => 'POST',
        'callback'            => 'slv_reader_track_share',
        'permission_callback' => '__return_true',
    ] );
}
add_action( 'rest_api_init', 'slv_reader_register_share_routes' );

/**
 * 生成短链接（基于 post_id 的哈希短码）。
 *
 * 无外部服务时使用内部短链，可后续替换为 Bitly。
 *
 * @since 1.0.0
 * @param WP_REST_Request $request 请求对象。
 * @return WP_REST_Response
 */
function slv_reader_create_shortlink( WP_REST_Request $request ): WP_REST_Response {
    $url = esc_url_raw( (string) $request['url'] );
    if ( '' === $url ) {
        return new WP_REST_Response( [ 'success' => false ], 400 );
    }

    // 优先匹配站内文章
    $post_id = url_to_postid( $url );
    if ( $post_id ) {
        $code = slv_reader_encode_short_code( $post_id );
        $short = home_url( '/s/' . $code );
        return new WP_REST_Response( [
            'success'   => true,
            'short_url' => $short,
        ], 200 );
    }

    // 站外 URL：返回原 URL（可后续接入 Bitly）
    return new WP_REST_Response( [
        'success'   => true,
        'short_url' => $url,
    ], 200 );
}

/**
 * 将 post_id 编码为短码。
 *
 * @since 1.0.0
 * @param int $post_id 文章 ID。
 * @return string
 */
function slv_reader_encode_short_code( int $post_id ): string {
    $alphabet = 'abcdefghijkmnpqrstuvwxyz23456789';
    $base     = strlen( $alphabet );
    $code     = '';
    $n        = $post_id;
    while ( $n > 0 ) {
        $code  = $alphabet[ $n % $base ] . $code;
        $n     = intdiv( $n, $base );
    }
    return $code ?: 'a';
}

/**
 * 解析短码为 post_id。
 *
 * @since 1.0.0
 * @param string $code 短码。
 * @return int
 */
function slv_reader_decode_short_code( string $code ): int {
    $alphabet = 'abcdefghijkmnpqrstuvwxyz23456789';
    $base     = strlen( $alphabet );
    $n        = 0;
    $len      = strlen( $code );
    for ( $i = 0; $i < $len; $i++ ) {
        $pos = strpos( $alphabet, $code[ $i ] );
        if ( false === $pos ) {
            return 0;
        }
        $n = $n * $base + $pos;
    }
    return $n;
}

/**
 * 注册短链重写规则。
 *
 * @since 1.0.0
 */
function slv_reader_register_shortlink_rewrite(): void {
    add_rewrite_rule( '^s/([a-z0-9]+)/?$', 'index.php?slv_short=$matches[1]', 'top' );
}
add_action( 'init', 'slv_reader_register_shortlink_rewrite' );

/**
 * 注册 slv_short query var。
 *
 * @since 1.0.0
 */
function slv_reader_register_shortlink_query_var( array $vars ): array {
    $vars[] = 'slv_short';
    return $vars;
}
add_filter( 'query_vars', 'slv_reader_register_shortlink_query_var' );

/**
 * 处理短链访问。
 *
 * @since 1.0.0
 */
function slv_reader_handle_shortlink(): void {
    $code = (string) get_query_var( 'slv_short' );
    if ( '' === $code ) {
        return;
    }
    $post_id = slv_reader_decode_short_code( $code );
    if ( $post_id <= 0 || 'publish' !== get_post_status( $post_id ) ) {
        wp_safe_redirect( home_url( '/' ), 302 );
        exit;
    }
    wp_safe_redirect( get_permalink( $post_id ), 301 );
    exit;
}
add_action( 'template_redirect', 'slv_reader_handle_shortlink' );

/**
 * 记录分享事件。
 *
 * @since 1.0.0
 * @param WP_REST_Request $request 请求对象。
 * @return WP_REST_Response
 */
function slv_reader_track_share( WP_REST_Request $request ): WP_REST_Response {
    $post_id = (int) $request['post_id'];
    $target  = sanitize_key( (string) $request['target'] );

    if ( $post_id <= 0 || '' === $target ) {
        return new WP_REST_Response( [ 'success' => false ], 400 );
    }

    if ( function_exists( 'slv_track_event' ) ) {
        slv_track_event( $post_id, 'share', 1 );
    }

    return new WP_REST_Response( [ 'success' => true ], 200 );
}

/**
 * 获取分享平台列表（根据用户地区）。
 *
 * @since 1.0.0
 * @return array
 */
function slv_reader_get_share_targets(): array {
    $region = slv_detect_user_region();
    if ( 'cn' === $region ) {
        return [
            [ 'id' => 'wechat',   'label' => '微信' ],
            [ 'id' => 'weibo',    'label' => '微博' ],
            [ 'id' => 'qzone',    'label' => 'QQ空间' ],
            [ 'id' => 'copy',     'label' => '复制链接' ],
            [ 'id' => 'qrcode',   'label' => '二维码' ],
        ];
    }
    return [
        [ 'id' => 'twitter',  'label' => 'Twitter' ],
        [ 'id' => 'linkedin', 'label' => 'LinkedIn' ],
        [ 'id' => 'reddit',   'label' => 'Reddit' ],
        [ 'id' => 'copy',     'label' => 'Copy Link' ],
        [ 'id' => 'qrcode',   'label' => 'QR Code' ],
    ];
}