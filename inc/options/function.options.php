<?php
global $cms_base, $woocommerce, $mof_data;
/* get local fonts. */
$local_fonts = is_admin() ? $cms_base->getListLocalFontsName() : array() ;
$fontawesome_cheatsheet = 'http://fortawesome.github.io/Font-Awesome/cheatsheet/';

/* Get menu position */
$menus = array();
$menus[''] = 'Default';
$obj_menus = wp_get_nav_menus();
foreach ($obj_menus as $obj_menu){
    $menus[$obj_menu->term_id] = $obj_menu->name;
}
$navs = get_registered_nav_menus();

/**
 * Body
 *
 * @author Fox
 */
$this->sections[] = array(
    'title' => esc_html__('Body', 'foldery'),
    'icon' => 'el-icon-website',
    'fields' => array(
        array(
            'subtitle' => esc_html__('Set layout boxed default(Wide).', 'foldery'),
            'id' => 'body_layout',
            'type' => 'switch',
            'title' => esc_html__('Boxed Layout', 'foldery'),
            'default' => false,
        ),
        array(
            'id'       => 'body_background',
            'type'     => 'background',
            'title'    => esc_html__( 'Background', 'foldery' ),
            'subtitle' => esc_html__( 'body background with image, color, etc.', 'foldery' ),
            'output'   => array('body'),
            'default'  => array(
                'background-color' => '#ffffff'
            )
        ),
        array(
            'id'             => 'body_margin', 
            'type'           => 'spacing',
            'output'         => array('body'),
            'mode'           => 'margin',
            'units'          => array('px'),
            'units_extended' => 'false',
            'title'          => esc_html__('Margin Option', 'foldery'),
            'subtitle'       => esc_html__('in pixels, top right bottom left, ex: 10px 10px 10px 10px', 'foldery'),
            'desc'           => esc_html__('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'foldery'),
            'default'            => array(
                'margin-top'     => '', 
                'margin-right'   => '', 
                'margin-bottom'  => '', 
                'margin-left'    => '',
                'units'          => 'px', 
            )
        ),
        array(
            'id'             => 'body_padding',
            'type'           => 'spacing',
            'output'         => array('body'),
            'mode'           => 'padding',
            'units'          => array('px'),
            'units_extended' => 'false',
            'title'          => esc_html__('Padding Option', 'foldery'),
            'subtitle'       => esc_html__('in pixels, top right bottom left, ex: 10px 10px 10px 10px', 'foldery'),
            'desc'           => esc_html__('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'foldery'),
            'default'            => array(
                'padding-top'     => '', 
                'padding-right'   => '', 
                'padding-bottom'  => '', 
                'padding-left'    => '',
                'units'          => 'px', 
            )
        )
    )
);

/**
 * Content
 * 
 * Archive, Pages, Single, 404, Search, Category, Tags .... 
 * @author Fox
 */
$this->sections[] = array(
    'title' => esc_html__('Content', 'foldery'),
    'icon' => 'el-icon-compass',
    'subsection' => true,
    'fields' => array(
        array(
            'id'       => 'container_background',
            'type'     => 'background',
            'title'    => esc_html__( 'Background', 'foldery' ),
            'subtitle' => esc_html__( 'Container background with image, color, etc.', 'foldery' ),
            'output'   => array('#cms-content-wrapper'),
            'default'  => array(
                'background-color' => '#ffffff'
            )
        ),
        array(
            'id'             => 'container_margin',
            'type'           => 'spacing',
            'output'         => array('#cms-content-wrapper'),
            'mode'           => 'margin',
            'units'          => array('px'),
            'units_extended' => 'false',
            'title'          => esc_html__('Margin Option', 'foldery'),
            'subtitle'       => esc_html__('in pixels, top right bottom left, ex: 10px 10px 10px 10px', 'foldery'),
            'desc'           => esc_html__('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'foldery'),
            'default'            => array(
                'margin-top'     => '', 
                'margin-right'   => '', 
                'margin-bottom'  => '', 
                'margin-left'    => '',
                'units'          => 'px', 
            )
        ),
        array(
            'id'             => 'container_padding',
            'type'           => 'spacing',
            'output'         => array('#cms-content-wrapper'),
            'mode'           => 'padding',
            'units'          => array('px'),
            'units_extended' => 'false',
            'title'          => esc_html__('Padding Option', 'foldery'),
            'subtitle'       => esc_html__('in pixels, top right bottom left, ex: 10px 10px 10px 10px', 'foldery'),
            'desc'           => esc_html__('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'foldery'),
            'default'            => array(
                'padding-top'     => '', 
                'padding-right'   => '', 
                'padding-bottom'  => '', 
                'padding-left'    => '',
                'units'          => 'px', 
            )
        )
    )
);

/**
 * Styling
 * 
 * css color.
 * @author Fox
 */
$this->sections[] = array(
    'title' => esc_html__('Styling', 'foldery'),
    'icon' => 'el-icon-adjust',
    'fields' => array(
        array(
            
            'id' => 'primary_color',
            'type' => 'color',
            'title' => esc_html__('Primary Color', 'foldery'),
            'subtitle' => esc_html__('set color main color.', 'foldery'),
            'default' => '#b46d70'
        ),
        array(
            'id' => 'secondary_color',
            'type' => 'color',
            'title' => esc_html__('Secondary Color', 'foldery'),
            'default' => '#888888'
        ),
        array(
            
            'id' => 'link_color',
            'type' => 'color',
            'title' => esc_html__('Link Color', 'foldery'),
            'subtitle' => esc_html__('set color for tags <a></a>.', 'foldery'),
            'output'  => array('a'),
            'default' => '#b46d70'
        ),
        array(
            
            'id' => 'link_color_hover',
            'type' => 'color',
            'title' => esc_html__('Link Color Hover', 'foldery'),
            'subtitle' => esc_html__('set color for tags <a></a> when mouse hover.', 'foldery'),
            'output'  => array('a:hover'),
            'default' => '#888888'
        )
    )
);

/**
 * Typography
 * 
 * @author Fox
 */
$this->sections[] = array(
    'title' => esc_html__('Typography', 'foldery'),
    'icon' => 'el-icon-text-width',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'font_body',
            'type' => 'typography',
            'title' => esc_html__('Body Font', 'foldery'),
            'google' => true,
            'font-backup' => true,
            'all_styles' => true,
            'word-spacing' => true,
            'letter-spacing' => true,
            'output'  => array('body, .tags-list a'),
            'units' => 'px',
            'subtitle' => esc_html__('Typography option with each property can be called individually.', 'foldery'),
            'default' => array(
                'color' => '#888888',
                'font-weight' => '',
                'font-style' => 'normal',
                'font-family' => '',
                'google' => true,
                'font-size' => '14px',
                'line-height' => '24px',
                'text-align' => '',
                'letter-spacing' => '0.5px',
                'word-spacing' => ''
            )
        ),
        array(
            'id' => 'font_h1',
            'type' => 'typography',
            'title' => esc_html__('H1', 'foldery'),
            'google' => true,
            'font-backup' => true,
            'all_styles' => true,
            'word-spacing' => true,
            'letter-spacing' => true,
            'output'  => array('h1,.h1,h1 a,.h1 a'),
            'units' => 'px',
            'default' => array(
                'color' => '#1f1f1f',
                'font-weight' => '',
                'font-style' => 'normal',
                'font-family' => '',
                'google' => true,
                'font-size' => '48px',
                'line-height' => '77px',
                'text-align' => '',
                'letter-spacing' => '1px',
                'word-spacing' => ''
            )
        ),
        array(
            'id' => 'font_h2',
            'type' => 'typography',
            'title' => esc_html__('H2', 'foldery'),
            'google' => true,
            'font-backup' => true,
            'all_styles' => true,
            'word-spacing' => true,
            'letter-spacing' => true,
            'output'  => array('h2,.h2,h2 a,.h2 a'),
            'units' => 'px',
            'default' => array(
                'color' => '#1f1f1f',
                'font-weight' => '',
                'font-style' => 'normal',
                'font-family' => '',
                'google' => true,
                'font-size' => '24px',
                'line-height' => '39px',
                'text-align' => '',
                'letter-spacing' => '1px',
                'word-spacing' => ''
            )
        ),
        array(
            'id' => 'font_h3',
            'type' => 'typography',
            'title' => esc_html__('H3', 'foldery'),
            'google' => true,
            'font-backup' => true,
            'all_styles' => true,
            'word-spacing' => true,
            'letter-spacing' => true,
            'output'  => array('h3,.h3,h3 a,.h3 a'),
            'units' => 'px',
            'default' => array(
                'color' => '#1f1f1f',
                'font-weight' => '',
                'font-style' => 'normal',
                'font-family' => '',
                'google' => true,
                'font-size' => '18px',
                'line-height' => '29px',
                'text-align' => '',
                'letter-spacing' => '1px',
                'word-spacing' => ''
            )
        ),
        array(
            'id' => 'font_h4',
            'type' => 'typography',
            'title' => esc_html__('H4', 'foldery'),
            'google' => true,
            'font-backup' => true,
            'all_styles' => true,
            'word-spacing' => true,
            'letter-spacing' => true,
            'output'  => array('h4,.h4,h4 a,.h4 a'),
            'units' => 'px',
            'default' => array(
                'color' => '#1f1f1f',
                'font-weight' => '400',
                'font-style' => 'normal',
                'font-family' => '',
                'google' => true,
                'font-size' => '16px',
                'line-height' => '26px',
                'text-align' => '',
                'letter-spacing' => '1px',
                'word-spacing' => ''
            )
        ),
        array(
            'id' => 'font_h5',
            'type' => 'typography',
            'title' => esc_html__('H5', 'foldery'),
            'google' => true,
            'font-backup' => true,
            'all_styles' => true,
            'word-spacing' => true,
            'letter-spacing' => true,
            'output'  => array('h5,.h5,h5 a,.h5 a, .product-name a, thead, ul.product_list_widget span.product-title'),
            'units' => 'px',
            'default' => array(
                'color' => '#1f1f1f',
                'font-weight' => '',
                'font-style' => 'normal',
                'font-family' => '',
                'google' => true,
                'font-size' => '14px',
                'line-height' => '20px',
                'text-align' => '',
                'letter-spacing' => '1px',
                'word-spacing' => ''
            )
        ),
        array(
            'id' => 'font_h6',
            'type' => 'typography',
            'title' => esc_html__('H6', 'foldery'),
            'google' => true,
            'font-backup' => true,
            'all_styles' => true,
            'word-spacing' => true,
            'letter-spacing' => true,
            'output'  => array('h6,.h6,h6 a,.h6 a'),
            'units' => 'px',
            'default' => array(
                'color' => '#1f1f1f',
                'font-weight' => '',
                'font-style' => 'normal',
                'font-family' => '',
                'google' => true,
                'font-size' => '12px',
                'line-height' => '16px',
                'text-align' => '',
                'letter-spacing' => '1px',
                'word-spacing' => ''
            )
        )
    )
);

/* extra font. */
$this->sections[] = array(
    'title' => esc_html__('Extra Fonts', 'foldery'),
    'icon' => 'el el-fontsize',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'google-font-1',
            'type' => 'typography',
            'title' => esc_html__('Font 1', 'foldery'),
            'google' => true,
            'font-backup' => false,
            'font-style' => true,
            'color' => false,
            'text-align'=> false,
            'line-height'=>false,
            'font-size'=> false,
            'subsets'=> false,
            'default' => array(
                'google' => true,
                'font-family' => 'Playfair Display',
                'font-weight' => '400',
                'font-style' => 'italic',
            )
        ),
        array(
            'id' => 'google-font-selector-1',
            'type' => 'textarea',
            'title' => esc_html__('Selector 1', 'foldery'),
            'subtitle' => esc_html__('add html tags ID or class (body,a,.class,#id)', 'foldery'),
            'validate' => 'no_html',
            'default' => '.page-sub-title, .cms-meta, .cms-grid-masonry .cms-grid-categories a, blockquote, .blockquote, .quote-content, .tags-list a, .tagcloud a, .widget_newsletterwidget, .playfairdisplay, .single-product .product_meta > span a, .woocommerce-info a'
        ),
        array(
            'id' => 'google-font-2',
            'type' => 'typography',
            'title' => esc_html__('Font 2', 'foldery'),
            'google' => true,
            'font-backup' => true,
            'font-style' => true,
            'color' => false,
            'text-align'=> false,
            'line-height'=>false,
            'font-size'=> false,
            'subsets'=> false,
        ),
        array(
            'id' => 'google-font-selector-2',
            'type' => 'textarea',
            'title' => esc_html__('Selector 2', 'foldery'),
            'subtitle' => esc_html__('add html tags ID or class (body,a,.class,#id)', 'foldery'),
            'validate' => 'no_html',
        ),
    )
);

/* local fonts. */
$this->sections[] = array(
    'title' => esc_html__('Local Fonts', 'foldery'),
    'icon' => 'el-icon-bookmark',
    'subsection' => true,
    'fields' => array(
        array(
            'id'       => 'local-fonts-1',
            'type'     => 'select',
            'title'    => esc_html__( 'Fonts 1', 'foldery' ),
            'options'  => $local_fonts,
            'default'  => 'proxima_nova_ltlight',
        ),
        array(
            'id' => 'local-fonts-selector-1',
            'type' => 'textarea',
            'title' => esc_html__('Selector 1', 'foldery'),
            'subtitle' => esc_html__('add html tags ID or class (body,a,.class,#id)', 'foldery'),
            'validate' => 'no_html',
            'default' => 'body',
            'required' => array(
                0 => 'local-fonts-1',
                1 => '!=',
                2 => ''
            )
        ),
        array(
            'id'       => 'local-fonts-2',
            'type'     => 'select',
            'title'    => esc_html__( 'Fonts 2', 'foldery' ),
            'options'  => $local_fonts,
            'default'  => 'proxima_nova_ltsemibold',
        ),
        array(
            'id' => 'local-fonts-selector-2',
            'type' => 'textarea',
            'title' => esc_html__('Selector 2', 'foldery'),
            'subtitle' => esc_html__('add html tags ID or class (body,a,.class,#id)', 'foldery'),
            'validate' => 'no_html',
            'default' => 'h1,.h1,h2,.h2, h3, .h3, h4, .h4, h5,.h5,h6,.h6, #cms-portfolio-masonry-sort, .btn, .btn-primary, button, input[type="button"], input[type="reset"], input[type="submit"], #cms-search input[type="text"], .main-navigation > div ul:first-child > li > a, ul.product_list_widget span.product-title',
            'required' => array(
                0 => 'local-fonts-2',
                1 => '!=',
                2 => ''
            )
        )
    )
);

/**
 * Header Options
 * 
 * @author Fox
 */
$this->sections[] = array(
    'title' => esc_html__('Header', 'foldery'),
    'icon' => 'el-icon-credit-card',
    'fields' => array(
        array(
            'id' => 'header_layout',
            'title' => esc_html__('Layouts', 'foldery'),
            'subtitle' => esc_html__('select a layout for header', 'foldery'),
            'default' => '',
            'type' => 'image_select',
            'options' => array(
                '' => get_template_directory_uri().'/inc/options/images/header/h-default.png',
                'v1' => get_template_directory_uri().'/inc/options/images/header/h-v1.png',
                'v2' => get_template_directory_uri().'/inc/options/images/header/h-v2.png',
                'v3' => get_template_directory_uri().'/inc/options/images/header/h-v3.png',
                'v4' => get_template_directory_uri().'/inc/options/images/header/h-v4.png'
            )
        ),
        array(
            'id' => 'header_left_menu',
            'type' => 'select',
            'title' => esc_html__('Header Left Menu', 'foldery'),
            'subtitle' => esc_html__('Choose the Left menu.', 'foldery'),
            'options' => $menus,
            'default' => '',
            'required' => array( 0 => 'header_layout', 1 => '=', 2 => 'v2' )
        ),
        array(
            'id' => 'header_right_menu',
            'type' => 'select',
            'title' => esc_html__('Header Right Menu', 'foldery'),
            'subtitle' => esc_html__('Choose the Right menu.', 'foldery'),
            'options' => $menus,
            'default' => '',
            'required' => array( 0 => 'header_layout', 1 => '=', 2 => 'v2' )
        ),
        array(
            'title' => esc_html__('Select Main Menu Logo', 'foldery'),
            'subtitle' => esc_html__('Select an image file for your logo.', 'foldery'),
            'id' => 'header_main_logo',
            'type' => 'media',
            'url' => true,
            'default' => array(),
            'required' => array( 0 => 'header_layout', 1 => '=', 2 => array('v4'))
        ),
        array(
            'title' => esc_html__('Header Type', 'foldery'),
            'subtitle' => esc_html__('Choose a type for your header.', 'foldery'),
            'id' => 'header_fixed',
            'type' => 'select',
            'options' => array(
                ''      => 'Default',
                'ontop' => 'On Top',
                'fixed' => 'Fixed Top'
            ),
            'default' => '',
            'required' => array( 0 => 'header_layout', 1 => '=', 2 => array('','v2','v3','v4') )
        ),
        array(
            'id' => 'fixed_menu_bg',
            'type' => 'color_rgba',
            'title' => esc_html__('Header On Top/Fixed Top Background Color', 'foldery'),
            'subtitle' => esc_html__('Choose background color style for header On Top/Fixed Top when scroll down.', 'foldery'),
            'output'   => array('background-color' => '.cms-header-fixed-bg #cms-header.header-fixed, .cms-header-fixed-bg #cms-header.header-ontop'),
            'default'   => 'rgba(0,0,0,0)',
            'required' => array( 0 => 'header_fixed', 1 => '=', 2 => array('ontop','fixed'))
        ),
        array(
            'subtitle' => esc_html__('in pixels.', 'foldery'),
            'id' => 'header_height',
            'type' => 'text',
            'title' => 'Header Height',
            'default' => '110px',
            'required' => array( 0 => 'header_layout', 1 => '=', 2 => array('','v2','v3','v4') )
        ),
        array(
            'id' => 'header_fullwidth',
            'type' => 'switch',
            'title' => 'Header Full Width',
            'subtitle' => esc_html__('Make your header full width', 'foldery'),
            'default' => false,
            'required' => array( 0 => 'header_layout', 1 => '=', 2 => array('','v2','v3','v4') )
        ),
        array(
            'id' => 'header_position',
            'title' => esc_html__('Position', 'foldery'),
            'subtitle' => esc_html__('select position for header', 'foldery'),
            'default' => 'left',
            'type' => 'select',
            'options' => array(
                'left' => 'Left',
                'right' => 'Right'
            ),
            'required' => array( 0 => 'header_layout', 1 => '=', 2 => 'v1' )
        ),
        array(
            'subtitle' => esc_html__('in pixels.', 'foldery'),
            'id' => 'header_width',
            'type' => 'text',
            'title' => 'Header Width',
            'default' => '260px',
            'required' => array( 0 => 'header_layout', 1 => '=', 2 => 'v1' )
        ),
        
        array(
            'id'             => 'header_margin',
            'type'           => 'spacing',
            'output'         => array('#masthead'),
            'mode'           => 'margin',
            'units'          => array('px'),
            'units_extended' => 'false',
            'title'          => esc_html__('Margin Option', 'foldery'),
            'subtitle'       => esc_html__('in pixels, top right bottom left, ex: 10px 10px 10px 10px', 'foldery'),
            'desc'           => esc_html__('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'foldery'),
            'default'            => array(
                'margin-top'     => '', 
                'margin-right'   => '', 
                'margin-bottom'  => '', 
                'margin-left'    => '',
                'units'          => 'px', 
            )
        ),
        array(
            'id'             => 'header_padding',
            'type'           => 'spacing',
            'output'         => array('#masthead, #masthead #cms-header.header-sticky'),
            'mode'           => 'padding',
            'units'          => array('px'),
            'units_extended' => 'false',
            'title'          => esc_html__('Padding Option', 'foldery'),
            'subtitle'       => esc_html__('in pixels, top right bottom left, ex: 10px 10px 10px 10px', 'foldery'),
            'desc'           => esc_html__('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'foldery'),
            'default'            => array(
                'padding-top'     => '', 
                'padding-right'   => '', 
                'padding-bottom'  => '', 
                'padding-left'    => '',
                'units'          => 'px', 
            )
        ),
        array( 
            'id'       => 'header_border',
            'type'     => 'border',
            'title'    => esc_html__('Header Border Bottom Option', 'foldery'),
            'subtitle' => esc_html__('Only color validation can be done on this field type', 'foldery'),
            'output'   => array('#masthead'),
            'desc'     => esc_html__('Choose the border bottom style for your header.', 'foldery'),
            'top'     => 'false',
            'right'     => 'false',
            'left'     => 'false', 
            'default'  => array(
                'border-color'  => 'transparent', 
                'border-style'  => 'solid',  
                'border-bottom' => '0px', 
            ),
        ),
        array( 
            'id'       => 'header_border_on_home',
            'type'     => 'select',
            'title'    => esc_html__('Show Header Border Bottom Home Page', 'foldery'),
            'subtitle' => esc_html__('Show/Hide header border bottom on home page', 'foldery'),
            'options'  => array(
                '1'  => 'Yes',
                '0'  => 'No',   
            ),
            'default' => '0'
        ),

    )
);

/* Logo */
$this->sections[] = array(
    'title' => esc_html__('Logo', 'foldery'),
    'icon' => 'el-icon-picture',
    'subsection' => true,
    'fields' => array(
        array(
            'title' => esc_html__('Select Logo', 'foldery'),
            'subtitle' => esc_html__('Select an image file for your logo.', 'foldery'),
            'id' => 'main_logo',
            'type' => 'media',
            'url' => true,
            'default' => array()
        ),
        array(
            'subtitle' => esc_html__('in pixels.', 'foldery'),
            'id' => 'main_logo_height',
            'type' => 'text',
            'title' => 'Logo Height',
            'default' => '44px'
        ),
        array(
            'id'             => 'main_logo_margin',
            'type'           => 'spacing',
            'output'         => array('#masthead #cms-header:not(.header-sticky) #cms-header-logo'),
            'mode'           => 'margin',
            'units'          => array('px'),
            'units_extended' => 'false',
            'title'          => esc_html__('Margin Option', 'foldery'),
            'subtitle'       => esc_html__('in pixels, top right bottom left, ex: 10px 10px 10px 10px', 'foldery'),
            'desc'           => esc_html__('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'foldery'),
            'default'            => array(
                'margin-top'     => '', 
                'margin-right'   => '', 
                'margin-bottom'  => '', 
                'margin-left'    => '',
                'units'          => 'px', 
            ),
        ),
    )
);
if(class_exists('WooCommerce')){
    $header_widget_cart = array(
            'id' => 'header_widget_cart',
            'type' => 'switch',
            'title' => 'Widget Cart',
            'subtitle' => esc_html__('Show cart icon.', 'foldery'),
            'default' => false,
            'required' => array( 0 => 'header_widget', 1 => '=', 2 => 1 )
        );
} else {
    $header_widget_cart = '';
}
/* Widget in header  */
$this->sections[] = array(
    'title' => esc_html__('Widget', 'foldery'),
    'icon' => 'dashicons-before dashicons-admin-plugins',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'header_widget',
            'type' => 'switch',
            'title' => 'Header Widget',
            'subtitle' => esc_html__('Show/hide widget in header.', 'foldery'),
            'default' => true
        ),
        array(
            'id' => 'header_widget_search',
            'type' => 'switch',
            'title' => 'Widget Search',
            'subtitle' => esc_html__('Show search icon.', 'foldery'),
            'default' => true,
            'required' => array( 0 => 'header_widget', 1 => '=', 2 => 1 )
        ),
        $header_widget_cart
        ,
        
    )
);

/* Menu in header  */
$this->sections[] = array(
    'title' => esc_html__('Default Header', 'foldery'),
    'icon' => 'el-icon-credit-card',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'header_menu_position',
            'type' => 'select',
            'title' => 'Menu Position',
            'subtitle' => esc_html__('Choose the postion of menu.', 'foldery'),
            'options' => array(
                'left' => 'Left',
                'right' => 'Right',
                'center' => 'Center'
            ),
            'default' => 'right',
            'required' => array( 0 => 'header_layout', 1 => '=', 2 => array('','v3','v4'))
        ),
        array(
            'id'       => 'header_background',
            'type'     => 'background',
            'title'    => esc_html__( 'Background', 'foldery' ),
            'subtitle' => esc_html__( 'background with image, color, etc. This option just apply for Header Type is Default', 'foldery' ),
            'output'   => array('#masthead'),
            'default'   => array(
                'background-color' => 'transparent'
            ),
        ),
        array(
            'id'       => 'menu_first_level_color',
            'type'     => 'link_color',
            'title'    => esc_html__('Color Option', 'foldery'),
            'subtitle' => esc_html__('Only color validation can be done on this field type', 'foldery'),
            'desc'     => esc_html__('set color for First Level of your menu', 'foldery'),
            'default'  => array(
                'regular'  => '#212121', 
                'hover'    => '#b46d70', 
                'active'   => '#b46d70',
            ),
        ),
        
        array(
            'id' => 'menu_first_level_typography',
            'type' => 'typography',
            'title' => esc_html__('Typography', 'foldery'),
            'google' => true,
            'font-backup' => true,
            'all_styles' => true,
            'word-spacing' => true,
            'letter-spacing' => true,
            'color'          => false,
            'text-transform' => true,
            'line-height' => false,
            'output'  => array('.main-navigation > div ul:first-child > li > a'),
            'units' => 'px',
            'subtitle' => esc_html__('Typography option with title text.', 'foldery'),
            'default' => array(
                'font-style' => '',
                'font-weight' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '13px',
                'line-height' => '',
                'text-align' => '',
                'letter-spacing' => '1px',
                'word-spacing' => '',
                'text-transform' => 'uppercase'
            )
        ),
        /* Will update in version 1.0.4 
        array(
            'id'             => 'menu_padding_first_level',
            'type'           => 'spacing',
            'output'         => array('.main-navigation > div ul:first-child > li'),
            'mode'           => 'padding',
            'units'          => array('px'),
            'units_extended' => 'false',
            'title'          => esc_html__('Padding Option', 'foldery'),
            'subtitle'       => esc_html__('in pixels, top right bottom left, ex: 10px 10px 10px 10px', 'foldery'),
            'desc'           => esc_html__('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'foldery'),
            'default'            => array(
                'padding-top'     => '', 
                'padding-right'   => '40px', 
                'padding-bottom'  => '', 
                'padding-left'    => '',
                'units'          => 'px', 
            ),
        ),
        array(
            'id'             => 'menu_margin_first_level',
            'type'           => 'spacing',
            'output'         => array('.main-navigation > div ul:first-child > li'),
            'mode'           => 'margin',
            'units'          => array('px'),
            'units_extended' => 'false',
            'title'          => esc_html__('Margin Option', 'foldery'),
            'subtitle'       => esc_html__('in pixels, top right bottom left, ex: 10px 10px 10px 10px', 'foldery'),
            'desc'           => esc_html__('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'foldery'),
            'default'            => array(
                'margin-top'     => '', 
                'margin-right'   => '', 
                'margin-bottom'  => '', 
                'margin-left'    => '',
                'units'          => 'px', 
            ),
        ),  */      
    )
);
/* Sticky Header */
$this->sections[] = array(
    'title' => esc_html__('Sticky Header', 'foldery'),
    'icon' => 'el-icon-credit-card',
    'subsection' => true,
    'fields' => array(
        array(
            'subtitle' => esc_html__('enable sticky mode for menu.', 'foldery'),
            'id' => 'menu_sticky',
            'type' => 'switch',
            'title' => esc_html__('Sticky Header', 'foldery'),
            'default' => false,
        ),
        array(
            'subtitle' => esc_html__('enable sticky mode for menu Tablets.', 'foldery'),
            'id' => 'menu_sticky_tablets',
            'type' => 'switch',
            'title' => esc_html__('Sticky Tablets', 'foldery'),
            'default' => false,
            'required' => array( 0 => 'menu_sticky', 1 => '=', 2 => 1 )
        ),
        array(
            'subtitle' => esc_html__('enable sticky mode for menu Mobile.', 'foldery'),
            'id' => 'menu_sticky_mobile',
            'type' => 'switch',
            'title' => esc_html__('Sticky Mobile', 'foldery'),
            'default' => false,
            'required' => array( 0 => 'menu_sticky', 1 => '=', 2 => 1 )
        ),
        array(
            'subtitle' => esc_html__('in pixels.', 'foldery'),
            'id' => 'menu_sticky_height',
            'type' => 'text',
            'title' => 'Sticky Header Height',
            'default' => '80px',
            'required' => array( 0 => 'menu_sticky', 1 => '=', 2 => 1 )
        ),
        array(
            'subtitle' => esc_html__('in pixels.', 'foldery'),
            'id' => 'sticky_logo_height',
            'type' => 'text',
            'title' => 'Sticky Logo Height',
            'default' => '22px',
            'required' => array( 0 => 'menu_sticky', 1 => '=', 2 => 1 )
        ),
        array(
            'id'       => 'sticky_header_background',
            'type'     => 'background',
            'title'    => esc_html__( 'Background', 'foldery' ),
            'subtitle' => esc_html__( 'background with image, color, etc.', 'foldery' ),
            'output'   => array('#masthead #cms-header.header-sticky'),
            'default'   => array(
                'background-color' => '#ffffff'
            ),
            'required' => array( 0 => 'menu_sticky', 1 => '=', 2 => 1 )
        ),
        array(
            'id'       => 'sticky_menu_first_level_color',
            'type'     => 'link_color',
            'title'    => esc_html__('Color Option', 'foldery'),
            'subtitle' => esc_html__('Only color validation can be done on this field type', 'foldery'),
            'desc'     => esc_html__('set color for First Level of your menu', 'foldery'),
            'default'  => array(
                'regular'  => '#212121', 
                'hover'    => '#b46d70', 
                'active'   => '#b46d70',
            ),
            'required' => array( 0 => 'menu_sticky', 1 => '=', 2 => 1 )
        ),
        array(
            'id' => 'sticky_header_typography',
            'type' => 'typography',
            'title' => esc_html__('Typography', 'foldery'),
            'google' => true,
            'font-backup' => true,
            'all_styles' => true,
            'word-spacing' => true,
            'letter-spacing' => true,
            'text-transform' => true,
            'color' => false,
            'line-height' => false,
            'output'  => array('
                #cms-header.header-sticky #cms-header-logo a, 
                #cms-header.header-sticky .cshero-header-cart-search .header a,
                #cms-header.header-sticky .main-navigation > div ul:first-child > li > a'),
            'units' => 'px',
            'subtitle' => esc_html__('Typography option with title text.', 'foldery'),
            'default' => array(
                'font-style' => '',
                'font-weight' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '13px',
                'line-height' => '',
                'text-align' => '',
                'letter-spacing' => '',
                'word-spacing' => '',
                'text-transform' => 'uppercase'
            ),
            'required' => array( 0 => 'menu_sticky', 1 => '=', 2 => 1 )
        ) 
    )
);
/*
    * Dropdown Menu
*/
$this->sections[] =array(
    'title' => esc_html__('Dropdown Menu', 'foldery'),
    'subsection' => true,
    'icon'  => 'dashicons-before dashicons-networking',
    'fields' => array(
        array(
            'id'       => 'menu_dropdown_background',
            'type'     => 'background',
            'title'    => esc_html__( 'Background', 'foldery' ),
            'subtitle' => esc_html__( 'background with image, color, etc.', 'foldery' ),
            'output'   => array('.main-navigation > div ul:first-child ul'),
            'default'   => array(
                'background-color'=>'#000000',
                'background-image'=> '',
                'background-repeat'=>'',
                'background-size'=>'',
                'background-attachment'=>'',
                'background-position'=>''
            ),
        ),
        array(
            'id'       => 'menu_dropdown_color',
            'type'     => 'link_color',
            'title'    => esc_html__('Color Option', 'foldery'),
            'subtitle' => esc_html__('Only color validation can be done on this field type', 'foldery'),
            'default'  => array(
                'regular'  => '#ffffff', 
                'hover'    => '#b46d70', 
                'active'   => '#b46d70',  
                'focus'    => '#b46d70',  
            ),
            'output'  => array('.main-navigation > div ul:first-child > li li a'),
        ),
        array(
            'id' => 'menu_dropdown_typography',
            'type' => 'typography',
            'title' => esc_html__('Typography', 'foldery'),
            'google' => true,
            'font-backup' => true,
            'all_styles' => true,
            'word-spacing' => true,
            'letter-spacing' => true,
            'color'          => false,
            'text-transform' => true,
            'output'  => array('.main-navigation > div ul:first-child > li li, .main-navigation > div ul:first-child > li li a'),
            'units' => 'px',
            'subtitle' => esc_html__('Typography option with title text.', 'foldery'),
            'default' => array(
                'font-style' => 'normal',
                'font-weight' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '14px',
                'line-height' => '24px',
                'text-align' => '',
                'letter-spacing' => '',
                'word-spacing' => '',
                'text-transform' => ''
            )
        ),
        
        /* This option can update on V1.0.4.
        array(
            'id'             => 'menu_dropdown_padding',
            'type'           => 'spacing',
            'output'         => array(''),
            'mode'           => 'padding',
            'units'          => array('px'),
            'units_extended' => 'false',
            'title'          => esc_html__('Padding Option', 'foldery'),
            'subtitle'       => esc_html__('in pixels, top right bottom left, ex: 10px 10px 10px 10px', 'foldery'),
            'desc'           => esc_html__('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'foldery'),
            'default'            => array(
                'padding-top'     => '5px', 
                'padding-right'   => '', 
                'padding-bottom'  => '5px', 
                'padding-left'    => '',
                'units'          => 'px', 
            ),
        ),
        array(
            'id'             => 'menu_dropdown_margin',
            'type'           => 'spacing',
            'output'         => array(''),
            'mode'           => 'margin',
            'units'          => array('px'),
            'units_extended' => 'false',
            'title'          => esc_html__('Margin Option', 'foldery'),
            'subtitle'       => esc_html__('in pixels, top right bottom left, ex: 10px 10px 10px 10px', 'foldery'),
            'desc'           => esc_html__('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'foldery'),
            'default'            => array(
                'margin-top'     => '', 
                'margin-right'   => '', 
                'margin-bottom'  => '', 
                'margin-left'    => '',
                'units'          => 'px', 
            ),
        ),
        */
        array( 
            'id'       => 'menu_dropdown_hover_background',
            'type'     => 'background',
            'title'    => esc_html__('Menu Item Background Hover/Active', 'foldery'),
            'subtitle' => esc_html__('Only color validation can be done on this field type', 'foldery'),
            'output'   => array('
                .main-navigation > div ul:first-child > li li:not(.group):hover, 
                .main-navigation > div ul:first-child > li li.current-menu-item:not(.group)
            '),
            'desc'     => esc_html__('Choose background style for menu item in sub level in hover/active state.', 'foldery'),
            'default'  => array(
                    'background-color' => 'transparent'
                )
        ),
        array( 
            'id'       => 'menu_dropdown_border_bottom',
            'type'     => 'border',
            'title'    => esc_html__('Border Bottom', 'foldery'),
            'subtitle' => esc_html__('Only color validation can be done on this field type', 'foldery'),
            'output'   => array('
                .main-navigation > div ul:first-child > li li:not(.group), 
                .main-navigation > div ul:first-child > li > ul.multicolumn > li.group > a
            '),
            'desc'     => esc_html__('Choose border bottom style for menu item in sub level.', 'foldery'),
            'top'      => 'false',
            'right'      => 'false',
            'left'      => 'false',
            'default'  => array(
                'border-color'  => '#999999', 
                'border-style'  => 'solid', 
                'border-width' => '0px', 
            )
        ),
    )
);
/*
    * Mobile Menu
*/
$this->sections[] =array(
    'title' => esc_html__('Mobile Menu', 'foldery'),
    'subsection' => true,
    'icon'  => 'dashicons-before dashicons-tablet',
    'fields' => array(
        array(
            'id'       => 'menu_mobile_background',
            'type'     => 'background',
            'title'    => esc_html__( 'Background', 'foldery' ),
            'subtitle' => esc_html__( 'background with image, color, etc.', 'foldery' ),
            'output'   => array('#masthead #cms-header-navigation.tablets-nav'),
            'default'   => array(
                'background-color'=>'#000000',
                'background-image'=> '',
                'background-repeat'=>'',
                'background-size'=>'',
                'background-attachment'=>'',
                'background-position'=>''
            ),
        ),
        array(
            'id'       => 'menu_mobile_color',
            'type'     => 'link_color',
            'title'    => esc_html__('Color Option', 'foldery'),
            'subtitle' => esc_html__('Only color validation can be done on this field type', 'foldery'),
            'default'  => array(
                'regular'  => '#ffffff', 
                'hover'    => '#b46d70', 
                'active'   => '#b46d70',  
                'focus'    => '#b46d70',  
            ),
        ),
        array(
            'id' => 'menu_mobile_typography',
            'type' => 'typography',
            'title' => esc_html__('Typography', 'foldery'),
            'google' => true,
            'font-backup' => true,
            'all_styles' => true,
            'word-spacing' => true,
            'letter-spacing' => true,
            'color'          => false,
            'text-transform' => true,
            'output'  => array('#cms-header-navigation.tablets-nav .main-navigation a'),
            'units' => 'px',
            'subtitle' => esc_html__('Typography option with title text.', 'foldery'),
            'default' => array(
                'font-style' => 'normal',
                'font-weight' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '14px',
                'line-height' => '24px',
                'text-align' => '',
                'letter-spacing' => '',
                'word-spacing' => '',
                'text-transform' => ''
            )
        ),
        array( 
            'id'       => 'menu_mobile_hover_background',
            'type'     => 'background',
            'title'    => esc_html__('Menu Item Background Hover/Active', 'foldery'),
            'subtitle' => esc_html__('Only color validation can be done on this field type', 'foldery'),
            'output'   => array('#cms-header-navigation.tablets-nav a:hover, #cms-header-navigation.tablets-nav .current-menu-item > a'),
            'desc'     => esc_html__('Choose background style for menu item in sub level in hover/active state.', 'foldery'),
            'default'  => array(
                    'background-color' => 'transparent'
                )
        ),
        array( 
            'id'       => 'menu_mobile_border_bottom',
            'type'     => 'border',
            'title'    => esc_html__('Border Bottom', 'foldery'),
            'subtitle' => esc_html__('Only color validation can be done on this field type', 'foldery'),
            'output'   => array('#cms-header-navigation.tablets-nav .nav-menu > a'),
            'desc'     => esc_html__('Choose border bottom style for menu item in sub level.', 'foldery'),
            'top'      => 'false',
            'right'      => 'false',
            'left'      => 'false',
            'default'  => array(
                'border-color'  => '#999999', 
                'border-style'  => 'solid', 
                'border-width' => '0px', 
            )
        ),
    )
);
/**
 * Page Title
 *
 * @author Fox
 */
$this->sections[] = array(
    'title' => esc_html__('Page Title & BC', 'foldery'),
    'icon' => 'el-icon-map-marker',
    'fields' => array(
        array(
            'id' => 'page_title_layout',
            'title' => esc_html__('Layouts', 'foldery'),
            'subtitle' => esc_html__('select a layout for page title', 'foldery'),
            'default' => '5',
            'type' => 'image_select',
            'options' => array(
                '' => get_template_directory_uri().'/inc/options/images/pagetitle/pt-s-0.png',
                '1' => get_template_directory_uri().'/inc/options/images/pagetitle/pt-s-1.png',
                '2' => get_template_directory_uri().'/inc/options/images/pagetitle/pt-s-2.png',
                '3' => get_template_directory_uri().'/inc/options/images/pagetitle/pt-s-3.png',
                '4' => get_template_directory_uri().'/inc/options/images/pagetitle/pt-s-4.png',
                '5' => get_template_directory_uri().'/inc/options/images/pagetitle/pt-s-5.png',
                '6' => get_template_directory_uri().'/inc/options/images/pagetitle/pt-s-6.png'
            )
        ),
        array(
            'id' => 'page_title_fullwidth',
            'type' => 'switch',
            'title' => esc_html__('Full Width', 'foldery'),
            'subtitle' => esc_html__('Make page title full width or not', 'foldery'),
            'default' => true,
            'required' => array( 0 => 'page_title_layout', 1 => '!=', 2 => '' )
        ),
        array(
            'id'       => 'page_title_background',
            'type'     => 'background',
            'title'    => esc_html__( 'Background', 'foldery' ),
            'subtitle' => esc_html__( 'page title background with image, color, etc.', 'foldery' ),
            'output'   => array('.page-title'),
            'default'   => array(),
            'required' => array( 0 => 'page_title_layout', 1 => '!=', 2 => '' )
        ),
        array(
            'id'             => 'page_title_margin',
            'type'           => 'spacing',
            'output'         => array('#page-title'),
            'mode'           => 'margin',
            'units'          => array('px'),
            'units_extended' => 'false',
            'title'          => esc_html__('Margin Option', 'foldery'),
            'subtitle'       => esc_html__('in pixels, top right bottom left, ex: 10px 10px 10px 10px', 'foldery'),
            'desc'           => esc_html__('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'foldery'),
            'default'            => array(
                'margin-top'     => '0px', 
                'margin-right'   => '0px', 
                'margin-bottom'  => '100px', 
                'margin-left'    => '0px',
                'units'          => 'px', 
            ),
            'required' => array( 0 => 'page_title_layout', 1 => '!=', 2 => '' )
        ),
        array(
            'id'             => 'page_title_padding',
            'type'           => 'spacing',
            'output'         => array('#page-title'),
            'mode'           => 'padding',
            'units'          => array('px'),
            'units_extended' => 'false',
            'title'          => esc_html__('Padding Option', 'foldery'),
            'subtitle'       => esc_html__('in pixels, top right bottom left, ex: 10px 10px 10px 10px', 'foldery'),
            'desc'           => esc_html__('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'foldery'),
            'default'            => array(
                'padding-top'     => '200px', 
                'padding-right'   => '0px', 
                'padding-bottom'  => '100px', 
                'padding-left'    => '0px',
                'units'          => 'px', 
            ),
            'required' => array( 0 => 'page_title_layout', 1 => '!=', 2 => '' )
        )
    )
);
/* Page Title */
$this->sections[] = array(
    'icon' => 'el-icon-podcast',
    'title' => esc_html__('Page Title', 'foldery'),
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'page_title_typography',
            'type' => 'typography',
            'title' => esc_html__('Typography', 'foldery'),
            'google' => true,
            'font-backup' => true,
            'all_styles' => true,
            'output'  => array('.page-title #page-title-text h1, .page-sub-title'),
            'units' => 'px',
            'subtitle' => esc_html__('Typography option with title text.', 'foldery'),
            'default' => array(
                'color' => '#ffffff',
            ),
            'required' => array( 0 => 'page_title_layout', 1 => '!=', 2 => '' )
        ),
    )
);
/* Breadcrumb */
$this->sections[] = array(
    'icon' => 'el-icon-random',
    'title' => esc_html__('Breadcrumb', 'foldery'),
    'subsection' => true,
    'fields' => array(
        array(
            'subtitle' => esc_html__('The text before the breadcrumb home.', 'foldery'),
            'id' => 'breacrumb_home_prefix',
            'type' => 'text',
            'title' => esc_html__('Breadcrumb Home Prefix', 'foldery'),
            'default' => 'Home',
            'required' => array( 0 => 'page_title_layout', 1 => '!=', 2 => '' )
        ),
        array(
            'id' => 'breacrumb_typography',
            'type' => 'typography',
            'title' => esc_html__('Typography', 'foldery'),
            'google' => true,
            'font-backup' => true,
            'all_styles' => true,
            'output'  => array('.page-title #breadcrumb-text','.page-title #breadcrumb-text ul li a:hover'),
            'units' => 'px',
            'subtitle' => esc_html__('Typography option with title text.', 'foldery'),
            'default' => array(
                'color' => '#ffffff',
            ),
            'required' => array( 0 => 'page_title_layout', 1 => '!=', 2 => '' )
        ),
    )
);



/**
 * Blog Option
 * 
 * css color.
 * @author Chinh Duong Manh
 */
$this->sections[] = array(
    'title' => esc_html__('Blog Option', 'foldery'),
    'icon' => 'el-icon-blogger',
    'fields' => array(
        array(
            'id' => 'blog_introtext',
            'type' => 'switch',
            'title' => esc_html__('Blog Intro Text', 'foldery'),
            'subtitle' => esc_html__('Show / Hide introtext of post in Blog page', 'foldery'),
            'default' => false
        ),   
        array(
            'id' => 'blog_nav',
            'type' => 'select',
            'title' => esc_html__('Blog Navigation', 'foldery'),
            'subtitle' => esc_html__('Choose your blog navigation is default or Load More.', 'foldery'),
            'options' => array(
                '' =>'Hide',
                '0' =>'Default',
                '1' =>'Load more',
                '2' => 'Older / Newer'
             ),
            'default' => '0'
        )
    )
);
/**
 * Single Post Option
 * 
 * css color.
 * @author Chinh Duong Manh
 */
$this->sections[] = array(
    'title' => esc_html__('Post Meta', 'foldery'),
    'subtitle'  => esc_html__('This option applied for Blog / Archive / Single Post page', 'foldery'),
    'icon' => 'el-icon-blogger',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'meta_post_date',
            'type' => 'switch',
            'title' => esc_html__('Show Date', 'foldery'),
            'subtitle' => esc_html__('Show / Hide post date', 'foldery'),
            'default' => true
        ),
        array(
            'id' => 'meta_post_author',
            'type' => 'switch',
            'title' => esc_html__('Show Author', 'foldery'),
            'subtitle' => esc_html__('Show / Hide Author name', 'foldery'),
            'default' => true
        ),   
        array(
            'id' => 'meta_post_category',
            'type' => 'switch',
            'title' => esc_html__('Show Category', 'foldery'),
            'subtitle' => esc_html__('Show / Hide Category', 'foldery'),
            'default' => true
        ),   
        array(
            'id' => 'meta_post_comment',
            'type' => 'switch',
            'title' => esc_html__('Show Comment', 'foldery'),
            'subtitle' => esc_html__('Show / Hide comment count', 'foldery'),
            'default' => true
        ),   
        array(
            'id' => 'meta_post_like',
            'type' => 'switch',
            'title' => esc_html__('Show Like', 'foldery'),
            'subtitle' => esc_html__('Show / Hide like count', 'foldery'),
            'default' => true
        ),   
    )
);
/**
 * Page Option
 * 
 * css color.
 * @author Chinh Duong Manh
 */
$this->sections[] = array(
    'title' => esc_html__('Page Option', 'foldery'),
    'icon' => 'el-icon-edit',
    'fields' => array(   
        array(
            'id' => 'page_comment',
            'type' => 'switch',
            'title' => esc_html__('Show Comment', 'foldery'),
            'subtitle' => esc_html__('Show / Hide comment on page.', 'foldery'),
            'default' => true
        ),
    )
);
/**
 * Portfolio Option
 * 
 * css color.
 * @author Chinh Duong Manh
 */
if(function_exists('cptui_create_custom_post_types')){
    $this->sections[] = array(
        'title' => esc_html__('Portfolio Option', 'foldery'),
        'icon' => 'el-icon-th',
        'fields' => array(   
            array(
                'id' => 'single_portfolio_layout',
                'type' => 'select',
                'title' => esc_html__('Single Portfolio Layout', 'foldery'),
                'subtitle' => esc_html__('Choose your single portfolio layout.', 'foldery'),
                'options' => array(
                    'standard' =>'Standard',
                    'fullwidth' =>'Full Width'
                 ),
                'default' => 'standard'
            ),
            array(
                'id' => 'single_portfolio_related',
                'type' => 'switch',
                'title' => esc_html__('Related portfolio', 'foldery'),
                'subtitle' => esc_html__('Show/Hide related portfolio item on single portfolio page.', 'foldery'),
                'default' => true
            ),
        )
    );
}

/**
 * WooCommerce Option
 * 
 * @author Chinh Duong Manh
 */
if($woocommerce){
    $this->sections[] = array(
        'title' => esc_html__('WooCommerce Option', 'foldery'),
        'icon' => 'el-icon-shopping-cart',
        'fields' => array(
            array(
                'id' => 'enable_shop_page_title',
                'type' => 'switch',
                'title' => esc_html__('Enable Shop Page Title', 'foldery'),
                'subtitle' => esc_html__('This option will show the title in Archive product page</i>.', 'foldery'),
                'default' => false,
            ),
            array(
                'id' => 'shop_page_title_fullwidth',
                'type' => 'switch',
                'title' => esc_html__('Full Width', 'foldery'),
                'subtitle' => esc_html__('Make page title full width or not', 'foldery'),
                'default' => true,
                'required' => array( 0 => 'enable_shop_page_title', 1 => '=', 2 => 1 )
            ),
            array(
                'id' => 'shop_page_title_layout',
                'title' => esc_html__('Layouts', 'foldery'),
                'subtitle' => esc_html__('select a layout for page title', 'foldery'),
                'default' => '5',
                'type' => 'image_select',
                'options' => array(
                    '1' => get_template_directory_uri().'/inc/options/images/pagetitle/pt-s-1.png',
                    '2' => get_template_directory_uri().'/inc/options/images/pagetitle/pt-s-2.png',
                    '3' => get_template_directory_uri().'/inc/options/images/pagetitle/pt-s-3.png',
                    '4' => get_template_directory_uri().'/inc/options/images/pagetitle/pt-s-4.png',
                    '5' => get_template_directory_uri().'/inc/options/images/pagetitle/pt-s-5.png',
                    '6' => get_template_directory_uri().'/inc/options/images/pagetitle/pt-s-6.png'
                ),
                'required' => array( 0 => 'enable_shop_page_title', 1 => '=', 2 => 1 )
            ),
    
            array(
                'id'       => 'shop_page_title_background',
                'type'     => 'background',
                'title'    => esc_html__( 'Background', 'foldery' ),
                'subtitle' => esc_html__( 'Show page title background with image, color, etc.', 'foldery' ),
                'output'   => array('#shop-page-title'),
                'default'   => array(
                    'background-color'=>'',
                    'background-image'=>get_template_directory_uri().'/assets/images/dummy/bg-page-title.jpg',
                    'background-repeat'=>'repeat',
                    'background-size'=>'inherit',
                    'background-attachment'=>'fixed',
                    'background-position'=>'center top'
                ),
                'required' => array( 0 => 'enable_shop_page_title', 1 => '=', 2 => 1 )
            ),
            array(
                'id'             => 'shop_page_title_margin',
                'type'           => 'spacing',
                'output'         => array('#shop-page-title'),
                'mode'           => 'margin',
                'units'          => array('px'),
                'units_extended' => 'false',
                'title'          => esc_html__('Margin Option', 'foldery'),
                'subtitle'       => esc_html__('in pixels, top right bottom left, ex: 10px 10px 10px 10px', 'foldery'),
                'desc'           => esc_html__('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'foldery'),
                'default'            => array(
                    'margin-top'     => '0px', 
                    'margin-right'   => '0px', 
                    'margin-bottom'  => '100px', 
                    'margin-left'    => '0px',
                    'units'          => 'px', 
                ),
                'required' => array( 0 => 'enable_shop_page_title', 1 => '=', 2 => 1 )
            ),
            array(
                'id'             => 'shop_page_title_padding',
                'type'           => 'spacing',
                'output'         => array('#shop-page-title'),
                'mode'           => 'padding',
                'units'          => array('px'),
                'units_extended' => 'false',
                'title'          => esc_html__('Padding Option', 'foldery'),
                'subtitle'       => esc_html__('in pixels, top right bottom left, ex: 10px 10px 10px 10px', 'foldery'),
                'desc'           => esc_html__('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'foldery'),
                'default'            => array(
                    'padding-top'     => '220px', 
                    'padding-right'   => '0px', 
                    'padding-bottom'  => '250px', 
                    'padding-left'    => '0px',
                    'units'          => 'px', 
                ),
                'required' => array( 0 => 'enable_shop_page_title', 1 => '=', 2 => 1 )
            )  
        )
    );
    $this->sections[] = array(
        'title' => esc_html__('Shop page title', 'foldery'),
        'icon' => 'el-icon-credit-card',
        'subsection' => true,
        'fields' => array(
            array(
                'id' => 'shop_page_title_typography',
                'type' => 'typography',
                'title' => esc_html__('Typography', 'foldery'),
                'google' => true,
                'font-backup' => true,
                'all_styles' => true,
                'output'  => array('.shop-page-title #shop-page-title-text h1, .shop-page-sub-title'),
                'units' => 'px',
                'subtitle' => esc_html__('Typography option with title text.', 'foldery'),
                'default' => array(
                    'color' => '#ffffff',
                )
            ),
            array(
                'id' => 'shop_page_title',
                'type' => 'text',
                'title' => esc_html__('Shop Page Title', 'foldery'),
                'subtitle' => esc_html__('Enter the text you want to show in shop page title.', 'foldery'),
                'default' => 'Monaco Shop'
            ),
            array(
                'id' => 'shop_page_sub_title',
                'type' => 'text',
                'title' => esc_html__('Shop Sub Page Title', 'foldery'),
                'subtitle' => esc_html__('Enter the text you want to show in shop page sub title.', 'foldery'),
                'default' => 'Vintage products with good price'
            )
        )
    );
    /* Shop Breadcrumb */
    $this->sections[] = array(
        'icon' => 'el-icon-random',
        'title' => esc_html__('Shop Breadcrumb', 'foldery'),
        'subsection' => true,
        'fields' => array(
            array(
                'id' => 'shop_breacrumb_typography',
                'type' => 'typography',
                'title' => esc_html__('Typography', 'foldery'),
                'google' => true,
                'font-backup' => true,
                'all_styles' => true,
                'output'  => array('.shop-page-title #shop-breadcrumb-text','.shop-page-title #shop-breadcrumb-text a'),
                'units' => 'px',
                'subtitle' => esc_html__('Typography option with title text.', 'foldery'),
                'default' => array(
                    'color' => '#ffffff',
                )
            )
        )
    );
    $this->sections[] = array(
        'title' => esc_html__('Product list layout', 'foldery'),
        'icon' => 'el-icon-list',
        'subsection' => true,
        'fields' => array(
            array(
                'title'         => esc_html__('Product List Columns', 'foldery'),
                'subtitle'      => esc_html__('Choose number of columns in product list.', 'foldery'),
                'id'            => 'product_list_column',
                'type'          => 'slider',
                'default'       => 3,
                'min'           => 2,
                'step'          => 1,
                'max'           => 4,
                'display_value' => 'label',
            ),
            array(
                'title'         => esc_html__('Number product per page', 'foldery'),
                'desc'          => esc_html__('Choose number of product in product list.', 'foldery'),
                'id'            => 'zk_monaco_wc_archive_per_page',
                'type'          => 'slider',
                'default'       => 12,
                'min'           => 3,
                'step'          => 1,
                'max'           => 100,
                'display_value' => 'label',
            ),
            array(
                'id'       => 'zk_monaco_wc_archive_sidebar',
                'type'     => 'switch',
                'title'    => esc_html__('Enable Shop Sidebar', 'foldery'),
                'subtitle' => esc_html__('This option will show sidebar on Archive product page.', 'foldery'),
                'default'  => true,
            ),
        )
    ); 
    $this->sections[] = array(
        'title' => esc_html__('Single Product', 'foldery'),
        'icon' => 'el-icon-list',
        'subsection' => true,
        'fields' => array(
            array(
                'id' => 'zk_monaco_wc_single_sidebar',
                'type' => 'switch',
                'title' => esc_html__('Enable Shop Sidebar', 'foldery'),
                'subtitle' => esc_html__('This option will show sidebar on single product page.', 'foldery'),
                'default' => true,
            ),
            array(
                'title'   => esc_html__('Gallery', 'foldery'),
                'desc'    => esc_html__('Choose style for product image', 'foldery'),
                'id'      => 'zk_monaco_wc_single_gallery',
                'type'      => 'button_set',
                'options'   => array(
                    '1'     => esc_html__('Lightbox','foldery'),
                    '2'     => esc_html__('Slider','foldery'),
                    '3'     => esc_html__('Zoom','foldery'),
                ),
                'default'   => '1'
            ),
        )
    );
}
/**
 * Footer
 *
 * @author Fox
 */
$this->sections[] = array(
    'title' => esc_html__('Footer', 'foldery'),
    'icon' => 'el-icon-credit-card',
    'fields' => array(
        array(
            'id'             => 'footer_wrap_margin',
            'type'           => 'spacing',
            'output'         => array('#footer-wrapper'),
            'mode'           => 'margin',
            'units'          => array('px'),
            'units_extended' => 'false',
            'title'          => esc_html__('Margin Option', 'foldery'),
            'subtitle'       => esc_html__('in pixels, top right bottom left, ex: 10px 10px 10px 10px.', 'foldery'),
            'desc'           => esc_html__('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'foldery'),
            'default'            => array(
                'margin-top'     => '100px', 
                'units'          => 'px', 
            ),
        ),
        array(
            'id'             => 'footer_wrap_padding',
            'type'           => 'spacing',
            'output'         => array('#footer-wrapper'),
            'mode'           => 'padding',
            'units'          => array('px'),
            'units_extended' => 'false',
            'title'          => esc_html__('Padding Option', 'foldery'),
            'subtitle'       => esc_html__('in pixels, top right bottom left, ex: 10px 10px 10px 10px.', 'foldery'),
            'desc'           => esc_html__('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'foldery'),
            'default'            => array(
                'units'          => 'px', 
            ),
        ),
        array( 
            'id'       => 'footer_wrap_top_border',
            'type'     => 'border',
            'title'    => esc_html__('Footer Border Top Option', 'foldery'),
            'subtitle' => esc_html__('Choose the border top style for your footer.', 'foldery'),
            'output'   => array('#footer-wrapper'),
            'right'    => false,
            'bottom'   => false,
            'left'     => false,
            'all'      => false,
            'units'      => array('px'),
            'default'  => array(
                'border-style'  => 'none'
            ),
        ),
        array(
            'id'       => 'footer_wrap_background',
            'type'     => 'background',
            'title'    => esc_html__( 'Background', 'foldery' ),
            'subtitle' => esc_html__( 'footer background with image, color, etc.', 'foldery' ),
            'output'   => array('footer#footer-wrapper'),
            'default'  => array(
                'background-color' => ''
            ),
        ),
        array(
            'id'       => 'footer_wrap_background_overlay',
            'type'     => 'color_rgba',
            'title'    => esc_html__( 'Overlay Background', 'foldery' ),
            'subtitle' => esc_html__( 'footer background overlay color.', 'foldery' ),
            'output'   => array(
                'background-color' => 'footer#footer-wrapper .footer-wrapper-inner:before'
            ),
            'default'  => ""
        ),
    )
);

/* Footer top */
$this->sections[] = array(
    'title' => esc_html__('Footer Top', 'foldery'),
    'icon' => 'el-icon-fork',
    'subsection' => true,
    'fields' => array(
        array(
            'subtitle' => esc_html__('Enable footer top.', 'foldery'),
            'id' => 'enable_footer_top',
            'type' => 'switch',
            'title' => esc_html__('Enable Footer Top', 'foldery'),
            'default' => true,
        ),
        array(
            'id' => 'footer_top_typo',
            'type' => 'typography',
            'title' => esc_html__('Footer Top Typography', 'foldery'),
            'google' => true,
            'font-backup' => true,
            'all_styles' => true,
            'word-spacing' => true,
            'letter-spacing' => true,
            'output'  => array('#cms-footer-top, #cms-footer-top a'),
            'units' => 'px',
            'subtitle' => esc_html__('Typography option with each property can be called individually.', 'foldery'),
            'default' => array(
                'color' => '#ffffff',
                'font-weight' => '',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => '',
                'text-align' => '',
                'letter-spacing' => '',
                'word-spacing' => ''
            ),
            'required' => array( 0 => 'enable_footer_top', 1 => '=', 2 => 1 )
        ),
        array(
            'id'       => 'footer_background',
            'type'     => 'background',
            'title'    => esc_html__( 'Background', 'foldery' ),
            'subtitle' => esc_html__( 'footer top background with image, color, etc.', 'foldery' ),
            'output'   => array('footer #cms-footer-top'),
            'default'  => array(
                'background-color' => '#222222'
            ),
            'required' => array( 0 => 'enable_footer_top', 1 => '=', 2 => 1 )
        ),
        array(
            'id'             => 'footer_margin',
            'type'           => 'spacing',
            'output'         => array('footer #cms-footer-top'),
            'mode'           => 'margin',
            'units'          => array('px'),
            'units_extended' => 'false',
            'title'          => esc_html__('Margin Option', 'foldery'),
            'subtitle'       => esc_html__('in pixels, top right bottom left, ex: 10px 10px 10px 10px', 'foldery'),
            'desc'           => esc_html__('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'foldery'),
            'default'            => array(
                'margin-top'     => '', 
                'margin-right'   => '', 
                'margin-bottom'  => '', 
                'margin-left'    => '',
                'units'          => 'px', 
            ),
            'required' => array( 0 => 'enable_footer_top', 1 => '=', 2 => 1 )
        ),
        array(
            'id'             => 'footer_padding',
            'type'           => 'spacing',
            'output'         => array('footer #cms-footer-top'),
            'mode'           => 'padding',
            'units'          => array('px'),
            'units_extended' => 'false',
            'title'          => esc_html__('Padding Option', 'foldery'),
            'subtitle'       => esc_html__('in pixels, top right bottom left, ex: 10px 10px 10px 10px', 'foldery'),
            'desc'           => esc_html__('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'foldery'),
            'default'            => array(
                'padding-top'     => '100px', 
                'padding-right'   => '', 
                'padding-bottom'  => '', 
                'padding-left'    => '',
                'units'          => 'px', 
            ),
            'required' => array( 0 => 'enable_footer_top', 1 => '=', 2 => 1 )
        ),
        array( 
            'id'       => 'footer_top_border',
            'type'     => 'border',
            'title'    => esc_html__('Footer Top Border Bottom Option', 'foldery'),
            'subtitle' => esc_html__('Only color validation can be done on this field type', 'foldery'),
            'output'   => array('#cms-footer-top > .container:after'),
            'desc'     => esc_html__('Choose the border bottom style for your footer top.', 'foldery'),
            'top'     => 'false',
            'right'     => 'false',
            'left'     => 'false', 
            'default'  => array(
                'border-color'  => '#333', 
                'border-style'  => 'solid',  
                'border-bottom' => '1px', 
            ),
            'required' => array( 0 => 'enable_footer_top', 1 => '=', 2 => 1 )
        ),
        array(
            'id' => 'footer_top_wg_title_typo',
            'type' => 'typography',
            'title' => esc_html__('Footer Top Widget Title Typography', 'foldery'),
            'google' => true,
            'font-backup' => true,
            'all_styles' => true,
            'word-spacing' => true,
            'letter-spacing' => true,
            'output'  => array('#cms-footer-top aside.widget .wg-title'),
            'units' => 'px',
            'subtitle' => esc_html__('Typography option with each property can be called individually.', 'foldery'),
            'default' => array(
                'color' => '#ffffff',
                'font-weight' => '',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '',
                'line-height' => '',
                'text-align' => '',
                'letter-spacing' => '',
                'word-spacing' => ''
            ),
            'required' => array( 0 => 'enable_footer_top', 1 => '=', 2 => 1 )
        ),
    )
);

/* footer botton */
$this->sections[] = array(
    'title' => esc_html__('Footer Bottom', 'foldery'),
    'icon' => 'el-icon-bookmark',
    'subsection' => true,
    'fields' => array(
        array(
            
            'id' => 'footer_bottom_layout',
            'type' => 'select',
            'title' => 'Footer Bottom Layout',
            'subtitle' => 'Choose your footer bottom layout.',
            'options' => array(
                '1' => 'Layout 1',
                '2' => 'Layout 2',
                '3' => 'Layout 3',
                '4' => 'Layout 4',
                '5' => 'Layout 5',
                '6' => 'Layout 6',
                '7' => 'Layout 7',
                '8' => 'Layout 8',
                '13' => 'Special'
            ),
            'default' => '1'
        ),
        array(
            'title' => esc_html__('Footer Bottom Logo', 'foldery'),
            'subtitle' => esc_html__('Select an image file for your footer bottom logo.', 'foldery'),
            'id' => 'footer_bottom_logo',
            'type' => 'media',
            'url' => true,
            'default' => array(
                'url'=>''
            ),
            'required' => array( 0 => 'footer_bottom_layout', 1 => '=', 2 => array('7') )
        ),
        array(
            'id' => 'footer_bottom_typo',
            'type' => 'typography',
            'title' => esc_html__('Footer Bottom Font', 'foldery'),
            'google' => true,
            'font-backup' => true,
            'all_styles' => true,
            'word-spacing' => true,
            'letter-spacing' => true,
            'output'  => array('#cms-footer-bottom, #cms-footer-bottom a'),
            'units' => 'px',
            'subtitle' => esc_html__('Typography option with each property can be called individually.', 'foldery'),
            'default' => array(
                'color' => '#fff',
                'font-weight' => '',
                'font-style' => '',
                'font-family' => '',
                'google' => true,
                'font-size' => '16px',
                'line-height' => '24px',
                'text-align' => '',
                'letter-spacing' => '',
                'word-spacing' => ''
            )
        ),
        array(
            'id'       => 'footer_bottom_background',
            'type'     => 'background',
            'title'    => esc_html__( 'Background', 'foldery' ),
            'subtitle' => esc_html__( 'background with image, color, etc.', 'foldery' ),
            'output'   => array('footer #cms-footer-bottom'),
            'default'   => array(
                'background-color' => '#222222'
            ),
        ),
        array(
            'id'             => 'footer_bottom_margin',
            'type'           => 'spacing',
            'output'         => array('footer #cms-footer-bottom'),
            'mode'           => 'margin',
            'units'          => array('px'),
            'units_extended' => 'false',
            'title'          => esc_html__('Margin Option', 'foldery'),
            'subtitle'       => esc_html__('in pixels, top right bottom left, ex: 10px 10px 10px 10px', 'foldery'),
            'desc'           => esc_html__('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'foldery'),
            'default'            => array(
                'margin-top'     => '', 
                'margin-right'   => '', 
                'margin-bottom'  => '', 
                'margin-left'    => '',
                'units'          => 'px', 
            ),
        ),
        array(
            'id'             => 'footer_bottom_padding',
            'type'           => 'spacing',
            'output'         => array('footer #cms-footer-bottom'),
            'mode'           => 'padding',
            'units'          => array('px'),
            'units_extended' => 'false',
            'title'          => esc_html__('Padding Option', 'foldery'),
            'subtitle'       => esc_html__('in pixels, top right bottom left, ex: 10px 10px 10px 10px', 'foldery'),
            'desc'           => esc_html__('You can enable or disable any piece of this field. Top, Right, Bottom, Left, or Units.', 'foldery'),
            'default'            => array(
                'padding-top'     => '', 
                'padding-right'   => '', 
                'padding-bottom'  => '75px', 
                'padding-left'    => '',
                'units'          => 'px', 
            ),
        ),

        array(
            'id' => 'footer_address',
            'type' => 'textarea',
            'title' => 'Footer Address',
            'subtitle' => esc_html__('This section will hide when have an active widget in "Footer Bottom 1"', 'foldery'),
            'default' => esc_html__('14 Tottenham Court Road, London, England / (102) 3456 789 / info@zooka.io', 'foldery'),
        ),
        array(
            'id' => 'footer_copyright',
            'type' => 'textarea',
            'title' => 'Footer Copyright',
            'subtitle' => esc_html__('This section will hide when have an active widget in "Footer Bottom 2"', 'foldery'),
            'default' => esc_html__('Copyright &copy; 2015  Zooka.io', 'foldery'),
        ),
        array(
            'id' => 'footer_icon',
            'type' => 'switch',
            'title' => esc_html__('Enable Social icon in Footer', 'foldery'),
            'subtitle' => esc_html__('This section will hide when have an active widget in "Footer Bottom 3"', 'foldery'),
            'default' => true,
        ),
        array(
            'id' => 'footer_icon1_title',
            'type' => 'text',
            'title' => 'Social 1 Title',
            'subtitle' => esc_html__('Enter your Social title', 'foldery'),
            'default' => 'Facebook',
            'required' => array( 0 => 'footer_icon', 1 => '=', 2 => 1 )
        ),
        array(
            'id' => 'footer_icon1',
            'type' => 'text',
            'title' => 'Social 1 Icon',
            'subtitle' => sprintf( wp_kses( esc_html__( 'Enter class for get Awesome icon. <a href="%s" target="_blank" >Click here</a> to see what icon you can get.', 'foldery' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( $fontawesome_cheatsheet ) ),
            'default' => 'facebook',
            'required' => array( 0 => 'footer_icon', 1 => '=', 2 => 1 )
        ),
        array(
            'id' => 'footer_icon1_url',
            'type' => 'text',
            'title' => 'Social 1 url',
            'subtitle' => esc_html__('Enter your Social url', 'foldery'),
            'default' => 'http://facebook.com/',
            'required' => array( 0 => 'footer_icon', 1 => '=', 2 => 1 )
        ),

        array(
            'id' => 'footer_icon2_title',
            'type' => 'text',
            'title' => 'Social 2 Title',
            'subtitle' => esc_html__('Enter your Social title', 'foldery'),
            'default' => 'Twitter',
            'required' => array( 0 => 'footer_icon', 1 => '=', 2 => 1 )
        ),
        array(
            'id' => 'footer_icon2',
            'type' => 'text',
            'title' => 'Social 2 Icon',
            'subtitle' => sprintf( wp_kses( esc_html__( 'Enter class for get Awesome icon. <a href="%s" target="_blank" >Click here</a> to see what icon you can get.', 'foldery' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( $fontawesome_cheatsheet ) ),
            'default' => 'twitter',
            'required' => array( 0 => 'footer_icon', 1 => '=', 2 => 1 )
        ),
        array(
            'id' => 'footer_icon2_url',
            'type' => 'text',
            'title' => 'Social 2 url',
            'subtitle' => esc_html__('Enter your Social url', 'foldery'),
            'default' => 'http://twitter.com/',
            'required' => array( 0 => 'footer_icon', 1 => '=', 2 => 1 )
        ),
        array(
            'id' => 'footer_icon3_title',
            'type' => 'text',
            'title' => 'Social 3 Title',
            'subtitle' => esc_html__('Enter your Social title', 'foldery'),
            'default' => 'Instagram',
            'required' => array( 0 => 'footer_icon', 1 => '=', 2 => 1 )
        ),
        array(
            'id' => 'footer_icon3',
            'type' => 'text',
            'title' => 'Social 3 Icon',
            'subtitle' => sprintf( wp_kses( esc_html__( 'Enter class for get Awesome icon. <a href="%s" target="_blank" >Click here</a> to see what icon you can get.', 'foldery' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( $fontawesome_cheatsheet ) ),
            'default' => 'instagram',
            'required' => array( 0 => 'footer_icon', 1 => '=', 2 => 1 )
        ),
        array(
            'id' => 'footer_icon3_url',
            'type' => 'text',
            'title' => 'Social 3 url',
            'subtitle' => esc_html__('Enter your Social url', 'foldery'),
            'default' => 'http://instagram.com/',
            'required' => array( 0 => 'footer_icon', 1 => '=', 2 => 1 )
        ),
        array(
            'id' => 'footer_icon4_title',
            'type' => 'text',
            'title' => 'Social 4 title',
            'subtitle' => esc_html__('Enter your Social title', 'foldery'),
            'default' => 'Behance',
            'required' => array( 0 => 'footer_icon', 1 => '=', 2 => 1 )
        ),
        array(
            'id' => 'footer_icon4',
            'type' => 'text',
            'title' => 'Social 4 Icon',
            'subtitle' => sprintf( wp_kses( esc_html__( 'Enter class for get Awesome icon. <a href="%s" target="_blank" >Click here</a> to see what icon you can get.', 'foldery' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( $fontawesome_cheatsheet ) ),
            'default' => 'behance',
            'required' => array( 0 => 'footer_icon', 1 => '=', 2 => 1 )
        ),
        array(
            'id' => 'footer_icon4_url',
            'type' => 'text',
            'title' => 'Social 4 url',
            'subtitle' => esc_html__('Enter your Social url', 'foldery'),
            'default' => 'https://www.behance.net/',
            'required' => array( 0 => 'footer_icon', 1 => '=', 2 => 1 )
        ),
    )
);
/**
 * Back to TOP
 * 
 * @author Fox
 */
$this->sections[] = array(
    'title' => esc_html__('Back to TOP', 'foldery'),
    'icon' => 'el-icon-circle-arrow-up ',
    'subsection' => true,
    'fields' => array(
        array(
            'subtitle' => esc_html__('enable button back to top.', 'foldery'),
            'id' => 'footer_botton_back_to_top',
            'type' => 'switch',
            'title' => esc_html__('Back To Top', 'foldery'),
            'default' => true,
        )
    )
);
/**
 * Custom CSS
 * 
 * extra css for customer.
 * @author Fox
 */
$this->sections[] = array(
    'title' => esc_html__('Custom CSS', 'foldery'),
    'icon' => 'el-icon-bulb',
    'fields' => array(
        array(
            'id' => 'custom_css',
            'type' => 'ace_editor',
            'title' => esc_html__('CSS Code', 'foldery'),
            'subtitle' => esc_html__('create your css code here.', 'foldery'),
            'mode' => 'css',
            'theme' => 'monokai',
        )
    )
);
/**
 * Animations
 *
 * Animations options for theme. libs css, js.
 * @author Fox
 */
$this->sections[] = array(
    'title' => esc_html__('Animations', 'foldery'),
    'icon' => 'el-icon-magic',
    'fields' => array(
        array(
            'subtitle' => esc_html__('Enable animation mouse scroll...', 'foldery'),
            'id' => 'smoothscroll',
            'type' => 'switch',
            'title' => esc_html__('Smooth Scroll', 'foldery'),
            'default' => false
        ),
    )
);
/**
 * GutenBerg
 * 
 * Supported GutenBerg or Not
 * @author Chinh Duong Manh
 * @since 2.2
 */
$this->sections[] = array(
    'title'  => esc_html__('Gutenberg', 'foldery'),
    'icon'   => 'el-icon-edit',
    'fields' => array(
        array(
            'id'       => 'gutenberg',
            'type'     => 'switch',
            'title'    => esc_html__('Enable Gutenberg Editor', 'foldery'),
            'subtitle' => esc_html__('Make theme support Gutenberg or not', 'foldery'),
            'default'  => false
        )
    )
);
/**
 * Optimal Core
 * 
 * Optimal options for theme. optimal speed
 * @author Fox
 */
$this->sections[] = array(
    'title' => esc_html__('Optimal Core', 'foldery'),
    'icon' => 'el-icon-idea',
    'fields' => array(
        array(
            'subtitle' => esc_html__('no minimize , generate css over time...', 'foldery'),
            'id' => 'dev_mode',
            'type' => 'switch',
            'title' => esc_html__('Dev Mode (not recommended)', 'foldery'),
            'default' => false
        )
    )
);