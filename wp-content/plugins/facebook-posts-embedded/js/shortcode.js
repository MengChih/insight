(function ($) {
	tinymce.create('tinymce.plugins.fb_embed_post', {
		init : function(ed, url) {
			ed.addButton('fb_embed_post', {
				title : 'Embed a Facebook Post',
				image : url+'/icon.png',
				onclick : function() {
					var href = prompt("Copy/Paste the URL of a PUBLIC Facebook post", "http://facebook.com/..");
					var width = prompt("Specify width in px", "550");
					if (href !== 'undefined' && href !== '') {
						ed.selection.setContent('[fb_embed_post href="'+href+'/" width="'+width+'"/]');
					}

					return false;
				}
			});
		},
		createControl : function(n, cm) {
			return null;
		},
	});
	tinymce.PluginManager.add('fb_embed_post', tinymce.plugins.fb_embed_post);
})(jQuery);
