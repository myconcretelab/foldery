<?php
/**
 * The Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package ZookaStudio
 * @subpackage Monaco
 * @since 1.0.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<?php wp_head(); global $smof_data, $cms_meta, $woocommerce;  ?>
</head>
<body <?php body_class(); ?>><?php if ( function_exists( 'wp_body_open' ) ) wp_body_open(); else  do_action( 'wp_body_open' ); ?>
<div id="cms-page" class="<?php cms_page_class(); ?> <?php cms_header_wrap_class();?> ">
    <section id="cms-header-wrapper" class="clearfix">
        <?php if (is_front_page() && is_active_sidebar( 'sidebar-11' )):?>
            <?php dynamic_sidebar( 'sidebar-11' ); ?>
            <!-- #cms-showcase -->
        <?php endif; ?>
    	<?php if($smof_data['header_widget_search']){?>
    	<section id="cms-search" class="clearfix">
    		<div class="cms-search-inner container">
                <div class="cms-search-content">
                    <form role="search" method="get" action="<?php echo esc_url( home_url( '/'  ) );?>">
                        <div class="row">
                            <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9 nopaddingright">
                                <input type="text" value="<?php echo get_search_query();?>" name="s" placeholder="Type your search" autofocus/>
                            </div>
                            <div class="col-xs-10 col-sm-2 col-md-2 col-lg-2">
                                <input class="btn btn-primary btn-block submit nopaddingleft nopaddingright" type="submit" value="<?php echo esc_attr__( 'Search', 'foldery' )?>" />
                                <?php if($woocommerce):?>
                                    <?php if(is_woocommerce()):?>
                                    <input type="hidden" name="post_type" value="product" />
                                    <?php endif;?>
                                <?php endif;?>
                                
                            </div>
                            <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 text-right">
                            	<a id="header-widget-search-close" style="margin-right:15px;"><i class="fa fa-times"></i></a>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
    	</section>
        <!-- #cms-search -->
    	<?php } ?>
	
    	<header id="masthead" class="site-header <?php cms_header_wrap_class();?>" role="banner">
    		<?php cms_header(); ?>
    	</header><!-- #masthead -->
    </section><!-- #cms-header-wrapper -->
	<section id="cms-content-wrapper" class="<?php echo is_front_page()?'home':''; ?> clearfix">
    <?php cms_page_title(); ?>
	<div id="main" class="main clearfix">
