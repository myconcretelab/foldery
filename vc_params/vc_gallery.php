<?php
/**
 * Add vc_gallery params
 * 
 * @author Chinh Duong Manh
 * @since 1.2.0
 */
	/* Add new Gallary Type */
	add_action( 'vc_after_init', 'wp_hender_add_vc_gallery_new_style' ); /* Note: here we are using vc_after_init because WPBMap::GetParam and mutateParame are available only when default content elements are "mapped" into the system */
    function wp_hender_add_vc_gallery_new_style() {
      /* Get current values stored in the color param in "Accordion" element */
      $param = WPBMap::getParam( 'vc_gallery', 'type' );
      /* Append new value to the 'value' array */ 
      $param['value'][esc_html__( 'Theme Style', 'foldery' )] = ' theme_gallery';
      /* Finally "mutate" param with new values */
      vc_update_shortcode_param( 'vc_gallery', $param );
    }

    /* Add new params */
    vc_add_param("vc_gallery", array(
        "type" => "textfield",
        "heading" => esc_html__("Item Space", "foldery"),
        "param_name" => "cms_item_space",
        "description" => esc_html__("Enter space beetwen each item. Example : 10",  "foldery"),
        "group" => "Theme Options",
        'dependency' => array(
            'element' => 'type',
            'value' => "image_grid",
      ),
    ));
