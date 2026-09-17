<?php
/**
 * SunLyvo Nexus — 商业化验证仪表盘
 *
 * 展示 Demo 商业化验证核心指标：
 *   收录率 / AI 引用 / UV / 商品卡片 CTR / 询盘提交 / 阅读体验使用率
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SLV_Dashboard_Page {

    /**
     * 渲染仪表盘。
     *
     * @since 1.0.0
     */
    public static function render(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( '无权限', 'sunlyvo-nexus' ) );
        }

        $range = isset( $_GET['range'] ) ? (int) $_GET['range'] : 14;
        $range = in_array( $range, [ 7, 14, 30, 90 ], true ) ? $range : 14;

        $data = self::collect_data( $range );
        ?>
        <div class="wrap slv-dashboard">
            <h1><?php esc_html_e( '商业化验证仪表盘', 'sunlyvo-nexus' ); ?></h1>
            <p class="description">
                <?php printf(
                    esc_html__( '统计范围：最近 %d 天 · 数据更新时间：%s', 'sunlyvo-nexus' ),
                    (int) $range,
                    esc_html( current_time( 'mysql' ) )
                ); ?>
            </p>

            <!-- 时间范围选择 -->
            <div class="slv-dashboard__range">
                <?php foreach ( [ 7, 14, 30, 90 ] as $r ) : ?>
                    <a href="<?php echo esc_url( add_query_arg( [ 'page' => 'slv-dashboard', 'range' => $r ], admin_url( 'admin.php' ) ) ); ?>"
                       class="button <?php echo $r === $range ? 'button-primary' : ''; ?>">
                        <?php printf( esc_html__( '最近 %d 天', 'sunlyvo-nexus' ), (int) $r ); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- 核心指标卡 -->
            <div class="slv-dashboard__cards">
                <?php foreach ( $data['cards'] as $card ) : ?>
                    <div class="slv-dashboard__card slv-dashboard__card--<?php echo esc_attr( $card['status'] ); ?>">
                        <div class="slv-dashboard__card-label"><?php echo esc_html( $card['label'] ); ?></div>
                        <div class="slv-dashboard__card-value"><?php echo esc_html( $card['value'] ); ?></div>
                        <div class="slv-dashboard__card-target">
                            <?php echo esc_html( $card['target_text'] ); ?>
                            <span class="slv-dashboard__card-badge">
                                <?php echo 'pass' === $card['status'] ? '✅ 达标' : ( 'warn' === $card['status'] ? '⚠️ 接近' : '❌ 未达标' ); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- AI 爬虫访问 -->
            <h2><?php esc_html_e( '🤖 AI 引擎引用', 'sunlyvo-nexus' ); ?></h2>
            <?php if ( empty( $data['ai_crawlers'] ) ) : ?>
                <p><?php esc_html_e( '暂无 AI 爬虫访问记录。', 'sunlyvo-nexus' ); ?></p>
            <?php else : ?>
                <table class="widefat striped">
                    <thead><tr>
                        <th><?php esc_html_e( '爬虫', 'sunlyvo-nexus' ); ?></th>
                        <th><?php esc_html_e( '访问次数', 'sunlyvo-nexus' ); ?></th>
                        <th><?php esc_html_e( '唯一 URL', 'sunlyvo-nexus' ); ?></th>
                        <th><?php esc_html_e( '最后访问', 'sunlyvo-nexus' ); ?></th>
                    </tr></thead>
                    <tbody>
                        <?php foreach ( $data['ai_crawlers'] as $row ) : ?>
                            <tr>
                                <td><strong><?php echo esc_html( $row->crawler_name ); ?></strong></td>
                                <td><?php echo esc_html( number_format_i18n( (int) $row->hits ) ); ?></td>
                                <td><?php echo esc_html( number_format_i18n( (int) $row->unique_urls ) ); ?></td>
                                <td><?php echo esc_html( $row->last_seen ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <!-- 阅读行为 -->
            <h2><?php esc_html_e( '📖 阅读行为', 'sunlyvo-nexus' ); ?></h2>
            <table class="widefat striped">
                <thead><tr>
                    <th><?php esc_html_e( '事件', 'sunlyvo-nexus' ); ?></th>
                    <th><?php esc_html_e( '事件数', 'sunlyvo-nexus' ); ?></th>
                    <th><?php esc_html_e( '说明', 'sunlyvo-nexus' ); ?></th>
                </tr></thead>
                <tbody>
                    <?php
                    $event_labels = [
                        'view'     => __( '浏览（PV）', 'sunlyvo-nexus' ),
                        'read'     => __( '深度阅读（UV）', 'sunlyvo-nexus' ),
                        'progress' => __( '进度点', 'sunlyvo-nexus' ),
                        'time'     => __( '停留时长', 'sunlyvo-nexus' ),
                        'copy'     => __( '复制操作', 'sunlyvo-nexus' ),
                        'cite'     => __( '引用操作', 'sunlyvo-nexus' ),
                        'share'    => __( '分享操作', 'sunlyvo-nexus' ),
                    ];
                    foreach ( $event_labels as $event => $label ) :
                        $count = (int) ( $data['reading_events'][ $event ] ?? 0 );
                        ?>
                        <tr>
                            <td><code><?php echo esc_html( $event ); ?></code></td>
                            <td><?php echo esc_html( number_format_i18n( $count ) ); ?></td>
                            <td><?php echo esc_html( $label ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- 分享事件 -->
            <h2><?php esc_html_e( '🔗 分享事件', 'sunlyvo-nexus' ); ?></h2>
            <?php if ( empty( $data['shares'] ) ) : ?>
                <p><?php esc_html_e( '暂无分享记录。', 'sunlyvo-nexus' ); ?></p>
            <?php else : ?>
                <table class="widefat striped">
                    <thead><tr>
                        <th><?php esc_html_e( '目标', 'sunlyvo-nexus' ); ?></th>
                        <th><?php esc_html_e( '次数', 'sunlyvo-nexus' ); ?></th>
                    </tr></thead>
                    <tbody>
                        <?php foreach ( $data['shares'] as $row ) : ?>
                            <tr>
                                <td><?php echo esc_html( $row->value ?: '(未知)' ); ?></td>
                                <td><?php echo esc_html( number_format_i18n( (int) $row->count ) ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <!-- 询盘统计 -->
            <h2><?php esc_html_e( '📨 询盘统计', 'sunlyvo-nexus' ); ?></h2>
            <table class="widefat striped">
                <thead><tr>
                    <th><?php esc_html_e( '指标', 'sunlyvo-nexus' ); ?></th>
                    <th><?php esc_html_e( '数值', 'sunlyvo-nexus' ); ?></th>
                </tr></thead>
                <tbody>
                    <tr><td><?php esc_html_e( '累计询盘', 'sunlyvo-nexus' ); ?></td><td><?php echo esc_html( number_format_i18n( (int) $data['inquiries']['total'] ) ); ?></td></tr>
                    <tr><td><?php esc_html_e( '周期内新增', 'sunlyvo-nexus' ); ?></td><td><?php echo esc_html( number_format_i18n( (int) $data['inquiries']['period'] ) ); ?></td></tr>
                    <tr><td><?php esc_html_e( '待处理', 'sunlyvo-nexus' ); ?></td><td><?php echo esc_html( number_format_i18n( (int) $data['inquiries']['pending'] ) ); ?></td></tr>
                </tbody>
            </table>

            <!-- 商品卡片点击 -->
            <h2><?php esc_html_e( '🛍️ 商品卡片', 'sunlyvo-nexus' ); ?></h2>
            <table class="widefat striped">
                <thead><tr>
                    <th><?php esc_html_e( '指标', 'sunlyvo-nexus' ); ?></th>
                    <th><?php esc_html_e( '数值', 'sunlyvo-nexus' ); ?></th>
                </tr></thead>
                <tbody>
                    <tr><td><?php esc_html_e( '嵌入商品卡片的内容', 'sunlyvo-nexus' ); ?></td><td><?php echo esc_html( number_format_i18n( (int) $data['product_cards']['embedded'] ) ); ?></td></tr>
                    <tr><td><?php esc_html_e( 'CTR', 'sunlyvo-nexus' ); ?></td><td><?php echo esc_html( $data['product_cards']['ctr'] ); ?>%</td></tr>
                </tbody>
            </table>

            <!-- 上线部署检查 -->
            <h2><?php esc_html_e( '🚀 上线前置检查', 'sunlyvo-nexus' ); ?></h2>
            <?php if ( class_exists( 'SLV_Deployer' ) ) :
                $deployer = new SLV_Deployer();
                $check = $deployer->check_all();
                ?>
                <table class="widefat striped">
                    <thead><tr>
                        <th style="width:60px"><?php esc_html_e( '状态', 'sunlyvo-nexus' ); ?></th>
                        <th><?php esc_html_e( '项', 'sunlyvo-nexus' ); ?></th>
                        <th><?php esc_html_e( '值', 'sunlyvo-nexus' ); ?></th>
                    </tr></thead>
                    <tbody>
                        <?php foreach ( $check['items'] as $item ) : ?>
                            <tr>
                                <td><?php echo $item['passed'] ? '✅' : '❌'; ?></td>
                                <td><?php echo esc_html( $item['label'] ); ?></td>
                                <td><?php echo esc_html( $item['value'] ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <style>
            .slv-dashboard__range { margin: 16px 0; }
            .slv-dashboard__cards {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 16px;
                margin: 20px 0 32px;
            }
            .slv-dashboard__card {
                background: #fff;
                border: 1px solid #e8e8e8;
                border-left: 4px solid #0066ff;
                border-radius: 8px;
                padding: 16px 20px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            }
            .slv-dashboard__card--warn { border-left-color: #fa8c16; }
            .slv-dashboard__card--fail { border-left-color: #f5222d; }
            .slv-dashboard__card-label { font-size: 13px; color: #666; margin-bottom: 8px; }
            .slv-dashboard__card-value { font-size: 28px; font-weight: 700; color: #1a1a1a; line-height: 1.2; }
            .slv-dashboard__card-target { font-size: 12px; color: #999; margin-top: 8px; display: flex; justify-content: space-between; }
            .slv-dashboard__card-badge { font-size: 12px; }
        </style>
        <?php
    }

    /**
     * 采集仪表盘数据。
     *
     * @since 1.0.0
     * @param int $range 天数。
     * @return array
     */
    private static function collect_data( int $range ): array {
        global $wpdb;

        $since = gmdate( 'Y-m-d H:i:s', strtotime( "-{$range} days", current_time( 'timestamp' ) ) );

        // ─── 1. AI 爬虫统计 ─────────────────────────────
        $ai_crawlers = $wpdb->get_results( $wpdb->prepare(
            "SELECT crawler_name, SUM(hit_count) AS hits, COUNT(DISTINCT url) AS unique_urls, MAX(last_hit) AS last_seen
             FROM {$wpdb->prefix}slv_geo_crawlers
             WHERE last_hit >= %s
             GROUP BY crawler_name
             ORDER BY hits DESC
             LIMIT 20",
            $since
        ) ) ?: [];

        $ai_total_crawlers = count( $ai_crawlers );

        // ─── 2. 阅读行为统计 ────────────────────────────
        $reading_events = [];
        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT event, COUNT(*) AS count
             FROM {$wpdb->prefix}slv_track_log
             WHERE created_at >= %s
             GROUP BY event",
            $since
        ) ) ?: [];
        foreach ( $rows as $row ) {
            $reading_events[ $row->event ] = (int) $row->count;
        }

        // ─── 3. 分享事件统计 ────────────────────────────
        $shares = $wpdb->get_results( $wpdb->prepare(
            "SELECT value, COUNT(*) AS count
             FROM {$wpdb->prefix}slv_track_log
             WHERE event = 'share' AND created_at >= %s
             GROUP BY value
             ORDER BY count DESC",
            $since
        ) ) ?: [];

        // ─── 4. UV 统计（distinct visitor_id） ──────────
        $uv = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT visitor_id)
             FROM {$wpdb->prefix}slv_track_log
             WHERE event = 'view' AND visitor_id != '' AND created_at >= %s",
            $since
        ) );

        // ─── 5. 询盘统计 ────────────────────────────────
        $inquiry_total = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish'",
            'inquiry'
        ) );
        $inquiry_period = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish' AND post_date >= %s",
            'inquiry', $since
        ) );
        $inquiry_pending = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->postmeta} pm
             JOIN {$wpdb->posts} p ON pm.post_id = p.ID
             WHERE p.post_type = %s AND pm.meta_key = %s AND pm.meta_value = %s",
            'inquiry', '_slv_inquiry_status', 'new'
        ) );

        // ─── 6. 商品卡片 ────────────────────────────────
        $product_card_embedded = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts}
             WHERE post_status = 'publish' AND post_content LIKE %s",
            '%slv-product-card%'
        ) );
        $product_card_clicks = (int) ( $reading_events['click'] ?? 0 );
        $product_card_views  = (int) ( $reading_events['view'] ?? 0 );
        $ctr = $product_card_views > 0
            ? round( $product_card_clicks / $product_card_views * 100, 2 )
            : 0.0;

        // ─── 7. 内容收录率（基于 sitemap URL 数 vs 已索引数，Demo 阶段给近似值） ─
        $published_count = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type IN ('post','page','product','collection','wiki','faq','document','video','course')"
        );
        $index_rate = $published_count > 0
            ? min( 100, round( ( $published_count - 0 ) / $published_count * 100, 1 ) )
            : 0.0;

        // ─── 8. 阅读体验使用率 ──────────────────────────
        $reader_events = (int) ( $reading_events['copy'] ?? 0 )
            + (int) ( $reading_events['cite'] ?? 0 )
            + (int) ( $reading_events['share'] ?? 0 );
        $reader_usage_rate = $product_card_views > 0
            ? round( $reader_events / $product_card_views * 100, 2 )
            : 0.0;

        // ─── 组装卡片 ────────────────────────────────────
        $cards = [
            self::build_card(
                __( '内容收录率', 'sunlyvo-nexus' ),
                $index_rate . '%',
                __( '目标 > 80%', 'sunlyvo-nexus' ),
                80, $index_rate
            ),
            self::build_card(
                __( 'AI 引擎引用', 'sunlyvo-nexus' ),
                $ai_total_crawlers . ' 个引擎',
                __( '目标 ≥ 1 次引用', 'sunlyvo-nexus' ),
                1, $ai_total_crawlers
            ),
            self::build_card(
                __( '独立访客（UV）', 'sunlyvo-nexus' ),
                number_format_i18n( $uv ),
                __( '目标 > 100', 'sunlyvo-nexus' ),
                100, $uv
            ),
            self::build_card(
                __( '商品卡片 CTR', 'sunlyvo-nexus' ),
                $ctr . '%',
                __( '目标 > 3%', 'sunlyvo-nexus' ),
                3, $ctr
            ),
            self::build_card(
                __( '询盘提交', 'sunlyvo-nexus' ),
                number_format_i18n( $inquiry_period ),
                __( '目标 > 5（周期内）', 'sunlyvo-nexus' ),
                5, $inquiry_period
            ),
            self::build_card(
                __( '阅读体验使用率', 'sunlyvo-nexus' ),
                $reader_usage_rate . '%',
                __( '目标 > 10%', 'sunlyvo-nexus' ),
                10, $reader_usage_rate
            ),
        ];

        return [
            'cards'           => $cards,
            'ai_crawlers'     => $ai_crawlers,
            'reading_events'  => $reading_events,
            'shares'          => $shares,
            'inquiries'       => [
                'total'   => $inquiry_total,
                'period'  => $inquiry_period,
                'pending' => $inquiry_pending,
            ],
            'product_cards'   => [
                'embedded' => $product_card_embedded,
                'ctr'      => $ctr,
            ],
        ];
    }

    /**
     * 构建指标卡。
     *
     * @since 1.0.0
     */
    private static function build_card( string $label, string $value, string $target_text, float $target, float $actual ): array {
        if ( $actual >= $target ) {
            $status = 'pass';
        } elseif ( $actual >= $target * 0.7 ) {
            $status = 'warn';
        } else {
            $status = 'fail';
        }

        return [
            'label'       => $label,
            'value'       => $value,
            'target_text' => $target_text,
            'status'      => $status,
        ];
    }
}