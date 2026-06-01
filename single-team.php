<?php
/**
 * The Template for displaying all single posts
 *
 * @package ZookaStudio
 * @subpackage Foldery
 * @since 1.0.0
 */

get_header(); ?>
<div class="<?php cms_main_class(); ?>">
    <div class="row">
        <div id="primary" class="single col-xs-12 col-sm-9 col-md-8 col-lg-8">
            <div id="content" role="main">
                <?php while ( have_posts() ) : the_post(); 
                	/* Get Team Meta */
            		$team_meta = foldery_post_meta_data();
                ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('single-team single-post'); ?>>
						<?php if(has_post_thumbnail()) {?>
							<div class="entry-media entry-feature-image"><?php the_post_thumbnail( 'single-thumb' ); ?></div>
						<?php } ?>
						<div class="entry-header">
							<h2 class="entry-title"><?php the_title(); ?></h2>
							<?php if(!empty($team_meta->_cms_position)) echo  '<div class="cms-grid-team-position">'.esc_attr($team_meta->_cms_position).'</div>'; ?>
						</div>
						<!-- .entry-header -->
						<div class="entry-content">
							<?php the_content(); ?>
			                <div class="cms-team-social">
			                    <?php if(!empty($team_meta->_cms_facebook_url)) echo  '<a href="'.esc_attr($team_meta->_cms_facebook_url).'"><i class="fa fa-facebook-square"></i></a>'; ?>
			                    <?php if(!empty($team_meta->_cms_twitter_url)) echo  '<a href="'.esc_attr($team_meta->_cms_twitter_url).'"><i class="fa fa-twitter"></i></a>'; ?>
			                    <?php if(!empty($team_meta->_cms_instagram_url)) echo  '<a href="'.esc_attr($team_meta->_cms_instagram_url).'"><i class="fa fa-instagram"></i></a>'; ?>
			                </div>
						</div>
						<!-- .entry-content -->
						<footer class="entry-footer clearfix">
							<?php edit_post_link( esc_html__( 'Edit', 'foldery' ), '<span class="edit-link">', '</span>' ); ?>
						</footer>
						<!-- .entry-meta -->
						<?php
							wp_link_pages( array(
				        		'before'      => '<div class="pagination loop-pagination"><span class="page-links-title">' . esc_html__( 'Pages:', 'foldery' ) . '</span>',
				        		'after'       => '</div>',
				        		'link_before' => '<span class="page-numbers">',
				        		'link_after'  => '</span>',
				    		) );
						?>
					</article>
					<!-- #post -->
                <?php endwhile; // end of the loop. ?>

            </div><!-- #content -->
        </div><!-- #primary -->
        <div id="page-sidebar" class="col-xs-12 col-sm-3 col-md-4 col-lg-4">
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>