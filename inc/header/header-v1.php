<?php
/**
 * @name : Default
 * @package : CMSSuperHeroes
 * @author : Fox
 */
global $woocommerce;
?>
<?php global $smof_data, $cms_meta; ?>
    <div id="cms-header" class="cms-header <?php cms_header_class();?>">
    <div id="cms-header-inner">
        <div id="cms-header-logo">
            <a href="<?php echo home_url(); ?>"><img alt="<?php echo get_bloginfo('name');?>" src="<?php echo esc_url($smof_data['main_logo']['url']); ?>"></a>
        </div>
        <div id="cms-header-navigation">
            <nav id="site-navigation" class="main-navigation" role="navigation">
                <?php
                
                $attr = array(
                    'menu' =>cms_menu_location(),
                    'menu_class' => 'nav-menu menu-main-menu',
                );
                
                $menu_locations = get_nav_menu_locations();
                
                if(!empty($menu_locations['primary'])){
                    $attr['theme_location'] = 'primary';
                }
                
                /* main nav. */
                wp_nav_menu( $attr ); ?>

            </nav>
        </div>
        <div id="cms-nav-extra" class="cms-nav-extra main-navigation">
            <div id="cms-menu-mobile" class="pull-left"><ul><li><a><i class="fa fa-bars"></i></a></li></ul></div>
        </div>
    </div>
</div>
<!-- #site-navigation -->