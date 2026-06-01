<?php

/**
 * Auto create .css file from Theme Options
 * @author Fox
 * @version 1.0.0
 */

class Foldery_StaticCss
{

    public $scss;
    
    function __construct()
    {
        if(!class_exists('scssc')) return;
        global $smof_data;
        
        /* scss */
        $this->scss = new scssc();
        /* set paths scss */
        $this->scss->setImportPaths(get_template_directory() . '/assets/scss/');
        /* generate css over time */
        if (isset($smof_data['dev_mode']) && $smof_data['dev_mode'] === '1') {
            $this->generate_file();
        } else {
            /* save option generate css */
            add_action("redux/options/smof_data/saved", array(
                $this,
                'generate_file'
            ));
        }
    }

    /**
     * generate css file.
     *
     * @since 1.0.0
     */
    public function generate_file()
    {
        global $smof_data, $wp_filesystem, $woocommerce;

        if(!defined('FS_CHMOD_FILE'))
            define('FS_CHMOD_FILE',0644);
        
        if (! empty($smof_data) && ! empty($wp_filesystem)) {

            $options_scss = get_template_directory() . '/assets/scss/options.scss';
            
            /* delete files options.scss */
            $wp_filesystem->delete($options_scss);
            
            /* write options to scss file */
            $wp_filesystem->put_contents($options_scss, $this->css_render(), FS_CHMOD_FILE); // Save it
            
            /* minimize CSS styles */
            $this->scss->setFormatter('scss_formatter_compressed');
            
            /* compile scss to css */
            $css = $this->scss_render();
            
            $file = "static.css";
            
            if(isset($smof_data['presets_color']) && !empty($smof_data['presets_color']) && $smof_data['presets_color']){
                $file = "presets-".$smof_data['presets_color'].".css";
            }
            
            $file = get_template_directory() . '/assets/css/' . $file;
            
            /* delete files static.css */
            $wp_filesystem->delete($file);
            
            /* write static.css file */
            $wp_filesystem->put_contents($file, $css, FS_CHMOD_FILE); // Save it

             /* write woocommerce.css file */
            if($woocommerce){
                /* compile woo scss to css */
                $woo_css = $this->woo_scss_render();
                
                $woo_file = get_template_directory() . '/assets/css/woocommerce.css';
                /* delete files static.css */
                $wp_filesystem->delete($woo_file);
                /* write woocommerce.css file */
                $wp_filesystem->put_contents($woo_file, $woo_css, FS_CHMOD_FILE); // Save it
            }
        }
    }
    
    /**
     * scss compile
     * 
     * @since 1.0.0
     * @return string
     */
    public function scss_render(){
        /* compile scss to css */
        return $this->scss->compile('@import "master.scss"');
    }
    public function woo_scss_render(){
        global $woocommerce;
        /* compile scss to css */
        if($woocommerce){
            return $this->scss->compile('@import "woocommerce.scss"');
        }
    }
    
    /**
     * main css
     *
     * @since 1.0.0
     * @return string
     */
    public function css_render()
    {
        global $smof_data, $foldery_base;
        
        ob_start();
        
        /* google fonts. */
        if(isset($smof_data['google-font-1'])){
            $foldery_base->setGoogleFont($smof_data['google-font-1'], $smof_data['google-font-selector-1']);
        }
        if(isset($smof_data['google-font-2'])){
            $foldery_base->setGoogleFont($smof_data['google-font-2'], $smof_data['google-font-selector-2']);
        }
        /* local fonts. */
        if(isset($smof_data['local-fonts-1'])){
            $foldery_base->setFontFace($smof_data['local-fonts-1'], $smof_data['local-fonts-selector-1']);
        }
        if(isset($smof_data['local-fonts-2'])){
            $foldery_base->setFontFace($smof_data['local-fonts-2'], $smof_data['local-fonts-selector-2']);
        }
        /* forward options to scss. */
        /* Header */
        if(!empty($smof_data['header_width'])){
            echo '$header_width:'.esc_attr($smof_data['header_width']).';';
        }
        /* Get menu first level color (Default Header), since V1.0.3 */
        
        if(!empty($smof_data['menu_first_level_typography'])){
            echo '$menu_first_level_typography_font_size:'.esc_attr($smof_data['menu_first_level_typography']['font-size']).';';
        }
        if(!empty($smof_data['menu_first_level_color'])){
            echo '$menu_first_level_color:'.esc_attr($smof_data['menu_first_level_color']['regular']).';';
        }
        if(!empty($smof_data['menu_first_level_color'])){
            echo '$menu_first_level_color_hover:'.esc_attr($smof_data['menu_first_level_color']['hover']).';';
        }
        if(!empty($smof_data['menu_first_level_color'])){
            echo '$menu_first_level_color_active:'.esc_attr($smof_data['menu_first_level_color']['active']).';';
        }
        if(!empty($smof_data['menu_first_level_color'])){
            echo '$menu_first_level_color_visited:'.esc_attr($smof_data['menu_first_level_color']['active']).';';
        }

        /* Get menu first level color (Sticky Header), since V1.0.3 */
        if(!empty($smof_data['sticky_menu_first_level_color'])){
            echo '$sticky_menu_first_level_color:'.esc_attr($smof_data['sticky_menu_first_level_color']['regular']).';';
        }
        if(!empty($smof_data['sticky_menu_first_level_color'])){
            echo '$sticky_menu_first_level_color_hover:'.esc_attr($smof_data['sticky_menu_first_level_color']['hover']).';';
        }
        if(!empty($smof_data['sticky_menu_first_level_color'])){
            echo '$sticky_menu_first_level_color_active:'.esc_attr($smof_data['sticky_menu_first_level_color']['active']).';';
        }
        if(!empty($smof_data['sticky_menu_first_level_color'])){
            echo '$sticky_menu_first_level_color_visited:'.esc_attr($smof_data['sticky_menu_first_level_color']['active']).';';
        }

        /* Get Dropdown menu config (Applied for Default/ Fixed / Sticky Header), since V1.0.3 */
        if(!empty($smof_data['menu_dropdown_background'])){
            echo '$menu_dropdown_background_color:'.esc_attr($smof_data['menu_dropdown_background']['background-color']).';';
        }
        if(!empty($smof_data['menu_dropdown_typography'])){
            echo '$menu_dropdown_typography_font_size:'.esc_attr($smof_data['menu_dropdown_typography']['font-size']).';';
        }
        
        if(!empty($smof_data['menu_dropdown_color'])){
            echo '$menu_dropdown_color:'.esc_attr($smof_data['menu_dropdown_color']['regular']).';';
        }
        if(!empty($smof_data['menu_dropdown_color'])){
            echo '$menu_dropdown_color_hover:'.esc_attr($smof_data['menu_dropdown_color']['hover']).';';
        }
        if(!empty($smof_data['menu_dropdown_color'])){
            echo '$menu_dropdown_color_active:'.esc_attr($smof_data['menu_dropdown_color']['active']).';';
        }

        /* Get Mobile menu config (Applied for menu on Mobile), since V1.0.3 */
        if(!empty($smof_data['menu_mobile_background'])){
            echo '$menu_mobile_background_color:'.esc_attr($smof_data['menu_mobile_background']['background-color']).';';
        }

        if(!empty($smof_data['menu_mobile_color'])){
            echo '$menu_mobile_color:'.esc_attr($smof_data['menu_mobile_color']['regular']).';';
        }
        if(!empty($smof_data['menu_mobile_color'])){
            echo '$menu_mobile_color_hover:'.esc_attr($smof_data['menu_mobile_color']['hover']).';';
        }
        if(!empty($smof_data['menu_mobile_color'])){
            echo '$menu_mobile_color_active:'.esc_attr($smof_data['menu_mobile_color']['active']).';';
        }
        if(!empty($smof_data['menu_mobile_hover_background'])){
            echo '$menu_mobile_hover_background_color:'.esc_attr($smof_data['menu_mobile_hover_background']['background-color']).';';
        }
        

        /* Preset Color */    
        if(!empty($smof_data['primary_color'])){
            echo '$primary_color:'.esc_attr($smof_data['primary_color']).';';
        }
        if(!empty($smof_data['secondary_color'])){
            echo '$secondary_color:'.esc_attr($smof_data['secondary_color']).';';
        }
        if(!empty($smof_data['link_color'])){
            //echo '$link_color:'.esc_attr($smof_data['link_color']).';';
        }
        if(!empty($smof_data['link_color_hover'])){
            //echo '$link_color_hover:'.esc_attr($smof_data['link_color_hover']).';';
        }
        return ob_get_clean();
    }
}

new Foldery_StaticCss();