<?php
/**
 * @name : Default
 * @package : CMSSuperHeroes
 * @author : Fox
 */
?>
<?php 
    global $smof_data, $woocommerce; 
?>
<div id="cms-header" class="cms-header <?php cms_header_class();?>">
    <div id="cms-nav-extra" class="cms-nav-extra main-navigation pull-right">
        <?php if (is_page_template('page-templates/portfolio-masonry.php')) {?>
        <div class="pull-left hidden-xs hidden-sm">
            <ul id="cms-portfolio-masonry-sort">
                <li><span><?php echo __('Display', 'foldery' );?></span></li>
                <li><a id="columns2" class="change-columns active"><i class="fa fa-th-large"></i></a>
                <li><a id="columns3" class="change-columns"><i class="fa fa-th"></i></a></li>
            </ul>
        </div>
        <?php } ?>
        <div id="cms-menu-mobile" class="pull-right hidden-md hidden-lg"><ul><li><a><i class="fa fa-bars"></i></a></li></ul></div>
        <?php if($smof_data['header_widget']){?>
            <?php if($smof_data['header_widget_search']){?>
            <!-- Load WP Search -->
            <div class="pull-right">
                <ul>
                    <li>
                        <a id="header-widget-search"><i class="fa fa-search"></i></a>
                    </li>
                </ul>
            </div>
            <?php } ?>
            
            <?php if($woocommerce && $smof_data['header_widget_cart'] && is_active_sidebar('sidebar-8')) { ?>
            <!-- Load widget WooCommerce Cart -->
            <div class="pull-right">
                <?php dynamic_sidebar('sidebar-8'); ?> 
            </div>                          
            <?php } ?>
            
        <?php } ?>
    </div>
    <div class="<?php echo esc_attr($smof_data['header_fullwidth'])?'no-container':'container';?>">
        <div class="row">

            <div id="cms-header-navigation-left" class="cms-header-navigation col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <nav id="site-navigation" class="main-navigation pull-right clearfix" role="navigation">
                    <?php
                    
                    $attr = array(
                        'menu' =>cms_menu_left_location(),
                        'menu_class' => 'nav-menu',
                    );
                    
                    $menu_locations = get_nav_menu_locations();
                    
                    if(!empty($menu_locations['leftmenu'])){
                        $attr['theme_location'] = 'leftmenu';
                    }
                    
                    /* enable mega menu. */
                    if(class_exists('HeroMenuWalker')){ $attr['walker'] = new HeroMenuWalker(); }
                    
                    /* main nav. */
                    wp_nav_menu( $attr ); ?>
                </nav>
            </div>
            <div id="cms-header-logo" class="col-xs-12 col-sm-4 col-md-4 col-lg-4 text-center">
                <a href="<?php echo home_url(); ?>"><img alt="<?php echo get_bloginfo('name');?>" src="<?php echo esc_url($smof_data['main_logo']['url']); ?>"></a>
            </div>
            <div id="cms-header-navigation-right" class="cms-header-navigation col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <nav id="site-navigation" class="main-navigation pull-left clearfix" role="navigation">
                    <?php
                    
                    $attr = array(
                        'menu' =>cms_menu_right_location(),
                        'menu_class' => 'nav-menu',
                    );
                    
                    $menu_locations = get_nav_menu_locations();
                    
                    if(!empty($menu_locations['rightmenu'])){
                        $attr['theme_location'] = 'rightmenu';
                    }
                    
                    /* enable mega menu. */
                    if(class_exists('HeroMenuWalker')){ $attr['walker'] = new HeroMenuWalker(); }
                    
                    /* main nav. */
                    wp_nav_menu( $attr ); ?>
                </nav>
            </div>
        </div>
    </div>
    <div id="cms-header-navigation">
        <!-- Load mobile menu here  -->
        <div class="main-navigation"></div>
    </div>
</div>
<!-- #site-navigation -->