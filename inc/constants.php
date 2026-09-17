<?php
/**
 * SunLyvo Nexus 常量定义
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'SLV_VERSION', '1.0.0' );
define( 'SLV_DB_VERSION', '1.0.0' );
define( 'SLV_THEME_DIR', get_template_directory() );
define( 'SLV_THEME_URL', get_template_directory_uri() );
define( 'SLV_INC_DIR', SLV_THEME_DIR . '/inc' );
define( 'SLV_ASSETS_URL', SLV_THEME_URL . '/assets' );
define( 'SLV_REST_NAMESPACE', 'slv/v1' );
define( 'SLV_TEXT_DOMAIN', 'sunlyvo-nexus' );
define( 'SLV_CODE_PREFIX', 'slv_' );
// define( 'SLV_ENCRYPTION_KEY', defined( 'SLV_ENCRYPTION_KEY' ) ? SLV_ENCRYPTION_KEY : '' );
// ═══ 主密钥（由 SLV_Encryption_Manager 动态解析） ═══
// 优先级：环境变量 → wp-config.php 常量 → 文件 → 自动生成
// 此处仅声明占位，实际使用请调用 SLV_Encryption_Manager::get_key()
if ( ! defined( 'SLV_ENCRYPTION_KEY' ) ) {
    define( 'SLV_ENCRYPTION_KEY', '' );
}
define( 'SLV_MIN_PHP_VERSION', '8.2' );
define( 'SLV_MIN_WP_VERSION', '6.7' );