
<?php
    $atts['show_image'] = isset($atts['show_image'])?$atts['show_image']:'0';
    $posts = $atts['posts'];
    $nav = $atts['nav'];
    $nav_icon_image = isset($atts['nav_icon_image'])?$atts['nav_icon_image']:'';
?>
<div class="cms-carousel cms-carousel-testimonial <?php echo esc_attr($atts['template'].' '.$nav_icon_image);?>" id="<?php echo esc_attr($atts['html_id']);?>">
    <?php
    while($posts->have_posts()){
        $posts->the_post();
        ?>
        
            <?php switch ($nav) {
                case 'false': 
            ?>
                    <div class="cms-carousel-item row">
                        <?php  if($atts['show_image'] ) {
                            if(has_post_thumbnail() && !post_password_required() && !is_attachment() &&  wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full', false)):
                                $class = ' has-thumbnail';
                                $thumbnail = get_the_post_thumbnail(get_the_ID(),'medium');
                            else:
                                $class = ' no-image';
                                $thumbnail = '<img src="'.FOLDERY_IMAGES.'no-image.jpg" alt="'.get_the_title().'" />';
                            endif;
                            echo '<div class="cms-carousel-media col-xs-3 col-sm-3 col-md-3 col-lg-3'.esc_attr($class).'">'.$thumbnail.'</div>';
                        } ?>
                        <div class="cms-carousel-content-wrapper <?php if($atts['show_image']) echo 'col-xs-9 col-sm-9 col-md-9 col-lg-9'; else echo 'col-xs-12 col-sm-12 col-md-12 col-lg-12'; ?>">
                            <div class="cms-carousel-content playfairdisplay">
                                <?php the_content();?>
                            </div>
                            <div class="cms-carousel-title h4">
                                <?php the_title();?>
                            </div>
                        </div>
                    </div><!--.cms-carousel-item-->
            <?php    
                break;
                default: 
            ?>
                    <div class="cms-carousel-item row col-xs-12 col-sm-12 col-md-10 col-lg-10 col-md-offset-1 col-lg-offset-1">
                        <?php if($atts['show_image'] ) {
                            if(has_post_thumbnail() && !post_password_required() && !is_attachment() &&  wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full', false)):
                                $class = ' has-thumbnail';
                                $thumbnail = get_the_post_thumbnail(get_the_ID(),'medium');
                            else:
                                $class = ' no-image';
                                $thumbnail = '<img src="'.FOLDERY_IMAGES.'no-image.jpg" alt="'.get_the_title().'" />';
                            endif;
                            echo '<div class="cms-carousel-media col-xs-3 col-sm-3 col-md-3 col-lg-3'.esc_attr($class).'">'.$thumbnail.'</div>';
                        } ?>
                        <div class="cms-carousel-content-wrapper <?php  if($atts['show_image']) echo 'col-xs-9 col-sm-9 col-md-9 col-lg-9'; else echo 'col-xs-12 col-sm-12 col-md-12 col-lg-12'; ?>">
                            <div class="cms-carousel-content playfairdisplay">
                                <?php the_content();?>
                            </div>
                            <div class="cms-carousel-title h4">
                                <?php the_title();?>
                            </div>
                        </div>
                    </div><!--.cms-carousel-item-->
            <?php
                break;
            }?>  
        <?php
    }
    ?>
</div>