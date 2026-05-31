<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $thumbnail_size
 * @var $thumbnail_size_custom
 * @var $values
 * Shortcode class
 * @var $this WPBakeryShortCode_zk_clients_carousel
 */

$values = $thumbnail_class = '';
/* get Shortcode custom value */
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$wrap_css_class = array('zk-clients-wrap', $el_id);

$css_class_attr = $item_class = array();
$css_class_attr[] = 'layout-'.$layout_template;

if($layout_type === 'carousel'){
    $wrap_css_class[] = 'zk-owl-wrap';
    $css_class_attr[] = 'zk-carousel owl-carousel';
    $item_class[] = 'zk-carousel-item';
} else {
    $css_class_attr[] = 'zk-grid row';
    $item_class[] = 'zk-grid-item col-sm-'.round(12/$col_sm).' col-md-'.round(12/$col_md).' col-lg-'.round(12/$col_lg).' col-xl-'.round(12/$col_xl);
}
$css_class_attr[] = $content_align;
$css_class_attr[] = $el_class;

$clients = vc_map_get_attributes( $this->getShortcode(), $atts );
$values = (array) vc_param_group_parse_atts( $clients['values'] );
if(empty($values[0])) {
    echo '<p class="require required">'.esc_html__('Please add a client logo!','foldery').'</p>';
    return;
}

/* Image Size */
$custom_size = false;
if ($thumbnail_size === 'custom'){ 
    $custom_size = true;
    $thumbnail_size = $thumbnail_size_custom;
}
$count = count($values);
$i=1;
$j=0;
?>
<div class="<?php echo trim(implode(' ',$wrap_css_class));?>">
    <?php monaco_owl_dots_top($layout_type, $dot_pos, $dot_style); ?>
    <div id="<?php echo esc_attr($el_id);?>" class="<?php echo join(' ',$css_class_attr);?>">
        <?php
            foreach($values as $value){
                $j++; 
                if($i > $number_row) $i=1;
                /* parse image_link */
                $link = false;
                $link_open = '<span class="client-logo"><span>';
                $link_close = '</span></span>';
                if(isset($value['image_link'])){
                    $image_link = vc_build_link( $value['image_link']);
                    $image_link = ( $image_link == '||' ) ? '' : $image_link;
                    if ( strlen( $image_link['url'] ) > 0 ) {
                        $link = true;
                        $a_href = $image_link['url'];
                        $a_title = $image_link['title'] ? $image_link['title'] : '';
                        $a_target = strlen( $image_link['target'] ) > 0 ? str_replace(' ','',$image_link['target']) : '_self';
                        $link_open = '<a class="client-logo" href="'.esc_url($a_href).'" title="'.esc_attr($a_title).'" target="'.esc_attr($a_target).'"><span>';
                        $link_close = '</span></a>';
                    }
                }
                if(isset($value['image'])) {
                    $img = wpb_getImageBySize( array(
                        'attach_id' => $value['image'],
                        'thumb_size' => $thumbnail_size,
                        'class' => 'team-image'.$thumbnail_class,
                    ));
                    $thumbnail = $img['thumbnail'];
                    $dot_image = wp_get_attachment_image($value['image'],'thumbnail','',array('class'=>'img-circle'));
                    if($i==1) : ?>
                        <div class="<?php echo join(' ',$item_class);?>" data-dot='<?php echo wp_kses_post($dot_image); ?>'>
                    <?php  
                        endif;
                        echo '<div class="zk-client-item">';                
                        echo wp_kses_post($link_open);
                            echo wp_kses_post($thumbnail);
                        echo wp_kses_post($link_close);
                        echo '</div>';
                    if($i == $number_row || $j==$count) echo '</div>';
                    $i ++;
                }
            }
        ?>
    </div>
    <?php 
        monaco_owl_preload($layout_type);
        monaco_owl_dots($layout_type, $dot_style, $dot_pos);
        monaco_owl_nav($layout_type, $nav_style, $nav_pos);
        monaco_owl_dots_in_nav($layout_type, $nav_style);
    ?>
</div>
