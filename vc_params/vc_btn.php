<?php
/**
 * Add row params
 * 
 * @author Fox
 * @since 1.0.0
 */

    vc_add_param("vc_btn", array(
        "type" => "dropdown",
        "value" =>  array(
        	'Default' => 'btn btn-default',
        	'Default Alt White' => 'btn btn-alt-white',
        	'Primary' => 'btn btn-primary'
        ),
        'std'   => 'btn btn-default',
        "heading" => esc_html__("Button Type", 'foldery' ),
        "param_name" => "vc_btn_type",
        "description" => esc_html__("Choose Type of button", 'foldery' ),
        "group" => esc_html__("Custom Button", 'foldery' ),
        'dependency' => array(
			'element' => 'style',
			'value' => 'custom',
		),
    ));
    vc_add_param("vc_btn", array(
        "type" => "colorpicker",
        "heading" => esc_html__("Button Border Color", 'foldery' ),
        "param_name" => "vc_btn_border_color",
        "description" => esc_html__("Choose color button border", 'foldery' ),
        "group" => esc_html__("Custom Button", 'foldery' ),
        'dependency' => array(
			'element' => 'style',
			'value' => 'custom',
		),
        'std' => '',
    ));
