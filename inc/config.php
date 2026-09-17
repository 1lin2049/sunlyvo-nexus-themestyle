<?php
/**
 * SunLyvo Nexus 配置中心
 *
 * 统一配置管理、权限继承、审计日志。
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 读取配置值，按优先级继承。
 *
 * 优先级：商户 → 站长 → 区域 → 平台
 *
 * @since 1.0.0
 * @param string $key     配置键。
 * @param mixed  $default 默认值。
 * @return mixed
 */
function slv_get_config( string $key, $default = null ) {
    $owner = slv_get_config_owner();
    $owners = [
        [ 'vendor', $owner['vendor_id'] ?? 0 ],
        [ 'station', $owner['station_id'] ?? 0 ],
        [ 'region', $owner['region_id'] ?? 0 ],
        [ 'platform', 0 ],
    ];

    foreach ( $owners as [ $type, $id ] ) {
        if ( ! $id && 'platform' !== $type ) {
            continue;
        }

        $cache_key = "slv_config_{$key}_{$type}_{$id}";
        $value = wp_cache_get( $cache_key, 'slv_config' );

        if ( false === $value ) {
            global $wpdb;
            $value = $wpdb->get_var( $wpdb->prepare(
                "SELECT config_value FROM {$wpdb->prefix}slv_config_registry
                 WHERE config_key = %s AND owner_type = %s AND owner_id = %d
                 AND config_value IS NOT NULL LIMIT 1",
                $key, $type, $id
            ) );
            wp_cache_set( $cache_key, $value, 'slv_config', 3600 );
        }

        if ( null !== $value ) {
            return slv_cast_config_value( $value );
        }
    }

    return $default;
}

/**
 * 设置配置值。
 *
 * @since 1.0.0
 * @param string $key        配置键。
 * @param mixed  $value      配置值。
 * @param string $owner_type 所有者类型。
 * @param int    $owner_id   所有者 ID。
 * @return bool
 */
function slv_set_config( string $key, $value, string $owner_type = 'platform', int $owner_id = 0 ): bool {
    if ( ! slv_can_manage_config( $key, $owner_type, $owner_id ) ) {
        return false;
    }

    global $wpdb;

    $old_value = $wpdb->get_var( $wpdb->prepare(
        "SELECT config_value FROM {$wpdb->prefix}slv_config_registry
         WHERE config_key = %s AND owner_type = %s AND owner_id = %d",
        $key, $owner_type, $owner_id
    ) );

    $wpdb->replace( "{$wpdb->prefix}slv_config_registry", [
        'config_key'   => $key,
        'owner_type'   => $owner_type,
        'owner_id'     => $owner_id,
        'config_value' => is_array( $value ) ? wp_json_encode( $value ) : $value,
    ] );

    slv_log_config_change( $key, $owner_type, $owner_id, $old_value, $value );
    wp_cache_delete( "slv_config_{$key}_{$owner_type}_{$owner_id}", 'slv_config' );

    return true;
}

/**
 * 检查当前用户是否有权限管理指定配置。
 *
 * @since 1.0.0
 * @param string $config_key 配置键。
 * @param string $owner_type 所有者类型。
 * @param int    $owner_id   所有者 ID。
 * @return bool
 */
function slv_can_manage_config( string $config_key, string $owner_type, int $owner_id ): bool {
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        return false;
    }

    if ( is_super_admin( $user_id ) ) {
        return true;
    }

    $group = slv_get_config_group( $config_key );
    $role = slv_get_user_role_for_owner( $user_id, $owner_type, $owner_id );

    global $wpdb;
    $perm = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}slv_config_permissions
         WHERE role = %s AND config_group = %s",
        $role, $group
    ) );

    return $perm && (int) $perm->can_edit === 1;
}

/**
 * 记录配置变更审计日志。
 *
 * @since 1.0.0
 */
function slv_log_config_change( string $key, string $owner_type, int $owner_id, $old_value, $new_value ): void {
    global $wpdb;
    $wpdb->insert( "{$wpdb->prefix}slv_config_audit_log", [
        'config_key' => $key,
        'owner_type' => $owner_type,
        'owner_id'   => $owner_id,
        'old_value'  => is_array( $old_value ) ? wp_json_encode( $old_value ) : $old_value,
        'new_value'  => is_array( $new_value ) ? wp_json_encode( $new_value ) : $new_value,
        'changed_by' => get_current_user_id(),
        'changed_at' => current_time( 'mysql' ),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
    ] );
}

/**
 * 获取配置归属上下文。
 *
 * @since 1.0.0
 * @return array
 */
function slv_get_config_owner(): array {
    return [
        'vendor_id'  => (int) get_user_meta( get_current_user_id(), '_slv_vendor_id', true ),
        'station_id' => (int) get_user_meta( get_current_user_id(), '_slv_station_id', true ),
        'region_id'  => (int) get_user_meta( get_current_user_id(), '_slv_region_id', true ),
    ];
}

/**
 * 获取配置分组。
 *
 * @since 1.0.0
 * @param string $key 配置键。
 * @return string
 */
function slv_get_config_group( string $key ): string {
    $groups = [
        'commission'  => 'commerce',
        'payment'     => 'commerce',
        'member'      => 'membership',
        'points'      => 'membership',
        'seo'         => 'seo',
        'ai'          => 'ai',
        'sync'        => 'sync',
    ];

    foreach ( $groups as $prefix => $group ) {
        if ( str_starts_with( $key, $prefix ) ) {
            return $group;
        }
    }

    return 'general';
}

/**
 * 获取用户在指定所有者下的角色。
 *
 * @since 1.0.0
 */
function slv_get_user_role_for_owner( int $user_id, string $owner_type, int $owner_id ): string {
    $user = get_userdata( $user_id );
    if ( ! $user || empty( $user->roles ) ) {
        return '';
    }

    $role = $user->roles[0];

    $map = [
        'platform' => [ 'administrator', 'super_admin' ],
        'region'   => [ 'region_admin' ],
        'station'  => [ 'station_master' ],
        'vendor'   => [ 'vendor', 'vendor_staff' ],
    ];

    if ( isset( $map[ $owner_type ] ) && in_array( $role, $map[ $owner_type ], true ) ) {
        return $role;
    }

    return '';
}

/**
 * 类型转换配置值。
 *
 * @since 1.0.0
 * @param mixed $value 原始值。
 * @return mixed
 */
function slv_cast_config_value( $value ) {
    if ( is_string( $value ) ) {
        $decoded = json_decode( $value, true );
        if ( json_last_error() === JSON_ERROR_NONE && is_array( $decoded ) ) {
            return $decoded;
        }
        if ( 'true' === $value ) return true;
        if ( 'false' === $value ) return false;
        if ( is_numeric( $value ) ) return $value + 0;
    }
    return $value;
}