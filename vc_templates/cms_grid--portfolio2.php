<?php 
    /* get Shortcode custom value */
    extract(shortcode_atts(array(
        'fs_large'     => '1',
    ), $atts));
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

    /* Extra params in Foldery Theme */
    $atts['element_title_layout']  = isset($atts['element_title_layout'] )? $atts['element_title_layout']  : '1';
    $element_title_layout = $atts['element_title_layout'];
    $atts['element_title']  = isset($atts['element_title'] )? $atts['element_title']  : '';
    $atts['element_sub_title']  = isset($atts['element_sub_title'] )? $atts['element_sub_title']  : '';
    $atts['element_title_desc']  = isset($atts['element_title_desc'] )? $atts['element_title_desc']  : '';

    $atts['item_space'] = isset($atts['item_space'] )? $atts['item_space']  : '0';
?>
<div class="cms-grid-wraper cms-grid-portfolio cms-grid-portfolio2 <?php echo esc_attr($atts['template']);?>" id="<?php echo esc_attr($atts['html_id']);?>">
    <?php if($atts['element_title'] !='' || $atts['element_sub_title'] !='' || $atts['element_title_desc'] !='') : ?>
        <header class="cms-element-header layout-<?php echo esc_attr($element_title_layout);?>">
        <?php switch ($element_title_layout) {
                case '1':
            ?>
                <div class="cms-element-header-title">
                    <h1>
                        <?php 
                            $title = $atts['element_title'];
                            $pos = mb_strpos($title, ' ');
                            if ($pos != false) {
                                $title = '<span class="first-word">'.mb_substr($title, 0, $pos).'</span> '.mb_substr($title, $pos + 1);
                            } else {
                                $title = '<span class="first-word">'.$title.'</span>';
                            }

                            foldery_allowed_html($title) ;
                        ?>
                    </h1>
                </div>
                <div class="cms-element-subtitle playfairdisplay"><?php foldery_allowed_html($atts['element_sub_title']); ?></div>
                <div class="cms-element-desc"><?php foldery_allowed_html($atts['element_title_desc']); ?></div>
            <?php
                break;
                case '2' :
            ?>
                <div class="row">
                    <div class="cms-element-header-title col-xs-12 col-sm-4 col-md-4 col-lg-3">
                        <h1>
                            <?php 
                                $title = $atts['element_title'];
                                $pos = mb_strpos($title, ' ');
                                if ($pos != false) {
                                    $title = '<span class="first-word">'.mb_substr($title, 0, $pos).'</span> '.mb_substr($title, $pos + 1);
                                } else {
                                    $title = '<span class="first-word">'.$title.'</span>';
                                }

                                foldery_allowed_html($title) ;
                            ?>
                        </h1>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9 nopaddingleft">
                        <div class="cms-element-subtitle playfairdisplay"><?php foldery_allowed_html($atts['element_sub_title']); ?></div>
                        <div class="cms-element-desc"><?php foldery_allowed_html($atts['element_title_desc']); ?></div>
                    </div>
                </div>
            <?php
                break;
                case '3' :
            ?>
                <div class="container">
                <div class="row">
                    <div class="cms-element-header-title col-xs-12 col-sm-4 col-md-4 col-lg-3">
                        <h1>
                            <?php 
                                $title = $atts['element_title'];
                                $pos = mb_strpos($title, ' ');
                                if ($pos != false) {
                                    $title = '<span class="first-word">'.mb_substr($title, 0, $pos).'</span> '.mb_substr($title, $pos + 1);
                                } else {
                                    $title = '<span class="first-word">'.$title.'</span>';
                                }

                                foldery_allowed_html($title) ;
                            ?>
                        </h1>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9 nopaddingleft">
                        <div class="cms-element-subtitle playfairdisplay"><?php foldery_allowed_html($atts['element_sub_title']); ?></div>
                        <div class="cms-element-desc"><?php foldery_allowed_html(['element_title_desc']); ?></div>
                    </div>
                </div>
                </div>
            <?php
                break;
                default:
            ?>  
                <div class="cms-element-header-title">
                    <h1>
                        <?php 
                            $title = $atts['element_title'];
                            $pos = mb_strpos($title, ' ');
                            if ($pos != false) {
                                $title = '<span class="first-word">'.mb_substr($title, 0, $pos).'</span> '.mb_substr($title, $pos + 1);
                            } else {
                                $title = '<span class="first-word">'.$title.'</span>';
                            }

                            foldery_allowed_html($title) ;
                        ?>
                    </h1>
                </div>
                <div class="cms-element-subtitle playfairdisplay"><?php foldery_allowed_html($atts['element_sub_title']); ?></div>
                <div class="cms-element-desc"><?php foldery_allowed_html($atts['element_title_desc']); ?></div>
            <?php
                break;
            }
        ?>
        </header>
    <?php endif; ?>

    <?php if($atts['filter']=="true" and $atts['layout']=='masonry'):?>
        <div class="cms-grid-filter">
            <ul class="cms-filter-category cms-meta list-unstyled list-inline">
                <li><a class="active" href="#" data-group="all">All</a></li>
                <?php foreach($atts['categories'] as $category):?>
                    <?php $term = get_term( $category, $taxo );?>
                    <li><a href="#" data-group="<?php echo esc_attr('category-'.$term->slug);?>">
                            <?php echo esc_html($term->name);?>
                        </a>
                    </li>
                <?php endforeach;?>
            </ul>
        </div>
    <?php endif;?>
    
    <div class="<?php echo esc_attr($atts['grid_class']);?>" style="margin-left:<?php echo '-'.esc_attr($atts['item_space']);?>">
        <?php
        $posts = $atts['posts'];
        $size = ($atts['layout']=='basic')?'blog-grid':'blog-masonry';
        $d = 0;
        while($posts->have_posts()){
        	$d ++ ;
            $cls = '';
            if($fs_large){
        	    if($d =='1') {
                    $cls = 'first-item'; $size = 'medium';
                } elseif ($d =='2') {
                    $cls = 'second-item'; $size = 'small';
                } else {
                    $cls = ''; $size = 'small';
                }
            } else {
                $size = 'medium';
            }
            $posts->the_post();
            $groups = array();
            $groups[] = '"all"';
            foreach(foldery_get_categories_by_post_id(get_the_ID(),$taxo) as $category){
                $groups[] = '"category-'.$category->slug.'"';
            }
            ?>
            <div class="<?php echo esc_attr($atts['item_class']).' '.$cls.' nopaddingall';?>" data-groups='[<?php echo implode(',', $groups);?>]'>
                <div class="overlay-wrap" style="margin-left:<?php echo esc_attr($atts['item_space']);?>; margin-bottom:<?php echo esc_attr($atts['item_space']);?>;">
                    <?php 
                        if(has_post_thumbnail() && !post_password_required() && !is_attachment() &&  wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), $size, false)):
                            $class = ' has-thumbnail';
                            $thumbnail = get_the_post_thumbnail(get_the_ID(),$size);
                            $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
                            $image_url = $image[0];
                        else:
                            $class = ' no-image';
                            $thumbnail = '<img src="'.FOLDERY_IMAGES.'no-image.jpg" alt="'.get_the_title().'" />';
                            $image_url = FOLDERY_IMAGES.'no-image.jpg';
                        endif;
                        echo '<div class="cms-grid-media size-'. $size . ' ' .esc_attr($class).'">'.$thumbnail.'</div>';
                    ?>
                    <div class="overlay text-center"  href="<?php the_permalink(); ?>">
                    	<div class="overlay-content">
    		                <h4 class="cms-grid-title">
    		                    <a href="<?php the_permalink(); ?>"><?php the_title();?></a>
    		                </h4>
    		                <div class="cms-grid-categories playfairdisplay">
    		                    <?php echo get_field('annee'); //get_the_term_list( get_the_ID(), $taxo, '', ', ', '' ); ?>
    		                </div>
                            
    		            </div>
    		        </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>