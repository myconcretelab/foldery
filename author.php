<?php
/**
 * The template for displaying Author Archive pages
 *
 * Used to display archive-type pages for posts by an author.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package ZookaStudio
 * @subpackage Monaco
 * @since 1.0.0
 */
global $smof_data, $paged, $wp_query;
$limit = get_option('posts_per_page','10');
get_header(); ?>
	<div class="<?php cms_main_class(); ?>">
        <div class="row">
			<section id="primary" class="cms-blog blog-author cms-blog-standard col-xs-12 col-sm-9 col-md-9 col-lg-9">
				<div id="content" role="main">

				<?php if ( have_posts() ) : ?>

					<?php
						/* Queue the first post, that way we know
						 * what author we're dealing with (if that is the case).
						 *
						 * We reset this later so we can run the loop
						 * properly with a call to rewind_posts().
						 */
						the_post();
					?>
					<!-- 
					<header class="archive-header">
						<h1 class="archive-title"><?php printf( esc_html__( 'Author Archives: %s', 'foldery' ), '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( "ID" ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' ); ?></h1>
					</header>--><!-- .archive-header -->

					<?php
						/* Since we called the_post() above, we need to
						 * rewind the loop back to the beginning that way
						 * we can run the loop properly, in full.
						 */
						rewind_posts();
					?>

					

					<?php
					// If a user has filled out their description, show a bio on their entries.
					if ( get_the_author_meta( 'description' ) ) : ?>
					<div class="author-info">
						<div class="author-avatar">
							<?php
							/**
							 * Filter the author bio avatar size.
							 *
							 * @since Twenty Twelve 1.0
							 *
							 * @param int $size The height and width of the avatar in pixels.
							 */
							$author_bio_avatar_size = apply_filters( 'twentytwelve_author_bio_avatar_size', 68 );
							echo get_avatar( get_the_author_meta( 'user_email' ), $author_bio_avatar_size );
							?>
						</div><!-- .author-avatar -->
						<div class="author-description">
							<h2><?php printf( esc_html__( 'About %s', 'foldery' ), get_the_author() ); ?></h2>
							<p><?php the_author_meta( 'description' ); ?></p>
						</div><!-- .author-description	-->
					</div><!-- .author-info -->
					<?php endif; ?>

					<?php
                        if($smof_data['blog_nav']){
                            /*ajax media*/
                            wp_enqueue_style( 'wp-mediaelement' );
                            wp_enqueue_script( 'wp-mediaelement' );
                            /* js, css for load more */
                            wp_register_script( 'cms-loadmore-js', get_template_directory_uri().'/assets/js/cms_loadmore.js', array('jquery') ,'1.0',true);
                            // What page are we on? And what is the pages limit?
                            $max = $wp_query->max_num_pages;
                            $paged = ( get_query_var('paged') > 1 ) ? get_query_var('paged') : 1;

                            // Add some parameters for the JS.
                            wp_localize_script(
                                'cms-loadmore-js',
                                'cs_more_obj',
                                array(
                                    'startPage' => $paged,
                                    'maxPages' => $max,
                                    'total' => $wp_query->found_posts,
                                    'perpage' => $limit,
                                    'nextLink' => next_posts($max, false),
                                    'ajaxType' => 'Button',
                                    'masonry' => 'basic'
                                )
                            );
                            wp_enqueue_script( 'cms-loadmore-js' );
                        }
                    ?>

					<?php /* Start the Loop */ ?>
					<div class="cms-isotope-masonry-post cms-grid-masonry-isotope clearfix">
					<?php while ( have_posts() ) : the_post(); ?>
						<div class="cms-grid-item">
						<?php get_template_part( 'single-templates/standard/content', get_post_format() ); ?>
						</div>
					<?php endwhile; ?>

					<?php
                        if($smof_data['blog_nav']){
                            echo '<div class="cs_pagination"></div>';
                        }
                        else{
                            cms_paging_nav();    
                        }
                    ?>
                    </div>

				<?php else : ?>
					<?php get_template_part( 'single-templates/content', 'none' ); ?>
				<?php endif; ?>

				</div><!-- #content -->
			</section><!-- #primary -->
			<div id="page-sidebar" class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
		    	<?php get_sidebar(); ?>
		    </div>
		</div>
	</div>
<?php get_footer(); ?>