<?php
/**
 * SunLyvo Nexus — QA 报告生成器
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * QA 报告生成器。
 *
 * @since 1.0.0
 */
class SLV_QA_Report {

    private array $report;

    public function __construct( array $report ) {
        $this->report = $report;
    }

    /**
     * WP-CLI 表格输出。
     *
     * @since 1.0.0
     */
    public function print_table(): void {
        foreach ( $this->report['groups'] as $group ) {
            \WP_CLI::line( '' );
            \WP_CLI::line( '═══ ' . $group['label'] . ' ═══' );
            \WP_CLI::line( '' );

            $rows = [];
            foreach ( $group['items'] as $item ) {
                $icon = match ( $item['status'] ) {
                    'pass' => '',
                    'fail' => '',
                    default => '⏭️',
                };
                $rows[] = [
                    '状态' => $icon,
                    '项'   => $item['label'],
                    '值'   => mb_substr( $item['value'], 0, 60 ),
                ];
            }
            \WP_CLI\Utils\format_items( 'table', $rows, [ '状态', '项', '值' ] );
        }
    }

    /**
     * 输出 JSON。
     *
     * @since 1.0.0
     */
    public function to_json(): string {
        return wp_json_encode( $this->report, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
    }

    /**
     * 输出 Markdown。
     *
     * @since 1.0.0
     */
    public function to_markdown(): string {
        $r = $this->report;
        $out  = "# SunLyvo Nexus Demo 验证报告\n\n";
        $out .= "- **站点**：{$r['site_url']}\n";
        $out .= "- **生成时间**：{$r['generated']}\n";
        $out .= "- **WordPress**：{$r['wp_version']}\n";
        $out .= "- **PHP**：{$r['php_version']}\n";
        $out .= "- **耗时**：{$r['duration']}s\n\n";

        $s = $r['summary'];
        $out .= "## 总览\n\n";
        $out .= "| 项 | 数量 |\n|---|---|\n";
        $out .= "| 总数 | {$s['total']} |\n";
        $out .= "| 通过 | {$s['passed']} |\n";
        $out .= "| 失败 | {$s['failed']} |\n";
        $out .= "| 跳过 | {$s['skipped']} |\n\n";

        foreach ( $r['groups'] as $group ) {
            $out .= "## {$group['label']}\n\n";
            $out .= "| 状态 | 项 | 值 |\n|---|---|---|\n";
            foreach ( $group['items'] as $item ) {
                $icon = match ( $item['status'] ) {
                    'pass' => '',
                    'fail' => '',
                    default => '⏭️',
                };
                $out .= "| {$icon} | {$item['label']} | {$item['value']} |\n";
            }
            $out .= "\n";
        }

        return $out;
    }

    /**
     * 保存 Markdown 报告。
     *
     * @since 1.0.0
     */
    public function save_markdown(): string {
        $dir = WP_CONTENT_DIR . '/slv-reports';
        if ( ! file_exists( $dir ) ) {
            wp_mkdir_p( $dir );
        }
        $path = $dir . '/demo-verify-' . date( 'Ymd-His' ) . '.md';
        file_put_contents( $path, $this->to_markdown() );
        return $path;
    }

    /**
     * 生成 HTML 文件。
     *
     * @since 1.0.0
     */
    public function to_html_file(): string {
        $dir = WP_CONTENT_DIR . '/slv-reports';
        if ( ! file_exists( $dir ) ) {
            wp_mkdir_p( $dir );
        }
        $path = $dir . '/demo-verify-' . date( 'Ymd-His' ) . '.html';

        $html  = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>SunLyvo Nexus 验证报告</title>';
        $html .= '<style>body{font-family:-apple-system,sans-serif;max-width:960px;margin:40px auto;padding:0 20px;line-height:1.6}';
        $html .= 'table{width:100%;border-collapse:collapse;margin:16px 0}th,td{padding:8px;border:1px solid #e8e8e8;text-align:left}';
        $html .= 'th{background:#fafafa}h1{border-bottom:2px solid #0066ff;padding-bottom:8px}';
        $html .= '.pass{color:#00a854}.fail{color:#f5222d}.skip{color:#999}</style></head><body>';
        $html .= '<h1>SunLyvo Nexus Demo 验证报告</h1>';

        $r = $this->report;
        $html .= '<p><strong>站点</strong>：' . esc_html( $r['site_url'] ) . '<br>';
        $html .= '<strong>生成时间</strong>：' . esc_html( $r['generated'] ) . '<br>';
        $html .= '<strong>WordPress</strong>：' . esc_html( $r['wp_version'] ) . '<br>';
        $html .= '<strong>PHP</strong>：' . esc_html( $r['php_version'] ) . '</p>';

        $s = $r['summary'];
        $html .= '<h2>总览</h2><p>';
        $html .= '总数 <strong>' . $s['total'] . '</strong> · ';
        $html .= '<span class="pass">通过 ' . $s['passed'] . '</span> · ';
        $html .= '<span class="fail">失败 ' . $s['failed'] . '</span> · ';
        $html .= '<span class="skip">跳过 ' . $s['skipped'] . '</span></p>';

        foreach ( $r['groups'] as $group ) {
            $html .= '<h2>' . esc_html( $group['label'] ) . '</h2>';
            $html .= '<table><thead><tr><th>状态</th><th>项</th><th>值</th></tr></thead><tbody>';
            foreach ( $group['items'] as $item ) {
                $class = esc_attr( $item['status'] );
                $icon = match ( $item['status'] ) {
                    'pass' => '',
                    'fail' => '',
                    default => '⏭️',
                };
                $html .= '<tr>';
                $html .= '<td class="' . $class . '">' . $icon . '</td>';
                $html .= '<td>' . esc_html( $item['label'] ) . '</td>';
                $html .= '<td>' . esc_html( $item['value'] ) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
        }

        $html .= '</body></html>';
        file_put_contents( $path, $html );
        return $path;
    }

    /**
     * 后台 HTML 输出。
     *
     * @since 1.0.0
     */
    public function print_admin_html(): void {
        $r = $this->report;
        $s = $r['summary'];

        echo '<div class="notice notice-info"><p>';
        echo '<strong>' . esc_html__( '总览', 'sunlyvo-nexus' ) . '</strong>：';
        printf(
            esc_html__( '总数 %1$d · 通过 %2$d · 失败 %3$d · 跳过 %4$d · 耗时 %5$ss', 'sunlyvo-nexus' ),
            $s['total'], $s['passed'], $s['failed'], $s['skipped'], $r['duration']
        );
        echo '</p></div>';

        foreach ( $r['groups'] as $group ) {
            echo '<h2>' . esc_html( $group['label'] ) . '</h2>';
            echo '<table class="widefat striped"><thead><tr>';
            echo '<th style="width:60px">' . esc_html__( '状态', 'sunlyvo-nexus' ) . '</th>';
            echo '<th style="width:240px">' . esc_html__( '项', 'sunlyvo-nexus' ) . '</th>';
            echo '<th>' . esc_html__( '值', 'sunlyvo-nexus' ) . '</th>';
            echo '</tr></thead><tbody>';

            foreach ( $group['items'] as $item ) {
                $icon = match ( $item['status'] ) {
                    'pass' => '',
                    'fail' => '',
                    default => '⏭️',
                };
                echo '<tr>';
                echo '<td>' . $icon . '</td>';
                echo '<td><strong>' . esc_html( $item['label'] ) . '</strong></td>';
                echo '<td>' . esc_html( $item['value'] ) . '</td>';
                echo '</tr>';
            }

            echo '</tbody></table>';
        }

        echo '<hr />';
        echo '<h2>' . esc_html__( '保存报告', 'sunlyvo-nexus' ) . '</h2>';
        echo '<p>' . esc_html__( '运行 WP-CLI 保存 Markdown 报告：', 'sunlyvo-nexus' ) . '</p>';
        echo '<pre><code>wp slv verify-demo --save</code></pre>';
        echo '<p>' . esc_html__( '生成 HTML 报告：', 'sunlyvo-nexus' ) . '</p>';
        echo '<pre><code>wp slv verify-demo --format=html</code></pre>';
    }
}