<?php
/**
 * SunLyvo Nexus — 深色模式切换（用户级）
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SLV_Style_Switcher {

    /**
     * 渲染深色模式切换按钮。
     */
    public static function render(): void {
        $mode = SLV_Style_Resolver::resolve_theme_mode();
        ?>
        <button type="button"
                class="slv-theme-toggle"
                data-mode="<?php echo esc_attr( $mode ); ?>"
                aria-label="<?php esc_attr_e( '切换深色模式', 'sunlyvo-nexus' ); ?>">
            <span class="slv-theme-toggle__icon slv-theme-toggle__icon--light">
                <?php slv_icon( 'lightbulb', 18 ); ?>
            </span>
            <span class="slv-theme-toggle__icon slv-theme-toggle__icon--dark">
                <?php slv_icon( 'eye', 18 ); ?>
            </span>
        </button>
        <?php
    }
}