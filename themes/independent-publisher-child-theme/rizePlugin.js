(function () {
	tinymce.create('tinymce.plugins.rizeDB', {
		
		init: function (ed, url) {			
			/*
			ed.addButton('rizeDB', {
				title: 'rizeDB',
				image: url + '/fn.png',
				onclick: function () {
					var mySelection = ed.selection.getContent();					
					if (mySelection != '') {
						var apiUrl = prompt("API Url", "Enter your API Url Here.");
						ed.selection.setContent('[rizeDB url=' + apiUrl + ']' + mySelection + '[/rizeDB]');
					} 					
				}
			});
			*/

			ed.addButton('rizeDB', {
				title: 'rizeBase',
				image: url + '/rb.png',
				cmd: 'tweetthis_cmd'				
			});
 
			ed.addCommand('tweetthis_cmd', function() {
				ed.windowManager.open(
					//	Window Properties
					{
						//file: url + '/../../includes/tinymce-dialog.html',
						file: url + '/rizeDB.php',
						title: 'RizeBase',
						width: 650,
						height: 600,
						inline: 1
					},
					//	Windows Parameters/Arguments
					{
						editor: ed,
						jquery: jQuery // PASS JQUERY
					}
				);
			});

		},
		createControl: function (n, cm) {
			return null;
		}
	});
	tinymce.PluginManager.add('rizeDB', tinymce.plugins.rizeDB);
})();