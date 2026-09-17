<?php
/**
 * SunLyvo Nexus 安全加固
 *
 * 登录限流、文件保护、API Key 加密。
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 登录失败限流。
 *
 * 同一 IP 连续失败 5 次锁定 15 分钟。
 *
 * @since 1.0.0
 */
function slv_login_rate_limit( $username ) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if ( empty( $ip ) ) {
        return $username;
    }

    $transient_key = 'slv_login_fail_' . md5( $ip );
    $lockout_key   = 'slv_login_lockout_' . md5( $ip );

    if ( get_transient( $lockout_key ) ) {
        wp_die(
            esc_html__( '登录失败次数过多，请 15 分钟后重试。', 'sunlyvo-nexus' ),
            esc_html__( '登录受限', 'sunlyvo-nexus' ),
            [ 'response' => 429 ]
        );
    }

    $attempts = (int) get_transient( $transient_key );
    $attempts++;

    if ( $attempts >= 5 ) {
        set_transient( $lockout_key, 1, 15 * MINUTE_IN_SECONDS );
        delete_transient( $transient_key );
    } else {
        set_transient( $transient_key, $attempts, 15 * MINUTE_IN_SECONDS );
    }

    return $username;
}
add_action( 'wp_login_failed', 'slv_login_rate_limit' );

/**
 * 登录成功后清除失败计数。
 *
 * @since 1.0.0
 */
function slv_login_success_clear_attempts(): void {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if ( $ip ) {
        delete_transient( 'slv_login_fail_' . md5( $ip ) );
    }
}
add_action( 'wp_login', 'slv_login_success_clear_attempts' );

/**
 * 保护私有文件目录。
 *
 * 在 wp-content/uploads/slv-private/ 下写入 .htaccess。
 *
 * @since 1.0.0
 */
function slv_protect_private_uploads(): void {
    $upload_dir = wp_upload_dir();
    $private_dir = $upload_dir['basedir'] . '/slv-private';

    if ( ! file_exists( $private_dir ) ) {
        wp_mkdir_p( $private_dir );
    }

    $htaccess = $private_dir . '/.htaccess';
    if ( ! file_exists( $htaccess ) ) {
        file_put_contents( $htaccess, "deny from all\n" );
    }

    $index = $private_dir . '/index.php';
    if ( ! file_exists( $index ) ) {
        file_put_contents( $index, "<?php\n// Silence is golden.\n" );
    }
}

/**
 * AES-256-CBC 加密 API Key。
 *
 * @since 1.0.0
 * @param string $plain 明文。
 * @return string
 */
function slv_encrypt( string $plain ): string {
    if ( ! defined( 'SLV_ENCRYPTION_KEY' ) || empty( SLV_ENCRYPTION_KEY ) ) {
        return $plain;
    }

    $key = hash( 'sha256', SLV_ENCRYPTION_KEY, true );
    $iv  = openssl_random_pseudo_bytes( 16 );
    $encrypted = openssl_encrypt( $plain, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv );

    return base64_encode( $iv . $encrypted );
}

/**
 * AES-256-CBC 解密 API Key。
 *
 * @since 1.0.0
 * @param string $cipher 密文。
 * @return string
 */
function slv_decrypt( string $cipher ): string {
    if ( ! defined( 'SLV_ENCRYPTION_KEY' ) || empty( SLV_ENCRYPTION_KEY ) ) {
        return $cipher;
    }

    $data = base64_decode( $cipher, true );
    if ( false === $data || strlen( $data ) < 17 ) {
        return $cipher;
    }

    $key = hash( 'sha256', SLV_ENCRYPTION_KEY, true );
    $iv  = substr( $data, 0, 16 );
    $raw = substr( $data, 16 );

    $decrypted = openssl_decrypt( $raw, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv );

    return false !== $decrypted ? $decrypted : $cipher;
}

/**
 * 禁用文件编辑器。
 *
 * @since 1.0.0
 */
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
    define( 'DISALLOW_FILE_EDIT', true );
}