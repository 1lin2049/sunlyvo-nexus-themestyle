<?php
/**
 * SunLyvo Nexus — 最终修复集合 v2.0
 *
 * 修复：
 *   1. 商品归档主查询 —— 所有商品一页显示（无分页）
 *   2. 商品列表计数 —— 用主查询的 found_posts
 *   3. 商品无图占位 —— 居中 SVG，不铺满
 *   4. 单页侧栏 —— 自动注入
 *   5. 相关阅读 —— 修正 HTML 结构
 *   6. Emoji 全局清理
 *   7. 商品分类 REST 端点（供筛选栏 JS 加载）
 *
 * @package SunLyvo_Nexus
 * @since 3.1.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ═══════════════════════════════════════════════
   1. 商品归档 —— 强制一页显示所有商品
   ═══════════════════════════════════════════════ */

add_action( 'pre_get_posts', function ( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    if ( $query->is_post_type_archive( 'product' ) || $query->is_tax( 'product_cat' ) ) {
        $query->set( 'posts_per_page', 100 );
        $query->set( 'orderby', 'date' );
        $query->set( 'order', 'DESC' );
    }
}, 5 );

/* ═══════════════════════════════════════════════
   2. 商品分类 REST 端点（供筛选栏 JS 使用）
   ═══════════════════════════════════════════════ */

add_action( 'rest_api_init', function (): void {
    register_rest_route( 'slv/v1', '/product-categories', [
        'methods'             => 'GET',
        'permission_callback' => '__return_true',
        'callback'            => function () {
            $terms = get_terms( [
                'taxonomy'   => 'product_cat',
                'hide_empty' => false,
            ] );
            if ( is_wp_error( $terms ) ) {
                return new WP_REST_Response( [], 200 );
            }
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
   3. 商品无图占位 —— 居中 SVG，不铺满卡片
   ═══════════════════════════════════════════════ */

add_filter( 'post_thumbnail_html', function ( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
    if ( $html ) {
        return $html;
    }
    if ( get_post_type( $post_id ) !== 'product' ) {
        return $html;
    }

    $product_name = get_the_title( $post_id );
    $first_char   = mb_substr( wp_strip_all_tags( $product_name ), 0, 1, 'UTF-8' );
    if ( $first_char === '' ) {
        $first_char = 'P';
    }

    // 稳定哈希颜色
    $hue = abs( crc32( (string) $post_id ) ) % 360;

    // 尺寸：卡片中央小图标，不铺满
    $svg = '<div class="slv-product-placeholder" style="'
         . 'display:flex;align-items:center;justify-content:center;'
         . 'width:100%;height:100%;'
         . 'background:linear-gradient(135deg,hsl(' . $hue . ',60%,55%),hsl(' . ( ( $hue + 40 ) % 360 ) . ',55%,42%));'
         . '">';
    $svg .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120" width="120" height="120" style="width:60%;height:60%;max-width:120px;max-height:120px;display:block;opacity:0.95;">';
    $svg .= '<text x="60" y="80" font-family="-apple-system, PingFang SC, sans-serif" font-size="72" font-weight="800" fill="rgba(255,255,255,0.95)" text-anchor="middle">' . esc_html( $first_char ) . '</text>';
    $svg .= '</svg>';
    $svg .= '</div>';

    return $svg;
}, 10, 5 );

/* ═══════════════════════════════════════════════
   4. 商品计数 —— 用主查询 found_posts
   ═══════════════════════════════════════════════ */

add_action( 'template_redirect', function () {
    if ( is_admin() ) {
        return;
    }
    if ( ! is_post_type_archive( 'product' ) && ! is_tax( 'product_cat' ) ) {
        return;
    }

    ob_start( function ( $html ) {
        if ( strpos( $html, 'data-slv-total>—' ) === false ) {
            return $html;
        }
        global $wp_query;
        $total = (int) $wp_query->found_posts;
        return str_replace(
            'data-slv-total>—<',
            'data-slv-total>' . $total . '<',
            $html
        );
    } );
}, 1 );

/* ═══════════════════════════════════════════════
   5. 单页侧栏 —— 完整侧栏
   ═══════════════════════════════════════════════ */

if ( ! function_exists( 'slv_render_full_sidebar' ) ) {
    function slv_render_full_sidebar( int $post_id, string $type = 'post' ): void {
        // TOC
        echo '<div class="slv-sidebar-card"><div class="slv-toc" data-slv-toc></div></div>';

        // 作者卡
        if ( $type === 'post' ) {
            $author_id = (int) get_post_field( 'post_author', $post_id );
            $user      = get_userdata( $author_id );
            if ( $user ) {
                $bio = get_user_meta( $author_id, 'description', true );
                ?>
                <div class="slv-sidebar-card slv-author-mini">
                    <div class="slv-sidebar-card__title">作者</div>
                    <div style="display:flex;gap:12px;align-items:flex-start;">
                        <div style="width:48px;height:48px;border-radius:50%;overflow:hidden;flex-shrink:0;background:#e5e7eb;">
                            <?php echo get_avatar( $author_id, 48, '', $user->display_name ); ?>
                        </div>
                        <div style="min-width:0;flex:1;">
                            <div style="font-size:14px;font-weight:600;color:var(--slv-color-text);margin-bottom:4px;">
                                <?php echo esc_html( $user->display_name ); ?>
                            </div>
                            <?php if ( $bio ) : ?>
                                <div style="font-size:12px;line-height:1.5;color:var(--slv-color-text-muted);">
                                    <?php echo esc_html( wp_trim_words( $bio, 16 ) ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        }

        // 热门文章
        $hot = new WP_Query( [
            'post_type'      => $type,
            'posts_per_page' => 5,
            'orderby'        => 'comment_count',
            'order'          => 'DESC',
            'post__not_in'   => [ $post_id ],
            'post_status'    => 'publish',
        ] );
        if ( $hot->have_posts() ) {
            ?>
            <div class="slv-sidebar-card">
                <div class="slv-sidebar-card__title">热门阅读</div>
                <ul>
                    <?php while ( $hot->have_posts() ) : $hot->the_post(); ?>
                        <li>
                            <a href="<?php the_permalink(); ?>">
                                <?php echo esc_html( get_the_title() ); ?>
                            </a>
                        </li>
                    <?php endwhile; wp_reset_postdata(); ?>
                </ul>
            </div>
            <?php
        }

        // 标签
        $taxonomy = ( $type === 'post' ) ? 'post_tag' : ( ( $type === 'wiki' ) ? 'wiki_cat' : 'post_tag' );
        $tags     = get_the_terms( $post_id, $taxonomy );
        if ( $tags && ! is_wp_error( $tags ) ) {
            ?>
            <div class="slv-sidebar-card">
                <div class="slv-sidebar-card__title">标签</div>
                <div style="display:flex;flex-wrap:wrap;gap:6px;">
                    <?php foreach ( $tags as $tag ) : ?>
                        <a href="<?php echo esc_url( get_term_link( $tag ) ); ?>" style="display:inline-block;padding:3px 10px;background:#ffffff;border:1px solid var(--slv-color-border);border-radius:999px;font-size:12px;color:var(--slv-color-text-muted);text-decoration:none;">
                            <?php echo esc_html( $tag->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php
        }
    }
}

/* ═══════════════════════════════════════════════
   6. 相关阅读 —— 修正 HTML 结构
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
        $terms    = wp_get_post_terms( $post_id, $taxonomy, [ 'fields' => 'ids' ] );
        if ( is_wp_error( $terms ) ) {
            $terms = [];
        }

        $args = [
            'post_type'      => $post_type,
            'posts_per_page' => $limit,
            'post__not_in'   => [ $post_id ],
            'orderby'        => 'rand',
            'post_status'    => 'publish',
        ];
        if ( ! empty( $terms ) ) {
            $args['tax_query'] = [
                [ 'taxonomy' => $taxonomy, 'field' => 'term_id', 'terms' => $terms ],
            ];
        }

        $query = new WP_Query( $args );
        if ( ! $query->have_posts() ) {
            return;
        }
        ?>
        <section class="slv-related">
            <h2 class="slv-related__title"><?php esc_html_e( '相关阅读', 'sunlyvo-nexus' ); ?></h2>
            <div class="slv-related__grid">
                <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <article class="slv-related__item">
                        <a href="<?php the_permalink(); ?>" class="slv-related__link">
                            <div class="slv-related__media">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php the_post_thumbnail( 'medium', [ 'loading' => 'lazy' ] ); ?>
                                <?php endif; ?>
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

/* ═══════════════════════════════════════════════
   7. Emoji 全局清理
   ═══════════════════════════════════════════════ */

add_action( 'template_redirect', function () {
    if ( is_admin() ) {
        return;
    }

    ob_start( function ( $html ) {
        $replace_map = [
            '✅' => '',
            '❌' => '',
            '⚠️' => '',
            '🎉' => '',
            '🔧' => '',
            '🔐' => '',
            '🔄' => '',
            '🌐' => '',
            '💾' => '',
            '🤖' => '',
            '📖' => '',
            '🔗' => '',
            '📨' => '',
            '🛍️' => '',
            '🚀' => '',
            '🔍' => '',
            '⚙️' => '',
            '📝' => '',
            '💡' => '',
            '⭐' => '',
            '✨' => '',
            '👉' => '',
            '👈' => '',
            '🎯' => '',
        ];

        // 跳过 script/style 内容
        $parts = preg_split( '/(<script[\s\S]*?<\/script>|<style[\s\S]*?<\/style>)/i', $html, -1, PREG_SPLIT_DELIM_CAPTURE );
        if ( is_array( $parts ) ) {
            foreach ( $parts as $i => $part ) {
                if ( preg_match( '/^<(script|style)/i', $part ) ) {
                    continue;
                }
                $parts[ $i ] = str_replace( array_keys( $replace_map ), array_values( $replace_map ), $part );
            }
            $html = implode( '', $parts );
        }

        return $html;
    } );
}, 1 );

/* ═══════════════════════════════════════════════
   8. 筛选栏 JS —— 动态加载分类
   ═══════════════════════════════════════════════ */

add_action( 'wp_footer', function (): void {
    if ( ! is_post_type_archive( 'product' ) && ! is_tax( 'product_cat' ) ) {
        return;
    }
    $rest_url = esc_url_raw( rest_url( 'slv/v1/product-categories' ) );
    ?>
    <script>
    (function(){
        var list = document.querySelector('[data-slv-filter-categories]');
        if (!list) return;
        fetch(<?php echo wp_json_encode( $rest_url ); ?>)
            .then(function(r){ return r.json(); })
            .then(function(cats){
                if (!Array.isArray(cats) || cats.length === 0) {
                    list.innerHTML = '<li style="color:#8b919d;font-size:13px;">暂无分类</li>';
                    return;
                }
                list.innerHTML = cats.map(function(c){
                    return '<li><label>'
                        + '<input type="checkbox" name="category" value="' + c.slug + '" /> '
                        + c.name
                        + ' <span class="slv-product-filter__count">' + c.count + '</span>'
                        + '</label></li>';
                }).join('');
            })
            .catch(function(){
                list.innerHTML = '<li style="color:#8b919d;font-size:13px;">加载失败</li>';
            });
    })();
    </script>
    <?php
}, 99 );