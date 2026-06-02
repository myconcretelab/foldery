<?php get_header(); ?>

<main id="main" class="site-main container">
    <h1><?php printf( esc_html__( 'Search results for: %s', 'foldery' ), esc_html( get_search_query() ) ); ?></h1>

    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <div class="entry-content"><?php the_excerpt(); ?></div>
            </article>
        <?php endwhile; ?>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <p><?php esc_html_e( 'No result matched your search.', 'foldery' ); ?></p>
        <?php get_search_form(); ?>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
