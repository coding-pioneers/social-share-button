if ($('.share').length > 0) {
	
$('a.btn-share').on('click', function(event) {
	
	// Popup öffnen und Fokus setzen
	fenster = window.open($(this).attr('href'), "Beitrag Teilen", "width=650,height=450,resizable=yes");
	fenster.focus();

	// Normale Link-Click-Aktion unterbinden
	event.preventDefault();
	
});
	
$.ajax({
	url : 'http://www.coding-pioneers.com/load-shares.json',
	// data: "url="+window.location,
	data : "url="+ encodeURIComponent('http://www.coding-pioneers.com/'),
	type : 'POST',
	dataType : 'json',
	success : function(data) {

		// Alle Interaktionen setzen und sichtbarkeit ändern.
		if (data.all == 0) {
			$('.share .share_counter').text('Bisher gibt es keine Interaktionen mit dem Beitrag - Teile Ihn doch!');
		} else {
			$('.share .share_counter').prepend(data.all + ' ');
		}

		$('.share .share_counter').fadeIn();

		// Facebook Interaktionen
		if (data.facebook != 0) {
			$('.share .btn-facebook .text').text(data.facebook + (data.facebook == 1 ? ' like' : ' likes'));
		}

		// Twitter Interaktionen
		if (data.twitter != 0) {
			$('.share .btn-twitter .text').text(data.twitter + (data.twitter == 1 ? ' tweet' : ' tweets'));
		}

		// Google Interaktionen
		if (data.google != 0) {
			$('.share .btn-google .text').text(data.google);
		}

		// Xing Interaktionen
		if (data.xing != 0) {
			$('.share .btn-xing .text').text(data.xing + (data.xing == 1 ? ' share': ' shares'));
		}
	},
	error : function(error, msg) {
		// TODO ggf. Error-Reporting
	}
});
}
