<?php get_header(); ?>

<main id="main" class="site-main container">
    <header class="archive-header">
        <h1><?php the_archive_title(); ?></h1>
        <?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
    </header>

    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <div class="entry-content"><?php the_excerpt(); ?></div>
            </article>
        <?php endwhile; ?>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <p><?php esc_html_e( 'No content found.', 'foldery' ); ?></p>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
