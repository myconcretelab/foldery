<?php 
/*
* This template for make layout for related item in Portfolio Details 	
*/
?>
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
?>
<div class="cms-grid-wraper cms-grid-porelated <?php echo esc_attr($atts['template']);?>" id="<?php echo esc_attr($atts['html_id']);?>">
    <div class="row cms-grid <?php echo esc_attr($atts['grid_class']);?>">
        <?php
        $posts = $atts['posts'];
        $size = ($atts['layout']=='basic')?'blog-grid':'blog-masonry';
        while($posts->have_posts()){
            $posts->the_post();
            $groups = array();
            $groups[] = '"all"';
            foreach(cmsGetCategoriesByPostID(get_the_ID(),$taxo) as $category){
                $groups[] = '"category-'.$category->slug.'"';
            }
            ?>
            <div class="cms-grid-item text-center <?php echo esc_attr($atts['item_class']);?>" data-groups='[<?php echo implode(',', $groups);?>]'>
                <?php 
                    if(has_post_thumbnail() && !post_password_required() && !is_attachment() &&  wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), $size, false)):
                        $class = ' has-thumbnail';
                        $thumbnail = get_the_post_thumbnail(get_the_ID(),$size);
                        $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
                        $image_url = $image[0];
                    else:
                        $class = ' no-image';
                        $thumbnail = '<img src="'.CMS_IMAGES.'no-image.jpg" alt="'.get_the_title().'" />';
                        $image_url = CMS_IMAGES.'no-image.jpg';
                    endif;
                    /* Load Pretty Photo */
                    wp_enqueue_script('prettyphoto');
                    wp_enqueue_style('prettyphoto');
                    $overlay_content = '<div class="overlay"><div class="overlay-content"><a class="icon circle" href="'.get_the_permalink().'"><i class="fa fa-link"></i></a><a class="icon circle prettyphoto"  href="'.$image_url.'"><i class="fa fa-search"></i></a></div></div>';
                    echo '<div class="cms-grid-media overlay-wrap'.esc_attr($class).'">'.$thumbnail.$overlay_content.'</div>';
                ?>
                <h4 class="cms-grid-title">
                    <a href="<?php the_permalink() ?>"><?php the_title();?></a>
                </h4>
                <div class="cms-grid-category playfairdisplay">
                    <?php echo get_the_term_list( get_the_ID(), 'portfolio_cat', '', ', ', '' ); ?>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <?php 
        wp_reset_postdata();
    ?>
</div>