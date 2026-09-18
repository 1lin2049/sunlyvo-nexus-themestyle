<?php
/**
 * SunLyvo Nexus — 模板解析器
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SLV_Template_Resolver {

    /**
     * 解析当前行业模板。
     */
    public static function resolve(): string {
        return slv_get_current_template();
    }

    /**
     * 解析深色模式（用户级）。
     */
    public static function resolve_theme_mode(): string {
        $valid = [ 'light', 'dark', 'auto' ];

        if ( isset( $_GET['slv_theme'] ) ) {
            $t = sanitize_key( wp_unslash( $_GET['slv_theme'] ) );
            if ( in_array( $t, $valid, true ) ) {
                return $t;
            }
        }

        $user_id = get_current_user_id();
        if ( $user_id ) {
            $user_mode = (string) get_user_meta( $user_id, '_slv_theme_mode', true );
            if ( in_array( $user_mode, $valid, true ) ) {
                return $user_mode;
            }
        }

        if ( isset( $_COOKIE['slv_theme'] ) ) {
            $t = sanitize_key( wp_unslash( $_COOKIE['slv_theme'] ) );
            if ( in_array( $t, $valid, true ) ) {
                return $t;
            }
        }

        return 'auto';
    }

    public static function save_user_theme_mode( int $user_id, string $mode ): bool {
        if ( ! in_array( $mode, [ 'light', 'dark', 'auto' ], true ) ) {
            return false;
        }
        return update_user_meta( $user_id, '_slv_theme_mode', $mode );
    }
}