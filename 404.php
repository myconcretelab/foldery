<?php get_header(); ?>

<main id="main" class="site-main container">
    <article class="post error404 no-results not-found">
        <h1><?php esc_html_e( 'Page not found', 'foldery' ); ?></h1>
        <p><?php esc_html_e( 'Try searching for what you need.', 'foldery' ); ?></p>
        <?php get_search_form(); ?>
    </article>
</main>

<?php get_footer(); ?>
