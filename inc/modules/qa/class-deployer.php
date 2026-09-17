<?php
/**
 * SunLyvo Nexus — 上线部署检查器
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SLV_Deployer {

    public function check_all(): array {
        $items = [
            $this->check_php_version(),
            $this->check_wp_version(),
            $this->check_db_version(),
            $this->check_tables(),
            $this->check_roles(),
            $this->check_rewrite_rules(),
            $this->check_encryption_key(),
            $this->check_uploads_protected(),
            $this->check_object_cache(),
            $this->check_debug_off(),
            $this->check_ssl_hint(),   // ← 只提示，不强制
        ];

        $can_deploy = true;
        foreach ( $items as $item ) {
            if ( ! $item['passed'] && empty( $item['optional'] ) ) {
                $can_deploy = false;
                break;
            }
        }

        return [
            'items'      => $items,
            'can_deploy' => $can_deploy,
        ];
    }

    private function check_php_version(): array {
        $ok = version_compare( PHP_VERSION, '8.2', '>=' );
        return [ 'label' => 'PHP 版本 ≥ 8.2', 'passed' => $ok, 'value' => PHP_VERSION ];
    }

    private function check_wp_version(): array {
        $wp_version = get_bloginfo( 'version' );
        $ok = version_compare( $wp_version, '6.7', '>=' );
        return [ 'label' => 'WordPress 版本 ≥ 6.7', 'passed' => $ok, 'value' => $wp_version ];
    }

    private function check_db_version(): array {
        $db_v = get_option( 'slv_db_version', '' );
        $ok = $db_v === SLV_DB_VERSION;
        return [ 'label' => '数据库版本匹配', 'passed' => $ok, 'value' => $db_v ?: '未初始化' ];
    }

    private function check_tables(): array {
        global $wpdb;
        $required = [
            $wpdb->prefix . 'slv_products',
            $wpdb->prefix . 'slv_orders',
            $wpdb->prefix . 'slv_carts',
            $wpdb->prefix . 'slv_collections',
            $wpdb->prefix . 'slv_config_registry',
            $wpdb->prefix . 'slv_seo_meta',
            $wpdb->prefix . 'slv_aeo_faqs',
            $wpdb->prefix . 'slv_geo_crawlers',
        ];
        $existing = 0;
        foreach ( $required as $table ) {
            $found = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
            if ( $found === $table ) {
                $existing++;
            }
        }
        return [
            'label'  => '核心数据表已创建',
            'passed' => $existing === count( $required ),
            'value'  => "{$existing}/" . count( $required ) . ' 张',
        ];
    }

    private function check_roles(): array {
        $required = [ 'vendor', 'creator', 'company_admin', 'company_buyer', 'wholesale_customer' ];
        $existing = 0;
        foreach ( $required as $role ) {
            if ( get_role( $role ) ) {
                $existing++;
            }
        }
        return [
            'label'  => '核心角色已注册',
            'passed' => $existing === count( $required ),
            'value'  => "{$existing}/" . count( $required ),
        ];
    }

    private function check_rewrite_rules(): array {
        $rules = get_option( 'rewrite_rules', [] );
        $has_sitemap = false;
        $has_llms    = false;
        foreach ( (array) $rules as $pattern => $query ) {
            if ( str_contains( $pattern, 'slv-sitemap' ) ) $has_sitemap = true;
            if ( str_contains( $pattern, 'llms' ) )         $has_llms    = true;
        }
        return [
            'label'  => '重写规则已注册',
            'passed' => $has_sitemap && $has_llms,
            'value'  => 'sitemap=' . ( $has_sitemap ? '✓' : '✗' ) . ' llms=' . ( $has_llms ? '✓' : '✗' ),
        ];
    }

    private function check_encryption_key(): array {
        if ( class_exists( 'SLV_Encryption_Manager' ) ) {
            $source = SLV_Encryption_Manager::get_source();
            $ok = 'none' !== $source;
            $value = [
                'environment' => '环境变量',
                'constant'    => 'wp-config.php 常量',
                'file'        => '密钥文件',
                'none'        => '未配置',
            ][ $source ] ?? $source;
            return [ 'label' => 'SLV_ENCRYPTION_KEY 已配置', 'passed' => $ok, 'value' => $value ];
        }
        $ok = defined( 'SLV_ENCRYPTION_KEY' ) && '' !== SLV_ENCRYPTION_KEY;
        return [ 'label' => 'SLV_ENCRYPTION_KEY 已配置', 'passed' => $ok, 'value' => $ok ? '已配置' : '未配置' ];
    }

    private function check_uploads_protected(): array {
        $upload_dir = wp_upload_dir();
        $htaccess = $upload_dir['basedir'] . '/slv-private/.htaccess';
        $ok = file_exists( $htaccess ) && str_contains( (string) file_get_contents( $htaccess ), 'deny' );
        return [ 'label' => '私有文件目录已保护', 'passed' => $ok, 'value' => $ok ? '已保护' : '未保护' ];
    }

    private function check_object_cache(): array {
        $ok = file_exists( WP_CONTENT_DIR . '/object-cache.php' );
        $value = $ok ? '已启用' : '未启用（建议在 SunLyvo → 性能 中开启 Redis）';
        return [
            'label'    => '对象缓存已启用',
            'passed'   => $ok,
            'value'    => $value,
            'optional' => true,   // 非阻塞，仅提示
        ];
    }

    private function check_debug_off(): array {
        $ok = ! ( defined( 'WP_DEBUG' ) && WP_DEBUG );
        return [ 'label' => '生产环境关闭 WP_DEBUG', 'passed' => $ok, 'value' => $ok ? '已关闭' : '仍开启' ];
    }

    private function check_ssl_hint(): array {
        $ok = is_ssl() || str_starts_with( home_url(), 'https://' );
        return [
            'label'    => 'HTTPS（建议生产启用）',
            'passed'   => true,   // 始终通过
            'value'    => $ok ? '已启用' : '未启用（本地/内网可忽略）',
            'optional' => true,
        ];
    }
}