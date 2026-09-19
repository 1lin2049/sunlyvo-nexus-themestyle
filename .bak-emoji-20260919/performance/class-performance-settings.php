<?php
/**
 * SunLyvo Nexus — 性能设置页面
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SLV_Performance_Settings {

    public static function register_menu(): void {
        add_submenu_page(
            'slv-dashboard',
            __( '性能', 'sunlyvo-nexus' ),
            __( '性能', 'sunlyvo-nexus' ),
            'manage_options',
            'slv-performance',
            [ __CLASS__, 'render' ]
        );
    }

    public static function render(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( '无权限', 'sunlyvo-nexus' ) );
        }

        $result = null;
        if ( isset( $_POST['slv_perf_nonce'] ) &&
             wp_verify_nonce( sanitize_key( wp_unslash( $_POST['slv_perf_nonce'] ) ), 'slv_perf_save' ) ) {
            $result = self::handle_post();
        }

        $config = SLV_Object_Cache_Installer::get_config();
        $enabled = SLV_Object_Cache_Installer::is_enabled();
        $redis_ext = class_exists( 'Redis' );

        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'SunLyvo 性能设置', 'sunlyvo-nexus' ); ?></h1>

            <?php if ( $result ) : ?>
                <div class="notice notice-<?php echo esc_attr( $result['success'] ? 'success' : 'error' ); ?> is-dismissible">
                    <p><?php echo esc_html( $result['message'] ); ?></p>
                </div>
            <?php endif; ?>

            <div class="card" style="max-width:100%;margin-bottom:20px">
                <h2><?php esc_html_e( '当前状态', 'sunlyvo-nexus' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th><?php esc_html_e( 'PHP Redis 扩展', 'sunlyvo-nexus' ); ?></th>
                        <td><?php echo $redis_ext
                            ? '<span style="color:#00a854">✅ 已安装（版本 ' . esc_html( phpversion( 'redis' ) ) . '）</span>'
                            : '<span style="color:#f5222d">❌ 未安装</span>（请到 1Panel → PHP → 安装扩展 → 勾选 redis）'; ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( '对象缓存', 'sunlyvo-nexus' ); ?></th>
                        <td><?php echo $enabled
                            ? '<span style="color:#00a854">✅ 已启用</span>'
                            : '<span style="color:#fa8c16">⚠️ 未启用</span>'; ?></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( '配置文件', 'sunlyvo-nexus' ); ?></th>
                        <td><code><?php echo esc_html( str_replace( ABSPATH, '', SLV_Object_Cache_Installer::get_config_path() ) ); ?></code></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( 'Drop-in', 'sunlyvo-nexus' ); ?></th>
                        <td><code><?php echo esc_html( str_replace( ABSPATH, '', SLV_Object_Cache_Installer::get_dropin_path() ) ); ?></code></td>
                    </tr>
                </table>
            </div>

            <form method="post">
                <?php wp_nonce_field( 'slv_perf_save', 'slv_perf_nonce' ); ?>

                <div class="card" style="max-width:100%;margin-bottom:20px">
                    <h2><?php esc_html_e( 'Redis 对象缓存', 'sunlyvo-nexus' ); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th><?php esc_html_e( '启用', 'sunlyvo-nexus' ); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="enabled" value="1" <?php checked( ! empty( $config['enabled'] ) ); ?>>
                                    <?php esc_html_e( '启用 Redis 对象缓存（推荐）', 'sunlyvo-nexus' ); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Redis 主机', 'sunlyvo-nexus' ); ?></th>
                            <td><input type="text" name="host" value="<?php echo esc_attr( $config['host'] ?? '127.0.0.1' ); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( '端口', 'sunlyvo-nexus' ); ?></th>
                            <td><input type="number" name="port" value="<?php echo esc_attr( $config['port'] ?? 6379 ); ?>" class="small-text"></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( '密码', 'sunlyvo-nexus' ); ?></th>
                            <td><input type="password" name="password" value="<?php echo esc_attr( $config['password'] ?? '' ); ?>" class="regular-text" autocomplete="new-password">
                                <p class="description"><?php esc_html_e( '1Panel 安装的 Redis 默认为空密码。', 'sunlyvo-nexus' ); ?></p></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( '数据库编号', 'sunlyvo-nexus' ); ?></th>
                            <td><input type="number" name="database" value="<?php echo esc_attr( $config['database'] ?? 0 ); ?>" class="small-text" min="0" max="15">
                                <p class="description"><?php esc_html_e( '0-15，默认 0。多站点建议每站点独立数据库编号。', 'sunlyvo-nexus' ); ?></p></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Key 前缀', 'sunlyvo-nexus' ); ?></th>
                            <td><input type="text" name="prefix" value="<?php echo esc_attr( $config['prefix'] ?? 'slv:' ); ?>" class="regular-text"></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( '连接超时（秒）', 'sunlyvo-nexus' ); ?></th>
                            <td><input type="number" name="timeout" value="<?php echo esc_attr( $config['timeout'] ?? 1.0 ); ?>" step="0.1" class="small-text"></td>
                        </tr>
                    </table>
                </div>

                <p>
                    <button type="submit" name="slv_action" value="save" class="button button-primary button-hero">
                        💾 <?php esc_html_e( '保存并启用', 'sunlyvo-nexus' ); ?>
                    </button>
                    <button type="submit" name="slv_action" value="test" class="button">
                        🔍 <?php esc_html_e( '测试连接', 'sunlyvo-nexus' ); ?>
                    </button>
                    <?php if ( $enabled ) : ?>
                        <button type="submit" name="slv_action" value="uninstall" class="button button-link-delete"
                                onclick="return confirm('<?php echo esc_js( __( '确定要卸载对象缓存吗？', 'sunlyvo-nexus' ) ); ?>')">
                            <?php esc_html_e( '卸载对象缓存', 'sunlyvo-nexus' ); ?>
                        </button>
                    <?php endif; ?>
                </p>
            </form>
        </div>
        <?php
    }

    private static function handle_post(): array {
        $action = sanitize_key( wp_unslash( $_POST['slv_action'] ?? 'save' ) );

        if ( 'uninstall' === $action ) {
            return SLV_Object_Cache_Installer::uninstall();
        }

        $config = [
            'enabled'  => ! empty( $_POST['enabled'] ),
            'host'     => sanitize_text_field( wp_unslash( $_POST['host'] ?? '127.0.0.1' ) ),
            'port'     => (int) ( $_POST['port'] ?? 6379 ),
            'password' => (string) wp_unslash( $_POST['password'] ?? '' ),
            'database' => (int) ( $_POST['database'] ?? 0 ),
            'prefix'   => sanitize_text_field( wp_unslash( $_POST['prefix'] ?? 'slv:' ) ),
            'timeout'  => (float) ( $_POST['timeout'] ?? 1.0 ),
        ];

        if ( 'test' === $action ) {
            return SLV_Object_Cache_Installer::test_connection( $config );
        }

        return SLV_Object_Cache_Installer::install( $config );
    }
}