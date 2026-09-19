<?php
/**
 * SunLyvo Nexus — 主题初始化向导
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const SLV_SETUP_STEPS = [
    1 => '品牌与站点',
    2 => '默认模板',
    3 => '服务配置',
    4 => '中台部署',
    5 => '完成',
];

add_action( 'after_switch_theme', static function () {
    if ( ! get_option( 'slv_setup_completed' ) ) {
        set_transient( 'slv_activation_redirect', 1, 60 );
    }
} );

add_action( 'admin_init', static function () {
    if ( ! get_transient( 'slv_activation_redirect' ) ) {
        return;
    }
    delete_transient( 'slv_activation_redirect' );
    if ( isset( $_GET['activate-multi'] ) || is_network_admin() ) {
        return;
    }
    wp_safe_redirect( admin_url( 'admin.php?page=slv-setup&step=1' ) );
    exit;
} );

add_action( 'admin_menu', static function () {
    add_submenu_page(
        null,
        __( 'SunLyvo 设置向导', 'sunlyvo-nexus' ),
        '',
        'manage_options',
        'slv-setup',
        'slv_setup_render'
    );
} );

function slv_setup_render(): void {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( '无权限', 'sunlyvo-nexus' ) );
    }

    $step = isset( $_GET['step'] ) ? (int) $_GET['step'] : 1;
    $step = max( 1, min( 5, $step ) );

    if ( 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) && isset( $_POST['slv_setup_nonce'] ) ) {
        $nonce = sanitize_key( wp_unslash( $_POST['slv_setup_nonce'] ) );
        if ( wp_verify_nonce( $nonce, 'slv_setup_step_' . $step ) ) {
            slv_setup_handle_step( $step );
        }
    }

    $data = slv_setup_get_data();
    wp_enqueue_style( 'slv-setup', SLV_ASSETS_URL . '/css/setup.css', [], SLV_VERSION );
    ?>
    <div class="wrap slv-setup">
        <h1>SunLyvo Nexus 设置向导</h1>
        <div class="slv-setup__steps">
            <?php foreach ( SLV_SETUP_STEPS as $n => $label ) :
                $cls = $n === $step ? 'is-active' : ( $n < $step ? 'is-done' : '' );
                ?>
                <div class="slv-setup__step <?php echo esc_attr( $cls ); ?>">
                    <span class="slv-setup__step-num"><?php echo (int) $n; ?></span>
                    <span class="slv-setup__step-label"><?php echo esc_html( $label ); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <form method="post" class="slv-setup__form">
            <?php wp_nonce_field( 'slv_setup_step_' . $step, 'slv_setup_nonce' ); ?>
            <?php slv_setup_render_step( $step, $data ); ?>
        </form>
    </div>
    <?php
}

function slv_setup_get_data(): array {
    return [
        'blogname'        => get_option( 'blogname', 'SunLyvo Nexus' ),
        'blogdescription' => get_option( 'blogdescription', '内容电商新时代' ),
        'contact_email'   => get_option( 'slv_setup_contact_email', get_option( 'admin_email' ) ),
        'contact_phone'   => get_option( 'slv_setup_contact_phone', '' ),
        'timezone'        => get_option( 'timezone_string', 'Asia/Shanghai' ),
        'currency'        => get_option( 'slv_default_currency', 'CNY' ),
        'template'        => get_option( 'slv_site_template', 'brand' ),
        'deploy_mode'     => get_option( 'slv_admin_deploy_mode', 'subdir' ),
        'admin_url'       => get_option( 'slv_admin_url', home_url( '/app/' ) ),
    ];
}

function slv_setup_handle_step( int $step ): void {
    switch ( $step ) {
        case 1:
            update_option( 'blogname', sanitize_text_field( wp_unslash( $_POST['blogname'] ?? '' ) ) );
            update_option( 'blogdescription', sanitize_text_field( wp_unslash( $_POST['blogdescription'] ?? '' ) ) );
            update_option( 'slv_setup_contact_email', sanitize_email( wp_unslash( $_POST['contact_email'] ?? '' ) ) );
            update_option( 'slv_setup_contact_phone', sanitize_text_field( wp_unslash( $_POST['contact_phone'] ?? '' ) ) );
            update_option( 'timezone_string', sanitize_text_field( wp_unslash( $_POST['timezone'] ?? 'Asia/Shanghai' ) ) );
            update_option( 'slv_default_currency', sanitize_text_field( wp_unslash( $_POST['currency'] ?? 'CNY' ) ) );
            break;
        case 2:
            update_option( 'slv_site_template', sanitize_key( wp_unslash( $_POST['template'] ?? 'brand' ) ) );
            break;
        case 3:
            break;
        case 4:
            update_option( 'slv_admin_deploy_mode', sanitize_key( wp_unslash( $_POST['deploy_mode'] ?? 'subdir' ) ) );
            update_option( 'slv_admin_url', esc_url_raw( wp_unslash( $_POST['admin_url'] ?? '' ) ) );
            if ( function_exists( 'slv_write_admin_config_js' ) ) {
                slv_write_admin_config_js();
            }
            break;
        case 5:
            update_option( 'slv_setup_completed', 1 );
            wp_safe_redirect( admin_url( 'admin.php?page=slv-dashboard' ) );
            exit;
    }
    wp_safe_redirect( admin_url( 'admin.php?page=slv-setup&step=' . ( $step + 1 ) ) );
    exit;
}

function slv_setup_render_step( int $step, array $data ): void {
    switch ( $step ) {
        case 1:
            ?>
            <h2>品牌与站点信息</h2>
            <table class="form-table">
                <tr><th><label for="blogname">站点名称 *</label></th>
                    <td><input type="text" id="blogname" name="blogname" value="<?php echo esc_attr( $data['blogname'] ); ?>" class="regular-text" required></td></tr>
                <tr><th><label for="blogdescription">站点描述</label></th>
                    <td><input type="text" id="blogdescription" name="blogdescription" value="<?php echo esc_attr( $data['blogdescription'] ); ?>" class="regular-text"></td></tr>
                <tr><th><label for="contact_email">联系邮箱</label></th>
                    <td><input type="email" id="contact_email" name="contact_email" value="<?php echo esc_attr( $data['contact_email'] ); ?>" class="regular-text"></td></tr>
                <tr><th><label for="contact_phone">联系电话</label></th>
                    <td><input type="text" id="contact_phone" name="contact_phone" value="<?php echo esc_attr( $data['contact_phone'] ); ?>" class="regular-text"></td></tr>
                <tr><th><label for="timezone">时区</label></th>
                    <td><select id="timezone" name="timezone">
                        <?php foreach ( array_filter( timezone_identifiers_list(), static fn( $z ) => str_starts_with( $z, 'Asia/' ) ) as $z ) :
                            printf( '<option value="%s" %s>%s</option>', esc_attr( $z ), selected( $data['timezone'], $z, false ), esc_html( $z ) );
                        endforeach; ?>
                    </select></td></tr>
                <tr><th><label for="currency">默认货币</label></th>
                    <td><select id="currency" name="currency">
                        <?php foreach ( [ 'CNY' => '人民币 ¥', 'USD' => '美元 $', 'EUR' => '欧元 €', 'GBP' => '英镑 £', 'JPY' => '日元 ¥' ] as $code => $label ) : ?>
                            <option value="<?php echo esc_attr( $code ); ?>" <?php selected( $data['currency'], $code ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select></td></tr>
            </table>
            <?php
            break;
        case 2:
            $templates = class_exists( 'SLV_Template_Registry' ) ? SLV_Template_Registry::all() : [];
            ?>
            <h2>默认行业模板</h2>
            <div class="slv-setup__templates">
                <?php foreach ( $templates as $slug => $tpl ) : ?>
                    <label class="slv-setup__template <?php echo $data['template'] === $slug ? 'is-active' : ''; ?>">
                        <input type="radio" name="template" value="<?php echo esc_attr( $slug ); ?>" <?php checked( $data['template'], $slug ); ?>>
                        <strong><?php echo esc_html( $tpl['label'] ); ?></strong>
                        <p><?php echo esc_html( $tpl['description'] ); ?></p>
                    </label>
                <?php endforeach; ?>
            </div>
            <?php
            break;
        case 3:
            ?>
            <h2>外部服务配置</h2>
            <p>可选。稍后可在「SunLyvo → 服务配置」中填写 API Key。</p>
            <p><a href="<?php echo esc_url( admin_url( 'admin.php?page=slv-settings' ) ); ?>" class="button" target="_blank">打开服务配置</a></p>
            <?php
            break;
        case 4:
            ?>
            <h2>中台部署方式</h2>
            <div class="slv-setup__deploy">
                <label class="slv-setup__deploy-item <?php echo $data['deploy_mode'] === 'subdomain' ? 'is-active' : ''; ?>">
                    <input type="radio" name="deploy_mode" value="subdomain" <?php checked( $data['deploy_mode'], 'subdomain' ); ?>>
                    <strong>独立子域名</strong>
                    <p>如 admin.sunlyvo.com</p>
                </label>
                <label class="slv-setup__deploy-item <?php echo $data['deploy_mode'] === 'subdir' ? 'is-active' : ''; ?>">
                    <input type="radio" name="deploy_mode" value="subdir" <?php checked( $data['deploy_mode'], 'subdir' ); ?>>
                    <strong>子目录</strong>
                    <p>如 sunlyvo.com/app/</p>
                </label>
                <label class="slv-setup__deploy-item <?php echo $data['deploy_mode'] === 'iframe' ? 'is-active' : ''; ?>">
                    <input type="radio" name="deploy_mode" value="iframe" <?php checked( $data['deploy_mode'], 'iframe' ); ?>>
                    <strong>嵌入主题</strong>
                    <p>iframe 挂载</p>
                </label>
            </div>
            <table class="form-table">
                <tr><th><label for="admin_url">中台访问地址</label></th>
                    <td><input type="url" id="admin_url" name="admin_url" value="<?php echo esc_attr( $data['admin_url'] ); ?>" class="regular-text"></td></tr>
            </table>
            <?php
            break;
        case 5:
            ?>
            <h2>🎉 配置完成</h2>
            <p>SunLyvo Nexus 已配置完成。接下来：</p>
            <ol class="slv-setup__checklist">
                <li>构建中台：<code>cd src && pnpm build</code></li>
                <li>部署到：<code><?php echo esc_html( $data['admin_url'] ); ?></code></li>
                <li>访问「SunLyvo → 模板管理」</li>
                <li>访问「SunLyvo → 服务配置」</li>
                <li>访问「SunLyvo → 生产演示内容」</li>
            </ol>
            <?php
            break;
    }
    ?>
    <div class="slv-setup__actions">
        <?php if ( $step > 1 ) : ?>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=slv-setup&step=' . ( $step - 1 ) ) ); ?>" class="button">← 上一步</a>
        <?php else : ?><span></span><?php endif; ?>
        <button type="submit" class="button button-primary button-hero">
            <?php echo $step < 5 ? '下一步 →' : '完成'; ?>
        </button>
    </div>
    <?php
}