<?php
/**
 * SunLyvo Nexus — 通用辅助函数
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 获取 CPT 的中文标签。
 *
 * @since 1.0.0
 * @param string $post_type 文章类型。
 * @return string
 */
function slv_get_post_type_label( string $post_type ): string {
    $obj = get_post_type_object( $post_type );
    return $obj ? (string) $obj->labels->name : $post_type;
}

/**
 * 获取 CPT 归档 URL。
 *
 * @since 1.0.0
 * @param string $post_type 文章类型。
 * @return string
 */
function slv_get_post_type_archive_url( string $post_type ): string {
    $url = get_post_type_archive_link( $post_type );
    return $url ?: home_url( '/' );
}

/**
 * 判断当前页面是否为指定的 CPT。
 *
 * @since 1.0.0
 * @param string|array $post_types 文章类型。
 * @return bool
 */
function slv_is_post_type( $post_types ): bool {
    return is_singular( $post_types ) || is_post_type_archive( $post_types );
}