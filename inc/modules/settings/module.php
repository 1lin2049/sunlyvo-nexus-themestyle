<?php
/**
 * SunLyvo Nexus — 设置模块入口
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$slv_settings_dir   = __DIR__;
$slv_settings_files = [
    'class-encryption-manager.php',
    'class-service-config-repo.php',
    'services-registry.php',
    'class-settings-page.php',
    'class-dashboard-page.php',
    'class-repair-page.php',
];

foreach ( $slv_settings_files as $slv_rel ) {
    $slv_file = $slv_settings_dir . '/' . $slv_rel;
    if ( file_exists( $slv_file ) ) {
        require_once $slv_file;
    } elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( "[SunLyvo Nexus] Settings module missing: {$slv_rel}" );
    }
}

unset( $slv_settings_dir, $slv_settings_files, $slv_rel, $slv_file );

add_action( 'admin_menu', [ 'SLV_Settings_Page', 'register_menu' ] );

add_action( 'admin_init', static function () {
    if ( class_exists( 'SLV_Encryption_Manager' ) ) {
        SLV_Encryption_Manager::get_key();
    }
}, 1 );