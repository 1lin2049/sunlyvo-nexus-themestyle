<?php
/**
 * SunLyvo Nexus — 风格解析器
 *
 * 优先级：单页 meta > 用户偏好 > Cookie > 多站点默认 > 全站默认 > 硬编码默认
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
     * 解析当前风格。
     *
     * @since 1.0.0
     * @return string
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

        // 3. 用户偏好
        $user_id = get_current_user_id();
        if ( $user_id ) {
            $user_style = get_user_meta( $user_id, '_slv_style_preference', true );
            if ( $user_style && in_array( $user_style, $valid, true ) ) {
                return $user_style;
            }
        }

        // 4. Cookie
        if ( isset( $_COOKIE['slv_style'] ) ) {
            $cookie_style = sanitize_key( wp_unslash( $_COOKIE['slv_style'] ) );
            if ( in_array( $cookie_style, $valid, true ) ) {
                return $cookie_style;
            }
        }

        // 5. 多站点默认
        if ( function_exists( 'slv_get_current_city' ) ) {
            $city = slv_get_current_city();
            if ( $city && ! empty( $city->default_style ) && in_array( $city->default_style, $valid, true ) ) {
                return $city->default_style;
            }
        }

        // 6. 全站默认
        $default = SLV_Style_Registry::default_style();
        if ( in_array( $default, $valid, true ) ) {
            return $default;
        }

        // 7. 硬编码默认
        return 'brand';
    }

    /**
     * 保存用户偏好。
     *
     * @since 1.0.0
     * @param int    $user_id 用户 ID。
     * @param string $style   风格 slug。
     * @return bool
     */
    public static function save_user_preference( int $user_id, string $style ): bool {
        if ( ! in_array( $style, SLV_Style_Registry::valid_slugs(), true ) ) {
            return false;
        }
        return update_user_meta( $user_id, '_slv_style_preference', $style );
    }
}