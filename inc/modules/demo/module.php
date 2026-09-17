<?php
/**
 * SunLyvo Nexus — Demo 内容生产模块
 *
 * 提供三种触发方式：
 *   1. WP-CLI：wp slv seed-demo
 *   2. 后台顶级菜单「SunLyvo Demo」
 *   3. 后台「工具」→「生产演示内容」
 *   4. 后台「外观」→「生产演示内容」
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$slv_demo_dir   = __DIR__;
$slv_demo_files = [
    'class-demo-seeder.php',
    'content-posts.php',
    'content-wiki.php',
    'content-faq.php',
    'content-products.php',
    'content-collections.php',
    'content-pages.php',
];

foreach ( $slv_demo_files as $slv_rel ) {
    $slv_file = $slv_demo_dir . '/' . $slv_rel;
    if ( file_exists( $slv_file ) ) {
        require_once $slv_file;
    } elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( "[SunLyvo Nexus] Demo module missing: {$slv_rel}" );
    }
}

unset( $slv_demo_dir, $slv_demo_files, $slv_rel, $slv_file );

// ─── WP-CLI 命令 ───────────────────────────────────────────
if ( defined( 'WP_CLI' ) && WP_CLI ) {
    WP_CLI::add_command( 'slv seed-demo', 'slv_cli_seed_demo' );
}

/**
 * WP-CLI：生产演示内容。
 *
 * @since 1.0.0
 */
function slv_cli_seed_demo( array $args, array $assoc_args ): void {
    $force  = isset( $assoc_args['force'] );
    $seeder = new SLV_Demo_Seeder( $force );
    $stats  = $seeder->run();

    WP_CLI::success( sprintf(
        '演示内容生产完成：文章 %d，百科 %d，FAQ %d，商品 %d，合集 %d，页面 %d',
        $stats['posts'], $stats['wiki'], $stats['faq'],
        $stats['products'], $stats['collections'], $stats['pages']
    ) );
}

// ─── 后台入口 ─────────────────────────────────────────────

/**
 * 注册三重后台菜单。
 *
 * 1. 顶级菜单「SunLyvo Demo」
 * 2. 工具菜单下
 * 3. 外观菜单下
 *
 * @since 1.0.0
 */
function slv_demo_admin_menu(): void {
    // ① 顶级菜单
    add_menu_page(
        __( 'SunLyvo Demo', 'sunlyvo-nexus' ),
        __( 'SunLyvo Demo', 'sunlyvo-nexus' ),
        'manage_options',
        'slv-seed-demo',
        'slv_demo_admin_page',
        'dashicons-database-import',
        58
    );

    // ② 工具菜单下
    add_management_page(
        __( '生产演示内容', 'sunlyvo-nexus' ),
        __( '生产演示内容', 'sunlyvo-nexus' ),
        'manage_options',
        'slv-seed-demo-tools',
        'slv_demo_admin_page'
    );

    // ③ 外观菜单下
    add_theme_page(
        __( '生产演示内容', 'sunlyvo-nexus' ),
        __( '生产演示内容', 'sunlyvo-nexus' ),
        'manage_options',
        'slv-seed-demo-theme',
        'slv_demo_admin_page'
    );
}
add_action( 'admin_menu', 'slv_demo_admin_menu' );

/**
 * 后台触发页面。
 *
 * @since 1.0.0
 */
function slv_demo_admin_page(): void {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( '无权限', 'sunlyvo-nexus' ) );
    }

    $force  = isset( $_GET['force'] ) && '1' === $_GET['force'];
    $action = isset( $_GET['slv_action'] ) ? sanitize_key( wp_unslash( $_GET['slv_action'] ) ) : '';
    $stats  = null;

    if ( 'seed' === $action ) {
        check_admin_referer( 'slv_seed_demo' );
        $seeder = new SLV_Demo_Seeder( $force );
        $stats  = $seeder->run();
    }

    $page_url = admin_url( 'admin.php?page=slv-seed-demo' );
    $seed_url = wp_nonce_url(
        add_query_arg( [ 'slv_action' => 'seed', 'force' => $force ? '1' : '0' ], $page_url ),
        'slv_seed_demo'
    );
    $force_url = add_query_arg( [ 'force' => $force ? '0' : '1' ], $page_url );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( '生产演示内容', 'sunlyvo-nexus' ); ?></h1>

        <?php if ( $stats ) : ?>
            <div class="notice notice-success is-dismissible">
                <p><strong><?php esc_html_e( '生产完成', 'sunlyvo-nexus' ); ?></strong></p>
                <ul>
                    <li><?php printf( esc_html__( '博客：%d 篇', 'sunlyvo-nexus' ), (int) $stats['posts'] ); ?></li>
                    <li><?php printf( esc_html__( '百科：%d 篇', 'sunlyvo-nexus' ), (int) $stats['wiki'] ); ?></li>
                    <li><?php printf( esc_html__( 'FAQ：%d 篇', 'sunlyvo-nexus' ), (int) $stats['faq'] ); ?></li>
                    <li><?php printf( esc_html__( '商品：%d 个', 'sunlyvo-nexus' ), (int) $stats['products'] ); ?></li>
                    <li><?php printf( esc_html__( '合集：%d 个', 'sunlyvo-nexus' ), (int) $stats['collections'] ); ?></li>
                    <li><?php printf( esc_html__( '页面：%d 个', 'sunlyvo-nexus' ), (int) $stats['pages'] ); ?></li>
                </ul>
            </div>
        <?php endif; ?>

        <p><?php esc_html_e( '点击下方按钮将生成 Demo 阶段的 10 篇博客、5 篇百科、5 篇 FAQ、5 个商品、2 个合集、5 个页面。已存在的内容会被跳过（幂等）。', 'sunlyvo-nexus' ); ?></p>

        <p>
            <strong><?php esc_html_e( '当前模式：', 'sunlyvo-nexus' ); ?></strong>
            <?php echo $force
                ? esc_html__( '强制重建（覆盖已存在内容）', 'sunlyvo-nexus' )
                : esc_html__( '幂等（跳过已存在内容）', 'sunlyvo-nexus' ); ?>
            &nbsp;
            <a href="<?php echo esc_url( $force_url ); ?>" class="button button-secondary">
                <?php echo $force
                    ? esc_html__( '切换为幂等模式', 'sunlyvo-nexus' )
                    : esc_html__( '切换为强制重建', 'sunlyvo-nexus' ); ?>
            </a>
        </p>

        <p>
            <a href="<?php echo esc_url( $seed_url ); ?>" class="button button-primary button-hero">
                <?php esc_html_e( '开始生产', 'sunlyvo-nexus' ); ?>
            </a>
        </p>

        <hr />

        <h2><?php esc_html_e( '其他触发方式', 'sunlyvo-nexus' ); ?></h2>
        <p><?php esc_html_e( '如果后台页面无法使用，可通过 WP-CLI 执行：', 'sunlyvo-nexus' ); ?></p>
        <pre><code>wp slv seed-demo</code></pre>
        <p><?php esc_html_e( '强制重建：', 'sunlyvo-nexus' ); ?></p>
        <pre><code>wp slv seed-demo --force</code></pre>
    </div>
    <?php
}