<?php
/**
 * SunLyvo Nexus — 一键修复
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SLV_Repair_Page {

    public static function register_menu(): void {
        add_submenu_page(
            'slv-dashboard',
            __( '一键修复', 'sunlyvo-nexus' ),
            __( '一键修复', 'sunlyvo-nexus' ),
            'manage_options',
            'slv-repair',
            [ __CLASS__, 'render' ]
        );
    }

    public static function render(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( '无权限', 'sunlyvo-nexus' ) );
        }

        $result = null;
        if ( isset( $_POST['slv_repair_nonce'] ) &&
             wp_verify_nonce( sanitize_key( wp_unslash( $_POST['slv_repair_nonce'] ) ), 'slv_repair' ) ) {
            $result = self::do_repair();
        }

        $diag = self::diagnose();
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'SunLyvo 一键修复', 'sunlyvo-nexus' ); ?></h1>

            <?php if ( $result ) : ?>
                <div class="notice notice-success"><p><strong><?php esc_html_e( '修复完成', 'sunlyvo-nexus' ); ?></strong></p>
                    <ul>
                        <?php foreach ( $result as $msg ) : ?>
                            <li><?php echo esc_html( $msg ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <h2><?php esc_html_e( '模块加载诊断', 'sunlyvo-nexus' ); ?></h2>
            <table class="widefat striped">
                <thead><tr>
                    <th style="width:60px"><?php esc_html_e( '状态', 'sunlyvo-nexus' ); ?></th>
                    <th><?php esc_html_e( '模块', 'sunlyvo-nexus' ); ?></th>
                    <th><?php esc_html_e( '关键函数/类', 'sunlyvo-nexus' ); ?></th>
                    <th><?php esc_html_e( '文件位置', 'sunlyvo-nexus' ); ?></th>
                </tr></thead>
                <tbody>
                    <?php foreach ( $diag['modules'] as $m ) : ?>
                        <tr>
                            <td><?php echo $m['loaded'] ? '✅' : '❌'; ?></td>
                            <td><strong><?php echo esc_html( $m['name'] ); ?></strong></td>
                            <td><code><?php echo esc_html( $m['symbol'] ); ?></code></td>
                            <td><code><?php echo esc_html( $m['path'] ); ?></code> <?php echo $m['file_exists'] ? '✅' : '<span style="color:#f5222d">文件缺失</span>'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h2><?php esc_html_e( '环境状态', 'sunlyvo-nexus' ); ?></h2>
            <table class="widefat striped">
                <tbody>
                    <tr><td>重写规则（sitemap）</td><td><?php echo $diag['rewrite']['sitemap'] ? '✅' : '❌'; ?></td></tr>
                    <tr><td>重写规则（llms）</td><td><?php echo $diag['rewrite']['llms'] ? '✅' : '❌'; ?></td></tr>
                    <tr><td>加密密钥</td><td><?php echo esc_html( $diag['encryption']['source'] ); ?></td></tr>
                    <tr><td>私有目录保护</td><td><?php echo $diag['private_dir'] ? '✅' : '❌'; ?></td></tr>
                    <tr><td>HTTPS</td><td><?php echo $diag['https'] ? '✅' : '⚠️ 未启用'; ?></td></tr>
                    <tr><td>主题</td><td><?php echo esc_html( $diag['theme'] ); ?></td></tr>
                </tbody>
            </table>

            <h2><?php esc_html_e( '执行修复', 'sunlyvo-nexus' ); ?></h2>
            <p><?php esc_html_e( '将执行：刷新重写规则、生成密钥、保护私有目录、清理缓存。', 'sunlyvo-nexus' ); ?></p>
            <form method="post">
                <?php wp_nonce_field( 'slv_repair', 'slv_repair_nonce' ); ?>
                <button type="submit" class="button button-primary button-hero">🔧 <?php esc_html_e( '一键修复', 'sunlyvo-nexus' ); ?></button>
            </form>
        </div>
        <?php
    }

    private static function diagnose(): array {
        $modules = [
            [ 'name' => 'SEO',  'symbol' => 'slv_seo_output_meta',           'path' => 'inc/modules/seo/module.php',      'loaded' => function_exists( 'slv_seo_output_meta' ) ],
            [ 'name' => 'GEO',  'symbol' => 'slv_geo_generate_llms',          'path' => 'inc/modules/seo/geo/llms-txt.php', 'loaded' => function_exists( 'slv_geo_generate_llms' ) ],
            [ 'name' => 'AEO',  'symbol' => 'slv_aeo_build_faqpage_schema',   'path' => 'inc/modules/seo/aeo/faq-schema.php','loaded' => function_exists( 'slv_aeo_build_faqpage_schema' ) ],
            [ 'name' => 'Reader','symbol'=> 'slv_reader_is_environment',      'path' => 'inc/modules/reader/module.php',    'loaded' => function_exists( 'slv_reader_is_environment' ) ],
            [ 'name' => 'Settings','symbol'=>'SLV_Encryption_Manager',        'path' => 'inc/modules/settings/module.php',  'loaded' => class_exists( 'SLV_Encryption_Manager' ) ],
            [ 'name' => 'Security','symbol'=>'slv_login_rate_limit',          'path' => 'inc/modules/security/module.php',  'loaded' => function_exists( 'slv_login_rate_limit' ) ],
        ];

        foreach ( $modules as &$m ) {
            $m['file_exists'] = file_exists( SLV_THEME_DIR . '/' . $m['path'] );
        }
        unset( $m );

        $rewrite = get_option( 'rewrite_rules', [] );
        $has_sitemap = false;
        $has_llms    = false;
        foreach ( (array) $rewrite as $pattern => $query ) {
            if ( str_contains( $pattern, 'slv-sitemap' ) ) $has_sitemap = true;
            if ( str_contains( $pattern, 'llms' ) ) $has_llms = true;
        }

        $upload = wp_upload_dir();
        $private_dir = file_exists( $upload['basedir'] . '/slv-private/.htaccess' );

        $encryption_source = '未配置';
        if ( class_exists( 'SLV_Encryption_Manager' ) ) {
            $src = SLV_Encryption_Manager::get_source();
            $encryption_source = [
                'environment' => '✅ 环境变量',
                'constant'    => '✅ wp-config.php 常量',
                'file'        => '✅ 密钥文件（自动生成）',
                'none'        => '⚠️ 未配置',
            ][ $src ] ?? $src;
        }

        return [
            'modules'      => $modules,
            'rewrite'      => [ 'sitemap' => $has_sitemap, 'llms' => $has_llms ],
            'encryption'   => [ 'source' => $encryption_source ],
            'private_dir'  => $private_dir,
            'https'        => is_ssl() || str_starts_with( home_url(), 'https://' ),
            'theme'        => wp_get_theme()->get( 'Name' ) . ' ' . wp_get_theme()->get( 'Version' ),
        ];
    }

    private static function do_repair(): array {
        $messages = [];

        // 1. 刷新重写规则
        flush_rewrite_rules( true );
        $messages[] = '✅ 重写规则已刷新';

        // 2. 生成密钥
        if ( class_exists( 'SLV_Encryption_Manager' ) ) {
            SLV_Encryption_Manager::get_key();
            $messages[] = '✅ 加密密钥已就绪：' . SLV_Encryption_Manager::get_source();
        }

        // 3. 保护私有目录
        if ( function_exists( 'slv_protect_private_uploads' ) ) {
            slv_protect_private_uploads();
            $messages[] = '✅ 私有目录已保护';
        } else {
            // 手动执行
            $upload = wp_upload_dir();
            $dir = $upload['basedir'] . '/slv-private';
            if ( ! file_exists( $dir ) ) {
                wp_mkdir_p( $dir );
            }
            if ( ! file_exists( $dir . '/.htaccess' ) ) {
                file_put_contents( $dir . '/.htaccess', "Order deny,allow\nDeny from all\n<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n" );
            }
            if ( ! file_exists( $dir . '/index.php' ) ) {
                file_put_contents( $dir . '/index.php', "<?php\n// Silence is golden.\n" );
            }
            $messages[] = '✅ 私有目录已保护';
        }

        // 4. 清理缓存
        wp_cache_flush();
        $messages[] = '✅ 对象缓存已清理';

        // 5. 检查 SEO 模块
        if ( ! function_exists( 'slv_seo_output_meta' ) ) {
            $messages[] = '❌ SEO 模块仍未加载 — 请检查 inc/modules/seo/ 目录结构';
        } else {
            $messages[] = '✅ SEO 模块已加载';
        }

        return $messages;
    }
}