<?php

class Foldery_Base
{
    /**
     * Page title
     * 
     * @since 1.0.0
     */
    public function getPageTitle(){
        if ( is_home() ) {
            single_post_title();
            return;
        }
        if ( is_archive() ) {
            the_archive_title();
            return;
        }
        if ( is_search() ) {
            printf( esc_html__( 'Search Results for: %s', 'foldery' ), get_search_query() );
            return;
        }
        if ( is_404() ) {
            esc_html_e( 'Page not found', 'foldery' );
            return;
        }
        the_title();
    }

    public function getBreadCrumb(){
        echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'foldery' ) . '</a>';
        if ( is_singular() ) {
            echo ' / <span>' . esc_html( get_the_title() ) . '</span>';
        } elseif ( is_archive() ) {
            echo ' / <span>' . wp_kses_post( get_the_archive_title() ) . '</span>';
        } elseif ( is_search() ) {
            echo ' / <span>' . esc_html( get_search_query() ) . '</span>';
        }
    }

    public function getShortcodeFromContent($tag, $content = ''){
        if ( empty( $tag ) || empty( $content ) ) {
            return false;
        }

        $pattern = get_shortcode_regex( array( $tag ) );
        if ( preg_match( '/' . $pattern . '/s', $content, $matches ) ) {
            return $matches[0];
        }

        return false;
    }

    
    /**
     * Get list name local fonts.
     * 
     * @return multitype:unknown Ambigous <string, mixed>
     * @since 1.0.0
     */
    public static function getListLocalFontsName(){
        
        /* array fonts. */
        $localfonts = array();
        
        /* folder fonts. */
        $font_path = get_template_directory() . "/assets/fonts";
        
        if (!$handle = opendir($font_path)) {
        } else {
            while (false !== ($file = readdir($handle))) {
                if (strpos($file, ".ttf") !== false || strpos($file, ".eot") !== false || strpos($file, ".svg") !== false || strpos($file, ".woff") !== false) {
                    $file = str_replace(array('.ttf', '.eot', '.svg', '.woff'), '', $file);
                    $localfonts[$file] = $file;
                }
            }
        }
        closedir($handle);
        
        return $localfonts;
    }
    
    public static function setFontFace($name = '' , $selecter = ''){
        
        $font_part = get_template_directory_uri()."/assets/fonts/".esc_attr($name);
        
        /* load font files. */
        if($name){
            echo "@font-face {".
                         "font-family: '".esc_attr($name)."';".
                         "src: url('$font_part.eot');"./* IE9 Compat Modes */
                         "src:". 
                             "url('$font_part.eot?#iefix') format('embedded-opentype'),"./* IE6-IE8 */
                             "url('$font_part.woff') format('woff'),"./* Pretty Modern Browsers */
                             "url('$font_part.ttf') format('truetype'),"./* Safari, Android, iOS */
                             "url('$font_part.svg#".esc_attr($name)."') format('svg');"./* Legacy iOS */
                         "font-weight: normal;".
                         "font-style: normal;".
                    "}";
            /* add font selecter. */
            if($selecter){
                echo ''.$selecter."{font-family:'".esc_attr($name)."';}";
            }
        }
    }
    
    /**
     * set google font for selecter.
     * 
     * @param array $googlefont
     * @param string $selecter
     */
    public static function setGoogleFont($googlefont = array(), $selecter = ''){
        
        if(!empty($googlefont['font-family'])){
            /* add font selecter. */
            
            $font_weight =  !empty($googlefont['font-weight']) ? "font-weight:".esc_attr($googlefont['font-weight']).";" : '';
            $font_style =  !empty($googlefont['font-style']) ? "font-style:".esc_attr($googlefont['font-style']).";" : '';
                
            if(!empty($selecter)){
                echo ''.$selecter."{font-family:'".esc_attr($googlefont['font-family'])."';".$font_weight.$font_style."}";
            }
        }
    }
    
    /**
     * minimize CSS styles
     *
     * @since 1.1.0
     */
    public static function compressCss($buffer){
    
        /* remove comments */
        $buffer = preg_replace("!/\*[^*]*\*+([^/][^*]*\*+)*/!", "", $buffer);
        /* remove tabs, spaces, newlines, etc. */
        $buffer = str_replace("	", " ", $buffer); //replace tab with space
        $arr = array("\r\n", "\r", "\n", "\t", "  ", "    ", "    ");
        $rep = array("", "", "", "", " ", " ", " ");
        $buffer = str_replace($arr, $rep, $buffer);
        /* remove whitespaces around {}:, */
        $buffer = preg_replace("/\s*([\{\}:,])\s*/", "$1", $buffer);
        /* remove last ; */
        $buffer = str_replace(';}', "}", $buffer);
    
        return $buffer;
    }
}
