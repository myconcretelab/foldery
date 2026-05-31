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
<div id="cms-header-top">
    <div class="container">
        <div class="row">
            <div class="col-xs-8 col-sm-9 col-md-10 col-lg-11">
                <?php dynamic_sidebar( 'sidebar-12' ); ?>
            </div>
            <div class="col-xs-4 col-sm-3 col-md-2 col-lg-1">
                <div id="cms-nav-extra" class="cms-nav-extra main-navigation pull-right">
                    <div id="cms-menu-mobile" class="pull-right"><ul><li><a><i class="fa fa-bars"></i></a></li></ul></div>
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
                    <?php if (is_page_template('page-templates/portfolio-masonry.php')) {?>
                    <div class="pull-right hidden-xs hidden-sm">
                        <ul id="cms-portfolio-masonry-sort">
                            <li><span><?php echo __('Display', 'foldery' );?></span></li>
                            <li><a id="columns2" class="change-columns active"><i class="fa fa-th-large"></i></a>
                            <li><a id="columns3" class="change-columns"><i class="fa fa-th"></i></a></li>
                        </ul>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="cms-header" class="cms-header <?php cms_header_class();?>">
    <div class="<?php echo esc_attr($smof_data['header_fullwidth'])?'no-container':'container';?>">
        <div id="cms-header-logo" class="text-center">
            <a href="<?php echo home_url(); ?>"><img alt="<?php echo get_bloginfo('name');?>" src="<?php echo esc_url($smof_data['main_logo']['url']); ?>"></a>
        </div>
        <div id="cms-header-navigation" class="cms-header-navigation clearfix">
            <nav id="site-navigation" class="main-navigation <?php cms_mainmenu_position();?> clearfix" role="navigation">
                <?php
                
                $attr = array(
                    'menu' =>cms_menu_location(),
                    'menu_class' => 'nav-menu',
                );
                
                $menu_locations = get_nav_menu_locations();
                
                if(!empty($menu_locations['primary'])){
                    $attr['theme_location'] = 'primary';
                }
                
                /* enable mega menu. */
                if(class_exists('HeroMenuWalker')){ $attr['walker'] = new HeroMenuWalker(); }
                
                /* main nav. */
                wp_nav_menu( $attr ); ?>
            </nav>
        </div>
    </div>
</div>
<!-- #site-navigation -->