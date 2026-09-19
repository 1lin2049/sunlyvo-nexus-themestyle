<?php
/**
 * SunLyvo Nexus — 商品多图模块
 *
 * @package SunLyvo_Nexus
 * @since 5.1.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Meta Box：商品图库
 */
add_action( 'add_meta_boxes', function (): void {
    add_meta_box(
        'slv_product_gallery',
        __( '商品图库', 'sunlyvo-nexus' ),
        'slv_product_gallery_render',
        'product',
        'normal',
        'high'
    );
} );

function slv_product_gallery_render( WP_Post $post ): void {
    wp_nonce_field( 'slv_product_gallery_save', 'slv_product_gallery_nonce' );

    $raw = (string) get_post_meta( $post->ID, '_slv_product_gallery', true );
    $ids = array_filter( array_map( 'intval', explode( ',', $raw ) ) );
    ?>
    <div id="slv-gallery-wrap">
        <div id="slv-gallery-preview" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:10px;margin-bottom:12px;">
            <?php foreach ( $ids as $id ) : ?>
                <?php if ( ! wp_attachment_is_image( $id ) ) continue; ?>
                <div class="slv-gallery-item" data-id="<?php echo (int) $id; ?>" style="position:relative;aspect-ratio:1;border:1px solid #ddd;border-radius:6px;overflow:hidden;background:#f5f5f5;">
                    <?php echo wp_get_attachment_image( $id, 'thumbnail', false, [ 'style' => 'width:100%;height:100%;object-fit:cover;' ] ); ?>
                    <button type="button" class="slv-gallery-remove" style="position:absolute;top:4px;right:4px;background:#ef4444;color:#fff;border:none;border-radius:50%;width:20px;height:20px;cursor:pointer;font-size:12px;line-height:1;">×</button>
                </div>
            <?php endforeach; ?>
        </div>
        <input type="hidden" name="slv_product_gallery" id="slv-gallery-input" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>" />
        <button type="button" class="button" id="slv-gallery-add"><?php esc_html_e( '添加图片', 'sunlyvo-nexus' ); ?></button>
        <p class="description" style="margin-top:8px;"><?php esc_html_e( '建议上传 5-8 张，1:1 正方形，主图之外的图片会用于悬停切换展示。', 'sunlyvo-nexus' ); ?></p>
    </div>
    <script>
    (function(){
        var frame;
        var wrap = document.getElementById('slv-gallery-preview');
        var input = document.getElementById('slv-gallery-input');

        document.getElementById('slv-gallery-add').addEventListener('click', function(e){
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({
                title: '<?php echo esc_js( __( '选择商品图片', 'sunlyvo-nexus' ) ); ?>',
                button: { text: '<?php echo esc_js( __( '添加到图库', 'sunlyvo-nexus' ) ); ?>' },
                multiple: true,
                library: { type: 'image' }
            });
            frame.on('select', function(){
                var selection = frame.state().get('selection');
                var ids = input.value ? input.value.split(',').filter(Boolean) : [];
                selection.map(function(att){
                    var id = att.id;
                    if (ids.indexOf(String(id)) !== -1) return;
                    ids.push(String(id));
                    var url = att.attributes.sizes && att.attributes.sizes.thumbnail ? att.attributes.sizes.thumbnail.url : att.attributes.url;
                    var div = document.createElement('div');
                    div.className = 'slv-gallery-item';
                    div.dataset.id = id;
                    div.style.cssText = 'position:relative;aspect-ratio:1;border:1px solid #ddd;border-radius:6px;overflow:hidden;background:#f5f5f5;';
                    div.innerHTML = '<img src="' + url + '" style="width:100%;height:100%;object-fit:cover;" /><button type="button" class="slv-gallery-remove" style="position:absolute;top:4px;right:4px;background:#ef4444;color:#fff;border:none;border-radius:50%;width:20px;height:20px;cursor:pointer;font-size:12px;line-height:1;">×</button>';
                    wrap.appendChild(div);
                });
                input.value = ids.join(',');
            });
            frame.open();
        });

        wrap.addEventListener('click', function(e){
            var btn = e.target.closest('.slv-gallery-remove');
            if (!btn) return;
            var item = btn.closest('.slv-gallery-item');
            if (!item) return;
            var id = item.dataset.id;
            var ids = input.value.split(',').filter(function(x){ return x && x !== id; });
            input.value = ids.join(',');
            item.remove();
        });
    })();
    </script>
    <?php
}

/**
 * 保存商品图库
 */
add_action( 'save_post_product', function( $post_id ): void {
    if ( ! isset( $_POST['slv_product_gallery_nonce'] ) ) return;
    if ( ! wp_verify_nonce( sanitize_key( $_POST['slv_product_gallery_nonce'] ), 'slv_product_gallery_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $raw = isset( $_POST['slv_product_gallery'] ) ? sanitize_text_field( wp_unslash( $_POST['slv_product_gallery'] ) ) : '';
    $ids = array_filter( array_map( 'intval', explode( ',', $raw ) ) );
    $ids = array_values( array_unique( $ids ) );

    update_post_meta( $post_id, '_slv_product_gallery', implode( ',', $ids ) );
} );

/**
 * 商品列表页 admin 列
 */
add_filter( 'manage_product_posts_columns', function( $cols ) {
    $new = [];
    foreach ( $cols as $key => $label ) {
        if ( $key === 'title' ) {
            $new['slv_thumb'] = '主图';
        }
        $new[ $key ] = $label;
    }
    return $new;
} );

add_action( 'manage_product_posts_custom_column', function( $column, $post_id ): void {
    if ( $column !== 'slv_thumb' ) return;
    $thumb = get_the_post_thumbnail( $post_id, [ 60, 60 ], [ 'style' => 'width:60px;height:60px;object-fit:cover;border-radius:4px;' ] );
    echo $thumb ?: '<span style="color:#9ca3af;">—</span>';
}, 10, 2 );