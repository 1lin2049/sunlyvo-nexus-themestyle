<?php
/**
 * SunLyvo Nexus — React 嵌页挂载点 + REST 端点
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ─── React 挂载点 ─── */

function slv_react_mounts_map(): array {
    return [
        'cart'       => [ 'mount' => 'slv-react-cart',     'entry' => 'cart',     'width' => 'wide' ],
        'checkout'   => [ 'mount' => 'slv-react-checkout', 'entry' => 'checkout', 'width' => 'content' ],
        'my-account' => [ 'mount' => 'slv-react-account',  'entry' => 'account',  'width' => 'wide' ],
    ];
}

function slv_get_react_mount(): ?array {
    if ( is_admin() || ! is_singular( 'page' ) ) {
        return null;
    }
    $post = get_post();
    if ( ! $post ) {
        return null;
    }
    $slug = $post->post_name;
    if ( ! is_string( $slug ) || '' === $slug ) {
        return null;
    }
    $map = slv_react_mounts_map();
    return $map[ $slug ] ?? null;
}

add_filter( 'the_content', static function ( $content ) {
    if ( ! is_string( $content ) ) {
        return $content;
    }
    $mount = slv_get_react_mount();
    if ( ! $mount ) {
        return $content;
    }
    $id = $mount['mount'];
    if ( strpos( $content, 'id="' . $id . '"' ) !== false ) {
        return $content;
    }
    $html = sprintf(
        '<div id="%s" class="slv-react-mount" data-slv-entry="%s"><noscript>%s</noscript></div>',
        esc_attr( $id ),
        esc_attr( $mount['entry'] ),
        esc_html__( '此页面需要启用 JavaScript。', 'sunlyvo-nexus' )
    );
    return $html . $content;
}, 5 );

add_action( 'wp_enqueue_scripts', static function () {
    $mount = slv_get_react_mount();
    if ( ! $mount ) {
        return;
    }
    $entry     = $mount['entry'];
    $dist_url  = SLV_THEME_URL . '/assets/js/react/dist';
    $dist_path = SLV_THEME_DIR . '/assets/js/react/dist';

    $js = "{$dist_path}/{$entry}.js";
    if ( file_exists( $js ) ) {
        wp_enqueue_script( "slv-react-{$entry}", "{$dist_url}/{$entry}.js", [], (string) filemtime( $js ), true );
    }
    $css = "{$dist_path}/{$entry}.css";
    if ( file_exists( $css ) ) {
        wp_enqueue_style( "slv-react-{$entry}-style", "{$dist_url}/{$entry}.css", [], (string) filemtime( $css ) );
    }
}, 20 );

add_filter( 'script_loader_tag', static function ( $tag, $handle, $src ) {
    if ( strpos( (string) $handle, 'slv-react-' ) === 0 ) {
        return sprintf(
            '<script type="module" src="%s" id="%s-js"></script>' . "\n",
            esc_url( $src ),
            esc_attr( $handle )
        );
    }
    return $tag;
}, 10, 3 );

add_action( 'wp_head', static function () {
    $mount = slv_get_react_mount();
    if ( ! $mount ) {
        return;
    }
    $width = 'content' === $mount['width'] ? '--slv-layout-content' : '--slv-layout-wide';
    ?>
    <style>
        .slv-react-mount {
            max-width: var(<?php echo esc_attr( $width ); ?>);
            margin: var(--slv-space-8) auto;
            padding: 0 var(--slv-space-4);
            min-height: 400px;
        }
        .slv-react-mount noscript {
            display: block;
            padding: var(--slv-space-8);
            background: var(--slv-color-bg-subtle);
            border: 1px solid var(--slv-color-border-base);
            border-radius: var(--slv-radius-md);
            text-align: center;
            color: var(--slv-color-text-tertiary);
        }
    </style>
    <?php
}, 30 );

/* ─── REST 端点 ─── */

if ( ! function_exists( 'slv_get_cart_key' ) ) {
    function slv_get_cart_key(): string {
        if ( is_user_logged_in() ) {
            return 'user_' . get_current_user_id();
        }
        if ( ! empty( $_COOKIE['slv_cart_key'] ) ) {
            return sanitize_text_field( wp_unslash( $_COOKIE['slv_cart_key'] ) );
        }
        return '';
    }
}

add_action( 'rest_api_init', static function () {

    // GET /cart/count
    register_rest_route( SLV_REST_NAMESPACE, '/cart/count', [
        'methods'             => 'GET',
        'callback'            => static function (): WP_REST_Response {
            global $wpdb;
            $cart_key = slv_get_cart_key();
            if ( '' === $cart_key ) {
                return new WP_REST_Response( [ 'count' => 0 ], 200 );
            }
            $cart_id = $wpdb->get_var( $wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}slv_carts WHERE cart_key = %s AND status = 'active' LIMIT 1",
                $cart_key
            ) );
            if ( ! $cart_id ) {
                return new WP_REST_Response( [ 'count' => 0 ], 200 );
            }
            $count = (int) $wpdb->get_var( $wpdb->prepare(
                "SELECT COALESCE(SUM(quantity), 0) FROM {$wpdb->prefix}slv_cart_items WHERE cart_id = %d",
                $cart_id
            ) );
            return new WP_REST_Response( [ 'count' => $count ], 200 );
        },
        'permission_callback' => '__return_true',
    ] );

    // GET /cart/items
    register_rest_route( SLV_REST_NAMESPACE, '/cart/items', [
        'methods'             => 'GET',
        'callback'            => static function (): WP_REST_Response {
            global $wpdb;
            $cart_key = slv_get_cart_key();
            if ( '' === $cart_key ) {
                return new WP_REST_Response( [ 'items' => [] ], 200 );
            }
            $cart_id = $wpdb->get_var( $wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}slv_carts WHERE cart_key = %s AND status = 'active' LIMIT 1",
                $cart_key
            ) );
            if ( ! $cart_id ) {
                return new WP_REST_Response( [ 'items' => [] ], 200 );
            }
            $rows = $wpdb->get_results( $wpdb->prepare(
                "SELECT ci.id, ci.product_id, ci.quantity, ci.unit_price, ci.subtotal,
                        p.name AS product_name
                 FROM {$wpdb->prefix}slv_cart_items ci
                 LEFT JOIN {$wpdb->prefix}slv_products p ON p.id = ci.product_id
                 WHERE ci.cart_id = %d
                 ORDER BY ci.id ASC",
                $cart_id
            ), ARRAY_A );

            $items = [];
            foreach ( (array) $rows as $row ) {
                $items[] = [
                    'id'           => (int) $row['id'],
                    'product_id'   => (int) $row['product_id'],
                    'product_name' => $row['product_name'] ?: '商品 #' . $row['product_id'],
                    'unit_price'   => (float) $row['unit_price'],
                    'quantity'     => (int) $row['quantity'],
                    'subtotal'     => (float) $row['subtotal'],
                ];
            }
            return new WP_REST_Response( [ 'items' => $items ], 200 );
        },
        'permission_callback' => '__return_true',
    ] );

    // GET /orders
    register_rest_route( SLV_REST_NAMESPACE, '/orders', [
        'methods'             => 'GET',
        'callback'            => static function (): WP_REST_Response {
            $user_id = get_current_user_id();
            if ( ! $user_id ) {
                return new WP_REST_Response( [ 'data' => [] ], 200 );
            }
            global $wpdb;
            $rows = $wpdb->get_results( $wpdb->prepare(
                "SELECT id, order_number, status, total, currency, created_at
                 FROM {$wpdb->prefix}slv_orders
                 WHERE user_id = %d
                 ORDER BY created_at DESC
                 LIMIT 50",
                $user_id
            ), ARRAY_A );

            $orders = [];
            foreach ( (array) $rows as $row ) {
                $orders[] = [
                    'id'           => (int) $row['id'],
                    'order_number' => $row['order_number'],
                    'status'       => $row['status'],
                    'total'        => (float) $row['total'],
                    'created_at'   => $row['created_at'],
                ];
            }
            return new WP_REST_Response( [ 'data' => $orders ], 200 );
        },
        'permission_callback' => '__return_true',
    ] );

} );