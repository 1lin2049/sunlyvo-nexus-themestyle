<?php
/**
 * SunLyvo Nexus — 阅读体验子系统入口
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$slv_reader_dir = __DIR__;

$slv_reader_files = [
    'reader.php',
    'toc.php',
    'shortcuts.php',
    'reading-position.php',
    'share.php',
];

foreach ( $slv_reader_files as $slv_rel ) {
    $slv_file = $slv_reader_dir . '/' . $slv_rel;
    if ( file_exists( $slv_file ) ) {
        require_once $slv_file;
    } elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( "[SunLyvo Nexus] Reader module missing: {$slv_rel}" );
    }
}

unset( $slv_reader_dir, $slv_reader_files, $slv_rel, $slv_file );