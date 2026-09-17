<?php
/**
 * SunLyvo Nexus — 阅读快捷键配置
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 输出快捷键定义到前端 SLV_CONFIG。
 *
 * @since 1.0.0
 * @param array $config 已有配置。
 * @return array
 */
function slv_reader_shortcuts_config( array $config ): array {
    if ( ! slv_reader_is_environment() ) {
        return $config;
    }

    $config['shortcuts'] = [
        'toggleTheme'   => 't',
        'prevHeading'   => '[',
        'nextHeading'   => ']',
        'focusTOC'      => '\\',
        'scrollDown'    => 'j',
        'scrollUp'      => 'k',
        'scrollTop'     => 'Home',
        'closePanel'    => 'Escape',
        'citeSelection' => 'Ctrl/Cmd+Shift+C',
    ];

    return $config;
}
add_filter( 'slv_frontend_config', 'slv_reader_shortcuts_config' );

/**
 * 输出快捷键说明面板（PHP 模板调用）。
 *
 * @since 1.0.0
 */
function slv_reader_render_shortcuts_panel(): void {
    $shortcuts = [
        't'                 => __( '切换主题', 'sunlyvo-nexus' ),
        '['                 => __( '上一节', 'sunlyvo-nexus' ),
        ']'                 => __( '下一节', 'sunlyvo-nexus' ),
        '\\'                => __( '聚焦目录', 'sunlyvo-nexus' ),
        'j / k'             => __( '向下 / 向上滚动', 'sunlyvo-nexus' ),
        'Home'              => __( '回到顶部', 'sunlyvo-nexus' ),
        'Esc'               => __( '关闭面板', 'sunlyvo-nexus' ),
        'Ctrl/Cmd+Shift+C'  => __( '引用当前选区', 'sunlyvo-nexus' ),
    ];

    echo '<div class="slv-reader-shortcuts" hidden aria-hidden="true">';
    echo '<div class="slv-reader-shortcuts__panel">';
    echo '<h3 class="slv-reader-shortcuts__title">' . esc_html__( '快捷键', 'sunlyvo-nexus' ) . '</h3>';
    echo '<dl class="slv-reader-shortcuts__list">';
    foreach ( $shortcuts as $key => $desc ) {
        printf(
            '<dt><kbd>%s</kbd></dt><dd>%s</dd>',
            esc_html( $key ),
            esc_html( $desc )
        );
    }
    echo '</dl>';
    echo '</div>';
    echo '</div>';
}