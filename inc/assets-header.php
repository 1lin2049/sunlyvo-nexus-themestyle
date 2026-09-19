<?php
/**
 * SunLyvo Nexus — Header 版式（独立加载，优先级最高）
 *
 * @package SunLyvo_Nexus
 * @since 2.1.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_enqueue_scripts', function (): void {
    if ( is_admin() ) {
        return;
    }

    $css = get_template_directory_uri() . '/assets/css';
    $dir = get_template_directory() . '/assets/css';
    $file = $dir . '/header-layout.css';

    if ( file_exists( $file ) ) {
        wp_enqueue_style(
            'slv-header-layout',
            $css . '/header-layout.css',
            [ 'slv-main' ],
            (string) filemtime( $file )
        );
    }
}, 60 );