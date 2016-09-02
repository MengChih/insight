(function() {    
    tinymce.create('tinymce.plugins.Embedplusstats', {
        init : function(ed, url) {
            var plep = new Image();
            plep.src = url+'/../images/btn_embedplusstats.png';
            ed.addButton('embedplusstats', {
                title : 'Get simple answers to important questions about your advanced embedding performance »',
                onclick : function(ev) {
                    window.open(epbasesite + '/dashboard/easy-video-analytics-seo.aspx?ref=wysiwygbutton&domain=' + epdomain + '&prokey=' + epprokey, '_blank');
                }
            });

        },
        createControl : function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
                longname : "Embedplus Video Analytics Dashboard",
                author : 'EmbedPlus',
                authorurl : 'http://www.embedplus.com/',
                infourl : 'http://www.embedplus.com/',
                version : epversion
            };
        }
    });
    tinymce.PluginManager.add('embedplusstats', tinymce.plugins.Embedplusstats);

})();
