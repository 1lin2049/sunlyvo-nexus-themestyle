<?php
/**
 * SunLyvo Nexus — FAQ Meta Box
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 注册 FAQ Meta Box。
 *
 * @since 1.0.0
 */
function slv_aeo_register_faq_metabox(): void {
    add_meta_box(
        'slv-aeo-faqs',
        __( 'FAQ 问答', 'sunlyvo-nexus' ),
        'slv_aeo_render_faq_metabox',
        [ 'post', 'page', 'product', 'collection', 'faq', 'wiki' ],
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'slv_aeo_register_faq_metabox' );

/**
 * 渲染 FAQ Meta Box。
 *
 * @since 1.0.0
 * @param WP_Post $post 当前文章。
 */
function slv_aeo_render_faq_metabox( $post ): void {
    wp_nonce_field( 'slv_aeo_faq_save', 'slv_aeo_faq_nonce' );
    $faqs = slv_aeo_get_faqs( (int) $post->ID );

    echo '<div id="slv-aeo-faqs-container">';
    foreach ( $faqs as $i => $faq ) {
        slv_aeo_render_faq_row( $i, $faq );
    }
    echo '</div>';
    echo '<button type="button" class="button" id="slv-add-faq">' . esc_html__( '添加 FAQ', 'sunlyvo-nexus' ) . '</button>';

    echo '<style>
        .slv-faq-row{margin:12px 0;padding:12px;background:#fafafa;border:1px solid #e8e8e8;border-radius:6px}
        .slv-faq-row input,.slv-faq-row textarea{width:100%;margin-bottom:6px}
    </style>';
}

/**
 * 渲染单条 FAQ 行。
 *
 * @since 1.0.0
 */
function slv_aeo_render_faq_row( $index, $faq ): void {
    $question = is_array( $faq ) ? ( $faq['question'] ?? '' ) : '';
    $answer   = is_array( $faq ) ? ( $faq['answer'] ?? '' ) : '';
    ?>
    <div class="slv-faq-row" data-index="<?php echo esc_attr( (string) $index ); ?>">
        <input type="text" name="slv_faqs[<?php echo esc_attr( (string) $index ); ?>][question]"
               value="<?php echo esc_attr( (string) $question ); ?>"
               placeholder="<?php esc_attr_e( '问题', 'sunlyvo-nexus' ); ?>" />
        <textarea name="slv_faqs[<?php echo esc_attr( (string) $index ); ?>][answer]"
                  rows="3" placeholder="<?php esc_attr_e( '答案', 'sunlyvo-nexus' ); ?>"><?php echo esc_textarea( (string) $answer ); ?></textarea>
    </div>
    <?php
}

/**
 * 保存 FAQ 数据。
 *
 * @since 1.0.0
 * @param int $post_id 文章 ID。
 */
function slv_aeo_save_faqs( int $post_id ): void {
    if ( ! isset( $_POST['slv_aeo_faq_nonce'] ) ) {
        return;
    }
    $nonce = sanitize_key( wp_unslash( $_POST['slv_aeo_faq_nonce'] ) );
    if ( ! wp_verify_nonce( $nonce, 'slv_aeo_faq_save' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    global $wpdb;
    $wpdb->delete( "{$wpdb->prefix}slv_aeo_faqs", [ 'post_id' => $post_id ] );

    $faqs = isset( $_POST['slv_faqs'] ) && is_array( $_POST['slv_faqs'] ) ? wp_unslash( $_POST['slv_faqs'] ) : [];
    foreach ( $faqs as $i => $faq ) {
        $question = isset( $faq['question'] ) ? sanitize_text_field( $faq['question'] ) : '';
        $answer   = isset( $faq['answer'] ) ? wp_kses_post( $faq['answer'] ) : '';
        if ( '' === $question || '' === $answer ) {
            continue;
        }
        $wpdb->insert( "{$wpdb->prefix}slv_aeo_faqs", [
            'post_id'      => $post_id,
            'question'     => $question,
            'answer'       => $answer,
            'answer_short' => mb_substr( wp_strip_all_tags( $answer ), 0, 200 ),
            'sort_order'   => (int) $i,
        ] );
    }
}
add_action( 'save_post', 'slv_aeo_save_faqs' );

/**
 * 后台 FAQ 交互脚本。
 *
 * @since 1.0.0
 */
function slv_aeo_faq_admin_script(): void {
    $screen = get_current_screen();
    if ( ! $screen || 'post' !== $screen->base ) {
        return;
    }
    ?>
    <script>
    (function(){
        const btn = document.getElementById('slv-add-faq');
        const box = document.getElementById('slv-aeo-faqs-container');
        if (!btn || !box) return;
        let idx = box.querySelectorAll('.slv-faq-row').length;
        btn.addEventListener('click', function(){
            const html = '<div class="slv-faq-row" data-index="' + idx + '">'
                + '<input type="text" name="slv_faqs[' + idx + '][question]" placeholder="问题" />'
                + '<textarea name="slv_faqs[' + idx + '][answer]" rows="3" placeholder="答案"></textarea>'
                + '</div>';
            box.insertAdjacentHTML('beforeend', html);
            idx++;
        });
    })();
    </script>
    <?php
}
add_action( 'admin_footer', 'slv_aeo_faq_admin_script' );