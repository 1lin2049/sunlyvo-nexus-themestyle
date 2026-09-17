<?php
/**
 * SunLyvo Nexus — 风格注册表
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SLV_Style_Registry {

    /**
     * 所有风格定义。
     *
     * group：
     *   base   = 基础风格（brand / dark）
     *   industry = 行业风格（管理员可切换）
     *   aux    = 辅助风格（仅管理员可切换，用于无障碍）
     *
     * user_switchable：
     *   true = 用户可切换（目前只有 dark）
     */
    public static function all(): array {
        return [
            'brand' => [
                'label'            => '品牌默认',
                'group'            => 'base',
                'css'              => 'brand.css',
                'user_switchable'  => false,
                'admin_switchable' => true,
            ],
            'dark' => [
                'label'            => '深色模式',
                'group'            => 'base',
                'css'              => 'dark.css',
                'user_switchable'  => true,  // ← 唯一的用户级开关
                'admin_switchable' => true,
                'is_theme_mode'    => true,  // 特殊：用 data-theme 而非 data-style
            ],

            // 行业风格
            'industrial' => [
                'label'            => '工业机械',
                'group'            => 'industry',
                'css'              => 'industrial.css',
                'user_switchable'  => false,
                'admin_switchable' => true,
            ],
            'tech' => [
                'label'            => '消费电子',
                'group'            => 'industry',
                'css'              => 'tech.css',
                'user_switchable'  => false,
                'admin_switchable' => true,
            ],
            'consumer' => [
                'label'            => '日用消费品',
                'group'            => 'industry',
                'css'              => 'consumer.css',
                'user_switchable'  => false,
                'admin_switchable' => true,
            ],
            'medical' => [
                'label'            => '医疗健康',
                'group'            => 'industry',
                'css'              => 'medical.css',
                'user_switchable'  => false,
                'admin_switchable' => true,
            ],
            'energy' => [
                'label'            => '新能源',
                'group'            => 'industry',
                'css'              => 'energy.css',
                'user_switchable'  => false,
                'admin_switchable' => true,
            ],
            'home' => [
                'label'            => '家居建材',
                'group'            => 'industry',
                'css'              => 'home.css',
                'user_switchable'  => false,
                'admin_switchable' => true,
            ],

            // 辅助风格（仅管理员）
            'high-contrast' => [
                'label'            => '高对比',
                'group'            => 'aux',
                'css'              => 'high-contrast.css',
                'user_switchable'  => false,
                'admin_switchable' => true,
            ],
            'minimal' => [
                'label'            => '极简',
                'group'            => 'aux',
                'css'              => 'minimal.css',
                'user_switchable'  => false,
                'admin_switchable' => true,
            ],
        ];
    }

    public static function valid_slugs(): array {
        return array_keys( self::all() );
    }

    /**
     * 管理员可切换的风格（全站/站点/页面级）。
     */
    public static function admin_switchable(): array {
        return array_filter(
            self::valid_slugs(),
            static fn( $slug ) => ! empty( self::all()[ $slug ]['admin_switchable'] )
        );
    }

    /**
     * 用户可切换的风格（仅深色模式）。
     */
    public static function user_switchable(): array {
        return array_filter(
            self::valid_slugs(),
            static fn( $slug ) => ! empty( self::all()[ $slug ]['user_switchable'] )
        );
    }

    /**
     * 全站默认风格（管理员设置）。
     */
    public static function site_default(): string {
        $default = (string) get_option( 'slv_site_style', 'brand' );
        return in_array( $default, self::valid_slugs(), true ) ? $default : 'brand';
    }
}