<?php
/*Fancybox*/
if(class_exists('WPBakeryVisualComposerAbstract')){
	add_action( 'init', 'cms_integrateWithVC' );
}

if(!function_exists('cms_integrateWithVC')){
    function cms_integrateWithVC(){
        vc_map(
			array(
				"name" => esc_html__("Monaco Single Fancy Box", 'foldery' ),
			    "base" => "cms_fancybox_single",
			    "class" => "vc-cms-fancy-boxes",
			    "category" => esc_html__("Monaco", 'foldery' ),
			    "params" => array(
			    	
			    	array(
			            "type" => "textfield",
			            "heading" => esc_html__("Title", 'foldery' ),
			            "param_name" => "title",
			            "description" => esc_html__("Title Of Element", 'foldery' ),
			            "group" => esc_html__("General Settings", 'foldery' )
			        ),
			        array(
						"type" => "textfield",
			            "heading" => esc_html__("Sub Title", 'foldery' ),
			            "param_name" => "element_sub_title",
			            "description" => esc_html__("Sub title of Element", 'foldery' ),
			            "group" => esc_html__("General Settings", 'foldery' )
					),
			        array(
			            "type" => "textarea",
			            "heading" => esc_html__("Description", 'foldery' ),
			            "param_name" => "description",
			            "description" => esc_html__("Description Of Element", 'foldery' ),
			            "group" => esc_html__("General Settings", 'foldery' )
			        ),
			        array(
			            "type" => "dropdown",
			            "heading" => esc_html__("Content Align", 'foldery' ),
			            "param_name" => "content_align",
			            "value" => array(
			            	"Default" => "default",
			            	"Left" => "left",
			            	"Right" => "right",
			            	"Center" => "center"
			            	),
			            'std'	=> 'default',
			            "group" => esc_html__("General Settings", 'foldery' )
			        ),
			        /* Start Items */
			        /* Start Icon */
			        array(
						'type' => 'dropdown',
						'param_name' => 'icon_type',
						'heading' => esc_html__( 'Icon library', 'foldery' ),
						'value' => array(
							esc_html__( 'Font Awesome', 'foldery' ) => 'fontawesome',
							esc_html__( 'Open Iconic', 'foldery' ) => 'openiconic',
							esc_html__( 'Typicons', 'foldery' ) => 'typicons',
							esc_html__( 'Entypo', 'foldery' ) => 'entypo',
							esc_html__( 'Linecons', 'foldery' ) => 'linecons',
							esc_html__( 'Pixel', 'foldery' ) => 'pixelicons',
							esc_html__( 'P7 Stroke', 'foldery' ) => 'pe7stroke',			
						),
						'std' => 'fontawesome',
						'description' => esc_html__( 'Select icon library.', 'foldery' ),
						"group" => esc_html__("Fancy Icon Settings", 'foldery' )
					),
					array(
						'type' => 'iconpicker',
						'heading' => esc_html__( 'Icon Item', 'foldery' ),
						'param_name' => 'icon_fontawesome',
			            'value' => '',
						'settings' => array(
							'emptyIcon' => true, // default true, display an "EMPTY" icon?
							'type' => 'fontawesome',
							'iconsPerPage' => 200, // default 100, how many icons per/page to display
						),
						'dependency' => array(
							'element' => 'icon_type',
							'value' => 'fontawesome',
						),
						'description' => esc_html__( 'Select icon from library.', 'foldery' ),
						"group" => esc_html__("Fancy Icon Settings", 'foldery' )
					),
			        array(
						'type' => 'iconpicker',
						'heading' => esc_html__( 'Icon Item', 'foldery' ),
						'param_name' => 'icon_openiconic',
			            'value' => '',
						'settings' => array(
							'emptyIcon' => true, // default true, display an "EMPTY" icon?
							'type' => 'openiconic',
							'iconsPerPage' => 200, // default 100, how many icons per/page to display
						),
						'dependency' => array(
							'element' => 'icon_type',
							'value' => 'openiconic',
						),
						'description' => esc_html__( 'Select icon from library.', 'foldery' ),
						"group" => esc_html__("Fancy Icon Settings", 'foldery' )
					),
					array(
						'type' => 'iconpicker',
						'heading' => esc_html__( 'Icon Item', 'foldery' ),
						'param_name' => 'icon_typicons',
			            'value' => '',
						'settings' => array(
							'emptyIcon' => true, // default true, display an "EMPTY" icon?
							'type' => 'typicons',
							'iconsPerPage' => 200, // default 100, how many icons per/page to display
						),
						'dependency' => array(
							'element' => 'icon_type',
							'value' => 'typicons',
						),
						'description' => esc_html__( 'Select icon from library.', 'foldery' ),
						"group" => esc_html__("Fancy Icon Settings", 'foldery' )
					),
					array(
						'type' => 'iconpicker',
						'heading' => esc_html__( 'Icon Item', 'foldery' ),
						'param_name' => 'icon_entypo',
			            'value' => '',
						'settings' => array(
							'emptyIcon' => true, // default true, display an "EMPTY" icon?
							'type' => 'entypo',
							'iconsPerPage' => 200, // default 100, how many icons per/page to display
						),
						'dependency' => array(
							'element' => 'icon_type',
							'value' => 'entypo',
						),
						'description' => esc_html__( 'Select icon from library.', 'foldery' ),
						"group" => esc_html__("Fancy Icon Settings", 'foldery' )
					),
					array(
						'type' => 'iconpicker',
						'heading' => esc_html__( 'Icon Item', 'foldery' ),
						'param_name' => 'icon_linecons',
			            'value' => '',
						'settings' => array(
							'emptyIcon' => true, // default true, display an "EMPTY" icon?
							'type' => 'linecons',
							'iconsPerPage' => 200, // default 100, how many icons per/page to display
						),
						'dependency' => array(
							'element' => 'icon_type',
							'value' => 'linecons',
						),
						'description' => esc_html__( 'Select icon from library.', 'foldery' ),
						"group" => esc_html__("Fancy Icon Settings", 'foldery' )
					),
					array(
						'type' => 'iconpicker',
						'heading' => esc_html__( 'Icon Item', 'foldery' ),
						'param_name' => 'icon_pixelicons',
			            'value' => '',
						'settings' => array(
							'emptyIcon' => true, // default true, display an "EMPTY" icon?
							'type' => 'pixelicons',
							'iconsPerPage' => 200, // default 100, how many icons per/page to display
						),
						'dependency' => array(
							'element' => 'icon_type',
							'value' => 'pixelicons',
						),
						'description' => esc_html__( 'Select icon from library.', 'foldery' ),
						"group" => esc_html__("Fancy Icon Settings", 'foldery' )
					),
					array(
						'type' => 'iconpicker',
						'heading' => esc_html__( 'Icon Item', 'foldery' ),
						'param_name' => 'icon_pe7stroke',
						'settings' => array(
							'emptyIcon' => true, // default true, display an "EMPTY" icon?
							'type' => 'pe7stroke',
							'iconsPerPage' => 200, // default 100, how many icons per/page to display
						),
						'dependency' => array(
							'element' => 'icon_type',
							'value' => 'pe7stroke',
						),
						'description' => esc_html__( 'Select icon from library.', 'foldery' ),
						"group" => esc_html__("Fancy Icon Settings", 'foldery' )
					),
					/* End Icon */
					array(
			            "type" => "attach_image",
			            "heading" => esc_html__("Image Item", 'foldery' ),
			            "param_name" => "image",
			            "group" => esc_html__("Fancy Icon Settings", 'foldery' )
			        ),
			        array(
			            "type" => "textfield",
			            "heading" => esc_html__("Title Item", 'foldery' ),
			            "param_name" => "title_item",
			            "description" => esc_html__("Title Of Item", 'foldery' ),
			            "group" => esc_html__("Fancy Icon Settings", 'foldery' ),
			            "holder" => "div"
			        ),
			        array(
			            "type" => "textfield",
			            "heading" => esc_html__("Sub Title", 'foldery' ),
			            "param_name" => "sub_title_item",
			            "description" => esc_html__("Sub Title Of Item", 'foldery' ),
			            "group" => esc_html__("Fancy Icon Settings", 'foldery' ),
			        ),
			        array(
			            "type" => "textarea",
			            "heading" => esc_html__("Content Item", 'foldery' ),
			            "param_name" => "description_item",
			            "group" => esc_html__("Fancy Icon Settings", 'foldery' )
			        ),
			        /* End Items */
			        array(
			            "type" => "dropdown",
			            "heading" => esc_html__("Button Type", 'foldery' ),
			            "param_name" => "button_type",
			            "value" => array(
			            	"Button" => "button",
			            	"Text" => "text"
			            	),
			            'std'	=> 'button',
			            "group" => esc_html__("Buttons Settings", 'foldery' )
			        ),
			        array(
			            "type" => "vc_link",
			            "heading" => esc_html__("Link", 'foldery' ),
			            "param_name" => "button_link",
			            'description' => '',
			            "group" => esc_html__("Buttons Settings", 'foldery' )
			        ),
			        array(
			            "type" => "textfield",
			            "heading" => esc_html__("Extra Class", 'foldery' ),
			            "param_name" => "class",
			            'description' => '',
			            "group" => esc_html__("Template", 'foldery' )
			        ),
			    	array(
			            "type" => "cms_template",
			            "param_name" => "cms_template",
			            'std'	=> 'cms_fancybox_single.php',
			            "admin_label" => true,
			            "heading" => esc_html__("Shortcode Template", 'foldery' ),
			            "shortcode" => "cms_fancybox_single",
			            "group" => esc_html__("Template", 'foldery' ),
			        ),
			        array(
			            "type" => "dropdown",
			            "heading" => esc_html__("Icon/Image Align", 'foldery' ),
			            "param_name" => "image_align",
			            "value" => array(
			            	'Default' => '',
			            	'Left' => 'pull-left',
			            	'Right' => 'pull-right',
			            	'Center' => 'pull-center' 
			            ),
			            'std'	=> '',
			            "group" => "Template",
			            "class"  => "cms-extra-param",
			            "dependency" => array(
			            	'element' => 'cms_template',
			            	'value' => array(
			            		'cms_fancybox_single.php',
			            		'cms_fancybox_single--about.php',
			            		'cms_fancybox_single--overlay.php',
			            		'cms_fancybox_single--shopprocess.php',
			            		'cms_fancybox_single--onepage.php'
			            	)
			            ),
			        ),
				)
			)
		);
    }
}
