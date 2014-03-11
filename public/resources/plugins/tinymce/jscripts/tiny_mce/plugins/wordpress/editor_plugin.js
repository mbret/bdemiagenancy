/**
 * WordPress plugin.
 */

(function() {
	var DOM = tinymce.DOM;

	tinymce.create('tinymce.plugins.WordPress', {
		mceTout : 0,

		init : function(ed, url) {
			var t = this, moreHTML;
			moreHTML = '<img src="' + url + '/img/trans.gif" class="mceWPmore mceItemNoResize" title="'+ed.getLang('wordpress.wp_more_alt')+'" />';
			
			// Register commands
			ed.addCommand('WP_More', function() {
				ed.execCommand('mceInsertContent', 0, moreHTML);
			});

			// Register buttons
			ed.addButton('wp_more', {
				title : 'wordpress.wp_more_desc',
				image : url + '/img/more.gif',
				cmd : 'WP_More'
			});

			// Add listeners to handle more break
			t._handleMoreBreak(ed, url);
		},

		getInfo : function() {
			return {
				longname : 'Part of WordPress Plugin',
				author : 'WordPress', // add Moxiecode?
				authorurl : 'http://wordpress.org',
				infourl : 'http://wordpress.org',
				version : '3.0'
			};
		},

		_handleMoreBreak : function(ed, url) {
			var moreHTML;

			moreHTML = '<img src="' + url + '/img/trans.gif" alt="$1" class="mceWPmore mceItemNoResize" title="'+ed.getLang('wordpress.wp_more_alt')+'" />';

			// Load plugin specific CSS into editor
			ed.onInit.add(function() {
				ed.dom.loadCSS(url + '/css/content.css');
			});

			// Display morebreak instead if img in element path
			ed.onPostRender.add(function() {
				if (ed.theme.onResolveName) {
					ed.theme.onResolveName.add(function(th, o) {
						if (o.node.nodeName == 'IMG') {
							if ( ed.dom.hasClass(o.node, 'mceWPmore') )
								o.name = 'wpmore';
						}

					});
				}
			});

			// Replace morebreak with images
			ed.onBeforeSetContent.add(function(ed, o) {
				if ( o.content ) {
					o.content = o.content.replace(/<!--more(.*?)-->/g, moreHTML);
				}
			});

			// Replace images with morebreak
			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = o.content.replace(/<img[^>]+>/g, function(im) {
						if (im.indexOf('class="mceWPmore') !== -1) {
							var m, moretext = (m = im.match(/alt="(.*?)"/)) ? m[1] : '';
							im = '<!--more'+moretext+'-->';
						}

						return im;
					});
			});

			// Set active buttons if user selected more break
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('wp_more', n.nodeName === 'IMG' && ed.dom.hasClass(n, 'mceWPmore'));
			});
		}
	});

	// Register plugin
	tinymce.PluginManager.add('wordpress', tinymce.plugins.WordPress);
})();
