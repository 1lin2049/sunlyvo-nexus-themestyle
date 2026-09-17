<?php
/**
 * SunLyvo Nexus — 风格解析器（运营侧配置驱动）
 *
 * 优先级：
 *   1. 单页 meta (_slv_style)
 *   2. 按页面类型配置 (blog → magazine, shop → tech)
 *   3. 商户/创作者主页独立设置（需管理员授权）
 *   4. 站点默认（站长）
 *   5. 全站默认（平台管理员）
 *   6. brand（硬编码兜底）
 *
 * 用户级深色模式独立解析（data-theme）。
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SLV_Style_Resolver {

    /**
     * 解析当前风格（data-style）。
     */
    public static function resolve(): string {
        $valid = SLV_Style_Registry::valid_slugs();

        // 1. URL 参数（仅测试用）
        if ( isset( $_GET['slv_style'] ) ) {
            $s = sanitize_key( wp_unslash( $_GET['slv_style'] ) );
            if ( in_array( $s, $valid, true ) ) {
                return $s;
            }
        }

        // 2. 单页 meta
        if ( is_singular() ) {
            $page_style = get_post_meta( get_the_ID(), '_slv_style', true );
            if ( $page_style && in_array( $page_style, $valid, true ) ) {
                return $page_style;
            }
        }

        // 3. 按页面类型配置
        $page_type_style = self::resolve_by_page_type();
        if ( $page_type_style ) {
            return $page_type_style;
        }

        // 4. 商户/创作者主页独立设置
        $store_style = self::resolve_store_style();
        if ( $store_style ) {
            return $store_style;
        }

        // 5. 站点默认（站长）
        if ( is_multisite() && ! is_main_site() ) {
            $station_style = (string) get_option( 'slv_station_style', '' );
            if ( $station_style && in_array( $station_style, $valid, true ) ) {
                return $station_style;
            }
        }

        // 6. 全站默认（平台管理员）
        return SLV_Style_Registry::site_default();
    }

    /**
     * 解析用户级深色模式。
     *
     * @return string 'light' | 'dark' | 'auto'
     */
    public static function resolve_theme_mode(): string {
        $valid = [ 'light', 'dark', 'auto' ];

        // 1. URL 参数
        if ( isset( $_GET['slv_theme'] ) ) {
            $t = sanitize_key( wp_unslash( $_GET['slv_theme'] ) );
            if ( in_array( $t, $valid, true ) ) {
                return $t;
            }
        }

        // 2. 用户偏好
        $user_id = get_current_user_id();
        if ( $user_id ) {
            $user_mode = (string) get_user_meta( $user_id, '_slv_theme_mode', true );
            if ( in_array( $user_mode, $valid, true ) ) {
                return $user_mode;
            }
        }

        // 3. Cookie
        if ( isset( $_COOKIE['slv_theme'] ) ) {
            $t = sanitize_key( wp_unslash( $_COOKIE['slv_theme'] ) );
            if ( in_array( $t, $valid, true ) ) {
                return $t;
            }
        }

        // 4. 默认 auto（跟随系统）
        return 'auto';
    }

    /**
     * 保存用户深色模式偏好。
     */
    public static function save_user_theme_mode( int $user_id, string $mode ): bool {
        if ( ! in_array( $mode, [ 'light', 'dark', 'auto' ], true ) ) {
            return false;
        }
        return update_user_meta( $user_id, '_slv_theme_mode', $mode );
    }

    /**
     * 按页面类型解析。
     */
    private static function resolve_by_page_type(): ?string {
        $mapping = (array) get_option( 'slv_page_type_styles', [] );
        if ( empty( $mapping ) ) {
            return null;
        }

        $valid = SLV_Style_Registry::valid_slugs();

        if ( is_home() || is_archive() || is_singular( 'post' ) ) {
            $style = $mapping['blog'] ?? '';
            if ( $style && in_array( $style, $valid, true ) ) {
                return $style;
            }
        }

        if ( is_post_type_archive( [ 'product', 'collection' ] ) || is_singular( [ 'product', 'collection' ] ) ) {
            $style = $mapping['shop'] ?? '';
            if ( $style && in_array( $style, $valid, true ) ) {
                return $style;
            }
        }

        if ( is_singular( [ 'wiki', 'faq', 'document' ] ) ) {
            $style = $mapping['knowledge'] ?? '';
            if ( $style && in_array( $style, $valid, true ) ) {
                return $style;
            }
        }

        return null;
    }

    /**
     * 商户/创作者主页独立风格。
     */
    private static function resolve_store_style(): ?string {
        // 检查当前是否为某个店铺/创作者主页
        $store_id = (int) get_query_var( 'slv_store_id' );
        if ( ! $store_id ) {
            return null;
        }

        // 检查管理员是否允许
        if ( ! get_option( 'slv_allow_store_style', false ) ) {
            return null;
        }

        // 检查是否在管理员允许的风格范围内
        $allowed = (array) get_option( 'slv_store_allowed_styles', [] );
        $store_style = (string) get_user_meta( $store_id, '_slv_store_style', true );

        if ( $store_style && ( empty( $allowed ) || in_array( $store_style, $allowed, true ) ) ) {
            return $store_style;
        }

        return null;
    }
}