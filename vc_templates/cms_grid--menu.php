<?php 
    /* get categories */
    $taxo = 'category';
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
    $atts['filter']  = isset($atts['filter'] )? $atts['filter']  : '';
    $atts['layout']  = isset($atts['layout'] )? $atts['layout']  : '';
    $atts['element_title_layout']  = isset($atts['element_title_layout'] )? $atts['element_title_layout']  : '1';
    $element_title_layout = $atts['element_title_layout'];
    $atts['element_title']  = isset($atts['element_title'] )? $atts['element_title']  : '';
    $atts['element_sub_title']  = isset($atts['element_sub_title'] )? $atts['element_sub_title']  : '';
    $atts['element_title_desc']  = isset($atts['element_title_desc'] )? $atts['element_title_desc']  : '';
?>
<div class="cms-grid-wraper <?php echo esc_attr($atts['template']);?>" id="<?php echo esc_attr($atts['html_id']);?>">
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
                        <div class="cms-element-desc"><?php foldery_allowed_html($atts['element_title_desc']); ?></div>
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
    
    <div class="row <?php echo esc_attr($atts['grid_class']);?>">
        <?php
        $posts = $atts['posts'];
        $size = ($atts['layout']=='basic')?'blog-grid':'blog-masonry';
        while($posts->have_posts()){
            $posts->the_post();
            $groups = array();
            $groups[] = '"all"';
            foreach(foldery_get_categories_by_post_id(get_the_ID(),$taxo) as $category){
                $groups[] = '"category-'.$category->slug.'"';
            }
            ?>
            <div class="cms-grid-item <?php echo esc_attr($atts['item_class']);?>" data-groups='[<?php echo implode(',', $groups);?>]'>
                <h5 class="cms-grid-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title();?></a>
                </h5>
                <div class="cms-grid-content playfairdisplay">
                    <?php echo cms_limit_words(get_the_excerpt(),10); ?>
                </div>
                <div class="cms-grid-price primary-color">
                    <?php do_action('zooka_element_price'); ?>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>