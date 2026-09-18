<?php
/**
 * SunLyvo Nexus — 内容辅助函数
 *
 * @package SunLyvo_Nexus
 * @since 1.0.0
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'slv_content_type' ) ) {
    function slv_content_type(): string {
        if ( is_singular( 'wiki' ) ) return 'wiki';
        if ( is_singular( 'faq' ) ) return 'faq';
        if ( is_singular( 'collection' ) ) return 'collection';
        if ( is_singular( 'product' ) ) return 'product';
        if ( is_singular( 'post' ) ) return 'post';
        return 'page';
    }
}

if ( ! function_exists( 'slv_render_toc' ) ) {
    function slv_render_toc(): void {
        if ( ! is_singular( [ 'post', 'wiki', 'document', 'faq' ] ) ) return;
        echo '<nav class="slv-toc" aria-label="目录" tabindex="-1">';
        echo '<div class="slv-toc__header"><span class="slv-toc__title">目录</span>';
        echo '<button type="button" class="slv-toc__toggle" aria-expanded="true" aria-label="切换目录">';
        echo '<svg class="slv-icon" width="16" height="16" aria-hidden="true"><use href="#slv-icon-chevron-down"></use></svg>';
        echo '</button></div>';
        echo '<div class="slv-toc__body"></div>';
        echo '</nav>';
        echo '<div class="slv-reader-progress"><div class="slv-reader-progress__bar"></div></div>';
    }
}

if ( ! function_exists( 'slv_render_author_box' ) ) {
    function slv_render_author_box( int $post_id ): void {
        $author_id = $post_id
            ? (int) get_post_field( 'post_author', $post_id )
            : (int) get_the_author_meta( 'ID' );
        if ( ! $author_id ) return;

        $user = get_userdata( $author_id );
        if ( ! $user ) return;

        $bio = get_user_meta( $author_id, 'description', true );
        ?>
        <div class="slv-author-box slv-card">
            <div class="slv-author-box__avatar">
                <?php echo get_avatar( $author_id, 64, '', $user->display_name ); ?>
            </div>
            <div class="slv-author-box__body">
                <h3 class="slv-author-box__name"><?php echo esc_html( $user->display_name ); ?></h3>
                <?php if ( $bio ) : ?>
                    <p class="slv-author-box__bio"><?php echo esc_html( $bio ); ?></p>
                <?php endif; ?>
                <a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>" class="slv-author-box__link">
                    <?php esc_html_e( '查看全部文章', 'sunlyvo-nexus' ); ?>
                    <svg class="slv-icon" width="14" height="14" aria-hidden="true"><use href="#slv-icon-arrow-right"></use></svg>
                </a>
            </div>
        </div>
        <?php
    }
}

if ( ! function_exists( 'slv_render_related_posts' ) ) {
    function slv_render_related_posts( int $post_id, string $post_type = 'post', int $limit = 3 ): void {
        $taxonomy_map = [
            'post'       => 'post_tag',
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
                    <article class="slv-card slv-related__item">
                        <a href="<?php the_permalink(); ?>" class="slv-related__link">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="slv-related__media"><?php the_post_thumbnail( 'medium', [ 'loading' => 'lazy' ] ); ?></div>
                            <?php endif; ?>
                            <h3 class="slv-related__heading"><?php the_title(); ?></h3>
                            <p class="slv-related__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
                        </a>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </section>
        <?php
    }
}

if ( ! function_exists( 'slv_render_wiki_infobox' ) ) {
    function slv_render_wiki_infobox( int $post_id ): void {
        $infobox_raw = get_post_meta( $post_id, '_slv_wiki_infobox', true );
        $rows = json_decode( (string) $infobox_raw, true );
        if ( ! is_array( $rows ) || empty( $rows ) ) return;
        ?>
        <aside class="slv-wiki-infobox slv-card">
            <h3 class="slv-wiki-infobox__title"><?php esc_html_e( '基本信息', 'sunlyvo-nexus' ); ?></h3>
            <dl class="slv-wiki-infobox__list">
                <?php foreach ( $rows as $row ) : ?>
                    <?php if ( empty( $row['label'] ) ) continue; ?>
                    <dt><?php echo esc_html( $row['label'] ); ?></dt>
                    <dd><?php echo esc_html( $row['value'] ?? '' ); ?></dd>
                <?php endforeach; ?>
            </dl>
        </aside>
        <?php
    }
}

if ( ! function_exists( 'slv_render_wiki_definition' ) ) {
    function slv_render_wiki_definition( int $post_id ): void {
        $definition = get_post_meta( $post_id, '_slv_wiki_definition', true );
        if ( ! $definition ) return;
        ?>
        <div class="slv-wiki-definition slv-bluf-summary">
            <svg class="slv-icon" width="20" height="20" aria-hidden="true"><use href="#slv-icon-book-open"></use></svg>
            <div>
                <strong><?php esc_html_e( '定义', 'sunlyvo-nexus' ); ?></strong>
                <p><?php echo esc_html( $definition ); ?></p>
            </div>
        </div>
        <?php
    }
}

if ( ! function_exists( 'slv_render_wiki_aliases' ) ) {
    function slv_render_wiki_aliases( int $post_id ): void {
        $aliases = get_post_meta( $post_id, '_slv_wiki_aliases', true );
        if ( ! $aliases ) return;
        $items = array_filter( array_map( 'trim', explode( ',', (string) $aliases ) ) );
        if ( empty( $items ) ) return;
        ?>
        <div class="slv-wiki-aliases">
            <span class="slv-wiki-aliases__label"><?php esc_html_e( '别名', 'sunlyvo-nexus' ); ?></span>
            <?php foreach ( $items as $alias ) : ?>
                <span class="slv-badge"><?php echo esc_html( $alias ); ?></span>
            <?php endforeach; ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'slv_get_faq_pairs' ) ) {
    function slv_get_faq_pairs( int $post_id ): array {
        global $wpdb;
        $table = $wpdb->prefix . 'slv_aeo_faqs';
        $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
        if ( ! $exists ) return [];
        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT question, answer, answer_short FROM {$table}
             WHERE post_id = %d ORDER BY sort_order ASC",
            $post_id
        ), ARRAY_A );
        return $rows ?: [];
    }
}

if ( ! function_exists( 'slv_render_faq_accordion' ) ) {
    function slv_render_faq_accordion( int $post_id ): void {
        $pairs = slv_get_faq_pairs( $post_id );
        if ( empty( $pairs ) ) {
            echo '<div class="slv-faq-answer slv-bluf-summary">';
            echo wp_kses_post( get_the_excerpt() );
            echo '</div>';
            return;
        }
        ?>
        <div class="slv-faq-accordion">
            <?php foreach ( $pairs as $i => $pair ) : ?>
                <details class="slv-faq-item" <?php echo $i === 0 ? 'open' : ''; ?>>
                    <summary class="slv-faq-item__question">
                        <span><?php echo esc_html( $pair['question'] ); ?></span>
                        <svg class="slv-icon" width="16" height="16" aria-hidden="true"><use href="#slv-icon-chevron-down"></use></svg>
                    </summary>
                    <div class="slv-faq-item__answer slv-faq-answer">
                        <?php echo wp_kses_post( $pair['answer'] ); ?>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
        <?php
    }
}

if ( ! function_exists( 'slv_get_collection_chapters' ) ) {
    function slv_get_collection_chapters( int $collection_id ): array {
        $chapters = get_posts( [
            'post_type'      => 'chapter',
            'posts_per_page' => -1,
            'meta_key'       => '_slv_chapter_order',
            'orderby'        => 'meta_value_num',
            'order'          => 'ASC',
            'post_status'    => 'publish',
            'meta_query'     => [
                [
                    'key'     => '_slv_collection_id',
                    'value'   => $collection_id,
                    'compare' => '=',
                ],
            ],
        ] );
        return $chapters ?: [];
    }
}

if ( ! function_exists( 'slv_render_collection_chapters' ) ) {
    function slv_render_collection_chapters( int $collection_id ): void {
        $chapters = slv_get_collection_chapters( $collection_id );
        if ( empty( $chapters ) ) return;

        $user_id    = get_current_user_id();
        $can_access = function_exists( 'slv_can_access_collection' )
            ? slv_can_access_collection( $collection_id, $user_id )
            : true;

        ?>
        <section class="slv-collection-chapters">
            <h2 class="slv-collection-chapters__title">
                <?php printf( esc_html__( '合集内容（共 %d 章）', 'sunlyvo-nexus' ), count( $chapters ) ); ?>
            </h2>
            <ol class="slv-collection-chapters__list">
                <?php foreach ( $chapters as $i => $chapter ) :
                    $is_free = get_post_meta( $chapter->ID, '_slv_is_free_preview', true ) === '1';
                    $locked  = ! $can_access && ! $is_free;
                    ?>
                    <li class="slv-collection-chapters__item <?php echo $locked ? 'is-locked' : ''; ?>">
                        <span class="slv-collection-chapters__num"><?php echo (int) ( $i + 1 ); ?></span>
                        <div class="slv-collection-chapters__body">
                            <?php if ( $locked ) : ?>
                                <span class="slv-collection-chapters__title-text">
                                    <?php echo esc_html( get_the_title( $chapter ) ); ?>
                                    <svg class="slv-icon" width="14" height="14" aria-hidden="true"><use href="#slv-icon-lock"></use></svg>
                                </span>
                            <?php else : ?>
                                <a href="<?php echo esc_url( get_permalink( $chapter ) ); ?>" class="slv-collection-chapters__link">
                                    <?php echo esc_html( get_the_title( $chapter ) ); ?>
                                </a>
                            <?php endif; ?>
                            <?php if ( $is_free ) : ?>
                                <span class="slv-badge slv-badge--knowledge"><?php esc_html_e( '免费试读', 'sunlyvo-nexus' ); ?></span>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
        </section>
        <?php
    }
}

if ( ! function_exists( 'slv_render_collection_cta' ) ) {
    function slv_render_collection_cta( int $collection_id ): void {
        $price   = get_post_meta( $collection_id, '_slv_collection_price', true );
        $is_free = get_post_meta( $collection_id, '_slv_is_free', true ) === '1';
        ?>
        <aside class="slv-collection-cta slv-card">
            <?php if ( $is_free ) : ?>
                <div class="slv-collection-cta__price"><?php esc_html_e( '免费', 'sunlyvo-nexus' ); ?></div>
            <?php else : ?>
                <div class="slv-collection-cta__price">¥ <?php echo esc_html( $price ?: '0' ); ?></div>
            <?php endif; ?>
            <button
                type="button"
                class="slv-button slv-button--lg slv-button--block"
                data-slv-buy-collection
                data-id="<?php echo esc_attr( (string) $collection_id ); ?>"
            >
                <?php echo $is_free
                    ? esc_html__( '开始阅读', 'sunlyvo-nexus' )
                    : esc_html__( '立即购买', 'sunlyvo-nexus' ); ?>
            </button>
        </aside>
        <?php
    }
}