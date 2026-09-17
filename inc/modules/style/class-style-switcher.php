<?php
/**
 * SunLyvo Nexus — 前端风格切换按钮
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
     * 渲染切换按钮。
     *
     * @since 1.0.0
     */
    public static function render(): void {
        $current = SLV_Style_Resolver::resolve();
        $styles  = SLV_Style_Registry::all();
        ?>
        <div class="slv-style-switcher">
            <button type="button"
                    class="slv-style-switcher__toggle"
                    aria-haspopup="true"
                    aria-expanded="false"
                    aria-label="<?php esc_attr_e( '切换风格', 'sunlyvo-nexus' ); ?>">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.6">
                    <circle cx="9" cy="9" r="7"/>
                    <path d="M9 2v14M2 9h14"/>
                </svg>
            </button>
            <div class="slv-style-switcher__panel" hidden>
                <div class="slv-style-switcher__group-label"><?php esc_html_e( '基础', 'sunlyvo-nexus' ); ?></div>
                <?php foreach ( $styles as $slug => $s ) :
                    if ( 'base' !== $s['group'] ) continue; ?>
                    <button type="button" class="slv-style-switcher__item <?php echo $slug === $current ? 'is-active' : ''; ?>"
                            data-style="<?php echo esc_attr( $slug ); ?>">
                        <?php echo esc_html( $s['label'] ); ?>
                    </button>
                <?php endforeach; ?>

                <div class="slv-style-switcher__group-label"><?php esc_html_e( '行业', 'sunlyvo-nexus' ); ?></div>
                <?php foreach ( $styles as $slug => $s ) :
                    if ( 'industry' !== $s['group'] ) continue; ?>
                    <button type="button" class="slv-style-switcher__item <?php echo $slug === $current ? 'is-active' : ''; ?>"
                            data-style="<?php echo esc_attr( $slug ); ?>">
                        <?php echo esc_html( $s['label'] ); ?>
                    </button>
                <?php endforeach; ?>

                <div class="slv-style-switcher__group-label"><?php esc_html_e( '辅助', 'sunlyvo-nexus' ); ?></div>
                <?php foreach ( $styles as $slug => $s ) :
                    if ( 'aux' !== $s['group'] ) continue; ?>
                    <button type="button" class="slv-style-switcher__item <?php echo $slug === $current ? 'is-active' : ''; ?>"
                            data-style="<?php echo esc_attr( $slug ); ?>">
                        <?php echo esc_html( $s['label'] ); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}