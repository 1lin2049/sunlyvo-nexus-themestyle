<?php
/**
 * SunLyvo Nexus — TOC 服务端准备
 *
 * 在前端 toc.js 生成 TOC 之前，服务端预先输出基础结构供无 JS 场景降级。
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 从内容中提取标题树（服务端）。
 *
 * @since 1.0.0
 * @param string $content 内容 HTML。
 * @param int    $min     最小层级（默认 2）。
 * @param int    $max     最大层级（默认 4）。
 * @return array
 */
function slv_reader_extract_headings( string $content, int $min = 2, int $max = 4 ): array {
    if ( '' === trim( $content ) ) {
        return [];
    }
    $headings = [];
    $pattern  = '/<h([2-4])[^>]*id=["\']([^"\']+)["\'][^>]*>(.*?)<\/h\1>/is';
    if ( ! preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER ) ) {
        return [];
    }
    foreach ( $matches as $match ) {
        $level = (int) $match[1];
        if ( $level < $min || $level > $max ) {
            continue;
        }
        $headings[] = [
            'id'    => $match[2],
            'level' => $level,
            'text'  => trim( wp_strip_all_tags( $match[3] ) ),
        ];
    }
    return $headings;
}

/**
 * 构建嵌套标题树。
 *
 * @since 1.0.0
 * @param array $headings 扁平标题列表。
 * @return array
 */
function slv_reader_build_toc_tree( array $headings ): array {
    $root  = [ 'level' => 1, 'children' => [] ];
    $stack = [ &$root ];

    foreach ( $headings as $h ) {
        while ( count( $stack ) > 1 && end( $stack )['level'] >= $h['level'] ) {
            array_pop( $stack );
        }
        $node = [
            'id'       => $h['id'],
            'level'    => $h['level'],
            'text'     => $h['text'],
            'children' => [],
        ];
        $stack[ count( $stack ) - 1 ]['children'][] = $node;
        $stack[] = &$stack[ count( $stack ) - 1 ]['children'][ count( $stack[ count( $stack ) - 1 ]['children'] ) - 1 ];
        unset( $node );
    }

    return $root['children'];
}

/**
 * 渲染服务端 TOC（无 JS 时可见）。
 *
 * @since 1.0.0
 * @param array $tree TOC 树。
 * @param int   $depth 当前深度。
 */
function slv_reader_render_toc_html( array $tree, int $depth = 0 ): void {
    if ( empty( $tree ) ) {
        return;
    }
    echo '<ul class="slv-toc__list slv-toc__level-' . esc_attr( (string) $depth ) . '">';
    foreach ( $tree as $node ) {
        $level = (int) $node['level'];
        echo '<li class="slv-toc__item slv-toc__item--h' . esc_attr( (string) $level ) . '">';
        printf(
            '<a href="#%1$s" data-heading-id="%1$s" class="slv-toc__link">%2$s</a>',
            esc_attr( $node['id'] ),
            esc_html( $node['text'] )
        );
        if ( ! empty( $node['children'] ) ) {
            slv_reader_render_toc_html( $node['children'], $depth + 1 );
        }
        echo '</li>';
    }
    echo '</ul>';
}

/**
 * 输出 TOC 容器（PHP 模板调用）。
 *
 * @since 1.0.0
 */
function slv_reader_render_toc(): void {
    $post_id = (int) get_the_ID();
    if ( ! $post_id ) {
        return;
    }
    $content  = (string) get_post_field( 'post_content', $post_id );
    $headings = slv_reader_extract_headings( $content, 2, 4 );

    if ( count( $headings ) < 2 ) {
        return;
    }
    $tree = slv_reader_build_toc_tree( $headings );

    echo '<nav class="slv-toc" aria-label="' . esc_attr__( '目录', 'sunlyvo-nexus' ) . '" tabindex="-1">';
    echo '<div class="slv-toc__header">';
    echo '<span class="slv-toc__title">' . esc_html__( '目录', 'sunlyvo-nexus' ) . '</span>';
    echo '<button type="button" class="slv-toc__toggle" aria-expanded="true" aria-label="' . esc_attr__( '切换目录', 'sunlyvo-nexus' ) . '">';
    echo '<svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M3 6l5 5 5-5" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    echo '</button>';
    echo '</div>';
    echo '<div class="slv-toc__body">';
    slv_reader_render_toc_html( $tree );
    echo '</div>';
    echo '</nav>';

    // 移动端浮动按钮 + 抽屉
    echo '<button type="button" class="slv-toc__fab" aria-label="' . esc_attr__( '打开目录', 'sunlyvo-nexus' ) . '" aria-controls="slv-toc-drawer">';
    echo '<svg width="20" height="20" viewBox="0 0 20 20" aria-hidden="true"><path d="M3 5h14M3 10h14M3 15h9" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/></svg>';
    echo '</button>';

    echo '<div id="slv-toc-drawer" class="slv-toc__drawer" aria-hidden="true">';
    echo '<div class="slv-toc__drawer-mask" data-close></div>';
    echo '<div class="slv-toc__drawer-panel" role="dialog" aria-modal="true">';
    echo '<button type="button" class="slv-toc__drawer-close" data-close aria-label="' . esc_attr__( '关闭', 'sunlyvo-nexus' ) . '">×</button>';
    slv_reader_render_toc_html( $tree );
    echo '</div>';
    echo '</div>';
}