<?php
/**
 * SunLyvo Nexus — QA 验证模块
 *
 * 提供自动化测试：
 *   - WP-CLI：wp slv verify-demo
 *   - 后台：工具 → SunLyvo 验证
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$slv_qa_dir = __DIR__;
$slv_qa_files = [
    'class-qa-report.php',
    'class-qa-runner.php',
    'class-deployer.php',
    'checks/class-check-functional.php',
    'checks/class-check-schema.php',
    'checks/class-check-seo.php',
    'checks/class-check-performance.php',
    'checks/class-check-mobile.php',
];

foreach ( $slv_qa_files as $slv_rel ) {
    $slv_file = $slv_qa_dir . '/' . $slv_rel;
    if ( file_exists( $slv_file ) ) {
        require_once $slv_file;
    } elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( "[SunLyvo Nexus] QA module missing: {$slv_rel}" );
    }
}

unset( $slv_qa_dir, $slv_qa_files, $slv_rel, $slv_file );

// ─── WP-CLI 命令 ───────────────────────────────────────────
if ( defined( 'WP_CLI' ) && WP_CLI ) {
    WP_CLI::add_command( 'slv verify-demo', 'slv_cli_verify_demo' );
    WP_CLI::add_command( 'slv deploy-check', 'slv_cli_deploy_check' );
}

/**
 * WP-CLI：验证 Demo。
 *
 * @since 1.0.0
 */
function slv_cli_verify_demo( array $args, array $assoc_args ): void {
    $format = $assoc_args['format'] ?? 'table';
    $save   = isset( $assoc_args['save'] );

    $runner = new SLV_QA_Runner();
    $report = $runner->run_all();

    $report_obj = new SLV_QA_Report( $report );

    switch ( $format ) {
        case 'json':
            WP_CLI::line( $report_obj->to_json() );
            break;
        case 'md':
        case 'markdown':
            WP_CLI::line( $report_obj->to_markdown() );
            break;
        case 'html':
            $path = $report_obj->to_html_file();
            WP_CLI::success( "HTML 报告已保存：{$path}" );
            break;
        default:
            $report_obj->print_table();
    }

    if ( $save ) {
        $path = $report_obj->save_markdown();
        WP_CLI::success( "Markdown 报告已保存：{$path}" );
    }

    $summary = $report['summary'];
    if ( $summary['failed'] > 0 ) {
        WP_CLI::warning( sprintf(
            '验证未通过：通过 %d / 失败 %d / 跳过 %d',
            $summary['passed'], $summary['failed'], $summary['skipped']
        ) );
    } else {
        WP_CLI::success( sprintf(
            '验证通过：共 %d 项，全部通过',
            $summary['passed']
        ) );
    }
}

/**
 * WP-CLI：上线部署检查。
 *
 * @since 1.0.0
 */
function slv_cli_deploy_check( array $args, array $assoc_args ): void {
    $deployer = new SLV_Deployer();
    $result   = $deployer->check_all();

    $table = [];
    foreach ( $result['items'] as $item ) {
        $table[] = [
            '项'   => $item['label'],
            '状态' => $item['passed'] ? '' : '',
            '值'   => $item['value'],
        ];
    }
    WP_CLI\Utils\format_items( 'table', $table, [ '项', '状态', '值' ] );

    if ( $result['can_deploy'] ) {
        WP_CLI::success( '所有上线前置检查通过，可以部署。' );
    } else {
        WP_CLI::warning( '存在未通过项，请修复后再部署。' );
    }
}

// ─── 后台菜单 ─────────────────────────────────────────────
/**
 * 注册 QA 后台菜单。
 *
 * @since 1.0.0
 */
function slv_qa_admin_menu(): void {
    add_management_page(
        __( 'SunLyvo 验证', 'sunlyvo-nexus' ),
        __( 'SunLyvo 验证', 'sunlyvo-nexus' ),
        'manage_options',
        'slv-qa-verify',
        'slv_qa_admin_page'
    );
}
add_action( 'admin_menu', 'slv_qa_admin_menu' );

/**
 * QA 后台页面。
 *
 * @since 1.0.0
 */
function slv_qa_admin_page(): void {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( '无权限', 'sunlyvo-nexus' ) );
    }

    $action = isset( $_GET['slv_action'] ) ? sanitize_key( wp_unslash( $_GET['slv_action'] ) ) : '';
    $report = null;

    if ( 'run' === $action ) {
        check_admin_referer( 'slv_qa_run' );
        $runner = new SLV_QA_Runner();
        $report = $runner->run_all();
    }

    $run_url = wp_nonce_url(
        add_query_arg( [ 'page' => 'slv-qa-verify', 'slv_action' => 'run' ], admin_url( 'tools.php' ) ),
        'slv_qa_run'
    );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'SunLyvo 验证', 'sunlyvo-nexus' ); ?></h1>

        <?php if ( $report ) : ?>
            <?php $report_obj = new SLV_QA_Report( $report ); ?>
            <?php $report_obj->print_admin_html(); ?>
        <?php else : ?>
            <p><?php esc_html_e( '点击下方按钮运行完整的 Demo 验证（52 项）。', 'sunlyvo-nexus' ); ?></p>
            <p>
                <a href="<?php echo esc_url( $run_url ); ?>" class="button button-primary button-hero">
                    <?php esc_html_e( '开始验证', 'sunlyvo-nexus' ); ?>
                </a>
            </p>
            <hr />
            <h2><?php esc_html_e( '命令行方式', 'sunlyvo-nexus' ); ?></h2>
            <pre><code>wp slv verify-demo</code></pre>
            <pre><code>wp slv verify-demo --format=json</code></pre>
            <pre><code>wp slv verify-demo --save</code></pre>
        <?php endif; ?>
    </div>
    <?php
}