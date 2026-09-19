<?php
/**
 * SunLyvo Nexus — 修复集合 v4.0
 *
 * @package SunLyvo_Nexus
 * @since 4.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ═══════════════════════════════════════════════
   1. 商品归档 —— 一页显示所有商品
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
   2. 商品分类 REST 端点
   ═══════════════════════════════════════════════ */

add_action( 'rest_api_init', function (): void {
    register_rest_route( 'slv/v1', '/product-categories', [
        'methods'             => 'GET',
        'permission_callback' => '__return_true',
        'callback'            => function () {
            $terms = get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => false ] );
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
   3. 占位图 —— 网站 Logo 或固定占位图
   ═══════════════════════════════════════════════ */

if ( ! function_exists( 'slv_get_placeholder_image_url' ) ) {
    function slv_get_placeholder_image_url(): string {
        // 1. 站点 Logo
        $logo_id = (int) get_theme_mod( 'custom_logo' );
        if ( $logo_id > 0 ) {
            $url = wp_get_attachment_image_url( $logo_id, 'medium' );
            if ( $url ) {
                return (string) $url;
            }
        }
        // 2. 站点 Icon
        $icon = get_site_icon_url( 512 );
        if ( $icon ) {
            return (string) $icon;
        }
        // 3. 主题固定占位图
        $default = get_template_directory() . '/assets/images/placeholder.svg';
        if ( file_exists( $default ) ) {
            return get_template_directory_uri() . '/assets/images/placeholder.svg';
        }
        return '';
    }
}

if ( ! function_exists( 'slv_render_placeholder_image' ) ) {
    function slv_render_placeholder_image( string $alt = '' ): string {
        $url = slv_get_placeholder_image_url();
        if ( $url === '' ) {
            return '';
        }
        return sprintf(
            '<div class="slv-product-card__placeholder"><img src="%s" alt="%s" loading="lazy" /></div>',
            esc_url( $url ),
            esc_attr( $alt )
        );
    }
}

/* ═══════════════════════════════════════════════
   4. 商品卡片渲染
   ═══════════════════════════════════════════════ */

if ( ! function_exists( 'slv_render_product_card' ) ) {
    function slv_render_product_card( int $post_id, bool $mini = false ): string {
        $title     = get_the_title( $post_id );
        $permalink = get_permalink( $post_id );
        $excerpt   = get_the_excerpt( $post_id );
        $price     = get_post_meta( $post_id, '_slv_price', true );
        if ( $price === '' ) {
            $price = '—';
        }

        $thumb = get_the_post_thumbnail( $post_id, 'medium', [ 'loading' => 'lazy' ] );
        if ( ! $thumb ) {
            $thumb = slv_render_placeholder_image( $title );
        }

        $card_class = 'slv-product-card' . ( $mini ? ' slv-product-card--mini' : '' );

        ob_start();
        ?>
        <article class="<?php echo esc_attr( $card_class ); ?>">
            <a href="<?php echo esc_url( $permalink ); ?>" class="slv-product-card__media" aria-label="<?php echo esc_attr( $title ); ?>">
                <?php echo $thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </a>
            <div class="slv-product-card__body">
                <h3 class="slv-product-card__title">
                    <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
                </h3>
                <?php if ( ! $mini ) : ?>
                    <p class="slv-product-card__desc"><?php echo esc_html( wp_trim_words( $excerpt, 20 ) ); ?></p>
                <?php endif; ?>
                <div class="slv-product-card__footer">
                    <span class="slv-product-card__price">
                        <span class="slv-product-card__currency">¥</span>
                        <span class="slv-product-card__amount"><?php echo esc_html( $price ); ?></span>
                    </span>
                    <?php if ( ! $mini ) : ?>
                        <button type="button" class="slv-product-card__cart-btn" data-slv-quick-add="<?php echo (int) $post_id; ?>" aria-label="加入购物车">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="8" cy="21" r="1"/>
                                <circle cx="19" cy="21" r="1"/>
                                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                            </svg>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </article>
        <?php
        return ob_get_clean();
    }
}

/* ═══════════════════════════════════════════════
   5. Shortcode —— 商品网格
   ═══════════════════════════════════════════════ */

add_shortcode( 'slv_product_grid', function (): string {
    global $wp_query;

    if ( ! $wp_query->have_posts() ) {
        return '<div class="slv-product-empty"><p>暂无商品</p></div>';
    }

    ob_start();
    echo '<div class="slv-product-grid" data-slv-grid>';

    while ( $wp_query->have_posts() ) {
        $wp_query->the_post();
        echo slv_render_product_card( get_the_ID(), false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    echo '</div>';

    wp_reset_postdata();
    return ob_get_clean();
} );

/* ═══════════════════════════════════════════════
   6. Shortcode —— 推荐商品
   ═══════════════════════════════════════════════ */

add_shortcode( 'slv_product_recommend', function (): string {
    $query = new WP_Query( [
        'post_type'      => 'product',
        'posts_per_page' => 4,
        'orderby'        => 'rand',
        'post_status'    => 'publish',
        'no_found_rows'  => true,
    ] );

    if ( ! $query->have_posts() ) {
        return '';
    }

    ob_start();
    echo '<div class="slv-product-grid slv-product-grid--mini">';

    while ( $query->have_posts() ) {
        $query->the_post();
        echo slv_render_product_card( get_the_ID(), true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    echo '</div>';

    wp_reset_postdata();
    return ob_get_clean();
} );

/* ═══════════════════════════════════════════════
   7. <title> 修正 —— 移除"归档："前缀
   ═══════════════════════════════════════════════ */

add_filter( 'document_title_parts', function ( $title ) {
    if ( is_post_type_archive( 'product' ) ) {
        $title['title'] = '全部商品';
    } elseif ( is_post_type_archive() ) {
        $pt = get_query_var( 'post_type' );
        if ( $pt ) {
            $obj = get_post_type_object( $pt );
            if ( $obj ) {
                $title['title'] = $obj->labels->name;
            }
        }
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $title['title'] = single_term_title( '', false );
    } elseif ( is_author() ) {
        $title['title'] = get_the_author();
    } elseif ( is_post_type_archive() ) {
        $title['title'] = post_type_archive_title( '', false );
    }
    return $title;
}, PHP_INT_MAX );

/**
 * JS 兜底 —— 万一 filter 被其它插件覆盖
 */
add_action( 'wp_head', function (): void {
    if ( ! is_archive() ) {
        return;
    }
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
            if (t !== document.title) {
                document.title = t;
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fix);
        } else {
            fix();
        }
    })();
    </script>
    <?php
}, 1 );

/* ═══════════════════════════════════════════════
   8. 分类筛选 + 视图切换 JS
   ═══════════════════════════════════════════════ */

add_action( 'wp_footer', function (): void {
    if ( ! is_post_type_archive( 'product' ) && ! is_tax( 'product_cat' ) ) {
        return;
    }
    $rest_url = esc_url_raw( rest_url( 'slv/v1/product-categories' ) );
    ?>
    <script>
    (function(){
        /* 分类动态加载 */
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
                        return '<li><label>'
                            + '<input type="checkbox" name="category" value="' + c.slug + '" /> '
                            + c.name
                            + ' <span class="slv-product-filter__count">' + c.count + '</span>'
                            + '</label></li>';
                    }).join('');
                })
                .catch(function(){
                    list.innerHTML = '<li class="slv-product-filter__loading">加载失败</li>';
                });
        }

        /* 视图切换 */
        var toolbar = document.querySelector('.slv-product-toolbar__view');
        var grid    = document.querySelector('[data-slv-grid]');
        if (toolbar && grid) {
            toolbar.addEventListener('click', function(e){
                var btn = e.target.closest('[data-slv-view]');
                if (!btn) return;
                var view = btn.getAttribute('data-slv-view');

                toolbar.querySelectorAll('[data-slv-view]').forEach(function(b){
                    b.classList.toggle('is-active', b === btn);
                });

                grid.classList.toggle('is-list-view', view === 'list');
                grid.classList.toggle('is-grid-view', view === 'grid');
            });
        }

        /* 筛选组折叠 */
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
   9. 单页侧栏
   ═══════════════════════════════════════════════ */

if ( ! function_exists( 'slv_render_full_sidebar' ) ) {
    function slv_render_full_sidebar( int $post_id, string $type = 'post' ): void {
        echo '<div class="slv-sidebar-card"><div class="slv-toc" data-slv-toc></div></div>';

        if ( $type === 'post' ) {
            $author_id = (int) get_post_field( 'post_author', $post_id );
            $user      = get_userdata( $author_id );
            if ( $user ) {
                $bio = get_user_meta( $author_id, 'description', true );
                ?>
                <div class="slv-sidebar-card">
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

        $hot = new WP_Query( [
            'post_type'      => $type,
            'posts_per_page' => 5,
            'orderby'        => 'comment_count',
            'order'          => 'DESC',
            'post__not_in'   => [ $post_id ],
            'post_status'    => 'publish',
            'no_found_rows'  => true,
        ] );
        if ( $hot->have_posts() ) {
            ?>
            <div class="slv-sidebar-card">
                <div class="slv-sidebar-card__title">热门阅读</div>
                <ul>
                    <?php while ( $hot->have_posts() ) : $hot->the_post(); ?>
                        <li><a href="<?php the_permalink(); ?>"><?php echo esc_html( get_the_title() ); ?></a></li>
                    <?php endwhile; wp_reset_postdata(); ?>
                </ul>
            </div>
            <?php
        }
    }
}

/* ═══════════════════════════════════════════════
   10. 相关阅读
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
            'no_found_rows'  => true,
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
            <h2 class="slv-related__title">相关阅读</h2>
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
   11. Emoji 清理
   ═══════════════════════════════════════════════ */

add_action( 'template_redirect', function () {
    if ( is_admin() ) {
        return;
    }
    ob_start( function ( $html ) {
        $replace = [
            '' => '', '' => '', '' => '', '' => '', '' => '',
            '' => '', '' => '', '' => '', '' => '', '' => '',
            '' => '', '' => '', '' => '', '' => '', '' => '',
            '' => '', '' => '', '' => '', '' => '',
        ];
        $parts = preg_split( '/(<script[\s\S]*?<\/script>|<style[\s\S]*?<\/style>)/i', $html, -1, PREG_SPLIT_DELIM_CAPTURE );
        if ( is_array( $parts ) ) {
            foreach ( $parts as $i => $part ) {
                if ( preg_match( '/^<(script|style)/i', $part ) ) {
                    continue;
                }
                $parts[ $i ] = str_replace( array_keys( $replace ), array_values( $replace ), $part );
            }
            $html = implode( '', $parts );
        }
        return $html;
    } );
}, 1 );