<?php get_header(); ?>

<main id="main" class="site-main container">
    <?php while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <h1><?php the_title(); ?></h1>
            <div class="entry-content"><?php the_content(); ?></div>
        </article>
        <?php the_post_navigation(); ?>
        <?php comments_template(); ?>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
