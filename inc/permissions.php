<?php
/**
 * SunLyvo Nexus 统一权限函数
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 统一权限检查入口。
 *
 * @since 1.0.0
 * @param string $capability 能力名称。
 * @param int    $object_id  对象 ID。
 * @param int    $user_id    用户 ID。
 * @return bool
 */
function slv_user_can( string $capability, int $object_id = 0, int $user_id = 0 ): bool {
    if ( ! $user_id ) {
        $user_id = get_current_user_id();
    }

    if ( ! $user_id ) {
        return false;
    }

    if ( is_super_admin( $user_id ) ) {
        return true;
    }

    $allowed = user_can( $user_id, $capability, $object_id );

    /**
     * 过滤权限判断结果。
     *
     * @since 1.0.0
     */
    return (bool) apply_filters( 'slv_user_can', $allowed, $capability, $object_id, $user_id );
}

/**
 * 检查用户是否属于指定企业。
 *
 * @since 1.0.0
 */
function slv_user_in_company( int $user_id, int $company_id ): bool {
    return (int) get_user_meta( $user_id, '_slv_company_id', true ) === $company_id;
}

/**
 * 检查用户是否为指定商户的所有者或员工。
 *
 * @since 1.0.0
 */
function slv_user_in_vendor( int $user_id, int $vendor_id ): bool {
    $user = get_userdata( $user_id );
    if ( ! $user ) {
        return false;
    }

    if ( in_array( 'vendor', (array) $user->roles, true ) && (int) $user->ID === $vendor_id ) {
        return true;
    }

    $vendor_owner = (int) get_user_meta( $user_id, '_slv_vendor_id', true );
    return $vendor_owner === $vendor_id;
}