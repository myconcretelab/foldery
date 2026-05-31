<?php
/**
 * Add VC Icon feature
 * 
 * @author Chinh Duong Manh
 * @since VC 4.7.3
 * @since 1.2.0
 */

    vc_add_param("vc_icon", array(
        "heading" => esc_html__("Icon library", 'foldery' ),
        "description" => esc_html__("Select icon library.", 'foldery' ),
        "param_name" => "type",
        "type" => "dropdown",
        "value" =>  array(
            'P7 Stroke' => 'pe7stroke',
        	'Font Awesome' => 'fontawesome',
        	'Open Iconic' => 'openiconic',
        	'Typicons' => 'typicons',
            'Entypo' => 'entypo',
            'Linecons' => 'linecons', 
        ),
        "std" => "fontawesome",
        "admin_label" => true
    ));
    vc_add_param("vc_icon", array(
        'type' => 'iconpicker',
        'heading' => esc_html__( 'Icon', 'foldery' ),
        'param_name' => 'icon_pe7stroke',
        'std' => '',
        'settings' => array(
            'emptyIcon' => true, // default true, display an "EMPTY" icon?
            'type' => 'pe7stroke',
            'iconsPerPage' => 200, // default 100, how many icons per/page to display
        ),
        'dependency' => array(
            'element' => 'type',
            'value' => 'pe7stroke',
        ),
        'description' => esc_html__( 'Select icon from library.', 'foldery' ),
        "group" => esc_html__("Monaco Custom", 'foldery' )
    ));
