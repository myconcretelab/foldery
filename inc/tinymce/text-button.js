(function() {
    tinymce.PluginManager.add('cshero_quote_btn', function( editor, url ) {
        editor.addButton( 'cshero_quote_btn', {
            text: 'Quote',
            icon: false,
            type: 'menubutton',
            menu: [
                {
                    text: 'Style 1',
                    value: 'cms-quote-style-1',
                    onclick: function() {
                        editor.insertContent('<blockquote class="'+this.value()+'">'+tinyMCE.activeEditor.selection.getContent()+'<blockquote>');
                    }
                }  
           ]
        });
    });
})();