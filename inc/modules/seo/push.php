<?php
/**
 * SunLyvo Nexus — 多引擎主动推送
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 保存文章时推送到各搜索引擎。
 *
 * @since 1.0.0
 * @param int $post_id 文章 ID。
 */
function slv_seo_push_to_all_engines( int $post_id ): void {
    if ( 'publish' !== get_post_status( $post_id ) ) {
        return;
    }
    if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
        return;
    }

    $url    = get_permalink( $post_id );
    $region = slv_detect_user_region();

    if ( 'cn' === $region ) {
        slv_seo_push_baidu( $url );
        slv_seo_push_sogou( $url );
    } else {
        slv_seo_push_google( $url );
        slv_seo_push_bing( $url );
    }
}
add_action( 'save_post', 'slv_seo_push_to_all_engines', 20 );

/**
 * 百度主动推送。
 *
 * @since 1.0.0
 */
function slv_seo_push_baidu( string $url ): void {
    $api = slv_get_config( 'baidu_push_api' );
    if ( ! $api ) {
        return;
    }
    wp_remote_post( $api, [
        'body'    => [ 'urls' => $url ],
        'timeout' => 5,
    ] );
}

/**
 * 搜狗主动推送。
 *
 * @since 1.0.0
 */
function slv_seo_push_sogou( string $url ): void {
    $api = slv_get_config( 'sogou_push_api' );
    if ( ! $api ) {
        return;
    }
    wp_remote_post( $api, [
        'body'    => [ 'url' => $url ],
        'timeout' => 5,
    ] );
}

/**
 * Google Indexing API 推送。
 *
 * @since 1.0.0
 */
function slv_seo_push_google( string $url ): void {
    $token = slv_get_config( 'google_indexing_token' );
    if ( ! $token ) {
        return;
    }
    wp_remote_post( 'https://indexing.googleapis.com/v3/urlNotifications:publish', [
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type'  => 'application/json',
        ],
        'body'    => wp_json_encode( [ 'url' => $url, 'type' => 'URL_UPDATED' ] ),
        'timeout' => 10,
    ] );
}

/**
 * Bing 主动推送。
 *
 * @since 1.0.0
 */
function slv_seo_push_bing( string $url ): void {
    $api_key = slv_get_config( 'bing_api_key' );
    if ( ! $api_key ) {
        return;
    }
    wp_remote_post( 'https://ssl.bing.com/webmaster/api.svc/json/SubmitUrl?apikey=' . $api_key, [
        'headers' => [ 'Content-Type' => 'application/json; charset=utf-8' ],
        'body'    => wp_json_encode( [ 'siteUrl' => home_url(), 'url' => $url ] ),
        'timeout' => 10,
    ] );
}