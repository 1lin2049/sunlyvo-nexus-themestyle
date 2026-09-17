<?php
/**
 * Template Name: 结账
 *
 * @package SunLyvo_Nexus
 */

get_header();
?>

<main class="slv-page">
    <div class="slv-container">
        <div id="slv-react-checkout">
            <noscript><?php esc_html_e( '结账需要启用 JavaScript。', 'sunlyvo-nexus' ); ?></noscript>
        </div>
    </div>
</main>

<?php get_footer();