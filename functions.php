<?php
# Define stuff
define('THEME_URL', get_bloginfo('stylesheet_directory'));
define('THEME_DIR', get_stylesheet_directory());
define('THEME_STATIC_URL', THEME_URL.'/static');
define('THEME_IMG_URL', THEME_STATIC_URL.'/img');
define('THEME_JS_URL', THEME_STATIC_URL.'/js');
define('THEME_CSS_URL', THEME_STATIC_URL.'/css');
define('THEME_OPTIONS_GROUP', 'settings');
define('THEME_OPTIONS_NAME', 'theme');
define('THEME_OPTIONS_PAGE_TITLE', 'Theme Options');
define('EVENT_PROXY_URL', THEME_STATIC_URL.'/event_proxy.php');

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
	'Page', 'Form', 'Person'
);

Config::$custom_taxonomies = array(
	'RosenOrganizationalGroups'
);

Config::$body_classes = array();

/**
 * Configure theme settings, see abstract class Field's descendants for
 * available fields. -- functions-base.php
 **/
Config::$theme_settings = array(
	new TextField(array(
		'name'        => 'Google Analytics Account',
		'id'          => THEME_OPTIONS_NAME.'[ga_account]',
		'description' => 'Example: <em>UA-9876543-21</em>. Leave blank for development.',
		'default'     => null,
		'value'       => $theme_options['ga_account'],
	)),
	new TextField(array(
		'name'        => 'Google WebMaster Verification',
		'id'          => THEME_OPTIONS_NAME.'[gw_verify]',
		'description' => 'Example <em>9Wsa3fspoaoRE8zx8COo48-GCMdi5Kd-1qFpQTTXSIw</em>',
		'default'     => null,
		'value'       => $theme_options['gw_verify'],
	)),
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
	new TextField(array(
		'name'        => 'Number of Home Page Promos:',
		'id'          => THEME_OPTIONS_NAME.'[promo_post_num]',
		'description' => 'Controls how many promo posts will appear on the home page.',
		'value'       => $theme_options['promo_post_num']
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
		'name'        => 'Constant Contact Username:',
		'id'          => THEME_OPTIONS_NAME.'[constant_contact_username]',
		'description' => '',
		'default'     => null,
		'value'       => $theme_options['constant_contact_username']
	)),
	new TextField(array(
		'name'        => 'Constant Contact Password:',
		'id'          => THEME_OPTIONS_NAME.'[constant_contact_password]',
		'description' => '',
		'default'     => null,
		'value'       => $theme_options['constant_contact_password']
	)),
	new TextField(array(
		'name'        => 'Constant Contact API Key:',
		'id'          => THEME_OPTIONS_NAME.'[constant_contact_api_key]',
		'description' => '',
		'default'     => null,
		'value'       => $theme_options['constant_contact_api_key']
	)),
	new TextField(array(
		'name'        => 'Constant Contact List ID:',
		'id'          => THEME_OPTIONS_NAME.'[constant_contact_list]',
		'description' => 'ID of the email to add users to when they sign up for the newsletter. Defaults to 2 (Primary E-Mail List).',
		'default'     => null,
		'value'       => $theme_options['constant_contact_list']
	)),
	new TextField(array(
		'name'        => 'Search Domain',
		'id'          => THEME_OPTIONS_NAME.'[search_domain]',
		'description' => 'Domain to use for the built-in google search.  Useful for development or if the site needs to search a domain other than the one it occupies. Example <em>some.domain.com</em>',
		'default'     => null,
		'value'       => $theme_options['search_domain'],
		)),
	new TextField(array(
		'name'        => 'Search Results Per Page',
		'id'          => THEME_OPTIONS_NAME.'[search_per_page]',
		'description' => 'Number of search results to show per page of results',
		'default'     => 10,
		'value'       => $theme_options['search_per_page'],
	)),
	new TextField(array(
		'name'        => 'Catering Spotlight Title',
		'id'          => THEME_OPTIONS_NAME.'[catering_spotlight_title]',
		'description' => 'Title will be contained in an H3.',
		'default'     => null,
		'value'       => $theme_options['catering_spotlight_title'],
	)),
	new TextareaField(array(
		'name'        => 'Catering Spotlight Content',
		'id'          => THEME_OPTIONS_NAME.'[catering_spotlight_content]',
		'description' => '',
		'default'     => null,
		'value'       => $theme_options['catering_spotlight_content'],
	)),
);

Config::$links = array(
	array('rel' => 'shortcut icon', 'href' => THEME_IMG_URL.'/favicon.ico',),
	array('rel' => 'alternate', 'type' => 'application/rss+xml', 'href' => get_bloginfo('rss_url'),),
);

Config::$styles = array(
	array('admin' => True, 'src' => THEME_CSS_URL.'/admin.css',),
	'http://universityheader.ucf.edu/bar/css/bar.css',
	THEME_CSS_URL.'/jquery-ui.css',
	THEME_CSS_URL.'/jquery-uniform.css',
	THEME_CSS_URL.'/blueprint-screen.css',
	array('media' => 'print', 'src' => THEME_CSS_URL.'/blueprint-print.css',),
	THEME_CSS_URL.'/yahoo-reset.css',
	THEME_CSS_URL.'/yahoo-fonts.css',
	THEME_CSS_URL.'/webcom-base.css',
	get_bloginfo('stylesheet_url'),
	THEME_CSS_URL.'/jquery.lightbox-0.5.css',
);

Config::$scripts = array(
	array('admin' => True, 'src' => THEME_JS_URL.'/admin.js',),
	'http://universityheader.ucf.edu/bar/js/university-header.js',
	array('name' => 'jquery', 'src' => 'http://code.jquery.com/jquery-1.6.1.min.js',),
	THEME_JS_URL.'/jquery-ui.js',
	THEME_JS_URL.'/jquery-browser.js',
	THEME_JS_URL.'/jquery-uniform.js',
	'http://events.ucf.edu/tools/script.js',
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

#Add custom javascript to admin
function provost_admin_scripts(){
	wp_enqueue_script('custom-admin', PROVOST_JS_URL.'/admin.js', array('jquery'), False, True);
}
add_action('admin_enqueue_scripts', 'provost_admin_scripts');