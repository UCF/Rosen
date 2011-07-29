var analytics = function($){
	if ((typeof GA_ACCOUNT !== 'undefined') && Boolean(GA_ACCOUNT)){
		// Google analytics code
		var _sf_startpt=(new Date()).getTime();
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', GA_ACCOUNT]);
		_gaq.push(['_setDomainName', 'none']);
		_gaq.push(['_setAllowLinker', true]);
		_gaq.push(['_trackPageview']);
		(function(){
			var ga = document.createElement('script');
			ga.type = 'text/javascript';
			ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0];
			s.parentNode.insertBefore(ga, s);
		})();
	}
};

var handleExternalLinks = function($){
	$('a').each(function(){
		var url  = $(this).attr('href');
		var host = window.location.host.toLowerCase();
		
		if (url.search(host) < 0 && url.search('http') > -1){
			$(this).attr('target', '_blank');
			$(this).addClass('external');
		}
	});
};

var chartbeat = function($){
	if ((typeof CB_UID !== 'undefined') && Boolean(CB_UID)){
		var _sf_async_config={
			uid   : parseInt(CB_UID),
			domain: CB_DOMAIN
		};
		(function(){
			function loadChartbeat() {
				window._sf_endpt=(new Date()).getTime();
				var e = document.createElement('script');
				e.setAttribute('language', 'javascript');
				e.setAttribute('type', 'text/javascript');
				e.setAttribute('src',
					(
						("https:" == document.location.protocol) ?
						"https://s3.amazonaws.com/" : "http://"
					) + "static.chartbeat.com/js/chartbeat.js"
				);
				document.body.appendChild(e);
			}
			var oldonload = window.onload;
			window.onload = (typeof window.onload != 'function') ?
				loadChartbeat : function() {
					oldonload(); loadChartbeat();
				};
		})();
	}
};

// Events RSS reader functions
function get_month_from_rss(str){
	var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	var index  = Number(str.substr(5, 2));
	return months[index - 1];
}

function get_day_from_rss(str){
	return str.substr(8, 2);
}

var eventsCallback = function($, _this){
	var calendar = _this.attr('data-calendar-id');
	var url      = _this.attr('data-url');
	var limit    = _this.attr('data-limit');
	if (!calendar){calendar = 1;}
	if (!url)     {url = 'http://events.ucf.edu';}
	if (!limit)   {limit = 4;}

	$.getUCFEvents({
			'calendar_id' : calendar,
			'url'         : EVENT_PROXY_URL,
			'limit'       : limit}, function(data, status, request){
		if (data == null){return;}

		for (var i = 0; i < data.length; i++){
			var e     = data[i];
			var event = $('<li />', {'class' : 'event'});
			var date  = $('<div />', {'class' : 'date'});
			var month = $('<span />', {'class' : 'month'});
			var day   = $('<span />', {'class' : 'day'});
			var title = $('<a>', {'class' : 'title', 'href' : url + '?eventdatetime_id='+e.id});
			var end   = $('<div>', {'class' : 'end'});

			title.text(e.title);
			day.text(get_day_from_rss(e.starts));
			month.text(get_month_from_rss(e.starts));

			date.append(month);
			date.append(day);
			event.append(date);
			event.append(title);
			event.append(end);
			_this.append(event);
		}
	});
}
var loadMoreSearchResults = function($){
	var more  = '#search-results .more';
	var items = '#search-results .result-list .item';
	var list  = '#search-results .result-list';
	
	var next = null;
	var sema = null;
	
	var load = (function(){
		if (sema){
			setTimeout(function(){load();}, 100);
			return;
		}
		
		if (next == null){return;}
		
		// Grab results content and append to current results
		var results = $(next).find(items);
		$(list).append(results);
		
		// Grab new more link and replace current with new
		var anchor = $(next).find(more);
		$(more).attr('href', anchor.attr('href'));
		
		next = null;
	});
	
	var prefetch = (function(){
		sema = true;
		// Fetch url for href via ajax
		var url = $(more).attr('href');
		$.ajax({
			'url'     : url,
			'success' : function(data){
				next = data;
			},
			'complete' : function(){
				sema = false;
			}
		});
	});
	
	var load_and_prefetch = (function(){
		load();
		prefetch();
	});
	
	if ($(more).length > 0){
		load_and_prefetch();
	
		$(more).click(function(){
			load_and_prefetch();
			return false;
		});
	}
};

var slideshow = function($){
	/**
	 * Create slideshow of arbitrary objects.  Class each item to be a slide
	 * as 'slide', and recommend you set a static height and width on the 
	 * slideshow container.
	 * 
	 * Example:
	 * <div class="slides">
	 *   <img class="slide"...>
	 *   <div class="slide">...</div>
	 * </div>
	 * 
	 * $('.slides').slideShow({
	 *   'transition_length' : 2000,
	 *   'cycle_length': 4000
	 * });
	 *
	 * The options can be overridden by setting the data-tranlen and
	 * data-cyclelen attributes on the slideshow container.
	 **/
	$.fn.slideShow = function (args){
		var cycle = function(items, index){
			if (items.length < 1){ return;}
			
			var next_index = (index + 1) % items.length;
			
			// Initialize active and null elements
			var active = $(items[index]);
			var next   = $(items[next_index]);
			
			next.css({'left' : -width});
			next.show();
			
			active.animate({
				'left' : '+=' + width
			}, options.transition_length, function(){
				next.css({'left' : 0});
			});
			
			next.animate({
				'left' : 0
			}, options.transition_length, function(){
				next.css({'left' : 0});
				
				setTimeout(function(){
					cycle(items, next_index);
				}, options.cycle_length);
			});
			
			return;
		};
		
		var defaults = {
			'transition_length' : 1000,
			'cycle_length'      : 5000
		};
		var options   = $.extend({}, defaults, args);
		var container = $(this);
		if (container.attr('data-tranlen')){
			options.transition_length = parseInt(container.attr('data-tranlen'));
		}
		if (container.attr('data-cyclelen')){
			options.cycle_length = parseInt(container.attr('data-cyclelen'));
		}
		var width     = container.width();
		var animating = false;
		var items     = container.children('.slide');
		var first     = $(items[0]);
		
		container.css({
			'position' : 'relative',
			'overflow' : 'hidden'
		});
		items.css({
			'position' : 'absolute',
			'width'    : width + 'px'
		});
		items.hide();
		first.show();
		
		return setTimeout(function(){
			cycle(items, 0);
		}, options.cycle_length);
	};
	
	$('.slideshow').slideShow();
};

(function($){
	slideshow($);
	chartbeat($);
	analytics($);
	handleExternalLinks($);
	$('.events').each(function(){eventsCallback($, $(this));});
	
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
	})();
	loadMoreSearchResults($);
})(jQuery);
