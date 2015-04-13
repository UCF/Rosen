jQuery(document).ready(function($) {
	Webcom.slideshow($);
	Webcom.chartbeat($);
	Webcom.analytics($);
	Webcom.handleExternalLinks($);
	Webcom.loadMoreSearchResults($);

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

		$('#flickr_gallery a, .venue-list a:has("img")').lightBox({
			imageLoading:THEME_IMG_URL + '/lightbox/lightbox-ico-loading.gif',
			imageBtnClose:THEME_IMG_URL + '/lightbox/lightbox-btn-close.gif',
			imageBtnPrev:THEME_IMG_URL + '/lightbox/lightbox-btn-prev.gif',
			imageBtnNext:THEME_IMG_URL + '/lightbox/lightbox-btn-next.gif',
			imageBlank: THEME_IMG_URL + '/lightbox/lightbox-blank.gif'
		});
		/*
		$('.dept > table > tbody > tr')
			.each(function(index, row) {
				row = $(row);
				var profile_url = row.attr('data-profile-url');
				if(profile_url != '') {
					row.click(function() {window.location.href = profile_url});
				}
			})
		*/

    var $mobileNav = $('#mobile-nav'),
        $mobileNavToggle = $mobileNav.find('#mobile-nav-toggle'),
        $mobileNavMenu = $mobileNav.find('#mobile-nav-menu'),
        $header = $('#header'),
        $splashContainer = $('#splash-container');

    $(window).on('load resize', function() {
      var mobileNavTop = $header.outerHeight(),
          splashContainerTop = mobileNavTop + $mobileNavToggle.outerHeight();

      // Position mobile navbar absolutely, directly below site title
      $mobileNav.css({
        'position': 'absolute',
        'top': $header.outerHeight(),
      });

      // At mobile sizes, push the home page splash image container down below
      // the site title and mobile navbar
      if ($(window).outerWidth() <= 950) {
        $splashContainer.css({
          'top': splashContainerTop,
        });
      }
      else {
        $splashContainer.attr('style', '');
      }
    });

    $mobileNavToggle.on('click', function() {
      $mobileNavMenu.slideToggle();
      $mobileNavToggle.toggleClass('active');
    });
	})();
});
