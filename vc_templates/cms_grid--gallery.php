<?php 
    extract(shortcode_atts(array(
        'show_nav'           => false,
        'nav_type'           => 0,
    ), $atts));
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

    /* Extra params in Monaco Theme */
    $atts['filter']  = isset($atts['filter'] )? $atts['filter']  : '';
    $atts['layout']  = isset($atts['layout'] )? $atts['layout']  : '';
    $atts['show_image']  = isset($atts['show_image'] )? $atts['show_image']  : '1';
    $atts['element_title_layout']  = isset($atts['element_title_layout'] )? $atts['element_title_layout']  : '1';
    $element_title_layout = $atts['element_title_layout'];
    $atts['element_title']  = isset($atts['element_title'] )? $atts['element_title']  : '';
    $atts['element_sub_title']  = isset($atts['element_sub_title'] )? $atts['element_sub_title']  : '';
    $atts['element_title_desc']  = isset($atts['element_title_desc'] )? $atts['element_title_desc']  : '';

    if($show_nav && $nav_type){
        /*ajax media*/
        wp_enqueue_style( 'wp-mediaelement' );
        wp_enqueue_script( 'wp-mediaelement' );
        /* js, css for load more */
        wp_register_script( 'cms-loadmore-js', CMS_JS.'cms_loadmore.js', array('jquery') ,'1.0',true);
        // What page are we on? And what is the pages limit?
        global $wp_query;
        $max = $wp_query->max_num_pages;
        $limit = $atts['limit'];
        $paged = ( get_query_var('paged') > 1 ) ? get_query_var('paged') : 1;

        // Add some parameters for the JS.
        $current_id =  str_replace('-','_',$atts['html_id']);
        wp_localize_script(
            'cms-loadmore-js',
            'cms_more_obj'.$current_id,
            array(
                'startPage' => $paged,
                'maxPages' => $max,
                'total' => $wp_query->found_posts,
                'perpage' => $limit,
                'nextLink' => next_posts($max, false),
                'masonry' => $atts['layout']
            )
        );
        wp_enqueue_script( 'cms-loadmore-js' );
    }
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

                                cms_allowed_html($title) ;
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
    
    <div class="row <?php echo esc_attr($atts['grid_class']);?>">
        <?php
        $posts = $atts['posts'];
        $size = ($atts['layout']=='basic')?'blog-grid':'blog-masonry';

        while($posts->have_posts()){
            $posts->the_post();
            $groups = array();
            $groups[] = '"all"';
            $pretty_rel_random = ' data-rel="prettyPhoto[rel-'.esc_attr($atts['html_id']).']"';
            foreach(cmsGetCategoriesByPostID(get_the_ID(),$taxo) as $category){
                $groups[] = '"category-'.$category->slug.'"';
            }
            ?>
            <div class="cms-grid-item text-center <?php echo esc_attr($atts['item_class']);?>" data-groups='[<?php echo implode(',', $groups);?>]'>
                <?php 
                    if($atts['show_image']){
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
                        $overlay_content = '<div class="overlay"><div class="overlay-content"><a class="icon circle prettyphoto"  href="'.$image_url.'" '.$pretty_rel_random.'><i class="fa fa-search"></i></a></div></div>';
                        echo '<div class="cms-grid-media overlay-wrap'.esc_attr($class).'">'.$thumbnail.$overlay_content.'</div>';
                    }
                ?>
            </div>
            <?php
        }
        ?>
    </div>
    <?php if($show_nav) {
        if($nav_type){
            echo '<div class="cms_pagination navigation paging-navigation clearfix"></div>';
        } else {
            cms_paging_nav();
        }
    } ?>
</div>