<?php
/**
 * SunLyvo Nexus 角色注册
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 注册所有自定义角色。
 *
 * @since 1.0.0
 */
function slv_register_roles(): void {
    $roles = [
        'region_admin' => [
            'name' => '区域管理员',
            'caps' => [
                'read'               => true,
                'manage_region'      => true,
                'manage_stations'    => true,
                'edit_posts'         => true,
                'publish_posts'      => true,
                'manage_categories'  => true,
                'upload_files'       => true,
            ],
        ],
        'station_master' => [
            'name' => '站长',
            'caps' => [
                'read'               => true,
                'manage_station'     => true,
                'manage_vendors'     => true,
                'edit_posts'         => true,
                'publish_posts'      => true,
                'manage_categories'  => true,
                'upload_files'       => true,
            ],
        ],
        'vendor' => [
            'name' => '商户',
            'caps' => [
                'read'               => true,
                'manage_vendor'      => true,
                'edit_products'      => true,
                'publish_products'   => true,
                'edit_shop_orders'   => true,
                'upload_files'       => true,
            ],
        ],
        'vendor_staff' => [
            'name' => '商户员工',
            'caps' => [
                'read'               => true,
                'edit_products'      => true,
                'edit_shop_orders'   => true,
                'upload_files'       => true,
            ],
        ],
        'creator' => [
            'name' => '创作者',
            'caps' => [
                'read'               => true,
                'edit_posts'         => true,
                'publish_posts'      => true,
                'upload_files'       => true,
                'manage_collections' => true,
            ],
        ],
        'company_admin' => [
            'name' => '企业管理员',
            'caps' => [
                'read'               => true,
                'manage_company'     => true,
                'manage_sub_accounts' => true,
                'edit_shop_orders'   => true,
            ],
        ],
        'company_buyer' => [
            'name' => '企业采购员',
            'caps' => [
                'read'               => true,
                'place_company_orders' => true,
            ],
        ],
        'company_viewer' => [
            'name' => '企业查看者',
            'caps' => [
                'read'               => true,
            ],
        ],
        'wholesale_customer' => [
            'name' => '批发客户',
            'caps' => [
                'read'               => true,
                'view_wholesale_prices' => true,
            ],
        ],
        'pending_wholesale' => [
            'name' => '待审核批发客户',
            'caps' => [
                'read'               => true,
            ],
        ],
        'customer' => [
            'name' => '客户',
            'caps' => [
                'read'               => true,
                'place_orders'       => true,
            ],
        ],
    ];

    foreach ( $roles as $slug => $role ) {
        if ( ! get_role( $slug ) ) {
            add_role( $slug, $role['name'], $role['caps'] );
        }
    }
}

add_action( 'after_setup_theme', 'slv_register_roles' );