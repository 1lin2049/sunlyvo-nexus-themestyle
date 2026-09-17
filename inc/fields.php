<?php
/**
 * SunLyvo Nexus — 字段定义系统
 *
 * 从 slv_field_definitions 表读取字段定义，动态渲染 Meta Box。
 * 只在字段定义发生变化时写入数据库。
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 获取内置字段定义。
 *
 * @since 1.0.0
 * @return array
 */
function slv_get_field_definitions(): array {
    return [
        // ─── 商品字段 ──────────────────────────────────────
        [
            'field_key'      => '_slv_wholesale_price',
            'field_label'    => '批发价',
            'field_type'     => 'number',
            'object_type'    => 'product',
            'field_group'    => 'pricing',
            'is_required'    => false,
            'is_searchable'  => false,
            'is_syncable'    => true,
            'sync_mode'      => 'full',
            'sort_order'     => 10,
        ],
        [
            'field_key'      => '_slv_moq',
            'field_label'    => '最小起订量 (MOQ)',
            'field_type'     => 'number',
            'object_type'    => 'product',
            'field_group'    => 'pricing',
            'default_value'  => '1',
            'is_syncable'    => true,
            'sort_order'     => 11,
        ],
        [
            'field_key'      => '_slv_hs_code',
            'field_label'    => 'HS 编码',
            'field_type'     => 'text',
            'object_type'    => 'product',
            'field_group'    => 'logistics',
            'is_syncable'    => true,
            'sort_order'     => 20,
        ],
        [
            'field_key'      => '_slv_sku',
            'field_label'    => 'SKU',
            'field_type'     => 'text',
            'object_type'    => 'product',
            'field_group'    => 'basic',
            'is_searchable'  => true,
            'is_syncable'    => true,
            'sort_order'     => 5,
        ],
        [
            'field_key'      => '_slv_lead_time',
            'field_label'    => '交货周期',
            'field_type'     => 'text',
            'object_type'    => 'product',
            'field_group'    => 'logistics',
            'is_syncable'    => true,
            'sort_order'     => 21,
        ],
        [
            'field_key'      => '_slv_vendor_id',
            'field_label'    => '所属商户 ID',
            'field_type'     => 'number',
            'object_type'    => 'product',
            'field_group'    => 'basic',
            'is_syncable'    => true,
            'sort_order'     => 3,
        ],

        // ─── 合集字段 ──────────────────────────────────────
        [
            'field_key'      => '_slv_collection_type',
            'field_label'    => '合集类型',
            'field_type'     => 'select',
            'object_type'    => 'collection',
            'field_group'    => 'basic',
            'options'        => [ 'article_series', 'video_course', 'mixed_knowledge', 'document_pack', 'publication' ],
            'is_required'    => true,
            'is_syncable'    => true,
            'sort_order'     => 5,
        ],
        [
            'field_key'      => '_slv_collection_price',
            'field_label'    => '价格',
            'field_type'     => 'number',
            'object_type'    => 'collection',
            'field_group'    => 'pricing',
            'is_syncable'    => true,
            'sort_order'     => 10,
        ],
        [
            'field_key'      => '_slv_is_free',
            'field_label'    => '免费',
            'field_type'     => 'boolean',
            'object_type'    => 'collection',
            'field_group'    => 'access',
            'default_value'  => '0',
            'is_syncable'    => true,
            'sort_order'     => 11,
        ],
        [
            'field_key'      => '_slv_member_only',
            'field_label'    => '仅会员',
            'field_type'     => 'boolean',
            'object_type'    => 'collection',
            'field_group'    => 'access',
            'default_value'  => '0',
            'is_syncable'    => true,
            'sort_order'     => 12,
        ],
        [
            'field_key'      => '_slv_total_items',
            'field_label'    => '内容项总数',
            'field_type'     => 'number',
            'object_type'    => 'collection',
            'field_group'    => 'basic',
            'is_syncable'    => true,
            'sort_order'     => 15,
        ],

        // ─── 章节字段 ──────────────────────────────────────
        [
            'field_key'      => '_slv_collection_id',
            'field_label'    => '所属合集 ID',
            'field_type'     => 'number',
            'object_type'    => 'chapter',
            'field_group'    => 'basic',
            'is_required'    => true,
            'is_syncable'    => true,
            'sort_order'     => 5,
        ],
        [
            'field_key'      => '_slv_chapter_order',
            'field_label'    => '章节顺序',
            'field_type'     => 'number',
            'object_type'    => 'chapter',
            'field_group'    => 'basic',
            'is_syncable'    => true,
            'sort_order'     => 6,
        ],
        [
            'field_key'      => '_slv_is_free_preview',
            'field_label'    => '免费试读',
            'field_type'     => 'boolean',
            'object_type'    => 'chapter',
            'field_group'    => 'access',
            'default_value'  => '0',
            'is_syncable'    => true,
            'sort_order'     => 10,
        ],

        // ─── 百科字段 ──────────────────────────────────────
        [
            'field_key'      => '_slv_wiki_definition',
            'field_label'    => '一句话定义',
            'field_type'     => 'textarea',
            'object_type'    => 'wiki',
            'field_group'    => 'content',
            'is_syncable'    => true,
            'sort_order'     => 5,
        ],
        [
            'field_key'      => '_slv_wiki_aliases',
            'field_label'    => '别名（逗号分隔）',
            'field_type'     => 'text',
            'object_type'    => 'wiki',
            'field_group'    => 'content',
            'is_searchable'  => true,
            'is_syncable'    => true,
            'sort_order'     => 6,
        ],
        [
            'field_key'      => '_slv_wiki_infobox',
            'field_label'    => '信息框（JSON）',
            'field_type'     => 'json',
            'object_type'    => 'wiki',
            'field_group'    => 'content',
            'is_syncable'    => true,
            'sort_order'     => 7,
        ],

        // ─── FAQ 字段 ──────────────────────────────────────
        [
            'field_key'      => '_slv_faq_question',
            'field_label'    => '问题',
            'field_type'     => 'text',
            'object_type'    => 'faq',
            'field_group'    => 'content',
            'is_required'    => true,
            'is_searchable'  => true,
            'is_syncable'    => true,
            'sort_order'     => 5,
        ],
        [
            'field_key'      => '_slv_faq_answer_short',
            'field_label'    => '简短答案（40-60字）',
            'field_type'     => 'textarea',
            'object_type'    => 'faq',
            'field_group'    => 'content',
            'is_syncable'    => true,
            'sort_order'     => 6,
        ],

        // ─── 视频字段 ──────────────────────────────────────
        [
            'field_key'      => '_slv_mux_asset_id',
            'field_label'    => 'Mux Asset ID',
            'field_type'     => 'text',
            'object_type'    => 'video',
            'field_group'    => 'media',
            'is_syncable'    => false,
            'sort_order'     => 5,
        ],
        [
            'field_key'      => '_slv_mux_playback_id',
            'field_label'    => 'Mux Playback ID',
            'field_type'     => 'text',
            'object_type'    => 'video',
            'field_group'    => 'media',
            'is_syncable'    => false,
            'sort_order'     => 6,
        ],
        [
            'field_key'      => '_slv_duration',
            'field_label'    => '时长（秒）',
            'field_type'     => 'number',
            'object_type'    => 'video',
            'field_group'    => 'media',
            'is_syncable'    => true,
            'sort_order'     => 7,
        ],

        // ─── 直播字段 ──────────────────────────────────────
        [
            'field_key'      => '_slv_mux_live_id',
            'field_label'    => 'Mux Live Stream ID',
            'field_type'     => 'text',
            'object_type'    => 'live',
            'field_group'    => 'media',
            'is_syncable'    => false,
            'sort_order'     => 5,
        ],
        [
            'field_key'      => '_slv_stream_key',
            'field_label'    => '推流密钥',
            'field_type'     => 'text',
            'object_type'    => 'live',
            'field_group'    => 'media',
            'is_syncable'    => false,
            'sort_order'     => 6,
        ],

        // ─── 课程字段 ──────────────────────────────────────
        [
            'field_key'      => '_slv_course_duration',
            'field_label'    => '课程总时长（分钟）',
            'field_type'     => 'number',
            'object_type'    => 'course',
            'field_group'    => 'basic',
            'is_syncable'    => true,
            'sort_order'     => 5,
        ],
        [
            'field_key'      => '_slv_lesson_count',
            'field_label'    => '课节数',
            'field_type'     => 'number',
            'object_type'    => 'course',
            'field_group'    => 'basic',
            'is_syncable'    => true,
            'sort_order'     => 6,
        ],

        // ─── 企业字段 ──────────────────────────────────────
        [
            'field_key'      => '_slv_company_vat',
            'field_label'    => 'VAT 税号',
            'field_type'     => 'text',
            'object_type'    => 'company',
            'field_group'    => 'billing',
            'is_syncable'    => true,
            'sort_order'     => 5,
        ],
        [
            'field_key'      => '_slv_company_credit_limit',
            'field_label'    => '信用额度',
            'field_type'     => 'number',
            'object_type'    => 'company',
            'field_group'    => 'billing',
            'is_syncable'    => true,
            'sort_order'     => 6,
        ],
        [
            'field_key'      => '_slv_company_status',
            'field_label'    => '企业状态',
            'field_type'     => 'select',
            'object_type'    => 'company',
            'field_group'    => 'basic',
            'options'        => [ 'pending', 'approved', 'rejected', 'suspended' ],
            'default_value'  => 'pending',
            'is_syncable'    => true,
            'sort_order'     => 7,
        ],

        // ─── 训练营字段 ────────────────────────────────────
        [
            'field_key'      => '_slv_campaign_start',
            'field_label'    => '开始时间',
            'field_type'     => 'datetime',
            'object_type'    => 'campaign',
            'field_group'    => 'schedule',
            'is_syncable'    => true,
            'sort_order'     => 5,
        ],
        [
            'field_key'      => '_slv_campaign_end',
            'field_label'    => '结束时间',
            'field_type'     => 'datetime',
            'object_type'    => 'campaign',
            'field_group'    => 'schedule',
            'is_syncable'    => true,
            'sort_order'     => 6,
        ],
        [
            'field_key'      => '_slv_campaign_capacity',
            'field_label'    => '人数上限',
            'field_type'     => 'number',
            'object_type'    => 'campaign',
            'field_group'    => 'schedule',
            'is_syncable'    => true,
            'sort_order'     => 7,
        ],

        // ─── 询盘字段 ──────────────────────────────────────
        [
            'field_key'      => '_slv_inquiry_product_id',
            'field_label'    => '关联商品 ID',
            'field_type'     => 'number',
            'object_type'    => 'inquiry',
            'field_group'    => 'basic',
            'is_syncable'    => false,
            'sort_order'     => 5,
        ],
        [
            'field_key'      => '_slv_inquiry_company',
            'field_label'    => '询盘企业',
            'field_type'     => 'text',
            'object_type'    => 'inquiry',
            'field_group'    => 'basic',
            'is_syncable'    => false,
            'sort_order'     => 6,
        ],
        [
            'field_key'      => '_slv_inquiry_status',
            'field_label'    => '询盘状态',
            'field_type'     => 'select',
            'object_type'    => 'inquiry',
            'field_group'    => 'basic',
            'options'        => [ 'new', 'in_progress', 'quoted', 'closed' ],
            'default_value'  => 'new',
            'is_syncable'    => true,
            'sort_order'     => 7,
        ],
    ];
}

/**
 * 同步字段定义到数据库（幂等）。
 *
 * @since 1.0.0
 */
function slv_sync_field_definitions(): void {
    if ( get_option( 'slv_fields_version' ) === SLV_DB_VERSION ) {
        return;
    }

    global $wpdb;
    $table = "{$wpdb->prefix}slv_field_definitions";

    foreach ( slv_get_field_definitions() as $field ) {
        $data = wp_parse_args( $field, [
            'object_subtype'     => '',
            'field_group'        => '',
            'default_value'      => '',
            'options'            => '',
            'validation_rules'   => '',
            'is_required'        => 0,
            'is_searchable'      => 0,
            'is_syncable'        => 1,
            'sync_mode'          => 'full',
            'capability_required'=> '',
            'sort_order'         => 0,
            'is_active'          => 1,
        ] );

        // 序列化复杂字段
        if ( is_array( $data['options'] ) ) {
            $data['options'] = wp_json_encode( $data['options'] );
        }
        if ( is_array( $data['validation_rules'] ) ) {
            $data['validation_rules'] = wp_json_encode( $data['validation_rules'] );
        }

        $existing_id = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM {$table}
             WHERE field_key = %s AND object_type = %s AND object_subtype = %s",
            $data['field_key'], $data['object_type'], $data['object_subtype']
        ) );

        if ( $existing_id ) {
            $wpdb->update( $table, $data, [ 'id' => (int) $existing_id ] );
        } else {
            $wpdb->insert( $table, $data );
        }
    }

    update_option( 'slv_fields_version', SLV_DB_VERSION );
}
add_action( 'after_setup_theme', 'slv_sync_field_definitions', 15 );

/**
 * 获取指定对象类型的字段定义。
 *
 * @since 1.0.0
 * @param string $object_type 对象类型。
 * @return array
 */
function slv_get_fields_for_object( string $object_type ): array {
    global $wpdb;
    $rows = $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}slv_field_definitions
         WHERE object_type = %s AND is_active = 1
         ORDER BY sort_order ASC",
        $object_type
    ), ARRAY_A );

    return array_map( static function( $row ) {
        if ( ! empty( $row['options'] ) ) {
            $row['options'] = json_decode( $row['options'], true ) ?: [];
        }
        if ( ! empty( $row['validation_rules'] ) ) {
            $row['validation_rules'] = json_decode( $row['validation_rules'], true ) ?: [];
        }
        return $row;
    }, $rows ?: [] );
}

/**
 * 注册字段 Meta Box。
 *
 * @since 1.0.0
 */
function slv_register_field_meta_boxes(): void {
    $post_types = [ 'product', 'collection', 'chapter', 'wiki', 'faq', 'video', 'live', 'course', 'company', 'campaign', 'inquiry' ];

    foreach ( $post_types as $post_type ) {
        $fields = slv_get_fields_for_object( $post_type );
        if ( empty( $fields ) ) {
            continue;
        }

        add_meta_box(
            "slv-fields-{$post_type}",
            __( 'SunLyvo 字段', 'sunlyvo-nexus' ),
            'slv_render_field_meta_box',
            $post_type,
            'normal',
            'high',
            [ 'fields' => $fields ]
        );
    }
}
add_action( 'add_meta_boxes', 'slv_register_field_meta_boxes' );

/**
 * 渲染字段 Meta Box。
 *
 * @since 1.0.0
 * @param WP_Post $post 当前文章。
 * @param array   $box  Meta Box 配置。
 */
function slv_render_field_meta_box( $post, $box ): void {
    $fields = $box['args']['fields'] ?? [];
    if ( empty( $fields ) ) {
        return;
    }

    wp_nonce_field( 'slv_save_fields', 'slv_fields_nonce' );

    echo '<div class="slv-fields-wrapper">';
    foreach ( $fields as $field ) {
        $key   = $field['field_key'];
        $value = get_post_meta( $post->ID, $key, true );
        if ( '' === $value && isset( $field['default_value'] ) ) {
            $value = $field['default_value'];
        }

        echo '<div class="slv-field-row">';
        printf(
            '<label for="%1$s" class="slv-field-label">%2$s%3$s</label>',
            esc_attr( $key ),
            esc_html( $field['field_label'] ),
            $field['is_required'] ? ' <span class="slv-required">*</span>' : ''
        );

        switch ( $field['field_type'] ) {
            case 'number':
                printf(
                    '<input type="number" id="%1$s" name="%1$s" value="%2$s" class="widefat" step="any" />',
                    esc_attr( $key ),
                    esc_attr( $value )
                );
                break;

            case 'boolean':
                printf(
                    '<input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s />',
                    esc_attr( $key ),
                    checked( $value, '1', false )
                );
                break;

            case 'select':
                printf( '<select id="%1$s" name="%1$s" class="widefat">', esc_attr( $key ) );
                echo '<option value="">' . esc_html__( '— 选择 —', 'sunlyvo-nexus' ) . '</option>';
                foreach ( (array) $field['options'] as $opt ) {
                    printf(
                        '<option value="%1$s" %2$s>%1$s</option>',
                        esc_attr( $opt ),
                        selected( $value, $opt, false )
                    );
                }
                echo '</select>';
                break;

            case 'textarea':
            case 'json':
                printf(
                    '<textarea id="%1$s" name="%1$s" class="widefat" rows="4">%2$s</textarea>',
                    esc_attr( $key ),
                    esc_textarea( $value )
                );
                break;

            case 'datetime':
                printf(
                    '<input type="datetime-local" id="%1$s" name="%1$s" value="%2$s" class="widefat" />',
                    esc_attr( $key ),
                    esc_attr( $value )
                );
                break;

            case 'text':
            default:
                printf(
                    '<input type="text" id="%1$s" name="%1$s" value="%2$s" class="widefat" />',
                    esc_attr( $key ),
                    esc_attr( $value )
                );
                break;
        }

        echo '</div>';
    }
    echo '</div>';
}

/**
 * 保存字段值。
 *
 * @since 1.0.0
 * @param int $post_id 文章 ID。
 */
function slv_save_field_values( int $post_id ): void {
    if ( ! isset( $_POST['slv_fields_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['slv_fields_nonce'] ) ), 'slv_save_fields' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $post_type = get_post_type( $post_id );
    $fields = slv_get_fields_for_object( $post_type );

    foreach ( $fields as $field ) {
        $key = $field['field_key'];
        if ( ! isset( $_POST[ $key ] ) ) {
            if ( 'boolean' === $field['field_type'] ) {
                update_post_meta( $post_id, $key, '0' );
            }
            continue;
        }

        $raw = wp_unslash( $_POST[ $key ] );

        switch ( $field['field_type'] ) {
            case 'number':
                $value = is_numeric( $raw ) ? (string) $raw : '';
                break;
            case 'boolean':
                $value = '1' === $raw ? '1' : '0';
                break;
            case 'select':
                $value = in_array( $raw, (array) $field['options'], true ) ? $raw : '';
                break;
            case 'json':
            case 'textarea':
                $value = wp_kses_post( $raw );
                break;
            case 'datetime':
                $value = sanitize_text_field( $raw );
                break;
            default:
                $value = sanitize_text_field( $raw );
                break;
        }

        update_post_meta( $post_id, $key, $value );
    }
}
add_action( 'save_post', 'slv_save_field_values' );

/**
 * 后台字段样式。
 *
 * @since 1.0.0
 */
function slv_field_admin_styles(): void {
    $screen = get_current_screen();
    if ( ! $screen || 'post' !== $screen->base ) {
        return;
    }
    echo '<style>
        .slv-fields-wrapper{display:grid;gap:16px}
        .slv-field-row{display:grid;gap:4px}
        .slv-field-label{font-weight:600}
        .slv-required{color:#f5222d}
    </style>';
}
add_action( 'admin_head', 'slv_field_admin_styles' );