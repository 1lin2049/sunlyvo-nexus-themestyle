<?php
/**
 * SunLyvo Nexus — 最终修复集合
 *
 * 一次性修复：
 *   1. 商品列表计数 & 分页
 *   2. 相关阅读 HTML 结构
 *   3. 商品占位图 SVG
 *   4. 单页侧栏内容
 *
 * @package SunLyvo_Nexus
 * @since 3.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ═══════════════════════════════════════════════
   1. 商品列表计数 —— 修正 "共 — 件商品"
   ═══════════════════════════════════════════════ */

/**
 * 在商品列表页把 count 占位符替换为真实数字。
 */
add_filter( 'the_content', function ( $content ) {
    if ( ! is_post_type_archive( 'product' ) && ! is_tax( 'product_cat' ) ) {
        return $content;
    }
    global $wp_query;
    $total = (int) $wp_query->found_posts;
    return str_replace(
        '<strong data-slv-total>—</strong>',
        '<strong data-slv-total>' . $total . '</strong>',
        $content
    );
}, 99 );

/**
 * 通过输出缓冲兜底处理模板中的占位符（模板非 the_content）。
 */
add_action( 'template_redirect', function () {
    if ( is_admin() ) return;
    if ( ! is_post_type_archive( 'product' ) && ! is_tax( 'product_cat' ) ) return;

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
   2. 相关阅读 —— 重写输出 HTML
   ═══════════════════════════════════════════════ */

if ( ! function_exists( 'slv_render_related_posts' ) ) {
    function slv_render_related_posts( int $post_id, string $post_type = 'post', int $limit = 3 ): void {
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
        ];
        if ( ! empty( $terms ) ) {
            $args['tax_query'] = [
                [ 'taxonomy' => $taxonomy, 'field' => 'term_id', 'terms' => $terms ],
            ];
        }

        $query = new WP_Query( $args );
        if ( ! $query->have_posts() ) return;
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
   3. 商品占位图 —— 无图商品用 SVG 占位
   ═══════════════════════════════════════════════ */

/**
 * 自动为无特色图的商品输出 SVG 占位。
 */
add_filter( 'post_thumbnail_html', function ( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
    if ( $html ) return $html;
    if ( get_post_type( $post_id ) !== 'product' ) return $html;

    $product_name = get_the_title( $post_id );
    $first_char = mb_substr( wp_strip_all_tags( $product_name ), 0, 1, 'UTF-8' );
    if ( $first_char === '' ) $first_char = 'P';

    // 稳定哈希颜色
    $hue = abs( crc32( (string) $post_id ) ) % 360;

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400" preserveAspectRatio="xMidYMid slice" width="400" height="400" style="width:100%;height:100%;display:block;object-fit:cover;">';
    $svg .= '<defs><linearGradient id="slvg' . esc_attr( (string) $post_id ) . '" x1="0" y1="0" x2="1" y2="1">';
    $svg .= '<stop offset="0%" stop-color="hsl(' . $hue . ', 65%, 55%)"/>';
    $svg .= '<stop offset="100%" stop-color="hsl(' . ( ( $hue + 40 ) % 360 ) . ', 60%, 40%)"/>';
    $svg .= '</linearGradient></defs>';
    $svg .= '<rect width="400" height="400" fill="url(#slv' . esc_attr( (string) $post_id ) . ')"/>';
    $svg .= '<circle cx="320" cy="80" r="120" fill="rgba(255,255,255,0.08)"/>';
    $svg .= '<circle cx="80" cy="320" r="140" fill="rgba(0,0,0,0.06)"/>';
    $svg .= '<text x="200" y="220" font-family="-apple-system, PingFang SC, sans-serif" font-size="140" font-weight="800" fill="rgba(255,255,255,0.95)" text-anchor="middle" dominant-baseline="middle">' . esc_html( $first_char ) . '</text>';
    $svg .= '<text x="200" y="300" font-family="-apple-system, PingFang SC, sans-serif" font-size="16" font-weight="500" fill="rgba(255,255,255,0.7)" text-anchor="middle">SunLyvo</text>';
    $svg .= '</svg>';

    return $svg;
}, 10, 5 );

/* ═══════════════════════════════════════════════
   4. 单页侧栏 —— 自动注入侧栏内容
   ═══════════════════════════════════════════════ */

/**
 * 输出完整侧栏（TOC + 作者 + 热门 + 标签）。
 */
if ( ! function_exists( 'slv_render_full_sidebar' ) ) {
    function slv_render_full_sidebar( int $post_id, string $type = 'post' ): void {
        // TOC
        echo '<div class="slv-sidebar-card"><div class="slv-toc" data-slv-toc></div></div>';

        // 作者卡
        if ( $type === 'post' ) {
            $author_id = (int) get_post_field( 'post_author', $post_id );
            $user = get_userdata( $author_id );
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

        // 标签云
        $taxonomy = ( $type === 'post' ) ? 'post_tag' : ( ( $type === 'wiki' ) ? 'wiki_cat' : 'post_tag' );
        $tags = get_the_terms( $post_id, $taxonomy );
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
   5. 商品列表分页修正
   ═══════════════════════════════════════════════ */

add_action( 'pre_get_posts', function ( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) return;
    if ( is_post_type_archive( 'product' ) ) {
        $query->set( 'posts_per_page', 12 );
        $query->set( 'orderby', 'date' );
        $query->set( 'order', 'DESC' );
    }
} );

/* ═══════════════════════════════════════════════
   6. Emoji 清理 —— 对所有输出做兜底替换
   ═══════════════════════════════════════════════ */

/**
 * 全局输出缓冲，移除已知 Emoji 字符（仅在前台）。
 */
add_action( 'template_redirect', function () {
    if ( is_admin() ) return;

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
        // 只替换 HTML 正文中的字符（跳过 script/style 内的 JS/CSS）
        $parts = preg_split( '/(<script[\s\S]*?<\/script>|<style[\s\S]*?<\/style>)/i', $html, -1, PREG_SPLIT_DELIM_CAPTURE );
        if ( is_array( $parts ) ) {
            foreach ( $parts as $i => $part ) {
                if ( preg_match( '/^<(script|style)/i', $part ) ) {
                    continue; // 跳过
                }
                $parts[ $i ] = str_replace( array_keys( $replace_map ), array_values( $replace_map ), $part );
            }
            $html = implode( '', $parts );
        }
        return $html;
    } );
}, 1 );