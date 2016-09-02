(function() {
    tinymce.create('tinymce.plugins.Embedpluswiz', {
        init : function(ed, url) {
            ed.addButton('embedpluswiz', {
                title : 'EmbedPlus Shortcode Wizard',
                onclick : function(ev) {
                    modalw = Math.round(jQuery(window).width() *.9);
                    modalh = Math.round(jQuery(window).height() *.8);
                    ed.windowManager.open({
                        title : "EmbedPlus Shortcode Wizard - for YouTube",
                        file : epbasesite + '/wpembedcode.aspx?pluginversion=' + epversion + 
                                '&blogwidth=' + epblogwidth + 
                                '&domain=' + escape(window.location.toString()) + 
                                '&prokey=' + escape(epprokey) + 
                                '&eadopt=' + epeadopt + 
                                '&mydefaults=' + epdefaults,
                        width : 950,
                        height : modalh,
                        inline : true,
                        resizable: true,
                        scrollbars: true
                    }, {
                        plugin_url : url, // Plugin absolute URL
                        some_custom_arg : '' // Custom argument
                    });
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
                longname : "Embedplus Shortcode Wizard",
                author : 'EmbedPlus',
                authorurl : 'http://www.embedplus.com/',
                infourl : 'http://www.embedplus.com/',
                version : epversion
            };
        }
    });
    tinymce.PluginManager.add('embedpluswiz', tinymce.plugins.Embedpluswiz);


})();


////(function() {
//    tinymce.PluginManager.add('embedpluswiz', function(editor, url) {
//        var plep = new Image();
//        plep.src = url + '/../images/btn_embedpluswiz.png';
//
//        // Add a button that opens a window
//        editor.addButton('embedpluswiz', {
//            title: 'EmbedPlus Shortcode Wizard',
//            //text: 'FB Test Button',
//            icon: url + '/../images/btn_embedpluswiz.png',
//            onclick: function() {
//                // Open window
//                modalw = Math.round(jQuery(window).width() * .9);
//                modalh = Math.round(jQuery(window).height() * .8);
//
//                editor.windowManager.open({
//                    title: "EmbedPlus Shortcode Wizard - for YouTube",
//                    url: epbasesite + '/wpembedcode.aspx?blogwidth=' + epblogwidth + '&domain=' + escape(window.location.toString()) + '&prokey=' + escape(epprokey) + '&eadopt=' + epeadopt,
//                    width: 950,
//                    height: modalh,
//                    inline: true,
//                    resizable: true,
//                    scrollbars: true
//                }, {
//                    someArg: "someValue"
//                });
//            }
//
//        });
//
//    });
//
//})();