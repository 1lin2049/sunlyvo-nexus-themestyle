<?php
/**
 * SunLyvo Nexus — 后端输出格式
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 格式化输出。
 *
 * @since 1.0.0
 * @param array  $data 数据。
 * @param string $format 格式：json / table / md / cli。
 * @return string
 */
function slv_format_output( array $data, string $format = 'json' ): string {
    switch ( $format ) {
        case 'md':
        case 'markdown':
            return slv_format_markdown( $data );
        case 'cli':
            return slv_format_cli( $data );
        case 'json':
        default:
            return wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
    }
}

function slv_format_markdown( array $data ): string {
    $out = '';
    foreach ( $data as $key => $value ) {
        if ( is_array( $value ) ) {
            $out .= "## {$key}\n\n";
            $out .= "| 键 | 值 |\n|---|---|\n";
            foreach ( $value as $k => $v ) {
                $out .= "| {$k} | " . ( is_scalar( $v ) ? $v : wp_json_encode( $v ) ) . " |\n";
            }
            $out .= "\n";
        } else {
            $out .= "- **{$key}**：" . $value . "\n";
        }
    }
    return $out;
}

function slv_format_cli( array $data ): string {
    $lines = [];
    foreach ( $data as $key => $value ) {
        if ( is_array( $value ) ) {
            $lines[] = "[{$key}]";
            foreach ( $value as $k => $v ) {
                $lines[] = "  {$k} = " . ( is_scalar( $v ) ? $v : wp_json_encode( $v ) );
            }
        } else {
            $lines[] = "{$key} = {$value}";
        }
    }
    return implode( "\n", $lines );
}