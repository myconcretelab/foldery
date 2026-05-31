<div class="cms-carousel <?php echo esc_attr($atts['template']);?>" id="<?php echo esc_attr($atts['html_id']);?>">
    <?php
    /* get categories */
    $taxo = 'portfolio_cat';
    $_category = array();
    if(!isset($atts['cat']) || $atts['cat']==''){
        $terms = get_terms($taxo);
        foreach ($terms as $cat){
            $_category[] = $cat->term_id;
        }
    } else {
        $_category  = explode(',', $atts['cat']);
    }
    $atts['categories'] = $_category;

    $posts = $atts['posts'];
    while($posts->have_posts()){
        $posts->the_post();
        ?>
        <div class="cms-carousel-item overlay-wrap">
            <?php 
                if(has_post_thumbnail() && !post_password_required() && !is_attachment() &&  wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full', false)):
                    $class = ' has-thumbnail';
                    $thumbnail = get_the_post_thumbnail(get_the_ID(),'blog-grid');
                else:
                    $class = ' no-image';
                    $thumbnail = '<img src="'.CMS_IMAGES.'no-image.jpg" alt="'.get_the_title().'" />';
                endif;
                echo '<div class="cms-grid-media '.esc_attr($class).'">'.$thumbnail.'</div>';
            ?>
            <div class="overlay">
            	<div class="overlay-content text-center color-white">
		            <div class="cms-carousel-title">
		                <h4><a href="<?php the_permalink(); ?>" alt="<?php get_the_title();?>" title="<?php get_the_title();?>"><?php the_title();?></a></h4>
		            </div>
		            <div class="cms-meta cms-carousel-categories">
		                <?php echo get_the_term_list( get_the_ID(), $taxo, '', ', ', '' ); ?>
		            </div>
	            </div>
	        </div>
        </div>
        <?php
    }
    ?>
</div>