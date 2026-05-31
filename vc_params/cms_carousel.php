<?php 
	$params = array(
		array(
	        'type' => 'dropdown',
	        'heading' => esc_html__("Show/Hide Image", 'foldery' ),
	        'param_name' => 'show_image',
	        'value' => array(
	            'No' => '0',
	            'Yes' => '1'
	        ),
	        'std' => '0',
	        'description' => '',
	        'template' => 'cms_carousel--testimonial.php'
	    ),
        /* Add option change Nav icon from font to use image */
	    array(
			"type" => "dropdown",
			"heading" => esc_html__("Nav icon as image", 'foldery' ),
			"param_name" => "nav_icon_image",
			"value" =>  array(
			    'No' => '', 
			    'Yes' => 'nav_icon_image'
			    ),
			'std' => '',
			'description' => '',
			'template' => 'cms_carousel--testimonial.php'
		)
	) 
?>