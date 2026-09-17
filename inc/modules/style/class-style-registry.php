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
     * 获取所有风格。
     *
     * @since 1.0.0
     * @return array
     */
    public static function all(): array {
        return [
            // 基础
            'brand'         => [ 'label' => '品牌默认',    'group' => 'base',     'region' => 'all',           'css' => 'brand.css' ],
            'dark'          => [ 'label' => '深色模式',    'group' => 'base',     'region' => 'all',           'css' => 'dark.css' ],

            // 行业风格
            'industrial'    => [ 'label' => '工业机械',    'group' => 'industry', 'region' => 'all',           'css' => 'industrial.css' ],
            'tech'          => [ 'label' => '消费电子',    'group' => 'industry', 'region' => 'all',           'css' => 'tech.css' ],
            'consumer'      => [ 'label' => '日用消费品',  'group' => 'industry', 'region' => 'all',           'css' => 'consumer.css' ],
            'medical'       => [ 'label' => '医疗健康',    'group' => 'industry', 'region' => 'all',           'css' => 'medical.css' ],
            'energy'        => [ 'label' => '新能源',      'group' => 'industry', 'region' => 'all',           'css' => 'energy.css' ],
            'home'          => [ 'label' => '家居建材',    'group' => 'industry', 'region' => 'all',           'css' => 'home.css' ],

            // 辅助风格
            'high-contrast' => [ 'label' => '高对比',      'group' => 'aux',      'region' => 'all',           'css' => 'high-contrast.css' ],
            'minimal'       => [ 'label' => '极简',        'group' => 'aux',      'region' => 'all',           'css' => 'minimal.css' ],
            'magazine'      => [ 'label' => '杂志',        'group' => 'aux',      'region' => 'all',           'css' => 'magazine.css' ],
        ];
    }

    /**
     * 获取默认风格。
     *
     * @since 1.0.0
     * @return string
     */
    public static function default_style(): string {
        return (string) get_theme_mod( 'slv_style_variant', 'brand' );
    }

    /**
     * 获取有效 slug 列表。
     *
     * @since 1.0.0
     * @return array
     */
    public static function valid_slugs(): array {
        return array_keys( self::all() );
    }
}