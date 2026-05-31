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
    <div class="<?php echo esc_attr($smof_data['header_fullwidth'])?'no-container':'container';?>">
        <div id="cms-header-logo" class="main-navigation pull-left">
            <?php zk_monaco_main_logo(); ?>
        </div>
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
            <?php if($smof_data['header_widget']){?>
                <div class="pull-left">
                    <?php if($woocommerce && $smof_data['header_widget_cart'] && is_active_sidebar('sidebar-8')) { ?>
                        <?php dynamic_sidebar('sidebar-8'); ?>                           
                    <?php } ?>
                </div>
                <div class="pull-left">
                    <?php if($smof_data['header_widget_search']){?>
                        <ul>
                            <li>
                                <a id="header-widget-search"><i class="fa fa-search"></i></a>
                            </li>
                        </ul>
                    <?php } ?>
                </div>
            <?php } ?>
            <div id="cms-menu-mobile" class="pull-left"><ul><li><a><i class="fa fa-bars"></i></a></li></ul></div>
        </div>
        <div id="cms-header-navigation" class="cms-header-navigation">
            <nav id="site-navigation" class="main-navigation clearfix" role="navigation">
                
                <div class="cms-menu <?php cms_mainmenu_position();?>">
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
                </div>
            </nav>
        </div>
    </div>
</div>
<!-- #site-navigation -->