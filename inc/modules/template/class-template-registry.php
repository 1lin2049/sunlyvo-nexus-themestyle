<?php
/**
 * SunLyvo Nexus — 模板注册表
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SLV_Template_Registry {

    /**
     * 扫描 assets/templates/ 目录获取所有模板。
     */
    public static function all(): array {
        static $cache = null;
        if ( null !== $cache ) {
            return $cache;
        }

        $cache = [];
        $dir   = SLV_THEME_DIR . '/assets/templates';
        if ( ! is_dir( $dir ) ) {
            return $cache;
        }

        foreach ( (array) glob( $dir . '/*', GLOB_ONLYDIR ) as $sub ) {
            $json = $sub . '/template.json';
            if ( ! file_exists( $json ) ) {
                continue;
            }
            $meta = json_decode( (string) file_get_contents( $json ), true );
            if ( ! is_array( $meta ) ) {
                continue;
            }
            $slug = $meta['slug'] ?? basename( $sub );
            $cache[ $slug ] = [
                'slug'             => $slug,
                'label'            => $meta['name'] ?? $slug,
                'description'      => $meta['description'] ?? '',
                'group'            => $meta['group'] ?? 'industry',
                'user_switchable'  => ! empty( $meta['user_switchable'] ),
                'admin_switchable' => ! empty( $meta['admin_switchable'] ),
                'is_theme_mode'    => 'dark' === $slug,
                'dir'              => $sub,
                'url'              => SLV_THEME_URL . '/assets/templates/' . $slug,
            ];
        }

        return $cache;
    }

    public static function valid_slugs(): array {
        return array_keys( self::all() );
    }

    public static function admin_switchable(): array {
        return array_keys( array_filter( self::all(), static fn( $m ) => ! empty( $m['admin_switchable'] ) ) );
    }

    public static function user_switchable(): array {
        return array_keys( array_filter( self::all(), static fn( $m ) => ! empty( $m['user_switchable'] ) ) );
    }

    public static function site_default(): string {
        $default = (string) get_option( 'slv_site_template', 'brand' );
        return in_array( $default, self::valid_slugs(), true ) ? $default : 'brand';
    }
}