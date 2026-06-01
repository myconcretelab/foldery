<?php
if (!class_exists('VC_Manager') ) return;
/**
 * Remove Element from EF4 FrameWork
*/
//add_filter('cms-shorcode-list', 'remove_ef4_shortcodes');
function remove_ef4_shortcodes(){
  return array();
}

/**
 * Custom CPT UI
 * Need to do this to add custom post type registered with CPT UI
 * show in list DATA SOURCE of VC POST GRID Element
 * referent link : https://wordpress.org/support/topic/custom-post-type-and-visual-composer-grid-block/#post-6182678
 * and : https://wordpress.org/support/topic/custom-post-type-and-visual-composer-grid-block/page/2#post-6182761
 *
 * @author Chinh Duong Manh
 * @since 1.0.0
 */
if (function_exists('cptui_create_custom_post_types')) {
  remove_action('init', 'cptui_create_custom_post_types', 10);
  add_action('init', 'cptui_create_custom_post_types', 2);
}

/**
 * Remove VC frontend Editor Post Link
 * add_action( 'vc_after_init', 'vc_remove_wp_edit_post_link' );
 *
 * @author Chinh Duong Manh
 * @since 1.0.0
 */
if(!function_exists('vc_remove_wp_edit_post_link')){
    add_action('vc_after_init', 'vc_remove_wp_edit_post_link');
    function vc_remove_wp_edit_post_link()
    {
        foldery_remove_filter('edit_post_link', array(vc_frontend_editor(), 'renderEditButton'));
    }
}


if(!function_exists('vc_remove_frontend_links')){
    add_action( 'vc_after_init', 'vc_remove_frontend_links' );
    function vc_remove_frontend_links() {
        vc_disable_frontend(); // this will disable frontend editor
    }
}


/**
  * Get post type list for VC
*/
if (!function_exists('foldery_get_post_types_for_vc')) {
    function foldery_get_post_types_for_vc()
    {
        $post_types = get_post_types(['public' => true], 'object');
        $excludedPostTypes = array(
            'revision',
            'nav_menu_item',
            'vc_grid_item',
            'page',
            'attachment',
            'custom_css',
            'customize_changeset',
            'oembed_cache',
        );
        $result = [];
        if (!is_array($post_types))
            return $result;
        foreach ($post_types as $post_type) {
            if (!$post_type instanceof WP_Post_Type)
                continue;
            if (in_array($post_type->name, $excludedPostTypes))
                continue;
            $result["{$post_type->label} ({$post_type->name})"] = $post_type->name;
        }
        return $result;
    }
}

/**
 * Change default class name of VC to use Bootstrap 4.x
 *
 * Filter to replace default css class names for vc_row shortcode and vc_column
*/
//add_filter( 'vc_shortcodes_css_class', 'foldery_css_classes_for_vc_row_and_vc_column', 10, 2 );
function foldery_css_classes_for_vc_row_and_vc_column( $class_string, $tag ) {
  if ( $tag == 'vc_row' || $tag == 'vc_row_inner' ) {
    $class_string = str_replace( 'vc_row-fluid', 'row', $class_string ); // This will replace "vc_row-fluid" with "my_row-fluid"
  }
  if ( $tag == 'vc_column' || $tag == 'vc_column_inner' ) {
    $class_string = preg_replace( '/vc_col-lg-(\d{1,2})/', 'col-xl-$1', $class_string ); // This will replace "vc_col-lg-%" with "col-lg-%"
    $class_string = preg_replace( '/vc_col-md-(\d{1,2})/', 'col-lg-$1', $class_string ); // This will replace "vc_col-md-%" with "col-md-%"
    $class_string = preg_replace( '/vc_col-sm-(\d{1,2})/', 'col-md-$1', $class_string ); // This will replace "vc_col-sm-%" with "col-sm-%"
    $class_string = preg_replace( '/vc_col-xs-(\d{1,2})/', 'col-$1', $class_string ); // This will replace "vc_col-sm-%" with "col-sm-%"
  }
  return $class_string; // Important: you should always return modified or original $class_string
}

/**
 * Custom VC shortcode output
 */
add_filter('vc_shortcode_output', 'foldery_vc_shortcode_output', 10, 3);
function foldery_vc_shortcode_output($html = '', $sc_obj = '', $atts = [])
{
    extract($atts);
    //modify shortcode use div as container
    $shortcode_modify = array(
        'vc_section',
        'vc_row',
        'vc_row_inner',
        'vc_column',
        'vc_column_inner'
    );
    $shortcode_name = $sc_obj->getShortcode();
    if (!in_array($shortcode_name, $shortcode_modify))
        return $html;
    //
    $modify = [
        'attrs'       => [], // for add attrs can use string or array
        'before'      => '',
        'after'       => '',
        'first-child' => '',
        'last-child'  => ''
    ];
    switch ($shortcode_name) {
        //case for $shortcode_modify element
        case 'vc_section':
            /* Text Color */
            if (isset($text_color)) $modify['attrs']['style'] = 'color:' . $text_color . ';';
            /* parallax overlay color */
            if (isset($parallax_overlay) && !empty($parallax_overlay)) 
              $modify['first-child'] = '<div class="parallax_overlay" style="background-color:' . esc_attr($parallax_overlay) . '"></div>'; 
            break;
        case 'vc_row':
            /* custom style */
            $styles = [];
            if (isset($text_color) && !empty($text_color)) $styles[] = 'color:' . $text_color;
            if (isset($bg_style) && 'img_parallax' === $bg_style) $styles[] = 'background-size: cover; background-position: center center';

            if(!empty($styles))
                $modify['attrs']['style'] = implode(';', $styles);
            /* parallax overlay color */
            if ( (isset($overlay_bg) && !empty($overlay_bg)) ) 
              $modify['first-child'] = '<div class="parallax_overlay" style="background-color:' . esc_attr($overlay_bg) . '"></div>'; //ex: '<div class="d-none">Row first child</div>';
            $modify['last-child'] = ''; //ex: '<div class="d-none">Row last child</div>';
            $modify['before'] = ''; //ex: '<div class="d-none">Row Before</div>';
            $modify['after'] = ''; //ex: '<div class="d-none">Row after</div>';
            break;
        case 'vc_row_inner':
            /* parallax overlay color */
            if (isset($parallax_overlay) && !empty($parallax_overlay))
                $modify['first-child'] = '<div class="parallax_overlay" style="background-color:' . esc_attr($parallax_overlay) . '"></div>';
            break;
        case 'vc_column':
            if (isset($text_color)) $modify['attrs']['style'] = 'color:' . $text_color . ';';
            /* parallax overlay color */
            if (isset($parallax_overlay) && !empty($parallax_overlay)) $modify['first-child'] = '<div class="parallax_overlay" style="background-color:' . esc_attr($parallax_overlay) . '"></div>';
            $modify['last-child'] = ''; //ex: '<div class="d-none">col last child</div>';
            $modify['before'] = ''; //ex: '<div class="d-none">col Before</div>';
            $modify['after'] = ''; //ex: '<div class="d-none">col after</div>';
            break;
        default:
            return $html;
            break;
    }
    // change VC_SECTION
    $html = str_replace('<section', '<div', $html);
    $html = str_replace('</section>', '</div>', $html);
    //begin modify
    if (!empty($modify['attrs'])) {
        if (is_array($modify['attrs'])) {
            $custom_attr_str = [];
            foreach ($modify['attrs'] as $key => $value) {
                $value = esc_attr($value);
                $custom_attr_str[] = "{$key}=\"{$value}\"";
            }
            $custom_attr_str = join(' ', $custom_attr_str);
        } else
            $custom_attr_str = $modify['attrs'];
        $html = '<div ' . $custom_attr_str . substr($html, 4);
    }
    if (!empty($modify['first-child'])) {
        $html_exp = explode('>', $html);
        $html_exp[1] = $modify['first-child'] . $html_exp[1];
        $html = join('>', $html_exp);
    }
    if (!empty($modify['last-child'])) {
        $html_exp = explode('</div>', $html);
        if (count($html_exp) > 2) {
            for ($index = count($html_exp) - 1; $index > 0; $index--) {
                if (empty(trim($html_exp[$index - 1])))
                    break;
            }
            $html_exp[$index - 1] .= $modify['last-child'];
            $html = join('</div>', $html_exp);
        } else
            $html = substr($html, 0, -6) . $modify['last-child'] . '</div>';
    }
    if (!empty($modify['before']))
        $html = $modify['before'] . $html;
    if (!empty($modify['after']))
        $html = $html . $modify['after'];
    return $html;
}

/**
 * Add custom class from custom param to VC Element
 * https://kb.wpbakery.com/docs/filters/vc_shortcodes_css_class/
 *
 */
add_filter('vc_shortcodes_css_class', 'foldery_vc_shortcodes_css_class', 10, 3);
function foldery_vc_shortcodes_css_class($class_string, $tag, $atts = '')
{
    $custom_class = array();
    extract($atts);
    if ($tag = 'vc_section' || $tag == 'vc_row' || $tag == 'vc_row_inner') {
        if (isset($row_priority)) {
            $custom_class[] = $row_priority;
        }
        if (isset($row_col_space)) {
            $custom_class[] = $row_col_space;
        }
        if (isset($parallax_position)) {
            $custom_class[] = $parallax_position;
        }
    }
    if ($tag == 'vc_column' || $tag == 'vc_column_inner') {
        if (isset($col_priority)) {
            $custom_class[] = $col_priority;
        }
        if (isset($col_space)) {
            $custom_class[] = $col_space;
        }
    }
    /* add custom loading delay time for VC Grid */
    if ($tag = 'vc_basic_grid' || $tag = 'vc_masonry_grid' || $tag = 'vc_media_grid' || $tag = 'vc_masonry_media_grid') {
        if (isset($element_width) && $element_width) {
            $custom_class[] = 'zk-iw-' . $element_width;
        }
        if (isset($item) && $item) {
            $custom_class[] = $item;
        }

        if (isset($vcbg_hover) && $vcbg_hover) {
            $custom_class[] = $vcbg_hover;
        }

        if (isset($vcbg_space) && $vcbg_space) {
            $custom_class[] = 'vc_gitem-row-' . $vcbg_space;
        }

        if (isset($delay_time) && $delay_time) {
            $custom_class[] = 'zk-loading-delay-' . $delay_time;
        }

        if (isset($pagination_top_space) && $pagination_top_space) {
            $custom_class[] = 'pagination-top-' . $pagination_top_space;
        }
    }

    $class_string .= ' ' . join(' ', $custom_class);
    return $class_string;
}

/*
 * Grid Settings 
*/
function foldery_grid_settings(array $args = array())
{
    extract($arr = array_merge(array(
        'group'      => '',
        'param_name' => '',
        'value'      => ''
    ), $args));
    $raw_params = array(
        array(
            'type'             => 'dropdown',
            'heading'          => esc_html__('Small Screen', 'foldery'),
            'param_name'       => 'col_sm',
            'edit_field_class' => 'vc_col-sm-3 vc_carousel_item',
            'value'            => array(1, 2, 3, 4, 6, 12),
            'std'              => 1,
            'dependency'       => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'            => $group
        ),
        array(
            'type'             => 'dropdown',
            'heading'          => esc_html__('Medium Screen', 'foldery'),
            'param_name'       => 'col_md',
            'edit_field_class' => 'vc_col-sm-3 vc_carousel_item',
            'value'            => array(1, 2, 3, 4, 6, 12),
            'std'              => 2,
            'dependency'       => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'            => $group
        ),
        array(
            'type'             => 'dropdown',
            'heading'          => esc_html__('Large Screen', 'foldery'),
            'param_name'       => 'col_lg',
            'edit_field_class' => 'vc_col-sm-3 vc_carousel_item',
            'value'            => array(1, 2, 3, 4, 6, 12),
            'std'              => 3,
            'dependency'       => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'            => $group
        ),
        array(
            'type'             => 'dropdown',
            'heading'          => esc_html__('Extra Large Screen', 'foldery'),
            'param_name'       => 'col_xl',
            'edit_field_class' => 'vc_col-sm-3 vc_carousel_item',
            'value'            => array(1, 2, 3, 4, 6, 12),
            'std'              => 4,
            'dependency'       => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'            => $group
        )
    );
    $params = [];
    foreach ($raw_params as $param) {
        if (!empty($param['dependency']) && empty($param['dependency']['element']))
            unset($param['dependency']);
        $params[] = $param;
    }
    return $params;
}

/* OWL Carousel Setting
 * All option will use in element use OWL Carousel Libs
*/
function foldery_owl_settings(array $args = array())
{
    extract($arr = array_merge(array(
        'group'      => '',
        'param_name' => '',
        'value'      => ''
    ), $args));
    $raw_params = array(
        array(
            'type'             => 'dropdown',
            'heading'          => esc_html__('Small Screen', 'foldery'),
            'param_name'       => 'owl_sm_items',
            'edit_field_class' => 'vc_col-sm-3 vc_carousel_item',
            'value'            => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12),
            'std'              => 1,
            'dependency'       => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'            => $group
        ),
        array(
            'type'             => 'dropdown',
            'heading'          => esc_html__('Medium Screen', 'foldery'),
            'param_name'       => 'owl_md_items',
            'edit_field_class' => 'vc_col-sm-3 vc_carousel_item',
            'value'            => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12),
            'std'              => 2,
            'dependency'       => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'            => $group
        ),
        array(
            'type'             => 'dropdown',
            'heading'          => esc_html__('Large Screen', 'foldery'),
            'param_name'       => 'owl_lg_items',
            'edit_field_class' => 'vc_col-sm-3 vc_carousel_item',
            'value'            => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12),
            'std'              => 3,
            'dependency'       => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'            => $group
        ),
        array(
            'type'             => 'dropdown',
            'heading'          => esc_html__('Extra Large Screen', 'foldery'),
            'param_name'       => 'owl_xl_items',
            'edit_field_class' => 'vc_col-sm-3 vc_carousel_item',
            'value'            => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12),
            'std'              => 4,
            'dependency'       => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'            => $group
        ),
        array(
            'type'        => 'dropdown',
            'heading'     => esc_html__('Number Row', 'foldery'),
            'description' => esc_html__('Choose number of row you want to show.', 'foldery'),
            'param_name'  => 'number_row',
            'value'       => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10),
            'std'         => 1,
            'dependency'  => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'       => $group,
        ),

        array(
            'type'             => 'checkbox',
            'heading'          => esc_html__('Loop Items', 'foldery'),
            'param_name'       => 'loop',
            'std'              => 'false',
            'edit_field_class' => 'vc_col-sm-3 vc_carousel_item',
            'dependency'       => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'            => $group
        ),
        array(
            'type'             => 'checkbox',
            'heading'          => esc_html__('Center', 'foldery'),
            'param_name'       => 'center',
            'std'              => 'false',
            'edit_field_class' => 'vc_col-sm-3 vc_carousel_item',
            'dependency'       => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'            => $group
        ),
        array(
            'type'             => 'checkbox',
            'heading'          => esc_html__('Auto Width', 'foldery'),
            'param_name'       => 'autowidth',
            'std'              => 'false',
            'edit_field_class' => 'vc_col-sm-3 vc_carousel_item',
            'dependency'       => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'            => $group
        ),
        array(
            'type'             => 'checkbox',
            'heading'          => esc_html__('Auto Height', 'foldery'),
            'param_name'       => 'autoheight',
            'std'              => 'true',
            'edit_field_class' => 'vc_col-sm-3 vc_carousel_item',
            'dependency'       => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'            => $group
        ),

        array(
            'type'             => 'textfield',
            'heading'          => esc_html__('Items Space', 'foldery'),
            'param_name'       => 'margin',
            'value'            => 30,
            'edit_field_class' => 'vc_col-sm-4 vc_carousel_item',
            'dependency'       => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'            => $group
        ),
        array(
            'type'             => 'textfield',
            'heading'          => esc_html__('Stage Padding', 'foldery'),
            'param_name'       => 'stagepadding',
            'value'            => '0',
            'edit_field_class' => 'vc_col-sm-4 vc_carousel_item',
            'dependency'       => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'            => $group
        ),

        array(
            'type'             => 'textfield',
            'heading'          => esc_html__('Start Position', 'foldery'),
            'param_name'       => 'startposition',
            'value'            => '0',
            'edit_field_class' => 'vc_col-sm-4 vc_carousel_item',
            'dependency'       => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'            => $group
        ),

        array(
            'type'       => 'checkbox',
            'param_name' => 'nav',
            'value'      => array(
                esc_html__('Show Next/Preview button', 'foldery') => 'true'
            ),
            'std'        => 'false',
            'dependency' => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'      => $group
        ),
        array(
            'type'             => 'dropdown',
            'heading'          => esc_html__('Nav Style', 'foldery'),
            'param_name'       => 'nav_style',
            'value'            => foldery_carousel_nav_style(),
            'std'              => '',
            'dependency'       => array(
                'element' => 'nav',
                'value'   => 'true',
            ),
            'edit_field_class' => 'vc_col-sm-6 vc_carousel_item',
            'group'            => $group
        ),
        array(
            'type'             => 'dropdown',
            'heading'          => esc_html__('Nav Position', 'foldery'),
            'param_name'       => 'nav_pos',
            'value'            => foldery_carousel_nav_pos(),
            'std'              => '',
            'dependency'       => array(
                'element'            => 'nav_style',
                'value_not_equal_to' => array('1'),
            ),
            'edit_field_class' => 'vc_col-sm-6 vc_carousel_item',
            'group'            => $group
        ),
        array(
            'type'       => 'checkbox',
            'value'      => array(
                esc_html__('Show Dots', 'foldery') => 'true'
            ),
            'param_name' => 'dots',
            'std'        => 'true',
            'dependency' => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'      => $group
        ),
        array(
            'type'             => 'dropdown',
            'heading'          => esc_html__('Dots Style', 'foldery'),
            'param_name'       => 'dot_style',
            'value'            => foldery_carousel_dots_style(),
            'std'              => '',
            'dependency'       => array(
                'element' => 'dots',
                'value'   => 'true',
            ),
            'edit_field_class' => 'vc_col-sm-6 vc_carousel_item',
            'dependency'       => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'            => $group
        ),
        array(
            'type'             => 'dropdown',
            'heading'          => esc_html__('Dots Position', 'foldery'),
            'param_name'       => 'dot_pos',
            'value'            => foldery_carousel_dot_pos(),
            'std'              => '',
            'dependency'       => array(
                'element' => 'dots',
                'value'   => array('true'),
            ),
            'edit_field_class' => 'vc_col-sm-6 vc_carousel_item',
            'group'            => $group
        ),

        array(
            'type'       => 'checkbox',
            'value'      => array(
                esc_html__('Auto Play', 'foldery') => 'true'
            ),
            'param_name' => 'autoplay',
            'std'        => 'true',
            'dependency' => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'      => $group
        ),
        array(
            'type'             => 'textfield',
            'heading'          => esc_html__('Smart Speed', 'foldery'),
            'param_name'       => 'smartspeed',
            'value'            => '250',
            'description'      => esc_html__('Speed scroll of each item', 'foldery'),
            'edit_field_class' => 'vc_col-sm-4 vc_carousel_item',
            'dependency'       => array(
                'element' => 'autoplay',
                'value'   => 'true',
            ),
            'group'            => $group
        ),
        array(
            'type'             => 'textfield',
            'heading'          => esc_html__('Auto Play TimeOut', 'foldery'),
            'param_name'       => 'autoplaytimeout',
            'value'            => '5000',
            'dependency'       => array(
                'element' => 'autoplay',
                'value'   => 'true',
            ),
            'edit_field_class' => 'vc_col-sm-4 vc_carousel_item',
            'group'            => $group
        ),
        array(
            'type'             => 'checkbox',
            'heading'          => esc_html__('Pause on mouse hover', 'foldery'),
            'param_name'       => 'autoplayhoverpause',
            'std'              => 'true',
            'dependency'       => array(
                'element' => 'autoplay',
                'value'   => 'true',
            ),
            'edit_field_class' => 'vc_col-sm-4 vc_carousel_item',
            'group'            => $group
        ),
        array(
            'type'             => 'animation_style',
            'class'            => '',
            'heading'          => esc_html__('Animation In', 'foldery'),
            'param_name'       => 'owlanimation_in',
            'std'              => '',
            'settings'         => array(
                'type' => array(
                    'in'
                ),
            ),
            'edit_field_class' => 'vc_col-sm-6 vc_carousel_item',
            'dependency'       => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'            => $group
        ),
        array(
            'type'             => 'animation_style',
            'class'            => '',
            'heading'          => esc_html__('Animation Out', 'foldery'),
            'param_name'       => 'owlanimation_out',
            'std'              => '',
            'settings'         => array(
                'type' => array(
                    'out'
                ),
            ),
            'edit_field_class' => 'vc_col-sm-6 vc_carousel_item',
            'dependency'       => array(
                'element' => $param_name,
                'value'   => $value,
            ),
            'group'            => $group
        ),
    );
    $params = [];
    foreach ($raw_params as $param) {
        if (!empty($param['dependency']) && empty($param['dependency']['element']))
            unset($param['dependency']);
        $params[] = $param;
    }
    return $params;
}

/**
 * OWL Nav & Dots
 * Nav Position foldery_carousel_nav_pos(),
 * Nav Style foldery_carousel_nav_style(),
 * Dot style foldery_carousel_dots_style()
 */
function foldery_carousel_nav_pos()
{
    $carousel_nav_pos = array(
        esc_html__('Default', 'foldery')          => '',
        esc_html__('Vertical Inside', 'foldery')  => 'nav-vertical inside',
        esc_html__('Vertical Outside', 'foldery') => 'nav-vertical outside',
    );
    return $carousel_nav_pos;
}

function foldery_carousel_nav_style()
{
    $carousel_nav_style = array(
        esc_html__('Default', 'foldery')     => '',
        esc_html__('Dots In Nav', 'foldery') => '1',
    );
    return $carousel_nav_style;
}

function foldery_carousel_dots_style()
{
    $carousel_dots_style = array(
        esc_html__('Default', 'foldery')   => '',
        esc_html__('Thumbnail', 'foldery') => 'dots-thumbnail',
        esc_html__('Progress', 'foldery')  => 'dots-progress',
    );
    return $carousel_dots_style;
}

function foldery_carousel_dot_pos()
{
    return array(
        esc_html__('Default', 'foldery') => '',
        esc_html__('Top', 'foldery')     => '1',
    );
}

function foldery_owl_preload($layout_type)
{
    if ($layout_type === 'carousel') echo '<div class="owl-preload"></div>';
}

function foldery_owl_nav($layout_type, $nav_style, $nav_pos)
{
    if ($layout_type === 'carousel') :
        if ($nav_style !== '1'): ?>
            <div class="<?php echo trim(implode(' ', array('owl-nav', $nav_pos))); ?>"></div>
        <?php endif;
    endif;
}

function foldery_owl_dots($layout_type, $dot_style, $dot_pos)
{
    if ($layout_type === 'carousel') :
        if ($dot_pos !== '1'): ?>
            <div class="<?php echo trim(implode(' ', array('owl-dots', $dot_style))); ?>"></div>
        <?php endif;
    endif;
}

function foldery_owl_dots_in_nav($layout_type, $nav_style)
{
    if ($layout_type === 'carousel' && $nav_style === '1') :
        ?>
        <div class="owl-nav-wrap">
            <div class="owl-dots-wrap"></div>
        </div>
    <?php endif;
}

function foldery_owl_dots_top($layout_type, $dot_pos, $dot_style)
{
    if ($layout_type === 'carousel' && $dot_pos === '1') echo '<div class="owl-dots ' . $dot_style . '"></div>';
}

/* Call OWL Settings */
function foldery_owl_call_settings($atts)
{
    extract($atts);
    if ($layout_type !== 'carousel') return;
    wp_enqueue_script('vc_pageable_owl-carousel');
    wp_enqueue_script('zk-owlcarousel');
    wp_enqueue_style('vc_pageable_owl-carousel-css');
    /* Carousel Settings */
    $icon_prev = is_rtl() ? 'right' : 'left';
    $icon_next = is_rtl() ? 'left' : 'right';

    $navContainer = '.' . $el_id . ' .owl-nav';
    $dotsContainer = '.' . $el_id . ' .owl-dots';

    $nav_icon = array('<i class="fa fa-angle-' . $icon_prev . '" data-title="' . esc_html__('Prev', 'foldery') . '"></i>', '<i class="fa fa-angle-' . $icon_next . '" data-title="' . esc_html__('Next', 'foldery') . '"></i>');
    $rtl = is_rtl() ? true : false;
    $dotsData = false;

    /* Dots Style */
    if ($dot_style === 'dots-thumbnail') {
        $dotsData = true;
    }
    global $cms_carousel;
    $cms_carousel[$el_id] = array(
        'rtl'                => $rtl,
        'margin'             => (int)$margin,
        'loop'               => $loop,
        'center'             => $center,
        'stagePadding'       => (int)$stagepadding,
        'autoWidth'          => $autowidth,
        'startPosition'      => (int)$startposition,
        'nav'                => $nav,
        'navContainer'       => $navContainer,
        'navText'            => $nav_icon,
        'dots'               => $dots,
        'dotsContainer'      => $dotsContainer,
        'dotsData'           => $dotsData,
        'autoplay'           => $autoplay,
        'autoplayTimeout'    => (int)$autoplaytimeout,
        'autoplayHoverPause' => $autoplayhoverpause,
        'smartSpeed'         => (int)$smartspeed,
        'autoHeight'         => $autoheight,
        'animateIn'          => $owlanimation_in,
        'animateOut'         => $owlanimation_out,
        'responsiveClass'    => true,
        'slideBy'            => 'page',
        'responsive'         => array(
            0    => array(
                'items' => (int)$owl_sm_items,
            ),
            768  => array(
                'items' => (int)$owl_md_items,
            ),
            992  => array(
                'items' => (int)$owl_lg_items,
            ),
            1200 => array(
                'items' => (int)$owl_xl_items,
            )
        )
    );
    wp_localize_script('vc_pageable_owl-carousel', 'cmscarousel', $cms_carousel);
}

/* Call Masonry Settings */
function foldery_masonry_call_settings($atts)
{
    extract($atts);
    if ($layout_type !== 'masonry') return;
    wp_enqueue_script('vc_masonry');
}

/**
 * Icon font libs
 *
 * Add default VC Icon
 * add new icon from theme
 *
 * Themify Icon
 *
 * @author Chinh Duong Manh
 * @since 1.0.0
 */
function foldery_icon_libs($args = array())
{
    $args = wp_parse_args($args, array(
        'group'        => esc_html__('Icon', 'foldery'),
        'field_prefix' => 'i_',
        'dependency'   => 'add_icon'
    ));
    extract($args);
    require_once vc_path_dir('CONFIG_DIR', 'content/vc-icon-element.php');
    /**
     * @source
     * vc_map_integrate_shortcode( $shortcode, $field_prefix = '', $group_prefix = '', $change_fields = null, $dependency = null )
     **/
    $icons_params = vc_map_integrate_shortcode(vc_icon_element_params(), $field_prefix, $group, array(
        'include_only_regex' => '/^(type|icon_\w*)/',
        // we need only type, icon_fontawesome, icon_blabla..., NOT color and etc
    ), array(
        'element' => $dependency,
        'value'   => 'true',
    ));

    // populate integrated vc_icons params.
    if (is_array($icons_params) && !empty($icons_params)) {
        foreach ($icons_params as $key => $param) {
            if (is_array($param) && !empty($param)) {
                if ($field_prefix . 'type' === $param['param_name']) {
                    /* append themeframe icon to dropdown 
                     * use: 
                     * $icons_params[ $key ]['value'][ esc_html__( 'Themify Icon', 'foldery' ) ] = 'themify';
                     * 
                    */
                    $icons_params[$key]['value'][esc_html__('Themify Icon', 'foldery')] = 'themify';
                    /* Change default font icon
                     * $icons_params[ $key ]['std'] = 'fontawesome';
                    */

                }
                if (isset($param['admin_label'])) {
                    /*remove admin label*/
                    unset($icons_params[$key]['admin_label']);
                }
            }
        }
    }
    return $icons_params;
}

function foldery_icon_libs_icon($args = array())
{
    $args = wp_parse_args($args, array(
        'group'        => esc_html__('Icon', 'foldery'),
        'field_prefix' => 'i_',
    ));
    extract($args);
    return array(
        /* Theme Icons */
        array(
            'type'        => 'iconpicker',
            'heading'     => esc_html__('Icon', 'foldery'),
            'param_name'  => $field_prefix . 'icon_themify',
            'value'       => 'ti-arrow-up',
            'settings'    => array(
                'emptyIcon'    => false,
                'type'         => 'themify',
                'iconsPerPage' => 100,
            ),
            'dependency'  => array(
                'element' => $field_prefix . 'type',
                'value'   => 'themify',
            ),
            'description' => esc_html__('Select icon from library.', 'foldery'),
            'group'       => $group
        )
    );
}

/**
 * Register icons for Visual Composer
 */
function foldery_vc_icon_fonts_register()
{
    wp_register_style('font-themify', get_template_directory_uri() . '/vc_customs/themify-icons/font-themify.min.css', array(), wp_get_theme()->get('Version'));
}

add_action('wp_enqueue_scripts', 'foldery_vc_icon_fonts_register');
add_action('admin_enqueue_scripts', 'foldery_vc_icon_fonts_register');

/**
 * Enqueues icons for Visual Composer
 */
function foldery_vc_icon_fonts_enqueue()
{
    wp_enqueue_style('font-themify');
}

add_action('vc_backend_editor_enqueue_js_css', 'foldery_vc_icon_fonts_enqueue');
add_action('vc_frontend_editor_enqueue_js_css', 'foldery_vc_icon_fonts_enqueue');

/**
 * Call icons for Visual Composer
 */
add_action('vc_enqueue_font_icon_element', 'foldery_vc_icon_font');
function foldery_vc_icon_font($font)
{
    switch ($font) {
        case 'themify':
            wp_enqueue_style('font-themify');
            break;
    }
}

/* Load new icon font */
foldery_require_folder('vc_customs/themify-icons', get_template_directory());


function foldery_btn_types()
{
    return array(
        esc_html__('Default', 'foldery')     => 'btn',
        esc_html__('Primary', 'foldery')     => 'btn-primary',
        esc_html__('Default Alt', 'foldery') => 'btn btn-alt',
        esc_html__('Primary Alt', 'foldery') => 'btn-primary btn-alt',
        esc_html__('White', 'foldery')       => 'btn btn-white',
        esc_html__('Alt White', 'foldery')   => 'btn btn-white btn-alt',
        esc_html__('Simple Link', 'foldery') => 'simple',
    );
}

function foldery_btn_size()
{
    return array(
        esc_html__('Default', 'foldery')     => '',
        esc_html__('Small', 'foldery')       => 'btn-sm',
        esc_html__('Medium', 'foldery')      => 'btn-md',
        esc_html__('Large', 'foldery')       => 'btn-lg',
        esc_html__('Extra Large', 'foldery') => 'btn-xl',
    );
}

/**
 * List of thumbnails size
 * @since 1.0.0
 * @author Chinh Duong Manh
 */
function foldery_thumbnail_sizes()
{
    return array(
        esc_html__('Thumbnail', 'foldery')      => 'thumbnail',
        esc_html__('Medium', 'foldery')         => 'medium',
        esc_html__('Large', 'foldery')          => 'large',
        esc_html__('Post Thumbnail', 'foldery') => 'post-thumbnail',
        esc_html__('Full', 'foldery')           => 'full',
        esc_html__('Custom', 'foldery')         => 'custom',
    );
}