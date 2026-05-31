<?php 
      vc_add_param( 'cms_grid', array(
            "type" => "dropdown",
            "heading" => esc_html__("Title Layout", 'foldery' ),
            "param_name" => "element_title_layout",
            "value" => array(
                  'Layout 1' => '1',
                  'Layout 2' => '2',
                  'Layout 3' => '3',
            ),
            'std' => '1',
            'weight' => '1',
            )
      );
	vc_add_param('cms_grid', array(
		"type" => "textfield",
            "heading" => esc_html__("Title", 'foldery' ),
            "param_name" => "element_title",
            "holder" => 'div'
	     )
	);
	vc_add_param('cms_grid', array(
		"type" => "textfield",
            "heading" => esc_html__("Sub Title", 'foldery' ),
            "param_name" => "element_sub_title",
		)
	);
	vc_add_param('cms_grid', array(
		"type" => "textarea",
            "heading" => esc_html__("Title Description", 'foldery' ),
            "param_name" => "element_title_desc",
		)
	);
?>
<?php 
      $params = array(
            /* Add option show/hide image for default CMS GRID Layout*/
           array(
                  "type" => "dropdown",
                  "heading" => esc_html__("Show Image", 'foldery' ),
                  "param_name" => "show_image",
                  "value" =>  array(
                        'Yes' => '1', 
                        'No' => '0'
                  ),
                  'std' => '1',
                  "template" =>  array(
                        'cms_grid.php',
                        'cms_grid--popup.php',
                  )
            ),
            /* Add option make first and second item is large for CMS Grid Portfolio Layout */
            array(
                  "type" => "dropdown",
                  "heading" => esc_html__("Make first and second item large", 'foldery' ),
                  "param_name" => "fs_large", 
                  "value" =>  array(
                        'Yes' => '1', 
                        'No' => '0'
                  ),
                  'std' => '1',
                  "template" =>  array('cms_grid--portfolio.php','cms_grid--portfolio2.php')
            ),
            /* Add option make space bettween each item */
            array(
                  "type" => "textfield",
                  "heading" => esc_html__("Add space bettween each item", 'foldery' ),
                  "param_name" => "item_space",
                  "template" =>  array('cms_grid--portfolio2.php')
            ),
            /* Add option show/hide pagination for layout Gallery */
            array(
                  "type" => "checkbox",
                  "heading" => esc_html__("Show Pagination", 'foldery' ),
                  "param_name" => "show_nav",
                  "std" =>  "false",
                  "template" =>  array(
                        'cms_grid--gallery.php',
                  )
            ),
            array(
                  "type" => "dropdown",
                  "heading" => esc_html__("Pagination Type", 'foldery' ),
                  "param_name" => "nav_type",
                  "value" =>  array(
                        esc_html__('Default', 'foldery') => '0', 
                        esc_html__('Load More', 'foldery') => '1'
                  ),
                  'std' => '0',
                  'dependency' => array(
                        'element' => 'show_nav',
                        'value' => "true",
                  ),
            ),
      ) 
?>