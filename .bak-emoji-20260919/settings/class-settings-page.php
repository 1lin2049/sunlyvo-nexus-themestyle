<?php
/**
 * SunLyvo Nexus — 后台设置页面
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SLV_Settings_Page {

    public static function register_menu(): void {
        // 顶级菜单
        add_menu_page(
            __( 'SunLyvo', 'sunlyvo-nexus' ),
            __( 'SunLyvo', 'sunlyvo-nexus' ),
            'manage_options',
            'slv-dashboard',
            [ 'SLV_Dashboard_Page', 'render' ],
            'dashicons-chart-area',
            56
        );

        // 商业化验证（默认页）
        add_submenu_page(
            'slv-dashboard',
            __( '商业化验证', 'sunlyvo-nexus' ),
            __( '商业化验证', 'sunlyvo-nexus' ),
            'manage_options',
            'slv-dashboard',
            [ 'SLV_Dashboard_Page', 'render' ]
        );

        // 服务配置
        add_submenu_page(
            'slv-dashboard',
            __( '服务配置', 'sunlyvo-nexus' ),
            __( '服务配置', 'sunlyvo-nexus' ),
            'manage_options',
            'slv-settings',
            [ __CLASS__, 'render' ]
        );

        // 性能（由 performance 模块注册）
        // 一键修复
        if ( class_exists( 'SLV_Repair_Page' ) ) {
            SLV_Repair_Page::register_menu();
        }
    }

    public static function render(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( '无权限', 'sunlyvo-nexus' ) );
        }

        self::handle_post();

        $source   = SLV_Encryption_Manager::get_source();
        $key_file = SLV_Encryption_Manager::get_key_file_path();
        $writable = SLV_Encryption_Manager::is_file_writable();
        $services = slv_get_services_registry();
        $repo     = new SLV_Service_Config_Repo();

        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'SunLyvo 服务配置', 'sunlyvo-nexus' ); ?></h1>
            <?php self::render_notice(); ?>

            <div class="card" style="max-width:100%;margin-bottom:20px">
                <h2><?php esc_html_e( '🔐 加密密钥状态', 'sunlyvo-nexus' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th><?php esc_html_e( '密钥来源', 'sunlyvo-nexus' ); ?></th>
                        <td>
                            <?php
                            $labels = [
                                'environment' => '<span style="color:#00a854">✅ 环境变量（最安全）</span>',
                                'constant'    => '<span style="color:#00a854">✅ wp-config.php 常量</span>',
                                'file'        => '<span style="color:#00a854">✅ 密钥文件（自动生成）</span>',
                                'none'        => '<span style="color:#fa8c16">⚠️ 未配置（首次访问将自动生成）</span>',
                            ];
                            echo wp_kses_post( $labels[ $source ] ?? $source );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( '密钥文件', 'sunlyvo-nexus' ); ?></th>
                        <td><code><?php echo esc_html( str_replace( ABSPATH, '', $key_file ) ); ?></code></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e( '文件可写', 'sunlyvo-nexus' ); ?></th>
                        <td><?php echo $writable
                            ? '<span style="color:#00a854">✅ 是</span>'
                            : '<span style="color:#f5222d">❌ 否（请检查目录权限）</span>'; ?></td>
                    </tr>
                </table>
                <p><strong><?php esc_html_e( '为什么后台不显示密钥明文？', 'sunlyvo-nexus' ); ?></strong><br>
                    <?php esc_html_e( '密钥与密文必须分开存储：密文在数据库，密钥在文件系统。如果密钥也存数据库，攻击者一次 SQL 注入就能同时拿到两者，加密形同虚设。密钥明文永不显示。', 'sunlyvo-nexus' ); ?>
                </p>
                <form method="post">
                    <?php wp_nonce_field( 'slv_rotate_key', 'slv_rotate_nonce' ); ?>
                    <input type="hidden" name="slv_action" value="rotate_key">
                    <button type="submit" class="button"
                            onclick="return confirm('<?php echo esc_js( __( '轮换密钥将用新密钥重新加密所有已保存的 API Key。确定继续？', 'sunlyvo-nexus' ) ); ?>')">
                        🔄 <?php esc_html_e( '轮换密钥', 'sunlyvo-nexus' ); ?>
                    </button>
                </form>
            </div>

            <h2><?php esc_html_e( '🌐 外部服务配置', 'sunlyvo-nexus' ); ?></h2>
            <p class="description"><?php esc_html_e( '所有 API Key 使用 AES-256-CBC 加密后存储。留空保持不变。', 'sunlyvo-nexus' ); ?></p>

            <form method="post">
                <?php wp_nonce_field( 'slv_save_services', 'slv_services_nonce' ); ?>
                <input type="hidden" name="slv_action" value="save_services">

                <?php foreach ( $services as $service_key => $service ) :
                    $existing = $repo->get( $service_key );
                    ?>
                    <div class="card" style="max-width:100%;margin-bottom:16px">
                        <h3>
                            <span class="dashicons <?php echo esc_attr( $service['icon'] ); ?>" style="margin-right:6px"></span>
                            <?php echo esc_html( $service['label'] ); ?>
                            <?php if ( 'cn' === ( $service['region'] ?? '' ) ) : ?>
                                <span style="background:#f5222d;color:#fff;padding:2px 6px;border-radius:3px;font-size:11px">国内</span>
                            <?php elseif ( 'international' === ( $service['region'] ?? '' ) ) : ?>
                                <span style="background:#0066ff;color:#fff;padding:2px 6px;border-radius:3px;font-size:11px">国际</span>
                            <?php endif; ?>
                            <?php if ( $existing ) : ?>
                                <span style="color:#00a854;font-size:13px;margin-left:6px">✅ 已配置</span>
                            <?php endif; ?>
                        </h3>

                        <table class="form-table">
                            <?php foreach ( $service['fields'] as $field_key => $field ) :
                                $value = ( $existing && isset( $existing[ $field_key ] ) ) ? $existing[ $field_key ] : '';
                                $has_value = '' !== $value;
                                $input_name = "services[{$service_key}][{$field_key}]";
                                ?>
                                <tr>
                                    <th><?php echo esc_html( $field['label'] ); ?><?php echo $field['required'] ? ' <span style="color:#f5222d">*</span>' : ''; ?></th>
                                    <td>
                                        <?php if ( 'textarea' === $field['type'] ) : ?>
                                            <textarea name="<?php echo esc_attr( $input_name ); ?>" rows="4" class="large-text code"
                                                      placeholder="<?php echo $has_value ? esc_attr__( '（已配置，留空保持不变）', 'sunlyvo-nexus' ) : ''; ?>"></textarea>
                                        <?php else : ?>
                                            <input type="<?php echo esc_attr( $field['type'] ); ?>"
                                                   name="<?php echo esc_attr( $input_name ); ?>"
                                                   class="regular-text" autocomplete="off"
                                                   placeholder="<?php echo $has_value ? esc_attr__( '（已配置，留空保持不变）', 'sunlyvo-nexus' ) : ''; ?>">
                                        <?php endif; ?>

                                        <?php if ( $has_value && 'password' === $field['type'] ) : ?>
                                            <span class="description"><?php printf( esc_html__( '当前值：%s', 'sunlyvo-nexus' ), esc_html( self::mask_secret( (string) $value ) ) ); ?></span>
                                        <?php elseif ( $has_value ) : ?>
                                            <span class="description"><?php echo esc_html( (string) $value ); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>

                        <?php if ( $existing ) : ?>
                            <p>
                                <button type="submit" name="slv_delete_service" value="<?php echo esc_attr( $service_key ); ?>"
                                        class="button button-link-delete"
                                        onclick="return confirm('<?php echo esc_js( __( '确定删除该服务配置？', 'sunlyvo-nexus' ) ); ?>')">
                                    <?php esc_html_e( '删除配置', 'sunlyvo-nexus' ); ?>
                                </button>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <p><button type="submit" class="button button-primary button-hero">💾 <?php esc_html_e( '保存所有配置', 'sunlyvo-nexus' ); ?></button></p>
            </form>
        </div>
        <?php
    }

    private static function handle_post(): void {
        if ( ! isset( $_POST['slv_action'] ) ) {
            return;
        }
        $action = sanitize_key( wp_unslash( $_POST['slv_action'] ) );

        if ( 'rotate_key' === $action ) {
            if ( ! isset( $_POST['slv_rotate_nonce'] ) ||
                 ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['slv_rotate_nonce'] ) ), 'slv_rotate_key' ) ) {
                return;
            }
            $result = SLV_Encryption_Manager::rotate();
            set_transient( 'slv_settings_notice', [
                'type'    => $result['success'] ? 'success' : 'error',
                'message' => $result['message'],
            ], 60 );
            wp_safe_redirect( admin_url( 'admin.php?page=slv-settings' ) );
            exit;
        }

        if ( 'save_services' === $action ) {
            if ( ! isset( $_POST['slv_services_nonce'] ) ||
                 ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['slv_services_nonce'] ) ), 'slv_save_services' ) ) {
                return;
            }

            $repo = new SLV_Service_Config_Repo();
            $services = slv_get_services_registry();

            if ( isset( $_POST['slv_delete_service'] ) ) {
                $service_key = sanitize_key( wp_unslash( $_POST['slv_delete_service'] ) );
                if ( isset( $services[ $service_key ] ) ) {
                    $repo->delete( $service_key );
                    set_transient( 'slv_settings_notice', [
                        'type'    => 'success',
                        'message' => sprintf( __( '%s 配置已删除。', 'sunlyvo-nexus' ), $services[ $service_key ]['label'] ),
                    ], 60 );
                    wp_safe_redirect( admin_url( 'admin.php?page=slv-settings' ) );
                    exit;
                }
            }

            $input = isset( $_POST['services'] ) && is_array( $_POST['services'] )
                ? wp_unslash( $_POST['services'] ) : [];
            $saved = 0;

            foreach ( $services as $service_key => $service ) {
                if ( empty( $input[ $service_key ] ) || ! is_array( $input[ $service_key ] ) ) {
                    continue;
                }
                $existing = $repo->get( $service_key ) ?: [];
                $merged = $existing;

                foreach ( $service['fields'] as $field_key => $field ) {
                    $raw = $input[ $service_key ][ $field_key ] ?? '';
                    if ( is_string( $raw ) ) {
                        $raw = trim( $raw );
                    }
                    if ( '' === $raw ) {
                        continue;
                    }
                    $merged[ $field_key ] = self::sanitize_field( $raw, $field['type'] );
                }

                if ( $merged !== $existing ) {
                    $repo->save( $service_key, $merged );
                    $saved++;
                }
            }

            set_transient( 'slv_settings_notice', [
                'type'    => 'success',
                'message' => sprintf( __( '已保存 %d 个服务配置。', 'sunlyvo-nexus' ), $saved ),
            ], 60 );
            wp_safe_redirect( admin_url( 'admin.php?page=slv-settings' ) );
            exit;
        }
    }

    private static function render_notice(): void {
        $notice = get_transient( 'slv_settings_notice' );
        if ( ! $notice ) {
            return;
        }
        delete_transient( 'slv_settings_notice' );
        printf(
            '<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
            esc_attr( $notice['type'] ),
            esc_html( $notice['message'] )
        );
    }

    private static function sanitize_field( $raw, string $type ): string {
        switch ( $type ) {
            case 'email': return sanitize_email( (string) $raw );
            case 'textarea': return (string) $raw;
            default: return sanitize_text_field( (string) $raw );
        }
    }

    private static function mask_secret( string $value ): string {
        $len = strlen( $value );
        if ( $len <= 8 ) {
            return str_repeat( '•', $len );
        }
        return substr( $value, 0, 4 ) . str_repeat( '•', max( 4, $len - 8 ) ) . substr( $value, -4 );
    }
}