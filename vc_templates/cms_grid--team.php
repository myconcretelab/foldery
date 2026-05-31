<?php 
    /* get categories */
    $taxo = 'team_cat';
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
    
    /* Extra params in Monaco Theme */
    $atts['element_title_layout']  = isset($atts['element_title_layout'] )? $atts['element_title_layout']  : '1';
    $element_title_layout = $atts['element_title_layout'];
    $atts['element_title']  = isset($atts['element_title'] )? $atts['element_title']  : '';
    $atts['element_sub_title']  = isset($atts['element_sub_title'] )? $atts['element_sub_title']  : '';
    $atts['element_title_desc']  = isset($atts['element_title_desc'] )? $atts['element_title_desc']  : '';
?>
<div class="cms-grid-wraper cms-grid-team <?php echo esc_attr($atts['template']);?>" id="<?php echo esc_attr($atts['html_id']);?>">
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

                            cms_allowed_html($title) ;
                        ?>
                    </h1>
                </div>
                <div class="cms-element-subtitle playfairdisplay"><?php cms_allowed_html($atts['element_sub_title']); ?></div>
                <div class="cms-element-desc"><?php cms_allowed_html($atts['element_title_desc']); ?></div>
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

                                cms_allowed_html($title) ;
                            ?>
                        </h1>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9 nopaddingleft">
                        <div class="cms-element-subtitle playfairdisplay"><?php cms_allowed_html($atts['element_sub_title']); ?></div>
                        <div class="cms-element-desc"><?php cms_allowed_html($atts['element_title_desc']); ?></div>
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

                                echo cms_allowed_html($title) ;
                            ?>
                        </h1>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9 nopaddingleft">
                        <div class="cms-element-subtitle playfairdisplay"><?php cms_allowed_html($atts['element_sub_title']); ?></div>
                        <div class="cms-element-desc"><?php cms_allowed_html($atts['element_title_desc']); ?></div>
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

                            cms_allowed_html($title) ;
                        ?>
                    </h1>
                </div>
                <div class="cms-element-subtitle playfairdisplay"><?php cms_allowed_html($atts['element_sub_title']); ?></div>
                <div class="cms-element-desc"><?php cms_allowed_html($atts['element_title_desc']); ?></div>
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
    <div class="row cms-grid <?php echo esc_attr($atts['grid_class']);?>">
        <?php
        $posts = $atts['posts'];
        $size = ($atts['layout']=='basic') ? 'monaco-team' : 'monaco-team' ;
        while($posts->have_posts()){
            $posts->the_post();
            $groups = array();
            $groups[] = '"all"';
            /* Get Team Meta */
            $team_meta = cms_post_meta_data();
            foreach(cmsGetCategoriesByPostID(get_the_ID(),$taxo) as $category){
                $groups[] = '"category-'.$category->slug.'"';
            }
            ?>
            <div class="cms-grid-item text-center <?php echo esc_attr($atts['item_class']);?>" data-groups='[<?php echo implode(',', $groups);?>]'>
                <?php 
                    if(has_post_thumbnail() && !post_password_required() && !is_attachment() &&  wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), $size, false)):
                        $class = ' has-thumbnail';
                        $thumbnail = get_the_post_thumbnail(get_the_ID(),$size);
                    else:
                        $class = ' no-image';
                        $thumbnail = '<img src="'.CMS_IMAGES.'no-image.jpg" alt="'.get_the_title().'" />';
                    endif;
                    echo '<div class="cms-grid-media '.esc_attr($class).'">'.$thumbnail.'</div>';
                ?>
                <h4 class="cms-grid-title">
                    <?php the_title();?>
                </h4>
                <?php if(!empty($team_meta->_cms_position)) echo  '<div class="cms-grid-team-position">'.esc_attr($team_meta->_cms_position).'</div>'; ?>

                <div class="cms-grid-team-social">
                    <?php if(!empty($team_meta->_cms_facebook_url)) echo  '<a href="'.esc_attr($team_meta->_cms_facebook_url).'" target="_blank"><i class="fa fa-facebook-square"></i></a>'; ?>
                    <?php if(!empty($team_meta->_cms_twitter_url)) echo  '<a href="'.esc_attr($team_meta->_cms_twitter_url).'" target="_blank"><i class="fa fa-twitter"></i></a>'; ?>
                    <?php if(!empty($team_meta->_cms_instagram_url)) echo  '<a href="'.esc_attr($team_meta->_cms_instagram_url).'" target="_blank"><i class="fa fa-instagram"></i></a>'; ?>
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