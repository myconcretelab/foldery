<?php 
    vc_icon_element_fonts_enqueue( $atts['icon_type'] );
    if($atts['icon_type']=='pe7stroke'){
        wp_enqueue_style('cms-icon-pe7stroke', get_template_directory_uri().'/pe-icon-7-stroke.css');
    }
    $icon_name = "icon_" . $atts['icon_type'];

    $iconClass = isset($atts[$icon_name])?$atts[$icon_name]:'';
    $image_align = isset($atts['image_align'])?$atts['image_align']:'';

    /* parse button_link */
    $a_href = $a_title = $a_target = '';
    if(!empty($atts['button_link'])){
        $button_link = vc_build_link( $atts['button_link'] );
        $button_link = ( $button_link == '||' ) ? '' : $button_link;
        $use_link = false;    
        if ( strlen( $button_link['url'] ) > 0 ) {
            $use_link = true; 
            $a_href = $button_link['url'];
            $a_title = $button_link['title'];
            $a_target = strlen( $button_link['target'] ) > 0 ? $button_link['target'] : '_self';
        }
    }
?>
<div class="cms-fancyboxes-wraper cms-fancy-box-single <?php echo esc_attr($atts['template']);?> clearfix" id="<?php echo esc_attr($atts['html_id']);?>">
    <?php if($atts['title']!=''):?>
        <div class="cms-fancyboxes-head">
            <div class="cms-fancyboxes-title">
                <?php echo apply_filters('the_title',$atts['title']);?>
            </div>
            <div class="cms-fancyboxes-description">
                <?php echo apply_filters('the_content',$atts['description']);?>
            </div>
        </div>
    <?php endif;?>
    <div class="cms-fancyboxes-body">
        <div class="cms-fancybox-item">
            <?php 
            $image_url = '';
            if (!empty($atts['image'])) {
                $attachment_image = wp_get_attachment_image_src($atts['image'], 'full');
                $image_url = $attachment_image[0];
            }
            ?>
            <?php if($image_url):?>
            <div class="fancy-box-image fancy-box-icon <?php echo esc_attr($image_align);?>">
                <div class="fancy-box-icon-inner">
                <img src="<?php echo esc_attr($image_url);?>" />
                </div>
            </div>
            <?php elseif($iconClass):?>
            <div class="fancy-box-icon <?php echo esc_attr($image_align);?>">
            	<div class="fancy-box-icon-inner">
                	<i class="<?php echo esc_attr($iconClass);?>"></i>
                </div>
            </div>
            <?php endif;?>
            <?php if($image_url || $iconClass) $cls = 'has-icon-image'; else $cls = ''; ?>
            <div class="fancy-box-content-wrap <?php echo esc_attr($cls);?>">
	            <?php if($atts['title_item']):?>
	                <h4><?php echo apply_filters('the_title',$atts['title_item']);?></h4>
	            <?php endif;?>
	            <div class="fancy-box-content">
	                <?php echo apply_filters('the_content',$atts['description_item']);?>
	            </div>
	            <?php if($a_href !='' && $a_title!=''):?>
	                <div class="cms-fancyboxes-foot">
	                    <?php
	                    $class_btn = ($atts['button_type']=='button')?'btn btn-large':'';
	                    ?>
	                    <a href="<?php echo esc_url($a_href);?>" target="<?php echo esc_attr($a_target); ?>" class="<?php echo esc_attr($class_btn);?>"><?php echo esc_attr($a_title);?></a>
	                </div>
	            <?php endif;?>
	        </div>
        </div>
    </div>
</div>