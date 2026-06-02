<?php
/**
 * Template Name: Galerie de series globale
 */

get_header();
?>

<main id="main" class="site-main container">
    <?php while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="entry-content">
                <?php the_content(); ?>
                <?php
                if ( ! has_shortcode( get_the_content(), 'foldery_series' ) && ! has_shortcode( get_the_content(), 'serie' ) ) {
                    echo do_shortcode( '[foldery_series folder_id="-1"]' );
                }
                ?>
            </div>
        </article>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
