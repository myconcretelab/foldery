<?php
	vc_add_param('cms_progressbar', array(
        "type" => "dropdown",
        "heading" => esc_html__("Mode",'foldery'),
        "param_name" => "mode",
        "value" => array(
        	"Horizontal" => "horizontal",
        	"Vertical" => "vertical",
        	),
        "std"	=> 'vertical',
        "group" => esc_html__("Progress Bar Settings", 'foldery')
    ));
?>