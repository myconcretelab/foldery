<?php
/**
 * Template Name: Listing Reproductions
 */

get_header();
?>

<main id="main" class="site-main container">
    <?php while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="entry-content">
                <?php the_content(); ?>
                <?php
                if ( ! has_shortcode( get_the_content(), 'foldery_reproductions' ) ) {
                    echo do_shortcode( '[foldery_reproductions]' );
                }
                ?>
            </div>
        </article>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
