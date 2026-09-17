<?php
/**
 * SunLyvo Nexus — 管理工具栏控制
 *
 * 非 wp-admin 页面禁用管理工具栏。
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 前台禁用管理工具栏。
 */
add_filter( 'show_admin_bar', static function ( $show ) {
    if ( is_admin() ) {
        return $show;
    }
    return false;
} );

/**
 * 彻底移除前台 admin-bar 的 CSS 和 JS。
 */
add_action( 'wp_enqueue_scripts', static function () {
    if ( ! is_admin() ) {
        wp_deregister_style( 'admin-bar' );
        wp_deregister_script( 'admin-bar' );
    }
}, 100 );

/**
 * 移除 wp_head 里的 admin-bar inline CSS。
 */
add_action( 'init', static function () {
    if ( ! is_admin() ) {
        remove_action( 'wp_head', '_admin_bar_bump_cb' );
    }
} );