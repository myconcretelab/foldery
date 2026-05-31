<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package ZookaStudio
 * @subpackage Monaco
 * @since 1.0.0
 */
global $smof_data;
$footer_bottom_layout = $smof_data['footer_bottom_layout'];
?>
        </div><!-- #main -->
        </section> <!-- #cms-content-wrapper -->
        <footer id="footer-wrapper" class="footer-bottom-layout-<?php echo esc_attr($footer_bottom_layout);?>">
        <div class="footer-wrapper-inner">

        </div>
        </footer><!-- #footer-wrapper -->
    </div><!-- #page -->
    <?php if($smof_data['header_layout'] == 'v4') { ?>
        <div id="cms-mainnav-v4" class="cms-menu">
            <a id="cms-hide-mainnav"><i class="pe-7s-close"></i></a>
            <div class="cms-mainnav-v4-logo">
                <a href="<?php echo home_url(); ?>"><img alt="<?php echo get_bloginfo('name');?>" src="<?php echo esc_url($smof_data['header_main_logo']['url']); ?>"></a>
            </div>
            <?php
            
            $attr = array(
                'menu' =>cms_menu_location(),
                'menu_class' => 'nav-menu',
            );
            
            $menu_locations = get_nav_menu_locations();
            
            if(!empty($menu_locations['primary'])){
                $attr['theme_location'] = 'primary';
            }
            

            
            /* main nav. */
            wp_nav_menu( $attr ); ?>
        </div>
    <?php } ?>
    <?php wp_footer(); ?>
</body>
</html>