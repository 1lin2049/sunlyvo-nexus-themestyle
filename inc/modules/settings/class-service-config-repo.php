<?php
/**
 * SunLyvo Nexus — 服务配置仓库
 *
 * 业务 API Key 用主密钥加密后存 slv_service_configs 表。
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SLV_Service_Config_Repo {

    private const CIPHER = 'AES-256-CBC';

    public function save( string $service_key, array $data, string $owner_type = 'platform', int $owner_id = 0 ): bool {
        $key = SLV_Encryption_Manager::get_key();
        if ( '' === $key ) {
            return false;
        }

        $encrypted = $this->encrypt( wp_json_encode( $data ), $key );

        global $wpdb;
        $table = "{$wpdb->prefix}slv_service_configs";

        $existing = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM {$table} WHERE service_key = %s AND owner_type = %s AND owner_id = %d",
            $service_key, $owner_type, $owner_id
        ) );

        if ( $existing ) {
            return false !== $wpdb->update(
                $table,
                [ 'config_data' => $encrypted, 'is_active' => 1, 'updated_at' => current_time( 'mysql' ) ],
                [ 'id' => (int) $existing ]
            );
        }

        return false !== $wpdb->insert( $table, [
            'service_key' => $service_key,
            'owner_type'  => $owner_type,
            'owner_id'    => $owner_id,
            'config_data' => $encrypted,
            'is_active'   => 1,
        ] );
    }

    public function get( string $service_key, string $owner_type = 'platform', int $owner_id = 0 ): ?array {
        $key = SLV_Encryption_Manager::get_key();
        if ( '' === $key ) {
            return null;
        }

        global $wpdb;
        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT config_data FROM {$wpdb->prefix}slv_service_configs
             WHERE service_key = %s AND owner_type = %s AND owner_id = %d AND is_active = 1",
            $service_key, $owner_type, $owner_id
        ) );

        if ( ! $row || ! $row->config_data ) {
            return null;
        }

        $decrypted = $this->decrypt( $row->config_data, $key );
        if ( null === $decrypted ) {
            return null;
        }

        $data = json_decode( $decrypted, true );
        return is_array( $data ) ? $data : null;
    }

    public function delete( string $service_key, string $owner_type = 'platform', int $owner_id = 0 ): bool {
        global $wpdb;
        return false !== $wpdb->delete(
            "{$wpdb->prefix}slv_service_configs",
            [ 'service_key' => $service_key, 'owner_type' => $owner_type, 'owner_id' => $owner_id ]
        );
    }

    public function re_encrypt_all( string $old_key, string $new_key ): array {
        global $wpdb;
        $rows = $wpdb->get_results( "SELECT id, config_data FROM {$wpdb->prefix}slv_service_configs" );

        if ( empty( $rows ) ) {
            return [ 'success' => true, 'count' => 0, 'message' => '' ];
        }

        $count = 0;
        foreach ( $rows as $row ) {
            $plain = $this->decrypt( $row->config_data, $old_key );
            if ( null === $plain ) {
                continue;
            }
            $new_cipher = $this->encrypt( $plain, $new_key );
            $wpdb->update(
                "{$wpdb->prefix}slv_service_configs",
                [ 'config_data' => $new_cipher ],
                [ 'id' => (int) $row->id ]
            );
            $count++;
        }

        return [ 'success' => true, 'count' => $count, 'message' => '' ];
    }

    private function encrypt( string $plain, string $key ): string {
        $derived = hash( 'sha256', $key, true );
        $iv      = random_bytes( 16 );
        $raw     = openssl_encrypt( $plain, self::CIPHER, $derived, OPENSSL_RAW_DATA, $iv );
        return base64_encode( $iv . $raw );
    }

    private function decrypt( string $cipher, string $key ): ?string {
        $data = base64_decode( $cipher, true );
        if ( false === $data || strlen( $data ) < 17 ) {
            return null;
        }
        $derived = hash( 'sha256', $key, true );
        $iv      = substr( $data, 0, 16 );
        $raw     = substr( $data, 16 );
        $plain   = openssl_decrypt( $raw, self::CIPHER, $derived, OPENSSL_RAW_DATA, $iv );
        return false !== $plain ? $plain : null;
    }
}