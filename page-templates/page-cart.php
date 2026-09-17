<?php
/**
 * Template Name: 购物车
 *
 * @package SunLyvo_Nexus
 */

get_header();
?>

<main class="slv-page">
    <div class="slv-container">
        <h1><?php the_title(); ?></h1>
        <div id="slv-react-cart">
            <noscript><?php esc_html_e( '购物车需要启用 JavaScript。', 'sunlyvo-nexus' ); ?></noscript>
        </div>
    </div>
</main>

<?php
get_footer();