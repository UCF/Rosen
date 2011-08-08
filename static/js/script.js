jQuery(document).ready(function($) {
	Webcom.slideshow($);
	Webcom.chartbeat($);
	Webcom.analytics($);
	Webcom.handleExternalLinks($);
	Webcom.loadMoreSearchResults($);
	
	$('.events').each(function(){Webcom.events.callback($, $(this));});

	(function() {
		$('#newsletter_signup input[type="text"]')
			.focus(function() {
				var email = $(this);
				if(email.val() == 'Enter Email Address...') {
					email.val('');
					email.addClass('focus');
				}
			})
			.blur(function() {
				var email = $(this);
				if(email.val() == '') {
					email.val('Enter Email Address...');
					email.removeClass('focus');
				}
			});
		$('#search input[type="text"]')
			.focus(function() {
				var email = $(this);
				if(email.val() == 'Enter Search Term...') {
					email.val('');
					email.addClass('focus');
				}
			})
			.blur(function() {
				var email = $(this);
				if(email.val() == '') {
					email.val('Enter Search Term...');
					email.removeClass('focus');
				}
			});
		
		$('#flickr_gallery a').lightBox({
			imageLoading:THEME_IMG_URL + '/lightbox/lightbox-ico-loading.gif',
			imageBtnClose:THEME_IMG_URL + '/lightbox/lightbox-btn-close.gif',
			imageBtnPrev:THEME_IMG_URL + '/lightbox/lightbox-btn-prev.gif',
			imageBtnNext:THEME_IMG_URL + '/lightbox/lightbox-btn-next.gif',
			imageBlank: THEME_IMG_URL + '/lightbox/lightbox-blank.gif'
		});
		
		$('.dept > table > tbody > tr')
			.each(function(index, row) {
				row = $(row);
				var profile_url = row.attr('data-profile-url');
				if(profile_url != '') {
					row.click(function() {window.location.href = profile_url});
				}
			})
	})();
});
