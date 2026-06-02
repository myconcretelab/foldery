<?php get_header(); ?>

<main id="main" class="site-main container">
    <?php
    $serie_slug = get_query_var( 'serie' );
    if ( $serie_slug ) {
        $serie_page = get_page_by_path( sanitize_title( $serie_slug ) );
        if ( $serie_page instanceof WP_Post ) {
            echo apply_filters( 'the_content', $serie_page->post_content );
        }
    }

    echo do_shortcode( '[foldery_serie_detail]' );
    ?>
</main>

<?php get_footer(); ?>
