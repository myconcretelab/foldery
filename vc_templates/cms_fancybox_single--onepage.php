<?php 
    vc_icon_element_fonts_enqueue( $atts['icon_type'] );
    if($atts['icon_type']=='pe7stroke'){
         wp_enqueue_style('cms-icon-pe7stroke', get_template_directory_uri().'/pe-icon-7-stroke.css');
    }
    $icon_name = "icon_" . $atts['icon_type'];
    $iconClass = isset($atts[$icon_name])?$atts[$icon_name]:'';
    $image_align = isset($atts['image_align'])?$atts['image_align']:'';
    
    $atts['element_sub_title']  = isset($atts['element_sub_title'] )? $atts['element_sub_title']  : '';

    $image_url = '';
    if (!empty($atts['image'])) {
        $attachment_image = wp_get_attachment_image_src($atts['image'], 'full');
        $image_url = $attachment_image[0];
    }

    //parse button_link
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
<div class="cms-fancyboxes-wraper cms-fancy-box-single onepage <?php echo esc_attr($atts['template']);?>" id="<?php echo esc_attr($atts['html_id']);?>">
    <?php if($atts['title'] || $atts['element_sub_title'] || $atts['description']) : ?>
	<header class="cms-element-header row layout-2">
		<div class="cms-element-header-title col-xs-12 col-sm-4 col-md-4 col-lg-3">
			<h1>
				<?php 
                    $title = $atts['title'];
                    $pos = mb_strpos($title, ' ');
                    if ($pos != false) {
                        $title = '<span class="first-word">'.mb_substr($title, 0, $pos).'</span>'.mb_substr($title, $pos + 1);
                    } else {
                        $title = '<span class="first-word">'.$title.'</span>';
                    }

                    foldery_allowed_html($title) ;
                ?>
			</h1>
		</div>
		<div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
			<div class="cms-element-subtitle playfairdisplay"><?php foldery_allowed_html($atts['element_sub_title']); ?></div>
	        <div class="cms-element-desc"><?php foldery_allowed_html($atts['description']); ?></div>
		</div>
	</header>
	<?php endif; ?>
    <div class="cms-fancyboxes-body">     
        <div class="cms-fancybox-item row <?php if($image_url) echo 'has-image';?>">
            <?php if($image_url && $image_align !='pull-right'): ?>
	            <div class="fancy-box-image col-xs-12 col-sm-12 col-md-6 col-lg-6">
	                <img src="<?php echo esc_attr($image_url);?>" />
	            </div>
            <?php endif; ?>
            <?php if($image_url) $cls = "col-xs-12 col-sm-12 col-md-5 col-lg-5 col-md-offset-1 col-lg-offset-1"; else $cls ="col-xs-12 col-sm-12 col-md-12 col-lg-12"; ?>
            <div class="fancy-box-content-wrap <?php echo esc_attr($cls); ?>">
	            <div class="fancy-box-content-inner">
		            <div class="fancy-box-content-inner2">
		            	<?php if($iconClass):?>
			            <div class="fancy-box-icon">
			            	<div class="fancy-box-icon-inner">
			                	<i class="<?php echo esc_attr($iconClass);?>"></i>
			                </div>
			            </div>
			            <?php endif;?>
			            <?php if($atts['title_item']):?>
			                <h1><?php echo apply_filters('the_title',$atts['title_item']);?></h1>
			                <div class="sub-title playfairdisplay"><?php echo apply_filters('the_title',$atts['sub_title_item']);?></div>
			            <?php endif;?>

			            <div class="fancy-box-content">
			                <?php echo apply_filters('the_content',$atts['description_item']);?>
			            </div>
			            <?php if($a_href !='' && $a_title!=''):?>
			                <div class="cms-fancyboxes-foot">
			                    <?php
			                    $class_btn = ($atts['button_type']=='button')?'btn btn-large':'';
			                    ?>
			                    <a href="<?php echo esc_url($a_href);?>" target="<?php echo esc_attr($a_target); ?>" class="prettyphoto <?php echo esc_attr($class_btn);?>" rel="prettyPhoto[iframes]"><?php echo esc_attr($a_title);?></a>
			                </div>
			            <?php endif;?>
			        </div>
		        </div>
	        </div>
	        <?php if($image_url && $image_align =='pull-right'): ?>
	            <div class="fancy-box-image col-xs-12 col-sm-12 col-md-6 col-lg-6">
	                <img src="<?php echo esc_attr($image_url);?>" />
	            </div>
            <?php endif; ?>
        </div>
    </div>
</div>