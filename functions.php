<?php
# Define stuff
define('THEME_URL', get_stylesheet_directory_uri());
define('THEME_DIR', get_stylesheet_directory());
define('THEME_STATIC_URL', THEME_URL.'/static');
define('THEME_IMG_URL', THEME_STATIC_URL.'/img');
define('THEME_JS_URL', THEME_STATIC_URL.'/js');
define('THEME_CSS_URL', THEME_STATIC_URL.'/css');
define('THEME_OPTIONS_GROUP', 'settings');
define('THEME_OPTIONS_NAME', 'theme');
define('THEME_OPTIONS_PAGE_TITLE', 'Theme Options');
define('FEED_FETCH_TIMEOUT', 10); // seconds

// Constant Contact add contact API url
// The username, pass and api key are contained in theme options
define('CC_ADD_CONTACT_API_URL', 'https://api.constantcontact.com/ws/customers/%s/contacts');

// Allow input and select tags in post bodies
$allowedposttags['input'] = Array('type'  => array(),
																	'value' => array(),
																	'id'    => array(),
																	'name'  => array(),
																	'class' => array());
$allowedposttags['select'] = Array('id'   => array(),
																	'name'  => array());
$allowedposttags['option'] = Array('id'   => array(),
																	'name'  => array(),
																	'value' => array());
//Custom Image Sizes
add_image_size('sidebar-feature', 303, 360, True);
add_image_size('sidebar-rss-thumb', 272, 200, True);

require_once('functions-base.php');     # Base theme functions
require_once('custom-taxonomies.php');	# Where per theme custom taxonomies are defined
require_once('custom-post-types.php');  # Where per theme post types are defined
require_once('shortcodes.php');         # Per theme shortcodes
require_once('functions-admin.php');    # Admin/login functions

$theme_options = get_option(THEME_OPTIONS_NAME);

define('GA_ACCOUNT', $theme_options['ga_account']);
define('GW_VERIFY', $theme_options['gw_verify']);
define('CB_UID', $theme_options['cb_uid']);
define('CB_DOMAIN', $theme_options['cb_domain']);

/**
 * Set config values including meta tags, registered custom post types, styles,
 * scripts, and any other statically defined assets that belong in the Config
 * object.
 **/
Config::$custom_post_types = array(
	'Page', 'Document', 'Person', 'Venue', 'Publication'
);

Config::$custom_taxonomies = array(
	'RosenOrganizationalGroups'
);

Config::$body_classes = array();

$categories = array();
$cats = get_categories();
	foreach( $cats as $category ) {
		$categories[$category->name] = $category->cat_ID;
}

/**
 * Configure theme settings, see abstract class Field's descendants for
 * available fields. -- functions-base.php
 **/
Config::$theme_settings = array(
	'Google' => array(
		new TextField(array(
			'name'        => 'Analytics Account',
			'id'          => THEME_OPTIONS_NAME.'[ga_account]',
			'description' => 'Example: <em>UA-9876543-21</em>. Leave blank for development.',
			'default'     => null,
			'value'       => $theme_options['ga_account'],
		)),
		new TextField(array(
			'name'        => 'WebMaster Verification',
			'id'          => THEME_OPTIONS_NAME.'[gw_verify]',
			'description' => 'Example <em>9Wsa3fspoaoRE8zx8COo48-GCMdi5Kd-1qFpQTTXSIw</em>',
			'default'     => null,
			'value'       => $theme_options['gw_verify'],
		)),
		new TextField(array(
			'name'        => 'Google Site Search Proxy URL',
			'id'          => THEME_OPTIONS_NAME.'[gss_proxy_url]',
			'description' => 'The proxy url for Google Site Search',
			'default'     => 'https://search.ucf.edu/proxy.php?type=google',
			'value'       => $theme_options['gss_proxy_url']
		)),
		new TextField(array(
			'name'        => 'GSA Search Domain',
			'id'          => THEME_OPTIONS_NAME.'[search_domain]',
			'description' => 'Domain to use for the built-in google search.  Useful for development or if the site needs to search a domain other than the one it occupies. Example <em>some.domain.com</em>',
			'default'     => null,
			'value'       => $theme_options['search_domain'],
			)),
		new TextField(array(
			'name'        => 'GSA Search Results Per Page',
			'id'          => THEME_OPTIONS_NAME.'[search_per_page]',
			'description' => 'Number of search results to show per page of results',
			'default'     => 10,
			'value'       => $theme_options['search_per_page'],
		)),
		new TextareaField(array(
			'name'        => 'Google Remarketing Code',
			'id'          => THEME_OPTIONS_NAME.'[ga_remarketing]',
			'description' => 'Paste your Google Remarketing Code here.',
			'value'       => $theme_options['ga_remarketing'],
		)),
	),
	'Chartbeat' => array(
		new TextField(array(
			'name'        => 'Chartbeat UID',
			'id'          => THEME_OPTIONS_NAME.'[cb_uid]',
			'description' => 'Example <em>1842</em>',
			'default'     => null,
			'value'       => $theme_options['cb_uid'],
		)),
		new TextField(array(
			'name'        => 'Chartbeat Domain',
			'id'          => THEME_OPTIONS_NAME.'[cb_domain]',
			'description' => 'Example <em>some.domain.com</em>',
			'default'     => null,
			'value'       => $theme_options['cb_domain'],
		)),
	),
	'Constant Contact' => array(
		new TextField(array(
			'name'        => 'Username:',
			'id'          => THEME_OPTIONS_NAME.'[constant_contact_username]',
			'description' => '',
			'default'     => null,
			'value'       => $theme_options['constant_contact_username']
		)),
		new TextField(array(
			'name'        => 'Password:',
			'id'          => THEME_OPTIONS_NAME.'[constant_contact_password]',
			'description' => '',
			'default'     => null,
			'value'       => $theme_options['constant_contact_password']
		)),
		new TextField(array(
			'name'        => 'API Key:',
			'id'          => THEME_OPTIONS_NAME.'[constant_contact_api_key]',
			'description' => '',
			'default'     => null,
			'value'       => $theme_options['constant_contact_api_key']
		)),
		new TextField(array(
			'name'        => 'List ID:',
			'id'          => THEME_OPTIONS_NAME.'[constant_contact_list]',
			'description' => 'ID of the email to add users to when they sign up for the newsletter. Defaults to 2 (Primary E-Mail List).',
			'default'     => null,
			'value'       => $theme_options['constant_contact_list']
		)),
	),
	'Events' => array(
		new TextField(array(
			'name'        => 'Events Feed URL',
			'id'          => THEME_OPTIONS_NAME.'[events_feed]',
			'description' => 'URL of the json feed for your calendar on the events system.',
			'default'     => 'http://events.ucf.edu/calendar/217/rosen-college-of-hospitality-management-events/upcoming/feed.json',
			'value'       => $theme_options['events_feed']
		)),
		new TextField(array(
			'name'        => 'Events Landing Page URL',
			'id'          => THEME_OPTIONS_NAME.'[events_url]',
			'description' => 'URL of your front-facing calendar on the events system.',
			'default'     => 'http://events.ucf.edu/calendar/217/rosen-college-of-hospitality-management-events/upcoming/',
			'value'       => $theme_options['events_url']
		)),
		new TextField(array(
			'name'        => 'Events Limit',
			'id'          => THEME_OPTIONS_NAME.'[events_max_items]',
			'description' => 'Max number of events to appear in events lists. Maximum is 8 events.',
			'default'     => '4',
			'value'       => $theme_options['events_max_items']
		)),
	),
	'Miscellaneous' => array(
		new TextField(array(
			'name'        => 'Number of Home Page Promos:',
			'id'          => THEME_OPTIONS_NAME.'[promo_post_num]',
			'description' => 'Controls how many promo posts will appear on the home page.',
			'value'       => $theme_options['promo_post_num']
		)),
		new CheckboxField(array(
			'name'        => 'Promo Categories to Exclude:',
			'id'          => THEME_OPTIONS_NAME.'[promo_post_categories]',
			'description' => 'Posts of categories specified here will be filtered out of promo posts.',
			'default'     => null,
			'choices'     => $categories,
			'value'       => $theme_options['promo_post_categories']
		)),
		new TextField(array(
			'name'        => 'UCF Today Rosen News RSS URL:',
			'id'          => THEME_OPTIONS_NAME.'[today_rosen_rss]',
			'description' => 'URL of the Rosen RSS feed on UCF Today that populated the sidebar news item.',
			'default'     => null,
			'value'       => $theme_options['today_rosen_rss']
		)),
		new TextField(array(
			'name'        => 'About Us Page Featured Group Name:',
			'id'          => THEME_OPTIONS_NAME.'[aboutus_featured_group]',
			'description' => 'The group name specified here will be featured on the About Us page as a series of
												images and titles rather than the default table format.',
			'default'     => 'Dean\'s Suite',
			'value'       => $theme_options['aboutus_featured_group']
		)),
		new TextField(array(
			'name'        => 'Gallery Feed URL:',
			'id'          => THEME_OPTIONS_NAME.'[gallery_feed_url]',
			'description' => 'Expects a Flickr RSS feed URL.',
			'default'     => 'http://api.flickr.com/services/feeds/photoset.gne?set=72157624283202883&nsid=36226710@N08&lang=en-us',
			'value'       => $theme_options['gallery_feed_url']
		)),
		new TextField(array(
			'name'        => 'Spotlight Title',
			'id'          => THEME_OPTIONS_NAME.'[home_page_spotlight_title]',
			'description' => 'Title will be contained in an H3.',
			'default'     => null,
			'value'       => $theme_options['home_page_spotlight_title'],
		)),
		new TextareaField(array(
			'name'        => 'Spotlight Content',
			'id'          => THEME_OPTIONS_NAME.'[home_page_spotlight_content]',
			'description' => '',
			'default'     => null,
			'value'       => $theme_options['home_page_spotlight_content'],
		)),
		new TextareaField(array(
			'name'        => 'Footer Content',
			'id'          => THEME_OPTIONS_NAME.'[footer_badge_content]',
			'description' => 'Area in right of footer, above the search form.',
			'default'     => null,
			'value'       => $theme_options['footer_badge_content'],
		)),
	)
);

Config::$links = array(
	array('rel' => 'shortcut icon', 'href' => THEME_IMG_URL.'/favicon.ico',),
	array('rel' => 'alternate', 'type' => 'application/rss+xml', 'href' => get_bloginfo('rss_url'),),
);

Config::$styles = array(
	array('admin' => True, 'src' => THEME_CSS_URL.'/admin.css',),
	THEME_CSS_URL.'/jquery-ui.css',
	THEME_CSS_URL.'/jquery-uniform.css',
	THEME_CSS_URL.'/blueprint-screen.css',
	THEME_CSS_URL.'/blueprint-responsive.css',
	array('media' => 'print', 'src' => THEME_CSS_URL.'/blueprint-print.css',),
	THEME_CSS_URL.'/yahoo-reset.css',
	THEME_CSS_URL.'/yahoo-fonts.css',
	THEME_CSS_URL.'/webcom-base.css',
	get_bloginfo('stylesheet_url'),
	THEME_CSS_URL.'/jquery.lightbox-0.5.css',
);

Config::$scripts = array(
	array('admin' => True, 'src' => THEME_JS_URL.'/admin.js',),
	'//universityheader.ucf.edu/bar/js/university-header.js',
	array('name' => 'jquery', 'src' => '//code.jquery.com/jquery-1.7.1.min.js',),
	THEME_JS_URL.'/jquery-ui.js',
	THEME_JS_URL.'/jquery-browser.js',
	THEME_JS_URL.'/jquery-uniform.js',
	'//events.ucf.edu/tools/script.js',
	array('name' => 'base-script',  'src' => THEME_JS_URL.'/webcom-base.js',),
	array('name' => 'theme-script', 'src' => THEME_JS_URL.'/script.js',),
	THEME_JS_URL.'/jquery.lightbox-0.5.pack.js',
);

Config::$metas = array(
	array('charset' => 'utf-8',),
);

if ((bool)$theme_options['gw_verify']){
	Config::$metas[] = array(
		'name'    => 'google-site-verification',
		'content' => htmlentities($theme_options['gw_verify']),
	);
}

function protocol_relative_attachment_url($url) {
    if (is_ssl()) {
        $url = str_replace('http://', 'https://', $url);
    }
    return $url;
}
add_filter('wp_get_attachment_url', 'protocol_relative_attachment_url');


/* Fetch events (json) */
function get_events($feed, $start, $limit){
	// Set a timeout
	$opts = array('http' => array(
						'method'  => 'GET',
						'timeout' => FEED_FETCH_TIMEOUT
	));
	$context = stream_context_create($opts);

	// Grab the events feed
	$raw_events = file_get_contents($feed, false, $context);
	if ($raw_events) {
		$events = json_decode($raw_events, TRUE);
		$events = array_slice($events, $start, $limit);
		return $events;
	}
	else { return NULL; }
}

/*
 * Display events data (from get_events()).
 * There is a weird execution order problem with this theme--
 * Fetch events from home.php where $theme_options is available.
 */
function display_events($events, $start=null, $limit=null) {
	$url     = $theme_options['events_url'];
	$feed    = $theme_options['events_feed'];
	$start	 = ($start) ? $start : 0;

	// Check for a given limit, then a set Options value, then if none exist, set to 4
	if ($limit) {
		$limit = intval($limit);
	}
	elseif ($theme_options['events_max_items']) {
		$limit = intval($theme_options['events_max_items']);
		if ($limit > 4) {
			$limit = 4;
		}
	}
	else {
		$limit = 4;
	}

	if($events !== NULL && count($events)): ?>
		<ul class="events clearfix">
			<?php foreach($events as $item):
				$start 		= new DateTime($item['starts']);
				$day 		= $start->format('d');
				$month 		= $start->format('M');
				$link 		= $item['url'];
				$title		= $item['title'];
			?>
			<li class="event">
				<div class="date">
					<span class="month"><?=$month?></span>
					<span class="day"><?=$day?></span>
				</div>
				<a class="title" href="<?=$link?>"><?=$title?></a>
				<div class="end"></div>
			</li>
			<?php endforeach;?>
		</ul>
	<?php else:?>
		<p>There are no upcoming events at this time.</p>
	<?php endif;
}

/**
 * Disable kses filtering to allow <iframe>, <script> tags, etc.
 * Necessary to allow non-superusers to save Publication embed codes.
 **/
add_filter('init', 'kses_remove_filters');

function get_remarketing_code() {
	global $theme_options;
	if ( isset( $theme_options['ga_remarketing'] ) ) {
		return $theme_options['ga_remarketing'];
	} else {
		return '';
	}
}
