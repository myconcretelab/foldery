<?php
add_action('admin_head', 'cshero_quote_btn');
function cshero_quote_btn() {
    global $typenow;
    // check user permissions
    if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
        return;
    }
    // check if WYSIWYG is enabled
    if ( get_user_option('rich_editing') == 'true') {
        add_filter("mce_external_plugins", "cshero_add_tinymce_plugin");
        add_filter('mce_buttons', 'cshero_register_quote_button');
    }
}
function cshero_add_tinymce_plugin($plugin_array) {
    $plugin_array['cshero_quote_btn'] = get_template_directory_uri().'/inc/tinymce/text-button.js'; // CHANGE THE BUTTON SCRIPT HERE
    return $plugin_array;
}
function cshero_register_quote_button($buttons) {
    array_push($buttons, "cshero_quote_btn");
    return $buttons;
}
?>