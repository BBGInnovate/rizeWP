jQuery(document).ready(function() { 
	// 
	// create social networking pop-ups
	// link selector and pop-up window size

	var shareConfig = {
		Link: "a.share",
		Width: 500,
		Height: 500
	};

	// add handler links
	var shareLink = document.querySelectorAll(shareConfig.Link);
	for (var a = 0; a < shareLink.length; a++) {
		shareLink[a].onclick = PopupHandler;
	}

	// create popup
	function PopupHandler(e) {

		/*you could tweet the highlighted/selected text by encoding and concatenating it with the URL
		var text = "";
		if (window.getSelection) {
			text = window.getSelection().toString();
		} else if (document.selection && document.selection.type != "Control") {
			text = document.selection.createRange().text;
		}
		console.log(text);
		*/

		e = (e ? e : window.event);

		//changed e.target.parentNode to e.target when i removed the <img/> tag
		//var t = (e.target.parentNode ? e.target.parentNode : e.srcElement);
		var t = (e.target ? e.target : e.srcElement);
		//logger(t)

		// popup position
		var px = Math.floor(((screen.availWidth || 1024) - shareConfig.Width) / 2),
			py = Math.floor(((screen.availHeight || 700) - shareConfig.Height) / 2);

		// open popup
		var popup = window.open(t.href, "social", 
			"width="+shareConfig.Width+",height="+shareConfig.Height+
			",left="+px+",top="+py+
			",location=0,menubar=0,toolbar=0,status=0,scrollbars=1,resizable=1");
		if (popup) {
			popup.focus();
			if (e.preventDefault) e.preventDefault();
			e.returnValue = false;
		}

		return !!popup;
	}

	//Dropdown navigation
	jQuery("#footerNav .focus" ).click(function() {
		jQuery( "#focusMenu #dropdown" ).slideToggle("drop");
		jQuery( "#footerNav .focus" ).toggleClass( "menuOpen" )
	});

	//tooltip plugin doesn't load on trending page, so we wrap this in a check
	if (jQuery().tooltip) {
		jQuery( document ).tooltip({
	      items: "a.rizeDBCard",
	      content: function() {
	        var element = jQuery( this );
	        var twitter=element.attr('data-twitter');
	        var image=element.attr('data-image');
	        var name=element.attr('data-title');
	        var description=element.attr('data-description');
	        return "<div'><strong>"+name+"</strong><BR><img width='50' height='50' src='"+image+"'><BR><a href='"+twitter+"'>@"+twitter+"</a><p style='font-size:.5em'>"+description+"</p></div>";
	      }
	    });
	}
});