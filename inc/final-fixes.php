<?php
/**
 * SunLyvo Nexus — 最终修复集合 v5.1
 *
 * @package SunLyvo_Nexus
 * @since 5.1.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ═══════════════════════════════════════════════
   1. 商品归档一页显示
   ═══════════════════════════════════════════════ */

add_action( 'pre_get_posts', function ( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) return;
    if ( $query->is_post_type_archive( 'product' ) || $query->is_tax( 'product_cat' ) ) {
        $query->set( 'posts_per_page', 100 );
        $query->set( 'orderby', 'date' );
        $query->set( 'order', 'DESC' );
    }
}, 5 );

/* ═══════════════════════════════════════════════
   2. 商品分类 REST 端点
   ═══════════════════════════════════════════════ */

add_action( 'rest_api_init', function (): void {
    register_rest_route( 'slv/v1', '/product-categories', [
        'methods'             => 'GET',
        'permission_callback' => '__return_true',
        'callback'            => function () {
            $terms = get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => false ] );
            if ( is_wp_error( $terms ) ) return new WP_REST_Response( [], 200 );
            $out = [];
            foreach ( $terms as $term ) {
                $out[] = [
                    'slug'  => $term->slug,
                    'name'  => $term->name,
                    'count' => (int) $term->count,
                ];
            }
            return new WP_REST_Response( $out, 200 );
        },
    ] );
} );

/* ═══════════════════════════════════════════════
   3. 商品详情数据 REST
   ═══════════════════════════════════════════════ */

add_action( 'rest_api_init', function (): void {
    register_rest_route( 'slv/v1', '/product/(?P<id>\d+)', [
        'methods'             => 'GET',
        'permission_callback' => '__return_true',
        'callback'            => function ( $request ) {
            $id = (int) $request['id'];
            $post = get_post( $id );
            if ( ! $post || $post->post_type !== 'product' ) {
                return new WP_Error( 'not_found', '商品不存在', [ 'status' => 404 ] );
            }
            return new WP_REST_Response( [
                'id'    => $id,
                'title' => get_the_title( $id ),
                'price' => (float) get_post_meta( $id, '_slv_price', true ) ?: 0,
                'sku'   => (string) get_post_meta( $id, '_slv_sku', true ),
                'stock' => (int) get_post_meta( $id, '_slv_stock', true ),
                'url'   => get_permalink( $id ),
            ], 200 );
        },
    ] );
} );

/* ═══════════════════════════════════════════════
   4. 商品缩略图 thumbs shortcode
   ═══════════════════════════════════════════════ */

add_shortcode( 'slv_product_gallery_thumbs', function (): string {
    $post_id = get_the_ID();
    if ( ! $post_id ) return '';

    $images = slv_get_product_images( $post_id );
    if ( empty( $images ) ) return '';

    ob_start();
    foreach ( $images as $i => $img_id ) :
        $thumb_url = wp_get_attachment_image_url( $img_id, 'thumbnail' );
        $full_url  = wp_get_attachment_image_url( $img_id, 'large' );
        if ( ! $thumb_url || ! $full_url ) continue;
        ?>
        <button type="button" class="slv-product-gallery__thumb<?php echo $i === 0 ? ' is-active' : ''; ?>"
                data-slv-thumb
                data-full-url="<?php echo esc_url( $full_url ); ?>"
                aria-label="<?php echo esc_attr( sprintf( __( '查看第 %d 张图片', 'sunlyvo-nexus' ), $i + 1 ) ); ?>">
            <img src="<?php echo esc_url( $thumb_url ); ?>" alt="" loading="lazy" />
        </button>
        <?php
    endforeach;
    return (string) ob_get_clean();
} );

/* ═══════════════════════════════════════════════
   5. <title> 修正
   ═══════════════════════════════════════════════ */

add_filter( 'document_title_parts', function ( $title ) {
    if ( is_post_type_archive( 'product' ) ) {
        $title['title'] = '全部商品';
    } elseif ( is_post_type_archive() ) {
        $pt = get_query_var( 'post_type' );
        if ( $pt && $obj = get_post_type_object( $pt ) ) {
            $title['title'] = $obj->labels->name;
        }
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $title['title'] = single_term_title( '', false );
    } elseif ( is_author() ) {
        $title['title'] = get_the_author();
    }
    return $title;
}, PHP_INT_MAX );

add_action( 'wp_head', function (): void {
    if ( ! is_archive() ) return;
    ?>
    <script>
    (function(){
        function fix(){
            var t = document.title;
            t = t.replace(/^归档[：:]\s*/, '');
            t = t.replace(/^分类[：:]\s*/, '');
            t = t.replace(/^标签[：:]\s*/, '');
            t = t.replace(/^作者[：:]\s*/, '');
            t = t.replace(/^日期[：:]\s*/, '');
            t = t.replace(/^Archives?[：:]\s*/i, '');
            t = t.replace(/^Category[：:]\s*/i, '');
            t = t.replace(/^Tag[：:]\s*/i, '');
            t = t.replace(/^Author[：:]\s*/i, '');
            if (t !== document.title) document.title = t;
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fix);
        } else fix();
    })();
    </script>
    <?php
}, 1 );

/* ═══════════════════════════════════════════════
   6. 商品列表 JS（筛选/视图切换）
   ═══════════════════════════════════════════════ */

add_action( 'wp_footer', function (): void {
    if ( ! is_post_type_archive( 'product' ) && ! is_tax( 'product_cat' ) ) return;
    $rest_url = esc_url_raw( rest_url( 'slv/v1/product-categories' ) );
    ?>
    <script>
    (function(){
        var list = document.querySelector('[data-slv-filter-categories]');
        if (list) {
            fetch(<?php echo wp_json_encode( $rest_url ); ?>)
                .then(function(r){ return r.json(); })
                .then(function(cats){
                    if (!Array.isArray(cats) || cats.length === 0) {
                        list.innerHTML = '<li class="slv-product-filter__loading">暂无分类</li>';
                        return;
                    }
                    list.innerHTML = cats.map(function(c){
                        return '<li><label><input type="checkbox" name="category" value="' + c.slug + '" /> ' + c.name + ' <span class="slv-product-filter__count">' + c.count + '</span></label></li>';
                    }).join('');
                })
                .catch(function(){
                    list.innerHTML = '<li class="slv-product-filter__loading">加载失败</li>';
                });
        }

        var toolbar = document.querySelector('.slv-product-toolbar__view');
        var grid = document.querySelector('[data-slv-grid]');
        if (toolbar && grid) {
            toolbar.addEventListener('click', function(e){
                var btn = e.target.closest('[data-slv-view]');
                if (!btn) return;
                var view = btn.getAttribute('data-slv-view');
                toolbar.querySelectorAll('[data-slv-view]').forEach(function(b){
                    b.classList.toggle('is-active', b === btn);
                });
                grid.classList.toggle('is-list-view', view === 'list');
            });
        }

        document.querySelectorAll('[data-slv-filter-toggle]').forEach(function(btn){
            btn.addEventListener('click', function(){
                var expanded = btn.getAttribute('aria-expanded') === 'true';
                btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            });
        });
    })();
    </script>
    <?php
}, 99 );

/* ═══════════════════════════════════════════════
   7. 商品详情 JS
   ═══════════════════════════════════════════════ */

add_action( 'wp_footer', function (): void {
    if ( ! is_singular( 'product' ) ) return;
    ?>
    <script>
    (function(){
        // Tab 切换
        var tabsWrap = document.querySelector('[data-slv-product-tabs]');
        if (tabsWrap) {
            tabsWrap.addEventListener('click', function(e){
                var tab = e.target.closest('[data-slv-tab]');
                if (!tab) return;
                var key = tab.getAttribute('data-slv-tab');
                tabsWrap.querySelectorAll('[data-slv-tab]').forEach(function(t){
                    t.classList.toggle('is-active', t === tab);
                });
                tabsWrap.querySelectorAll('[data-slv-panel]').forEach(function(p){
                    p.classList.toggle('is-active', p.getAttribute('data-slv-panel') === key);
                });
            });
        }

        // 缩略图切换主图
        var mainImg = document.querySelector('[data-slv-gallery-main] img');
        document.querySelectorAll('[data-slv-thumb]').forEach(function(thumb){
            thumb.addEventListener('click', function(){
                var url = thumb.getAttribute('data-full-url');
                if (url && mainImg) {
                    mainImg.src = url;
                    mainImg.removeAttribute('srcset');
                }
                document.querySelectorAll('[data-slv-thumb]').forEach(function(t){
                    t.classList.toggle('is-active', t === thumb);
                });
            });
        });

        // 面包屑当前标题
        var bc = document.querySelector('[data-slv-breadcrumb-current]');
        if (bc) {
            var h1 = document.querySelector('.slv-product-info__title');
            if (h1) bc.textContent = h1.textContent.trim();
        }

        // 价格 / SKU 通过 data-* 注入（如有）
        var priceEl = document.querySelector('[data-slv-product-price]');
        if (priceEl) {
            var meta = document.querySelector('meta[name="slv-product-price"]');
            if (meta) priceEl.textContent = meta.getAttribute('content');
        }
    })();
    </script>
    <?php
}, 99 );

/* ═══════════════════════════════════════════════
   8. 相关阅读 v2
   ═══════════════════════════════════════════════ */

if ( ! function_exists( 'slv_render_related_posts_v2' ) ) {
    function slv_render_related_posts_v2( int $post_id, string $post_type = 'post', int $limit = 3 ): void {
        $taxonomy_map = [
            'post'       => 'category',
            'wiki'       => 'wiki_cat',
            'faq'        => 'faq_cat',
            'collection' => 'collection_cat',
            'product'    => 'product_cat',
            'document'   => 'doc_category',
        ];
        $taxonomy = $taxonomy_map[ $post_type ] ?? 'post_tag';
        $terms = wp_get_post_terms( $post_id, $taxonomy, [ 'fields' => 'ids' ] );
        if ( is_wp_error( $terms ) ) $terms = [];

        $args = [
            'post_type'      => $post_type,
            'posts_per_page' => $limit,
            'post__not_in'   => [ $post_id ],
            'orderby'        => 'rand',
            'post_status'    => 'publish',
            'no_found_rows'  => true,
        ];
        if ( ! empty( $terms ) ) {
            $args['tax_query'] = [ [ 'taxonomy' => $taxonomy, 'field' => 'term_id', 'terms' => $terms ] ];
        }

        $query = new WP_Query( $args );
        if ( ! $query->have_posts() ) return;
        ?>
        <section class="slv-related">
            <h2 class="slv-related__title">相关阅读</h2>
            <div class="slv-related__grid">
                <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <article class="slv-related__item">
                        <a href="<?php the_permalink(); ?>" class="slv-related__link">
                            <div class="slv-related__media">
                                <?php if ( has_post_thumbnail() ) the_post_thumbnail( 'medium', [ 'loading' => 'lazy' ] ); ?>
                            </div>
                            <h3 class="slv-related__heading"><?php the_title(); ?></h3>
                            <p class="slv-related__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
                            <div class="slv-related__meta"><?php echo esc_html( get_the_date( 'Y-m-d' ) ); ?></div>
                        </a>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </section>
        <?php
    }
}