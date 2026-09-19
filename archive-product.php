<?php
/**
 * SunLyvo Nexus — 商品列表（PHP 经典模板）
 *
 * @package SunLyvo_Nexus
 * @since 5.3.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<main class="slv-product-list">

    <section class="slv-page-hero">
        <div class="slv-page-hero__inner">
            <nav class="slv-breadcrumb" aria-label="<?php esc_attr_e( '面包屑', 'sunlyvo-nexus' ); ?>">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">首页</a>
                <span aria-hidden="true">/</span>
                <span>商品</span>
            </nav>
            <h1 class="slv-page-hero__title">全部商品</h1>
            <p class="slv-page-hero__desc">精选优质商品，支持全球配送</p>
        </div>
    </section>

    <div class="slv-product-list__body">

        <!-- 筛选栏 -->
        <aside class="slv-product-filter" data-slv-filter>
            <div class="slv-product-filter__header">
                <h2 class="slv-product-filter__title">筛选</h2>
                <button type="button" class="slv-product-filter__clear" data-slv-filter-clear>
                    <?php echo slv_icon( 'x', 12 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    清空
                </button>
            </div>

            <!-- 分类 -->
            <div class="slv-product-filter__group">
                <button type="button" class="slv-product-filter__group-title" data-slv-filter-toggle aria-expanded="true">
                    <span>分类</span>
                    <?php echo slv_icon( 'chevron-down', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </button>
                <div class="slv-product-filter__group-body">
                    <ul class="slv-product-filter__list">
                        <?php
                        $cats = get_terms( [ 'taxonomy' => 'product_cat', 'hide_empty' => false ] );
                        if ( ! is_wp_error( $cats ) && ! empty( $cats ) ) :
                            foreach ( $cats as $cat ) :
                        ?>
                            <li>
                                <label>
                                    <input type="checkbox" name="category" value="<?php echo esc_attr( $cat->slug ); ?>" />
                                    <?php echo esc_html( $cat->name ); ?>
                                    <span class="slv-product-filter__count"><?php echo (int) $cat->count; ?></span>
                                </label>
                            </li>
                        <?php
                            endforeach;
                        else :
                        ?>
                            <li class="slv-product-filter__loading">暂无分类</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- 价格 -->
            <div class="slv-product-filter__group">
                <button type="button" class="slv-product-filter__group-title" data-slv-filter-toggle aria-expanded="true">
                    <span>价格</span>
                    <?php echo slv_icon( 'chevron-down', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </button>
                <div class="slv-product-filter__group-body">
                    <div class="slv-product-filter__price">
                        <input type="number" name="min_price" placeholder="最低" min="0" />
                        <span aria-hidden="true">—</span>
                        <input type="number" name="max_price" placeholder="最高" min="0" />
                    </div>
                    <button type="button" class="slv-button slv-button--secondary slv-button--block" data-slv-price-apply>应用</button>
                </div>
            </div>

            <!-- 标签 -->
            <div class="slv-product-filter__group">
                <button type="button" class="slv-product-filter__group-title" data-slv-filter-toggle aria-expanded="true">
                    <span>标签</span>
                    <?php echo slv_icon( 'chevron-down', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </button>
                <div class="slv-product-filter__group-body">
                    <div class="slv-product-filter__tags">
                        <button type="button" class="slv-tag-chip" data-tag="hot">热销</button>
                        <button type="button" class="slv-tag-chip" data-tag="new">新品</button>
                        <button type="button" class="slv-tag-chip" data-tag="sale">促销</button>
                    </div>
                </div>
            </div>
        </aside>

        <!-- 商品区 -->
        <div class="slv-product-list__main">

            <div class="slv-product-toolbar">
                <div class="slv-product-toolbar__left">
                    <span class="slv-product-toolbar__count">
                        共 <strong data-slv-total><?php echo (int) $GLOBALS['wp_query']->found_posts; ?></strong> 件商品
                    </span>
                </div>
                <div class="slv-product-toolbar__right">
                    <select class="slv-product-toolbar__sort" data-slv-sort aria-label="排序">
                        <option value="default">默认排序</option>
                        <option value="latest">最新上架</option>
                        <option value="price-asc">价格从低到高</option>
                        <option value="price-desc">价格从高到低</option>
                    </select>
                    <div class="slv-product-toolbar__view">
                        <button type="button" class="slv-view-btn is-active" data-slv-view="grid" aria-label="网格视图">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                        </button>
                        <button type="button" class="slv-view-btn" data-slv-view="list" aria-label="列表视图">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="8" x2="21" y1="6" y2="6"/><line x1="8" x2="21" y1="12" y2="12"/><line x1="8" x2="21" y1="18" y2="18"/><line x1="3" x2="3.01" y1="6" y2="6"/><line x1="3" x2="3.01" y1="12" y2="12"/><line x1="3" x2="3.01" y1="18" y2="18"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- 商品网格 -->
            <div class="slv-product-grid" data-slv-grid>
                <?php if ( have_posts() ) : ?>
                    <?php while ( have_posts() ) : the_post(); ?>
                        <?php echo slv_render_product_card( get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="slv-product-empty">
                        <?php echo slv_icon( 'package', 64 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <p>暂无商品</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 推荐 -->
            <?php
            $recommend = new WP_Query( [
                'post_type'      => 'product',
                'posts_per_page' => 4,
                'orderby'        => 'rand',
                'post_status'    => 'publish',
                'no_found_rows'  => true,
            ] );
            if ( $recommend->have_posts() ) :
            ?>
                <section class="slv-product-recommend">
                    <h2 class="slv-product-recommend__title">你可能还喜欢</h2>
                    <p class="slv-product-recommend__subtitle">基于你的浏览历史和兴趣推荐</p>
                    <div class="slv-product-grid">
                        <?php while ( $recommend->have_posts() ) : $recommend->the_post(); ?>
                            <?php echo slv_render_product_card( get_the_ID(), true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </section>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php
get_footer();