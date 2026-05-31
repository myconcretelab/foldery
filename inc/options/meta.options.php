<?php
/**
 * Meta options
 * 
 * @author Fox
 * @since 1.0.0
 */
if(!class_exists('CMSMetaOptions')) return;

class CMSMetaOptions_child extends CMSMetaOptions
{
    public function __construct()
    {
        parent::__construct();
    }
    /* add meta boxs */
    public function cms_add_mtbs()
    {
        $this->cms_add_mtb('template_page_options', __('Setting123', 'foldery' ), 'page');
        /*Add Portfolio page option */
        /*$this->add_meta_box('template_page_portfolio', __('Page Extra Option', 'foldery' ), 'page');*/

        /* Add portfolio extra field */
        $this->cms_add_mtb('portfolio_extra_field', __('Portfolio Extra Field123', 'foldery' ), 'portfolio');

        /* Add Team extra field */
        $this->cms_add_mtb('team_extra_field', __('Team Extra Field123', 'foldery' ), 'team');
    }
    /* --------------------- PAGE ---------------------- */
    function template_page_options() {
        ?>
        <div class="tab-container clearfix">
	        <ul class='etabs clearfix'>
	           <li class="tab"><a href="#tabs-header"><i class="fa fa-diamond"></i><?php esc_html_e('Header', 'foldery' ); ?></a></li>
	           <li class="tab"><a href="#tabs-page-title"><i class="fa fa-connectdevelop"></i><?php esc_html_e('Page Title', 'foldery' ); ?></a></li>
               <li class="tab tabs-page-option"><a href="#tabs-page-option"><?php esc_html_e('Page Option', 'foldery' ); ?></a></li>
               <li class="tab tabs-blog-option"><a href="#tabs-blog-option"><?php esc_html_e('Blog Option', 'foldery' ); ?></a></li>
               <?php if(function_exists('cptui_create_custom_post_types')){ ?>
               <li class="tab tabs-portfolio-option"><a href="#tabs-portfolio-option"><?php esc_html_e('Portfolio Option', 'foldery' ); ?></a></li>
               <?php } ?>
               
	        </ul>
	        <div class='panel-container'>
                
                <div id="tabs-header">
                <?php
                /* header. */
                cms_options(array(
                    'id' => 'header',
                    'label' => esc_html__('Custom', 'foldery' ),
                    'type' => 'switch',
                    'options' => array('on'=>'1','off'=>''),
                    'follow' => array('1'=>array('#page_header_enable'))
                ));
                ?>  
                <div id="page_header_enable">
                <?php
                cms_options(array(
                    'id' => 'header_layout',
                    'label' => esc_html__('Layout', 'foldery' ),
                    'type' => 'imegesselect',
                    'options' => array(
                        '' => get_template_directory_uri().'/inc/options/images/header/h-default.png',
                        'v1' => get_template_directory_uri().'/inc/options/images/header/h-v1.png',
                        'v2' => get_template_directory_uri().'/inc/options/images/header/h-v2.png',
                        'v3' => get_template_directory_uri().'/inc/options/images/header/h-v3.png',
                        'v4' => get_template_directory_uri().'/inc/options/images/header/h-v4.png'
                    )
                ));
                cms_options(array(
                    'label' => esc_html__('Header Type', 'foldery' ),
                    'subtitle' => esc_html__('Choose a type for your header.', 'foldery' ),
                    'id' => 'header_fixed',
                    'type' => 'select',
                    'options' => array(
                        ''      => 'Default',
                        'ontop' => 'On Top',
                        'fixed' => 'Fixed Top'
                    )
                ));
                ?> 
                <?php
                $menus = array();
                $menus[''] = 'Default';
                $obj_menus = wp_get_nav_menus();
                foreach ($obj_menus as $obj_menu){
                    $menus[$obj_menu->term_id] = $obj_menu->name;
                }
                $navs = get_registered_nav_menus();
                foreach ($navs as $key => $mav){
                    cms_options(array(
                    'id' => $key,
                    'label' => $mav,
                    'type' => 'select',
                    'options' => $menus
                    ));
                }
                ?>
                </div>
                </div>
                <div id="tabs-page-title">
                <?php
                /* page title. */
                cms_options(array(
                    'id' => 'page_title',
                    'label' => esc_html__('Custom', 'foldery' ),
                    'type' => 'switch',
                    'options' => array('on'=>'1','off'=>''),
                    'follow' => array('1'=>array('#page_title_enable'))
                ));
                ?>  <div id="page_title_enable"><?php
                cms_options(array(
                    'id' => 'page_title_text',
                    'label' => esc_html__('Title', 'foldery' ),
                    'type' => 'text',
                ));
                cms_options(array(
                    'id' => 'page_title_sub_text',
                    'label' => esc_html__('Sub Title', 'foldery' ),
                    'type' => 'text',
                ));
                cms_options(array(
                    'id' => 'page_title_type',
                    'label' => esc_html__('Layout', 'foldery' ),
                    'type' => 'imegesselect',
                    'options' => array(
                        '' => get_template_directory_uri().'/inc/options/images/pagetitle/pt-s-0.png',
                        '1' => get_template_directory_uri().'/inc/options/images/pagetitle/pt-s-1.png',
                        '2' => get_template_directory_uri().'/inc/options/images/pagetitle/pt-s-2.png',
                        '3' => get_template_directory_uri().'/inc/options/images/pagetitle/pt-s-3.png',
                        '4' => get_template_directory_uri().'/inc/options/images/pagetitle/pt-s-4.png',
                        '5' => get_template_directory_uri().'/inc/options/images/pagetitle/pt-s-5.png',
                        '6' => get_template_directory_uri().'/inc/options/images/pagetitle/pt-s-6.png'
                    ),
                    'value' => '5'
                ));
                ?>
                </div>
                </div>
                <div id="tabs-page-option">
                    <?php 
                        cms_options(array(
                            'id' => 'full_width',
                            'label' => esc_html__('Full Width', 'foldery' ),
                            'type' => 'switch',
                            'options' => array('on'=>'1','off'=>''),
                        ));
                        cms_options(array(
                            'id' => 'show_blog_sidebar',
                            'label' => esc_html__('Enable Sidebar', 'foldery' ),
                            'type' => 'switch',
                            'options' => array('on'=>'1','off'=>''),
                            'default' => '1'
                        ));
                        cms_options(array(
                            'id' => 'masonry_limit',
                            'label' => esc_html__('Posts Per Page', 'foldery' ),
                            'value' => '8',
                            'type' => 'text',
                        ));
                        cms_options(array(
                            'id' => 'masonry_columns',
                            'label' => esc_html__('Columns', 'foldery' ),
                            'type' => 'select',
                            'options' =>  array('' =>'Default' ,'1'=>'1 Column','2'=>'2 Columns','3'=>'3 Columns','4'=>'4 Columns','6'=>'6 Columns' )
                        ));
                        cms_options(array(
                            'id' => 'masonry_filter',
                            'label' => esc_html__('Enable Filter', 'foldery' ),
                            'type' => 'switch',
                            'options' => array('on'=>'1','off'=>''),
                            'default' => 'on'
                        ));
                        cms_options(array(
                            'id' => 'masonry_loadmore',
                            'label' => esc_html__('Enable Ajax Loadmore', 'foldery' ),
                            'type' => 'switch',
                            'options' => array('on'=>'1','off'=>''),
                            'default' => 'on'
                        ));
                    ?>
                </div>
                <div id="tabs-blog-option">
                    <?php 
                        $cat_options = array();
                        $categories = get_categories();
                        $cat_options[""] = "All";
                        foreach($categories as $category){
                            $cat_options[$category->slug] = $category->name;
                        }
                        cms_options(array(
                            'id' => 'post_categories',
                            'label' => esc_html__('Categories', 'foldery' ),
                            'type' => 'multiple',
                            "options" => $cat_options,
                            "value" => ''
                        ));
                    ?>
                </div>
                <?php if(function_exists('cptui_create_custom_post_types')){ ?>
                <div id="tabs-portfolio-option">
                    <?php 
                        $portfolio_cat = array();
                        $portfolio_categories = get_terms('portfolio_cat');
                        $portfolio_cat[""] = "All";
                        if (taxonomy_exists('portfolio_cat')) {
                            foreach($portfolio_categories as $category){
                                $portfolio_cat[$category->slug] = $category->name;
                            }
                            cms_options(array(
                                'id' => 'portfolio_categories',
                                'label' => esc_html__('Portfolio Categories', 'foldery' ),
                                'type' => 'multiple',
                                "options" => $portfolio_cat,
                                "value" => ''
                            ));
                        } else {
                            echo esc_html__('You need to use CPT UI to create an custom post type called <strong>portfolio</strong> and an custom taxonomies called <strong>portfolio_cat</strong> to use this Option ', 'foldery' );
                        }
                    ?>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php
    }
    
    function portfolio_extra_field(){
        ?>
        <div class="portfolio_extra_field">
            <?php
                cms_options(array(
                    'id' => 'single_layout',
                    'label' => esc_html__('Single Layout', 'foldery' ),
                    'type' => 'select',
                    'options' => array(
                        '' => 'Default',
                        'standard' => 'Standard',
                        'fullwidth' => 'Full Width'
                    )
                ));
                cms_options(array(
                    'id' => 'date',
                    'label' => esc_html__('Date', 'foldery' ),
                    'type' => 'date',
                    'format' => 'F d, Y'
                ));
                cms_options(array(
                    'id' => 'client',
                    'label' => esc_html__('Client', 'foldery' ),
                    'type' => 'text',
                ));
            ?>
        </div>
        <?php
    }

    function team_extra_field(){
        ?>
        <div class="team_extra_field">
            <?php
                cms_options(array(
                    'id' => 'position',
                    'label' => esc_html__('Position', 'foldery' ),
                    'type' => 'text'
                ));
                cms_options(array(
                    'id' => 'facebook_url',
                    'label' => esc_html__('Facebook URL', 'foldery' ),
                    'type' => 'text',
                ));
                cms_options(array(
                    'id' => 'twitter_url',
                    'label' => esc_html__('Twitter URL', 'foldery' ),
                    'type' => 'text',
                ));
                cms_options(array(
                    'id' => 'instagram_url',
                    'label' => esc_html__('Instagram URL', 'foldery' ),
                    'type' => 'text',
                ));
            ?>
        </div>
        <?php
    }
}

new CMSMetaOptions_child();