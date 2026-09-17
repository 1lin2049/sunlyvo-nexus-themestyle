<?php
/**
 * SunLyvo Nexus — Reader 核心
 *
 * 提供 reader 环境判定、meta 数据、阅读元信息。
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 判断当前是否为 reader 环境。
 *
 * @since 1.0.0
 * @return bool
 */
function slv_reader_is_environment(): bool {
    if ( is_singular( 'chapter' ) ) {
        return true;
    }
    if ( is_page_template( 'page-preview.php' ) ) {
        return true;
    }
    if ( is_singular( 'collection' ) && get_query_var( 'slv_reader' ) ) {
        return true;
    }
    return false;
}

/**
 * 获取当前文章的字数。
 *
 * 中文字符 + 英文单词 + 数字（按 A07 规则）。
 *
 * @since 1.0.0
 * @param int $post_id 文章 ID。
 * @return int
 */
function slv_reader_get_word_count( int $post_id ): int {
    $content = (string) get_post_field( 'post_content', $post_id );
    $text    = wp_strip_all_tags( $content );

    $cn_chars  = preg_match_all( '/[\x{4e00}-\x{9fa5}]/u', $text );
    $en_words  = preg_match_all( '/[a-zA-Z]+/', $text );
    $numbers   = preg_match_all( '/\d+/', $text );
    $images    = preg_match_all( '/<img[^>]+>/i', $content );
    $codeblocks = preg_match_all( '/<pre[^>]*>/i', $content );

    return (int) $cn_chars + (int) $en_words + (int) $numbers
        + ( (int) $images * 40 )     // 每图按 40 字折算
        + ( (int) $codeblocks * 80 ); // 每段代码按 80 字折算
}

/**
 * 获取预计阅读时间（分钟）。
 *
 * 中文 400 字/分 + 英文 200 词/分 + 数字 300 个/分
 * + 每张图 12 秒 + 每段代码 18 秒。
 *
 * @since 1.0.0
 * @param int $post_id 文章 ID。
 * @return int
 */
function slv_reader_get_reading_time( int $post_id ): int {
    $content = (string) get_post_field( 'post_content', $post_id );
    $text    = wp_strip_all_tags( $content );

    $cn_chars = preg_match_all( '/[\x{4e00}-\x{9fa5}]/u', $text );
    $en_words = preg_match_all( '/[a-zA-Z]+/', $text );
    $numbers  = preg_match_all( '/\d+/', $text );
    $images   = preg_match_all( '/<img[^>]+>/i', $content );
    $codes    = preg_match_all( '/<pre[^>]*>/i', $content );

    $minutes = ( $cn_chars / 400 )
        + ( $en_words / 200 )
        + ( $numbers / 300 )
        + ( ( $images * 12 ) / 60 )
        + ( ( $codes * 18 ) / 60 );

    return max( 1, (int) ceil( $minutes ) );
}

/**
 * 输出 reader meta 行。
 *
 * 格式：浏览 N · 深度阅读 N · 约 N 字 · 预计阅读 N 分钟 · 评论 N
 *
 * @since 1.0.0
 * @param int $post_id 文章 ID。
 */
function slv_reader_render_meta( int $post_id ): void {
    $stats = function_exists( 'slv_get_post_stats' ) ? slv_get_post_stats( $post_id ) : null;
    $views = $stats->views ?? 0;
    $reads = $stats->reads ?? 0;

    $word_count   = slv_reader_get_word_count( $post_id );
    $reading_time = slv_reader_get_reading_time( $post_id );
    $comments     = (int) get_comments_number( $post_id );

    echo '<div class="slv-reader__meta">';
    printf(
        '<span class="slv-reader__meta-item">%s %s</span>',
        esc_html__( '浏览', 'sunlyvo-nexus' ),
        esc_html( number_format_i18n( (int) $views ) )
    );
    printf(
        '<span class="slv-reader__meta-item">%s %s</span>',
        esc_html__( '深度阅读', 'sunlyvo-nexus' ),
        esc_html( number_format_i18n( (int) $reads ) )
    );
    printf(
        '<span class="slv-reader__meta-item">%s %s %s</span>',
        esc_html__( '约', 'sunlyvo-nexus' ),
        esc_html( number_format_i18n( $word_count ) ),
        esc_html__( '字', 'sunlyvo-nexus' )
    );
    printf(
        '<span class="slv-reader__meta-item">%s %s %s</span>',
        esc_html__( '预计阅读', 'sunlyvo-nexus' ),
        esc_html( (string) $reading_time ),
        esc_html__( '分钟', 'sunlyvo-nexus' )
    );
    printf(
        '<span class="slv-reader__meta-item">%s %s</span>',
        esc_html__( '评论', 'sunlyvo-nexus' ),
        esc_html( number_format_i18n( $comments ) )
    );
    echo '</div>';
}

/**
 * 为当前内容中的 H2/H3/H4 自动注入 ID（供 TOC 使用）。
 *
 * 若标题已有 ID 则保留。此函数在 the_content 过滤器中调用。
 *
 * @since 1.0.0
 * @param string $content 内容 HTML。
 * @return string
 */
function slv_reader_auto_inject_heading_ids( string $content ): string {
    if ( ! slv_reader_is_environment() ) {
        return $content;
    }
    if ( '' === trim( $content ) ) {
        return $content;
    }

    $counter = 0;
    $result  = preg_replace_callback(
        '/<h([234])([^>]*)>(.*?)<\/h\1>/is',
        static function ( array $m ) use ( &$counter ): string {
            $level = $m[1];
            $attrs = $m[2];
            $text  = $m[3];

            if ( preg_match( '/\sid\s*=\s*["\']([^"\']+)["\']/i', $attrs ) ) {
                return $m[0];
            }

            $counter++;
            $plain = trim( wp_strip_all_tags( $text ) );
            $slug  = sanitize_title( $plain );
            if ( '' === $slug ) {
                $slug = 'section-' . $counter;
            }
            $id = 'slv-h-' . $counter . '-' . $slug;

            return '<h' . $level . $attrs . ' id="' . esc_attr( $id ) . '">' . $text . '</h' . $level . '>';
        },
        $content
    );

    return is_string( $result ) ? $result : $content;
}
add_filter( 'the_content', 'slv_reader_auto_inject_heading_ids', 8 );