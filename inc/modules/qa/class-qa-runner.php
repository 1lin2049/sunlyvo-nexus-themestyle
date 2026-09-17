<?php
/**
 * SunLyvo Nexus — QA 测试运行器
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * QA 测试运行器。
 *
 * @since 1.0.0
 */
class SLV_QA_Runner {

    /**
     * 运行所有测试。
     *
     * @since 1.0.0
     * @return array
     */
    public function run_all(): array {
        $start = microtime( true );

        $groups = [
            'functional'  => new SLV_Check_Functional(),
            'schema'      => new SLV_Check_Schema(),
            'seo'         => new SLV_Check_SEO(),
            'performance' => new SLV_Check_Performance(),
            'mobile'      => new SLV_Check_Mobile(),
        ];

        $results = [];
        $summary = [ 'passed' => 0, 'failed' => 0, 'skipped' => 0, 'total' => 0 ];

        foreach ( $groups as $key => $group ) {
            $items = $group->run();
            $results[ $key ] = [
                'label' => $group->get_label(),
                'items' => $items,
            ];

            foreach ( $items as $item ) {
                $summary['total']++;
                if ( $item['status'] === 'pass' ) {
                    $summary['passed']++;
                } elseif ( $item['status'] === 'fail' ) {
                    $summary['failed']++;
                } else {
                    $summary['skipped']++;
                }
            }
        }

        return [
            'summary'    => $summary,
            'groups'     => $results,
            'duration'   => round( microtime( true ) - $start, 3 ),
            'site_url'   => home_url(),
            'generated'  => current_time( 'mysql' ),
            'wp_version' => get_bloginfo( 'version' ),
            'php_version'=> PHP_VERSION,
        ];
    }
}