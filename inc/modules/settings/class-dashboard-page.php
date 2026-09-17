<?php
/**
 * SunLyvo Nexus — 商业化验证仪表盘
 *
 * 严格区分真实数据 / 无数据 / 未接入。
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SLV_Dashboard_Page {

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

            <!-- 说明 -->
            <div class="notice notice-info">
                <p>
                    <strong><?php esc_html_e( '数据说明', 'sunlyvo-nexus' ); ?></strong><br>
                    <?php esc_html_e( '本仪表盘严格区分三类数据：', 'sunlyvo-nexus' ); ?>
                    <br>✅ <?php esc_html_e( '真实数据 — 来自数据库实际统计', 'sunlyvo-nexus' ); ?>
                    <br>— <?php esc_html_e( '无数据 — 埋点未触发或分母为 0，不显示虚假数字', 'sunlyvo-nexus' ); ?>
                    <br>⚙️ <?php esc_html_e( '未接入 — 需要外部 API，请到 服务配置 中启用', 'sunlyvo-nexus' ); ?>
                </p>
            </div>

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
                            <span class="slv-dashboard__card-badge"><?php echo esc_html( $card['badge'] ); ?></span>
                        </div>
                        <?php if ( ! empty( $card['hint'] ) ) : ?>
                            <div class="slv-dashboard__card-hint"><?php echo esc_html( $card['hint'] ); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- AI 爬虫访问 -->
            <h2><?php esc_html_e( '🤖 AI 引擎引用', 'sunlyvo-nexus' ); ?></h2>
            <?php if ( empty( $data['ai_crawlers'] ) ) : ?>
                <p><?php esc_html_e( '暂无 AI 爬虫访问记录。（这是真实数据：17 个 AI 爬虫均未访问本站）', 'sunlyvo-nexus' ); ?></p>
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
                    <th><?php esc_html_e( '埋点状态', 'sunlyvo-nexus' ); ?></th>
                </tr></thead>
                <tbody>
                    <?php
                    $event_meta = [
                        'view'     => [ 'label' => __( '浏览（PV）', 'sunlyvo-nexus' ),      'tracked' => true ],
                        'read'     => [ 'label' => __( '深度阅读（UV）', 'sunlyvo-nexus' ),  'tracked' => true ],
                        'progress' => [ 'label' => __( '进度点', 'sunlyvo-nexus' ),          'tracked' => true ],
                        'time'     => [ 'label' => __( '停留时长', 'sunlyvo-nexus' ),        'tracked' => true ],
                        'copy'     => [ 'label' => __( '复制操作', 'sunlyvo-nexus' ),        'tracked' => true ],
                        'cite'     => [ 'label' => __( '引用操作', 'sunlyvo-nexus' ),        'tracked' => true ],
                        'share'    => [ 'label' => __( '分享操作', 'sunlyvo-nexus' ),        'tracked' => true ],
                        'click'    => [ 'label' => __( '商品卡片点击', 'sunlyvo-nexus' ),    'tracked' => false ],
                    ];
                    foreach ( $event_meta as $event => $meta ) :
                        $count = (int) ( $data['reading_events'][ $event ] ?? 0 );
                        ?>
                        <tr>
                            <td><code><?php echo esc_html( $event ); ?></code></td>
                            <td><?php echo esc_html( number_format_i18n( $count ) ); ?></td>
                            <td><?php echo esc_html( $meta['label'] ); ?></td>
                            <td>
                                <?php if ( $meta['tracked'] ) : ?>
                                    <span style="color:#00a854">✅ 已埋点</span>
                                <?php else : ?>
                                    <span style="color:#fa8c16">⚙️ 未埋点（阶段二实现）</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- 分享事件 -->
            <h2><?php esc_html_e( '🔗 分享事件', 'sunlyvo-nexus' ); ?></h2>
            <?php if ( empty( $data['shares'] ) ) : ?>
                <p><?php esc_html_e( '暂无分享记录。（真实数据：无用户使用分享功能）', 'sunlyvo-nexus' ); ?></p>
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

            <!-- 商品卡片 -->
            <h2><?php esc_html_e( '🛍️ 商品卡片', 'sunlyvo-nexus' ); ?></h2>
            <table class="widefat striped">
                <thead><tr>
                    <th><?php esc_html_e( '指标', 'sunlyvo-nexus' ); ?></th>
                    <th><?php esc_html_e( '数值', 'sunlyvo-nexus' ); ?></th>
                </tr></thead>
                <tbody>
                    <tr>
                        <td><?php esc_html_e( '嵌入商品卡片的内容', 'sunlyvo-nexus' ); ?></td>
                        <td><?php echo esc_html( number_format_i18n( (int) $data['product_cards']['embedded'] ) ); ?></td>
                    </tr>
                    <tr>
                        <td><?php esc_html_e( 'CTR', 'sunlyvo-nexus' ); ?></td>
                        <td>
                            <?php if ( null === $data['product_cards']['ctr'] ) : ?>
                                <span style="color:#fa8c16">— 无数据（点击埋点未实现，阶段二实现）</span>
                            <?php else : ?>
                                <?php echo esc_html( $data['product_cards']['ctr'] ); ?>%
                            <?php endif; ?>
                        </td>
                    </tr>
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
            .slv-dashboard__card--no_data { border-left-color: #bfbfbf; }
            .slv-dashboard__card--not_configured { border-left-color: #8c8c8c; background: #fafafa; }
            .slv-dashboard__card-label { font-size: 13px; color: #666; margin-bottom: 8px; }
            .slv-dashboard__card-value { font-size: 28px; font-weight: 700; color: #1a1a1a; line-height: 1.2; }
            .slv-dashboard__card--no_data .slv-dashboard__card-value,
            .slv-dashboard__card--not_configured .slv-dashboard__card-value { color: #8c8c8c; }
            .slv-dashboard__card-target { font-size: 12px; color: #999; margin-top: 8px; display: flex; justify-content: space-between; }
            .slv-dashboard__card-badge { font-size: 12px; }
            .slv-dashboard__card-hint { font-size: 11px; color: #bfbfbf; margin-top: 6px; }
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

        // ─── 1. AI 爬虫统计（真实数据） ─────────────────────
        $ai_crawlers = $wpdb->get_results( $wpdb->prepare(
            "SELECT crawler_name, SUM(hit_count) AS hits, COUNT(DISTINCT url) AS unique_urls, MAX(last_hit) AS last_seen
             FROM {$wpdb->prefix}slv_geo_crawlers
             WHERE last_hit >= %s
             GROUP BY crawler_name
             ORDER BY hits DESC
             LIMIT 20",
            $since
        ) ) ?: [];

        $ai_engine_count = count( $ai_crawlers );

        // ─── 2. 阅读行为统计（真实数据） ────────────────────
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

        // ─── 3. 分享事件统计（真实数据） ────────────────────
        $shares = $wpdb->get_results( $wpdb->prepare(
            "SELECT value, COUNT(*) AS count
             FROM {$wpdb->prefix}slv_track_log
             WHERE event = 'share' AND created_at >= %s
             GROUP BY value
             ORDER BY count DESC",
            $since
        ) ) ?: [];

        // ─── 4. UV 统计（真实数据） ────────────────────────
        $uv = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT visitor_id)
             FROM {$wpdb->prefix}slv_track_log
             WHERE event = 'view' AND visitor_id != '' AND created_at >= %s",
            $since
        ) );

        // ─── 5. 询盘统计（真实数据） ────────────────────────
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

        // ─── 6. 商品卡片（真实数据 + 无数据处理） ──────────
        $product_card_embedded = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts}
             WHERE post_status = 'publish' AND post_content LIKE %s",
            '%slv-product-card%'
        ) );

        $view_count  = (int) ( $reading_events['view'] ?? 0 );
        $click_count = isset( $reading_events['click'] ) ? (int) $reading_events['click'] : null;

        // CTR 只有"已埋点 + 有浏览"时才计算
        $ctr = null;
        if ( null !== $click_count && $view_count > 0 ) {
            $ctr = round( $click_count / $view_count * 100, 2 );
        }

        // ─── 7. 内容收录率（需要外部 API） ──────────────────
        // 检查是否配置了 Search Console 等 API
        $index_rate = null; // 默认未接入
        // 如果未来集成了 Google Search Console / Bing / 百度站长平台，从此处读取
        // $index_rate = SLV_Indexing_Stats::get_index_rate();

        // ─── 8. 阅读体验使用率（真实数据 + 无数据处理） ─────
        $reader_events = (int) ( $reading_events['copy'] ?? 0 )
            + (int) ( $reading_events['cite'] ?? 0 )
            + (int) ( $reading_events['share'] ?? 0 );

        $reader_usage_rate = null;
        if ( $view_count > 0 ) {
            $reader_usage_rate = round( $reader_events / $view_count * 100, 2 );
        }

        // ─── 组装卡片 ────────────────────────────────────
        $cards = [
            // 1. 内容收录率
            self::build_card_unconfigured(
                __( '内容收录率', 'sunlyvo-nexus' ),
                __( '未接入', 'sunlyvo-nexus' ),
                __( '目标 > 80%', 'sunlyvo-nexus' ),
                __( '需配置 Google Search Console / Bing Webmaster / 百度站长平台 API', 'sunlyvo-nexus' )
            ),

            // 2. AI 引擎引用
            self::build_card(
                __( 'AI 引擎引用', 'sunlyvo-nexus' ),
                $ai_engine_count . ' ' . __( '个引擎', 'sunlyvo-nexus' ),
                __( '目标 ≥ 1 次引用', 'sunlyvo-nexus' ),
                1, $ai_engine_count
            ),

            // 3. UV
            self::build_card_auto(
                __( '独立访客（UV）', 'sunlyvo-nexus' ),
                $uv > 0 ? number_format_i18n( $uv ) : null,
                __( '目标 > 100', 'sunlyvo-nexus' ),
                100, $uv,
                __( '埋点已就绪，等待访客访问', 'sunlyvo-nexus' )
            ),

            // 4. 商品卡片 CTR
            $ctr === null
                ? self::build_card_no_data(
                    __( '商品卡片 CTR', 'sunlyvo-nexus' ),
                    __( '—', 'sunlyvo-nexus' ),
                    __( '目标 > 3%', 'sunlyvo-nexus' ),
                    __( '点击埋点未实现（阶段二实现）', 'sunlyvo-nexus' )
                )
                : self::build_card(
                    __( '商品卡片 CTR', 'sunlyvo-nexus' ),
                    $ctr . '%',
                    __( '目标 > 3%', 'sunlyvo-nexus' ),
                    3, $ctr
                ),

            // 5. 询盘提交
            self::build_card_auto(
                __( '询盘提交', 'sunlyvo-nexus' ),
                $inquiry_period > 0 ? number_format_i18n( $inquiry_period ) : null,
                __( '目标 > 5（周期内）', 'sunlyvo-nexus' ),
                5, $inquiry_period,
                __( '无用户提交询盘', 'sunlyvo-nexus' )
            ),

            // 6. 阅读体验使用率
            $reader_usage_rate === null
                ? self::build_card_no_data(
                    __( '阅读体验使用率', 'sunlyvo-nexus' ),
                    __( '—', 'sunlyvo-nexus' ),
                    __( '目标 > 10%', 'sunlyvo-nexus' ),
                    __( '无浏览数据（分母为 0）', 'sunlyvo-nexus' )
                )
                : self::build_card(
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
     * 构建标准指标卡（有数据）。
     */
    private static function build_card( string $label, string $value, string $target_text, float $target, float $actual ): array {
        if ( $actual >= $target ) {
            $status = 'pass';
            $badge  = '✅ 达标';
        } elseif ( $actual >= $target * 0.7 ) {
            $status = 'warn';
            $badge  = '⚠️ 接近';
        } else {
            $status = 'fail';
            $badge  = '❌ 未达标';
        }

        return [
            'label'       => $label,
            'value'       => $value,
            'target_text' => $target_text,
            'status'      => $status,
            'badge'       => $badge,
            'hint'        => '',
        ];
    }

    /**
     * 构建指标卡（自动处理"有数据"与"无数据"）。
     */
    private static function build_card_auto( string $label, ?string $value, string $target_text, float $target, float $actual, string $no_data_hint ): array {
        if ( null === $value ) {
            return self::build_card_no_data( $label, '—', $target_text, $no_data_hint );
        }
        return self::build_card( $label, $value, $target_text, $target, $actual );
    }

    /**
     * 构建"无数据"卡。
     */
    private static function build_card_no_data( string $label, string $value, string $target_text, string $hint ): array {
        return [
            'label'       => $label,
            'value'       => $value,
            'target_text' => $target_text,
            'status'      => 'no_data',
            'badge'       => '— 无数据',
            'hint'        => $hint,
        ];
    }

    /**
     * 构建"未接入"卡。
     */
    private static function build_card_unconfigured( string $label, string $value, string $target_text, string $hint ): array {
        return [
            'label'       => $label,
            'value'       => $value,
            'target_text' => $target_text,
            'status'      => 'not_configured',
            'badge'       => '⚙️ 未接入',
            'hint'        => $hint,
        ];
    }
}