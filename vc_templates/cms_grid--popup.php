<?php
	global $smof_data;

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
    $atts['filter']  = isset($atts['filter'] )? $atts['filter']  : '';
    $atts['layout']  = isset($atts['layout'] )? $atts['layout']  : '';
    $atts['show_image']  = isset($atts['show_image'] )? (int)$atts['show_image']  : 1;

    $atts['element_title_layout']  = isset($atts['element_title_layout'] )? $atts['element_title_layout']  : '1';
    $element_title_layout = $atts['element_title_layout'];
    $atts['element_title']  = isset($atts['element_title'] )? $atts['element_title']  : '';
    $atts['element_sub_title']  = isset($atts['element_sub_title'] )? $atts['element_sub_title']  : '';
    $atts['element_title_desc']  = isset($atts['element_title_desc'] )? $atts['element_title_desc']  : '';

    wp_enqueue_script('jquery.fullPage', get_template_directory_uri() . '/assets/js/jquery.fullPage.js', array( 'jquery' ), '2.6.6', true);
    wp_enqueue_script('jquery.fullscreen-popup', get_template_directory_uri() . '/assets/js/jquery.fullscreen-popup.js', array( 'jquery' ), '2.6.6', true);

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

    <div class="<?php echo esc_attr($atts['grid_class']);?> full-page clearfix">
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
            $portfolio_meta = foldery_post_meta_data();
			$portfolio_layout = $portfolio_meta->_cms_single_layout;
			if($portfolio_layout != '') $smof_data['single_portfolio_layout'] = $portfolio_layout;
            ?>
            <div class="cms-grid-item-fullpage clearfix">
                <div class="row cms-grid-item <?php echo esc_attr($atts['show_image'])?'has-image':'';?> clearfix" data-groups='[<?php echo implode(',', $groups);?>]'>
                    <div class="<?php if($atts['show_image']) echo 'col-xs-6 col-sm-6 col-md-5 col-lg-5 col-md-offset-1 col-lg-offset-1 flex-column'; else echo 'col-xs-12 col-sm-12 col-md-12 col-lg-12 flex-column';?>">
                    	<div class="cms-grid-popup-content">
                    		<div>
    		                	<h1 class="cms-grid-title">
    		                    	<?php the_title();?>
    			                </h1>
    			                <div class="cms-grid-content cms-meta">
    			                    <?php echo get_the_term_list( get_the_ID(), $taxo, '', ', ', '' ); ?>
    			                </div>
    			                <div class="cms-grid-link">
    			                    <a class="more-link cms-open-popup" title="<?php echo get_the_title();?>" href="#post<?php the_ID();?>">
    			                    	<?php echo __('Details &rarr;', 'foldery' );?>
    			                    </a>
    			                </div>
    		                </div>
    		            </div>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 flex-column">
                    	<?php
    	                    if($atts['show_image']){
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
    	                        $overlay_content = '<div class="overlay"><div class="overlay-content"><a class="icon circle" href="'.get_the_permalink().'"><i class="fa fa-link"></i></a><a class="icon circle prettyphoto"  href="'.$image_url.'"><i class="fa fa-search"></i></a></div></div>';

    	                        echo '<div class="cms-grid-media flex-column'.esc_attr($class).'" style="background-image:url('.$image_url.'); background-size:cover; background-position:center center;"></div>';
    	                    }

    	                ?>
                    </div>
                    <div id="post<?php the_ID();?>" class="cms-portfolio-popup primary single-portfolio" style="display:none;">
                        <header class="cms-portfolio-popup-header">
    	                	<h2> <?php the_title() ?></h2>
                    	</header>
                    	<?php get_template_part( 'single-templates/portfolio/detail', $smof_data['single_portfolio_layout'] ); ?>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>