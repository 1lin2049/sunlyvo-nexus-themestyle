<?php
/**
 * SunLyvo Nexus — 主密钥管理器
 *
 * 开箱即用：首次访问自动生成密钥，写入 wp-content/slv-keys/slv-encryption.key。
 * 优先级：环境变量 → 常量 → 文件 → 自动生成。
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SLV_Encryption_Manager {

    private const KEY_DIR  = 'slv-keys';
    private const KEY_FILE = 'slv-encryption.key';
    private static ?string $cached_key = null;

    /**
     * 获取主密钥（自动解析优先级 + 自动生成）。
     *
     * @since 1.0.0
     * @return string
     */
    public static function get_key(): string {
        if ( null !== self::$cached_key ) {
            return self::$cached_key;
        }

        // ① 环境变量
        $env = getenv( 'SLV_ENCRYPTION_KEY' );
        if ( ! $env && isset( $_ENV['SLV_ENCRYPTION_KEY'] ) ) {
            $env = (string) $_ENV['SLV_ENCRYPTION_KEY'];
        }
        if ( ! $env && isset( $_SERVER['SLV_ENCRYPTION_KEY'] ) ) {
            $env = (string) $_SERVER['SLV_ENCRYPTION_KEY'];
        }
        if ( $env ) {
            self::$cached_key = $env;
            return $env;
        }

        // ② 常量
        if ( defined( 'SLV_ENCRYPTION_KEY' ) && '' !== SLV_ENCRYPTION_KEY ) {
            self::$cached_key = (string) SLV_ENCRYPTION_KEY;
            return self::$cached_key;
        }

        // ③ 文件
        $file_key = self::read_file_key();
        if ( $file_key ) {
            self::$cached_key = $file_key;
            return $file_key;
        }

        // ④ 自动生成
        $new_key = self::generate_key();
        self::write_file_key( $new_key );
        self::$cached_key = $new_key;
        return $new_key;
    }

    public static function get_source(): string {
        if ( getenv( 'SLV_ENCRYPTION_KEY' ) || isset( $_SERVER['SLV_ENCRYPTION_KEY'] ) ) {
            return 'environment';
        }
        if ( defined( 'SLV_ENCRYPTION_KEY' ) && '' !== SLV_ENCRYPTION_KEY ) {
            return 'constant';
        }
        if ( file_exists( self::get_key_file() ) ) {
            return 'file';
        }
        return 'none';
    }

    public static function get_key_file_path(): string {
        return self::get_key_file();
    }

    public static function is_file_writable(): bool {
        $dir = self::get_key_dir();
        if ( ! file_exists( $dir ) ) {
            return wp_is_writable( WP_CONTENT_DIR );
        }
        return wp_is_writable( $dir );
    }

    /**
     * 轮换密钥。
     *
     * @since 1.0.0
     * @return array{success:bool,message:string}
     */
    public static function rotate(): array {
        $old_key = self::get_key();
        $new_key = self::generate_key();

        $repo = new SLV_Service_Config_Repo();
        $result = $repo->re_encrypt_all( $old_key, $new_key );

        if ( ! $result['success'] ) {
            return $result;
        }

        if ( ! self::write_file_key( $new_key ) ) {
            return [
                'success' => false,
                'message' => __( '新密钥写入失败，请检查 wp-content/slv-keys/ 目录权限。', 'sunlyvo-nexus' ),
            ];
        }

        self::$cached_key = $new_key;

        return [
            'success' => true,
            'message' => sprintf(
                __( '密钥已轮换，重新加密 %d 条配置。', 'sunlyvo-nexus' ),
                $result['count']
            ),
        ];
    }

    private static function generate_key(): string {
        try {
            return base64_encode( random_bytes( 48 ) );
        } catch ( \Exception $e ) {
            return hash( 'sha512', uniqid( (string) mt_rand(), true ) );
        }
    }

    private static function get_key_dir(): string {
        return WP_CONTENT_DIR . '/' . self::KEY_DIR;
    }

    private static function get_key_file(): string {
        return self::get_key_dir() . '/' . self::KEY_FILE;
    }

    private static function read_file_key(): ?string {
        $file = self::get_key_file();
        if ( ! file_exists( $file ) ) {
            return null;
        }
        $content = trim( (string) file_get_contents( $file ) );
        return '' !== $content ? $content : null;
    }

    private static function write_file_key( string $key ): bool {
        $dir = self::get_key_dir();
        if ( ! file_exists( $dir ) ) {
            if ( ! wp_mkdir_p( $dir ) ) {
                return false;
            }
        }
        self::protect_directory( $dir );

        $file = self::get_key_file();
        $result = file_put_contents( $file, $key, LOCK_EX );
        if ( false !== $result ) {
            @chmod( $file, 0600 );
            return true;
        }
        return false;
    }

    private static function protect_directory( string $dir ): void {
        $htaccess = $dir . '/.htaccess';
        if ( ! file_exists( $htaccess ) ) {
            file_put_contents( $htaccess,
                "Order deny,allow\nDeny from all\n" .
                "<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n"
            );
        }
        $index = $dir . '/index.php';
        if ( ! file_exists( $index ) ) {
            file_put_contents( $index, "<?php\n// Silence is golden.\n" );
        }
        $web_config = $dir . '/web.config';
        if ( ! file_exists( $web_config ) ) {
            file_put_contents( $web_config,
                "<?xml version=\"1.0\"?>\n<configuration>\n<system.webServer>\n<authorization>\n" .
                "<deny users=\"*\" />\n</authorization>\n</system.webServer>\n</configuration>\n"
            );
        }
    }
}