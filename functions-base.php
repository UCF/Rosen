<?php

/**
 * The Config class provides a set of static properties and methods which store
 * and facilitate configuration of the theme.
 **/
class ArgumentException extends Exception{}
class Config{
	static
		$body_classes			 = array(), # Body classes
		$theme_settings		 = array(), # Theme settings
		$custom_post_types = array(), # Custom post types to register
		$custom_taxonomies = array(), # Custom taxonomies to register
		$styles						 = array(), # Stylesheets to register
		$scripts					 = array(), # Scripts to register
		$links						 = array(), # <link>s to include in <head>
		$metas						 = array(); # <meta>s to include in <head>


	/**
	 * Creates and returns a normalized name for a resource url defined by $src.
	 **/
	static function generate_name($src, $ignore_suffix=''){
		$base = basename($src, $ignore_suffix);
		$name = slug($base);
		return $name;
	}


	/**
	 * Registers a stylesheet with built-in wordpress style registration.
	 * Arguments to this can either be a string or an array with required css
	 * attributes.
	 *
	 * A string argument will be treated as the src value for the css, and all
	 * other attributes will default to the most common values.	 To override
	 * those values, you must pass the attribute array.
	 *
	 * Array Argument:
	 * $attr = array(
	 *		'name'	=> 'theme-style',	 # Wordpress uses this to identify queued files
	 *		'media' => 'all',					 # What media types this should apply to
	 *		'admin' => False,					 # Should this be used in admin as well?
	 *		'src'		=> 'http://some.domain/style.css',
	 * );
	 **/
	static function add_css($attr){
		# Allow string arguments, defining source.
		if (is_string($attr)){
			$new				= array();
			$new['src'] = $attr;
			$attr				= $new;
		}

		if (!isset($attr['src'])){
			throw new ArgumentException('add_css expects argument array to contain key "src"');
		}
		$default = array(
			'name'	=> self::generate_name($attr['src'], '.css'),
			'media' => 'all',
			'admin' => False,
		);
		$attr = array_merge($default, $attr);

		$is_admin = (is_admin() or is_login());

		if (
			($attr['admin'] and $is_admin) or
			(!$attr['admin'] and !$is_admin)
		){
			wp_deregister_style($attr['name']);
			wp_enqueue_style($attr['name'], $attr['src'], null, null, $attr['media']);
		}
	}


	/**
	 * Functions similar to add_css, but appends scripts to the footer instead.
	 * Accepts a string or array argument, like add_css, with the string
	 * argument assumed to be the src value for the script.
	 *
	 * Array Argument:
	 * $attr = array(
	 *		'name'	=> 'jquery',	# Wordpress uses this to identify queued files
	 *		'admin' => False,			# Should this be used in admin as well?
	 *		'src'		=> 'http://some.domain/style.js',
	 * );
	 **/
	static function add_script($attr){
		# Allow string arguments, defining source.
		if (is_string($attr)){
			$new				= array();
			$new['src'] = $attr;
			$attr				= $new;
		}

		if (!isset($attr['src'])){
			throw new ArgumentException('add_script expects argument array to contain key "src"');
		}
		$default = array(
			'name'	=> self::generate_name($attr['src'], '.js'),
			'admin' => False,
		);
		$attr = array_merge($default, $attr);

		$is_admin = (is_admin() or is_login());

		if (
			($attr['admin'] and $is_admin) or
			(!$attr['admin'] and !$is_admin)
		){
			# Override previously defined scripts
			wp_deregister_script($attr['name']);
			wp_enqueue_script($attr['name'], $attr['src'], null, null, True);
		}
	}
}


abstract class Field{
	function __construct($attr){
		$this->name				 = @$attr['name'];
		$this->id					 = @$attr['id'];
		$this->value			 = @$attr['value'];
		$this->description = @$attr['description'];
		$this->default		 = @$attr['default'];

		if ($this->value === null){
			$this->value = $this->default;
		}
	}
}


abstract class ChoicesField extends Field{
	function __construct($attr){
		$this->choices = @$attr['choices'];
		parent::__construct($attr);
	}
}

class TextField extends Field{
	function html(){
		ob_start();
		?>
		<label class="block" for="<?=htmlentities($this->id)?>"><?=__($this->name)?></label>
		<input type="text" id="<?=htmlentities($this->id)?>" name="<?=htmlentities($this->id)?>" value="<?=htmlentities($this->value)?>" />
		<?php if($this->description):?>
		<p class="description"><?=__($this->description)?></p>
		<?php endif;?>
		<?php
		return ob_get_clean();
	}
}

class TextareaField extends Field{
	function html(){
		ob_start();
		?>
		<label class="block" for="<?=htmlentities($this->id)?>"><?=__($this->name)?></label>
		<textarea id="<?=htmlentities($this->id)?>" name="<?=htmlentities($this->id)?>"><?=htmlentities($this->value)?></textarea>
		<?php if($this->description):?>
		<p class="description"><?=__($this->description)?></p>
		<?php endif;?>
		<?php
		return ob_get_clean();
	}
}

class SelectField extends ChoicesField{
	function html(){
		ob_start();
		?>
		<label class="block" for="<?=$this->id?>"><?=__($this->name)?></label>
		<select name="<?=htmlentities($this->id)?>" id="<?=htmlentities($this->id)?>">
			<?php foreach($this->choices as $key=>$value):?>
			<option<?php if($this->value == $value):?> selected="selected"<?php endif;?> value="<?=htmlentities($value)?>"><?=htmlentities($key)?></option>
			<?php endforeach;?>
		</select>
		<?php if($this->description):?>
		<p class="description"><?=__($this->description)?></p>
		<?php endif;?>
		<?php
		return ob_get_clean();
	}
}

class RadioField extends ChoicesField{
	function html(){
		ob_start();
		?>
		<label class="block"><?=__($this->name)?></label>
		<ul class="radio-list">
			<?php $i = 0; foreach($this->choices as $key=>$value): $id = htmlentities($this->id).'_'.$i++;?>
			<li>
				<input<?php if($this->value == $value):?> checked="checked"<?php endif;?> type="radio" name="<?=htmlentities($this->id)?>" id="<?=$id?>" value="<?=htmlentities($value)?>" />
				<label for="<?=$id?>"><?=htmlentities($key)?></label>
			</li>
			<?php endforeach;?>
		</ul>
		<?php if($this->description):?>
		<p class="description"><?=__($this->description)?></p>
		<?php endif;?>
		<?php
		return ob_get_clean();
	}
}

class CheckboxField extends ChoicesField{
	function html(){
		ob_start();
		?>
		<label class="block"><?=__($this->name)?></label>
		<ul class="checkbox-list">
			<?php $i = 0; foreach($this->choices as $key=>$value): $id = htmlentities($this->id).'_'.$i++;?>
			<li>
				<input<?php if(is_array($this->value) and in_array($value, $this->value)):?> checked="checked"<?php endif;?> type="checkbox" name="<?=htmlentities($this->id)?>[]" id="<?=$id?>" value="<?=htmlentities($value)?>" />
				<label for="<?=$id?>"><?=htmlentities($key)?></label>
			</li>
			<?php endforeach;?>
		</ul>
		<?php if($this->description):?>
		<p class="description"><?=__($this->description)?></p>
		<?php endif;?>
		<?php
		return ob_get_clean();
	}
}


function cleanup($content){
	#Remove incomplete tags at start and end
	$content = preg_replace('/^<\/p>[\s]*/i', '', $content);
	$content = preg_replace('/[\s]*<p>$/i', '', $content);
	$content = preg_replace('/^<br \/>/i', '', $content);
	$content = preg_replace('/<br \/>$/i', '', $content);

	#Remove paragraph and linebreak tags wrapped around shortcodes
	$content = preg_replace('/(<p>|<br \/>)\[/i', '[', $content);
	$content = preg_replace('/\](<\/p>|<br \/>)/i', ']', $content);

	#Remove empty paragraphs
	$content = preg_replace('/<p><\/p>/i', '', $content);

	return $content;
}


/**
 * Given a mimetype, will attempt to return a string representing the
 * application it is associated with.
 **/
function mimetype_to_application($mimetype){
	switch($mimetype){
		default:
			$type = 'document';
			break;
		case 'text/html':
			$type = "html";
			break;
		case 'application/zip':
			$type = "zip";
			break;
		case 'application/pdf':
			$type = 'pdf';
			break;
		case 'application/msword':
		case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
			$type = 'word';
			break;
		case 'application/msexcel':
		case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
			$type = 'excel';
			break;
		case 'application/vnd.ms-powerpoint':
		case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
			$type = 'powerpoint';
			break;
	}
	return $type;
}


/**
 * Fetches objects defined by arguments passed, outputs the objects according
 * to the objectsToHTML method located on the object.  Used by the auto
 * generated shortcodes enabled on custom post types. See also:
 *
 *   CustomPostType::objectsToHTML
 *   CustomPostType::toHTML
 *
 **/
function sc_object_list($attr, $default_content=null){
	if (!is_array($attr)){return '';}

	# set defaults and combine with passed arguments
	$defaults = array(
		'type'  => null,
		'limit' => -1,
		'join'  => 'or',
	);
	$options = array_merge($defaults, $attr);

	# verify options
	if ($options['type'] == null){
		return '<p class="error">No type defined for object list.</p>';
	}
	if (!is_numeric($options['limit'])){
		return '<p class="error">Invalid limit argument, must be a number.</p>';
	}
	if (!in_array(strtoupper($options['join']), array('AND', 'OR'))){
		return '<p class="error">Invalid join type, must be one of "and" or "or".</p>';
	}
	if (null == ($class = get_custom_post_type($options['type']))){
		return '<p class="error">Invalid post type.</p>';
	}

	# get taxonomies and translation
	$translate  = array(
		'tags'       => 'post_tag',
		'categories' => 'category',
	);
	$taxonomies = array_diff(array_keys($attr), array_keys($defaults));

	# assemble taxonomy query
	$tax_queries             = array();
	$tax_queries['relation'] = strtoupper($options['join']);

	foreach($taxonomies as $tax){
		$terms = $options[$tax];
		$terms = trim(preg_replace('/\s+/', ' ', $terms));
		$terms = explode(' ', $terms);

		if (array_key_exists($tax, $translate)){
			$tax = $translate[$tax];
		}

		$tax_queries[] = array(
			'taxonomy' => $tax,
			'field'    => 'slug',
			'terms'    => $terms,
		);
	}

	# perform query
	$query_array = array(
		'tax_query'      => $tax_queries,
		'post_status'    => 'publish',
		'post_type'      => $options['type'],
		'posts_per_page' => $options['limit'],
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	);
	$query = new WP_Query($query_array);
	$class = new $class;


	global $post;
	$objects = array();
	while($query->have_posts()){
		$query->the_post();
		$objects[] = $post;
	}
	wp_reset_postdata();

	array_shift($tax_queries);
	if (count($objects)){
		$html = $class->objectsToHTML($objects, $tax_queries);
	}else{
		$html = $default_content;
	}
	return $html;
}


/**
 * Creates an array
 **/
function shortcodes(){
	$file = file_get_contents(THEME_DIR.'/shortcodes.php');

	$documentation = "\/\*\*(?P<documentation>.*?)\*\*\/";
	$declaration	 = "function[\s]+(?P<declaration>[^\(]+)";

	# Auto generated shortcode documentation.
	$codes = array();
	$auto	 = array_filter(installed_custom_post_types(), create_function('$c', '
		return $c->options("use_shortcode");
	'));
	foreach($auto as $code){
		$scode	= $code->options('name').'-list';
		$plural = $code->options('plural_name');
		$doc = <<<DOC
 Outputs a list of {$plural} filtered by arbitrary taxonomies, for example a tag
or category.  A default output for when no objects matching the criteria are
found.

 Example:
 # Output a maximum of 5 items tagged foo or bar, with a default output.
 [{$scode} tags="foo bar" limit="5"]No {$plural} were found.[/{$scode}]

 # Output all objects categorized as foo
 [{$scode} categories="foo"]

 # Output all objects matching the terms in the custom taxonomy named foo
 [{$scode} foo="term list example"]

 # Outputs all objects found categorized as staff and tagged as small.
 [{$scode} limit="5" join="and" categories="staff" tags="small"]
DOC;
		$codes[] = array(
			'documentation' => $doc,
			'shortcode'			=> $scode,
		);
	}

	# Defined shortcode documentation
	$found = preg_match_all("/{$documentation}\s*{$declaration}/is", $file, $matches);
	if ($found){
		foreach ($matches['declaration'] as $key=>$match){
			$codes[$match]['documentation'] = $matches['documentation'][$key];
			$codes[$match]['shortcode']			= str_replace(
				array('sc_', '_',),
				array('', '-',),
				$matches['declaration'][$key]
			);
		}
	}
	return $codes;
}


function admin_help(){
	global $post;
	$shortcodes = shortcodes();
	switch($post->post_title){
		default:
			?>
			<h2>Available shortcodes:</h2>
			<ul>
				<?php foreach($shortcodes as $sc):?>
				<li>
					<h3><?=$sc['shortcode']?></h3>
					<p><?=nl2br(str_replace(' *', '', htmlentities($sc['documentation'])))?></p>
				</li>
				<?php endforeach;?>
			</ul>
			<?php
			break;
	}
}


function admin_meta_boxes(){
	global $post;
	add_meta_box('page-help', 'Help', 'admin_help', 'page', 'normal', 'high');
}
add_action('admin_init', 'admin_meta_boxes');


/**
 * Returns true if the current request is on the login screen.
 **/
function is_login(){
	return in_array($GLOBALS['pagenow'], array(
			'wp-login.php',
			'wp-register.php',
	));
}


/**
 * Given an arbitrary number of arguments, will return a string with the
 * arguments dumped recursively.
 **/
function dump(){
	$args = func_get_args();
	$out	= array();
	foreach($args as $arg){
		$out[] = print_r($arg, True);
	}
	$out = implode("<br />", $out);
	return "<pre>{$out}</pre>";
}


/**
 * Will add a debug comment to the output when the get variable debug is set.
 * Any value, including null, is enough to trigger it.
 **/
function debug($string){
	if (!isset($_GET['debug'])){
		return;
	}
	print "<!-- DEBUG: {$string} -->\n";
}


/**
 * Responsible for running code that needs to be executed as wordpress is
 * initializing.	Good place to register scripts, stylesheets, theme elements,
 * etc.
 **/
function __init__(){
	add_theme_support('menus');
	add_theme_support('post-thumbnails');
	//register_nav_menu('header-menu', __('Header Menu'));
	register_nav_menu('student-resources', __('Student Resources'));
	register_nav_menu('academic-degrees', __('Academic Degrees'));
	register_nav_menu('sidebar-nav-menu', __('Sidebar Navigation Menu'));
	register_nav_menu('sidebar-social-menu', __('Sidebar Social Menu'));
	register_nav_menu('footer-menu-left', __('Footer Menu Left'));
	register_nav_menu('footer-menu-right', __('Footer Menu Right'));
	register_sidebar(array(
		'name'					=> __('Sidebar'),
		'id'						=> 'sidebar',
		'description'		=> 'Sidebar found throughout site',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'	=> '</div>',
	));
	foreach(Config::$styles as $style){Config::add_css($style);}
	foreach(Config::$scripts as $script){Config::add_script($script);}
}
add_action('after_setup_theme', '__init__');


/**
 * Uses the google search appliance to search the current site or the site
 * defined by the argument $domain.
 **/
function get_search_results(
		$query,
		$start=null,
		$per_page=null,
		$domain=null
	){

	$proxy_url = get_theme_option('gss_proxy_url');

	$start      = ($start) ? $start : 1;
	$per_page   = ($per_page) ? $per_page : 10;
	$domain     = ($domain) ? $domain : $_SERVER['SERVER_NAME'];
	$results    = array(
		'number' => 0,
		'items'  => array(),
	);
	$query     = trim($query);
	$per_page  = (int)$per_page;
	$start     = (int)$start;
	$query     = urlencode($query);
	$arguments = array(
		'num'        => $per_page,
		'start'      => $start,
		'siteSearch' => $domain,
		'q'          => $query,
	);

	if (strlen($query) > 0){
		$query_string = http_build_query($arguments);
		$url          = $proxy_url.'&'.$query_string;
		$response     = file_get_contents($url);

		if ($response){
			$json   = json_decode( $response );
			$items = $json->items;
			$total = $json->searchInformation->totalResults;

			$temp = array();

			if ($total){
				foreach($items as $result){
					$item            = array();
					$item['url']     = str_replace('https', 'http', $result->url);
					$item['title']   = $result->title;
					$item['snippet'] = $result->snippet;
					$item['mime']    = $result->mime;
					$temp[]          = $item;
				}
				$results['items'] = $temp;
			}
			$results['number'] = $total;
		}
	}

	return $results;
}


/**
 * Appends formatting styles for tinyMCE editor box.
 **/
function editor_styles($css){
	$css	 = array_map('trim', explode(',', $css));
	$css[] = THEME_CSS_URL.'/formatting.css';
	$css	 = implode(',', $css);
	return $css;
}
add_filter('mce_css', 'editor_styles');


/**
 * Edits second row of buttons in tinyMCE editor.
 **/
function editor_format_options($row){
	$found = array_search('underline', $row);
	if (False !== $found){
		unset($row[$found]);
	}
	return $row;
}
add_filter('mce_buttons_2', 'editor_format_options');

/**
 * Remove paragraph tag from excerpts
 **/
remove_filter('the_excerpt', 'wpautop');


/**
 * Really get the post type. A post type of revision will return it's parent
 * post type.
 **/
function post_type($post){
	if (is_int($post)){
		$post = get_post($post);
	}

	# check post_type field
	$post_type = $post->post_type;

	if ($post_type === 'revision'){
		$parent    = (int)$post->post_parent;
		$post_type = post_type($parent);
	}

	return $post_type;
}


/**
 * Will return a string $s normalized to a slug value.	The optional argument,
 * $spaces, allows you to define what spaces and other undesirable characters
 * will be replaced with.	 Useful for content that will appear in urls or
 * turning plain text into an id.
 **/
function slug($s, $spaces='-'){
	$s = strtolower($s);
	$s = preg_replace('/[-_\s\.]/', $spaces, $s);
	return $s;
}


/**
 * Given a name will return the custom post type's class name
 **/
function get_custom_post_type($name){
	$installed = installed_custom_post_types();
	foreach($installed as $object){
		if ($object->options('name') == $name){
			return get_class($object);
		}
	}
	return null;
}

/**
 * Custom function using some of WordPreses buit-ins. Only displays
 * submenus of the link of the current page.
 *
 * TODO: Clean up and make submenu exclusion optional.
 *
 **/
function get_menu($name, $classes=null, $id=null, $top_level_only = False){
	global $post;

	$locations = get_nav_menu_locations();
	$menu			 = @$locations[$name];

	if (!$menu){
		return "<div class='error'>No menu location found with name '{$name}'.</div>";
	}

	$items = wp_get_nav_menu_items($menu);

	$output           = '';
	$parent_ids       = array();
	$top_level_obj_id = 0;
	$top_count        = 0;
	$show_num         = 0;
	for($i = 0; $i < count($items);$i++) {
		$item = $items[$i];
		$prev = isset($items[$i - 1]) ? $items[$i - 1] : null;
		$next = isset($items[$i + 1]) ? $items[$i + 1] : null;
		$menu_item_parent = (int)$item->menu_item_parent;

		if($menu_item_parent == 0) {
			$top_count++;
			// Going all the way up
			while(count($parent_ids) > 0) {
				array_pop($parent_ids);
				$output .= '</ul></li>';
			}
			$output .= '<li class="'.implode(' ', $item->classes).'"><a href="'.$item->url.'">'.$item->title.'</a>';
			$top_level_obj_id = (int)$item->object_id;
		} else if(!$top_level_only){
			if($menu_item_parent == (int)$prev->menu_item_parent) {
				// Same level
				$output .= '<li class="'.implode(' ', $item->classes).'"><a href="'.$item->url.'">'.$item->title.'</a>';
			} else if(in_array($menu_item_parent, $parent_ids)) {
				// Going Up
				while($menu_item_parent != $parent_ids[count($parent_ids) - 1]) {
					array_pop($parent_ids);
					$output .= '</li></ul>';
				}
			} else { // Going Down
				array_push($parent_ids, $prev->ID);
				$output .= '<ul class="__hide'.$top_count.'"><li class="'.implode(' ', $item->classes).'"><a href="'.$item->url.'">'.$item->title.'</a>';
			}
		}
		if((int)$item->object_id == $post->ID) {
			$show_num = $top_count;
		}
	}

	while(count($parent_ids) > 0) {
		array_pop($parent_ids);
		$output .= '</ul></li>';
	}

	$output = str_replace('class="__hide'.$show_num.'"', '', $output);
	$output = preg_replace('/__hide\d+/', 'hide', $output);

	return '<ul id="'.$id.'" class="'.$classes.'">'.$output.'</ul>';
}

/**
 * Get the title of a menu specified by it's slug
 *
 * @return string
 * @author Chris Conover
 **/
function get_menu_title($name)
{
	$locations = get_nav_menu_locations();
	if(isset($locations[$name])) {
		$menu_id = $locations[$name];
		if( ($term = get_term_by('id', $menu_id, 'nav_menu')) !== False) {
			return $term->name;
		}
	}
}

/**
 * Creates an arbitrary html element.	 $tag defines what element will be created
 * such as a p, h1, or div.	 $attr is an array defining attributes and their
 * associated values for the tag created. $content determines what data the tag
 * wraps.	 And $self_close defines whether or not the tag should close like
 * <tag></tag> (False) or <tag /> (True).
 **/
function create_html_element($tag, $attr=array(), $content=null, $self_close=True){
	$attr_str = create_attribute_string($attr);
	if ($content){
		$element = "<{$tag}{$attr_str}>{$content}</{$tag}>";
	}else{
		if ($self_close){
			$element = "<{$tag}{$attr_str}/>";
		}else{
			$element = "<{$tag}{$attr_str}></{$tag}>";
		}
	}

	return $element;
}


/**
 * Creates a string of attributes and their values from the key/value defined by
 * $attr.	 The string is suitable for use in html tags.
 **/
function create_attribute_string($attr){
	$attr_string = '';
	foreach($attr as $key=>$value){
		$attr_string .= " {$key}='{$value}'";
	}
	return $attr_string;
}


/**
 * Indent content passed by n indentations.
 **/
function indent($html, $n){
	$tabs = str_repeat("\t", $n);
	$html = explode("\n", $html);
	foreach($html as $key=>$line){
		$html[$key] = $tabs.trim($line);
	}
	$html = implode("\n", $html);
	return $html;
}


/**
 * Footer content
 **/
function footer_($tabs=2){
	ob_start();
	wp_footer();
	$html = ob_get_clean();
	return indent($html, $tabs);
}


/**
 * Header content
 **/
function header_($tabs=2){
	ob_start();
	wp_head();
	print header_title()."\n";
	print header_meta()."\n";
	print header_links()."\n";
	$html = ob_get_clean();
	$html = indent($html, $tabs);
	return $html;
}


/**
 * Handles generating the meta tags configured for this theme.
 **/
function header_meta(){
	$metas		 = Config::$metas;
	$meta_html = array();
	$defaults	 = array();

	foreach($metas as $meta){
		$meta				 = array_merge($defaults, $meta);
		$meta_html[] = create_html_element('meta', $meta);
	}
	$meta_html = implode("\n", $meta_html);
	return $meta_html;
}


/**
 * Handles generating the link tags configured for this theme.
 **/
function header_links(){
	$links			= Config::$links;
	$links_html = array();
	$defaults		= array();

	foreach($links as $link){
		$link					= array_merge($defaults, $link);
		$links_html[] = create_html_element('link', $link, null, False);
	}

	$links_html = implode("\n", $links_html);
	return $links_html;
}


/**
 * Generates a title based on context page is viewed.
 **/
function header_title(){
	$site_name = get_bloginfo('name');
	$separator = '|';

	if ( is_single() ) {
		$content = get_person_title( single_post_title( '', FALSE ));
	}
	elseif ( is_home() || is_front_page() ) {
		$content = get_bloginfo('description');
	}
	elseif ( is_page() ) {
		$content = get_person_title( single_post_title('', FALSE) );
	}
	elseif ( is_search() ) {
		$content = __('Search Results for:');
		$content .= ' ' . esc_html(stripslashes(get_search_query()));
	}
	elseif ( is_category() ) {
		$content = __('Category Archives:');
		$content .= ' ' . single_cat_title("", false);;
	}
	elseif ( is_404() ) {
		$content = __('Not Found');
	}
	else {
		$content = get_bloginfo('description');
	}

	if (get_query_var('paged')) {
		$content .= ' ' .$separator. ' ';
		$content .= 'Page';
		$content .= ' ';
		$content .= get_query_var('paged');
	}

	if($content) {
		if (is_home() || is_front_page()) {
			$elements = array(
				'site_name' => $site_name,
				'separator' => $separator,
				'content' => $content,
			);
		} else {
			$elements = array(
				'content' => $content,
			);
		}
	} else {
		$elements = array(
			'site_name' => $site_name,
		);
	}

	// But if they don't, it won't try to implode
	if(is_array($elements)) {
	$doctitle = implode(' ', $elements);
	}
	else {
	$doctitle = $elements;
	}

	$doctitle = "<title>". $doctitle ."</title>";

	return $doctitle;
}



/**
 * Returns string to use for value of class attribute on body tag
 **/
function body_classes(){
	$classes = Config::$body_classes;
	$classes = array_merge($classes, browser_classes());

	return implode(' ', $classes);
}


/**
 * Returns a list of classes to determined by current user agent string, for
 * platform specific purposes.	Pulled from thematic wordpress theme
 * (http://themeshaper.com/)
 **/
function browser_classes() {
	$browser = $_SERVER[ 'HTTP_USER_AGENT' ];

	// Mac, PC ...or Linux
	if ( preg_match( "/Mac/", $browser ) ){
		$classes[] = 'mac';
	} elseif ( preg_match( "/Windows/", $browser ) ){
		$classes[] = 'windows';
	} elseif ( preg_match( "/Linux/", $browser ) ) {
		$classes[] = 'linux';
	} else {
		$classes[] = 'unknown-os';
	}

	// Checks browsers in this order: Chrome, Safari, Opera, MSIE, FF
	if ( preg_match( "/Chrome/", $browser ) ) {
		$classes[] = 'chrome';

		preg_match( "/Chrome\/(\d.\d)/si", $browser, $matches);
		$ch_version = 'ch' . str_replace( '.', '-', $matches[1] );
		$classes[] = $ch_version;
	} elseif ( preg_match( "/Safari/", $browser ) ) {
		$classes[] = 'safari';

		preg_match( "/Version\/(\d.\d)/si", $browser, $matches);
		$sf_version = 'sf' . str_replace( '.', '-', $matches[1] );
		$classes[] = $sf_version;
	} elseif ( preg_match( "/Opera/", $browser ) ) {
		$classes[] = 'opera';

		preg_match( "/Opera\/(\d.\d)/si", $browser, $matches);
		$op_version = 'op' . str_replace( '.', '-', $matches[1] );
		$classes[] = $op_version;
	} elseif ( preg_match( "/MSIE/", $browser ) ) {
		$classes[] = 'ie';

		if( preg_match( "/MSIE 6.0/", $browser ) ) {
			$classes[] = 'ie6';
		} elseif ( preg_match( "/MSIE 7.0/", $browser ) ){
			$classes[] = 'ie7';
		} elseif ( preg_match( "/MSIE 8.0/", $browser ) ){
			$classes[] = 'ie8';
		}
	} elseif ( preg_match( "/Firefox/", $browser ) && preg_match( "/Gecko/", $browser ) ) {
			$classes[] = 'firefox';

			preg_match( "/Firefox\/(\d)/si", $browser, $matches);
			$ff_version = 'ff' . str_replace( '.', '-', $matches[1] );
			$classes[] = $ff_version;
	} else {
		$classes[] = 'unknown-browser';
	}
	// return the $classes array
	return $classes;
}


/**
 * When called, prevents direct loads of the value of $page.
 **/
function disallow_direct_load($page){
	if ($page == basename($_SERVER['SCRIPT_FILENAME'])){
		die('No');
	}
}


/**
 * Adding custom post types to the installed array defined in this function
 * will activate and make available for use those types.
 **/
function installed_custom_post_types(){
	$installed = Config::$custom_post_types;

	return array_map(create_function('$class', '
		return new $class;
	'), $installed);
}

/**
 * Adding custom post types to the installed array defined in this function
 * will activate and make available for use those types.
 **/
function installed_custom_taxonomies(){
	$installed = Config::$custom_taxonomies;

	return array_map(create_function('$class', '
		return new $class;
	'), $installed);
}


function flush_rewrite_rules_if_necessary(){
	global $wp_rewrite;
	$start    = microtime(True);
	$original = get_option('rewrite_rules');
	$rules    = $wp_rewrite->rewrite_rules();

	if (!$rules or !$original){
		return;
	}
	ksort($rules);
	ksort($original);

	$rules    = md5(implode('', array_keys($rules)));
	$original = md5(implode('', array_keys($original)));

	if ($rules != $original){
		flush_rewrite_rules();
	}
}


/**
 * Registers all installed custom taxonomies
 *
 * @return void
 * @author Chris Conover
 **/
function register_custom_taxonomies(){
	#Register custom post types
	foreach(installed_custom_taxonomies() as $custom_taxonomy){
		$custom_taxonomy->register();
	}
}
add_action('init', 'register_custom_taxonomies');

/**
 * Registers all installed custom post types
 *
 * @return void
 * @author Jared Lang
 **/
function register_custom_post_types(){
	#Register custom post types
	foreach(installed_custom_post_types() as $custom_post_type){
		$custom_post_type->register();
	}

	#This ensures that the permalinks for custom posts work
	flush_rewrite_rules_if_necessary();
}
add_action('init', 'register_custom_post_types');

/**
 * Registers all metaboxes for install custom post types
 *
 * @return void
 * @author Jared Lang
 **/
function register_meta_boxes(){
	#Register custom post types metaboxes
	foreach(installed_custom_post_types() as $custom_post_type){
		$custom_post_type->register_metaboxes();
	}
}
add_action('do_meta_boxes', 'register_meta_boxes');


/**
 * Saves the data for a given post type
 *
 * @return void
 * @author Jared Lang
 **/
function save_meta_data($post){
	#Register custom post types metaboxes
	foreach(installed_custom_post_types() as $custom_post_type){
		if (post_type($post) == $custom_post_type->options('name')){
			$meta_box = $custom_post_type->metabox();
			break;
		}
	}
	return _save_meta_data($post, $meta_box);

}
add_action('save_post', 'save_meta_data');


/**
 * Displays the metaboxes for a given post type
 *
 * @return void
 * @author Jared Lang
 **/
function show_meta_boxes($post){
	#Register custom post types metaboxes
	foreach(installed_custom_post_types() as $custom_post_type){
		if (post_type($post) == $custom_post_type->options('name')){
			$meta_box = $custom_post_type->metabox();
			break;
		}
	}
	return _show_meta_boxes($post, $meta_box);
}

function save_default($post_id, $field){
	$old = get_post_meta( $post_id, $field['id'], true );
	$new = isset( $_POST[$field['id']]) ? $_POST[$field['id']] : null;

	// Update if new is not empty and is not the same value as old
	if ( $new !== "" and $new !== null and $new != $old ) {
		update_post_meta( $post_id, $field['id'], $new );
	}
	// Delete if we're sending a new null value and there was an old value
	elseif ( ( $new === "" or is_null( $new ) ) and $old ) {
		delete_post_meta( $post_id, $field['id'], $old );
	}
	// Otherwise we do nothing, field stays the same
	return;
}

function save_file($post_id, $field){
	$file_uploaded = @!empty($_FILES[$field['id']]);
	if ($file_uploaded){
		require_once(ABSPATH.'wp-admin/includes/file.php');
		$override['action'] = 'editpost';
		$file               = $_FILES[$field['id']];
		$uploaded_file      = wp_handle_upload($file, $override);

		# TODO: Pass reason for error back to frontend
		if ($uploaded_file['error']){return;}


		$attachment = array(
			'post_title'     => $file['name'],
			'post_type'      => 'attachment',
			'post_parent'    => $post_id,
			'post_mime_type' => $file['type'],
			'guid'           => $uploaded_file['url'],
			'post_content'   => ''
		);
		$id = wp_insert_attachment($attachment, $uploaded_file['file'], $post_id);

		wp_update_attachment_metadata(
			$id,
			wp_generate_attachment_metadata($id, $uploaded_file['file'])
		);
		update_post_meta($post_id, $field['id'], $id);
	}
}

/**
 * Handles saving a custom post as well as it's custom fields and metadata.
 *
 * @return void
 * @author Jared Lang
 **/
function _save_meta_data($post_id, $meta_box){
	// verify nonce
	if (!wp_verify_nonce($_POST['meta_box_nonce'], basename(__FILE__))) {
		return $post_id;
	}

	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}

	// check permissions
	if ('page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id)) {
			return $post_id;
		}
	} elseif (!current_user_can('edit_post', $post_id)) {
		return $post_id;
	}

	foreach ($meta_box['fields'] as $field) {
		switch ($field['type']){
			case 'file':
				save_file($post_id, $field);
				break;
			default:
				save_default($post_id, $field);
				break;
		}
	}
}

/**
 * Outputs the html for the fields defined for a given post and metabox.
 *
 * @return void
 * @author Jared Lang
 **/
function _show_meta_boxes($post, $meta_box){
	?>
	<input type="hidden" name="meta_box_nonce" value="<?=wp_create_nonce(basename(__FILE__))?>"/>
	<table class="form-table">
	<?php foreach($meta_box['fields'] as $field):
		$current_value = get_post_meta($post->ID, $field['id'], true);?>
		<tr>
			<th><label for="<?=$field['id']?>"><?=$field['name']?></label></th>
			<td>
			<?php if($field['desc']):?>
				<div class="description">
					<?=$field['desc']?>
				</div>
			<?php endif;?>

			<?php switch ($field['type']):
				case 'text':?>
				<input type="text" name="<?=$field['id']?>" id="<?=$field['id']?>" value="<?=($current_value) ? htmlentities($current_value) : $field['std']?>" />

			<?php break; case 'textarea':?>
				<textarea name="<?=$field['id']?>" id="<?=$field['id']?>" cols="60" rows="4"><?=($current_value) ? htmlentities($current_value) : $field['std']?></textarea>

			<?php break; case 'select':?>
				<select name="<?=$field['id']?>" id="<?=$field['id']?>">
					<option value=""><?=($field['default']) ? $field['default'] : '--'?></option>
				<?php foreach ($field['options'] as $k=>$v):?>
					<option <?=($current_value == $v) ? ' selected="selected"' : ''?> value="<?=$v?>"><?=$k?></option>
				<?php endforeach;?>
				</select>

			<?php break; case 'radio':?>
				<?php foreach ($field['options'] as $k=>$v):?>
				<label for="<?=$field['id']?>_<?=slug($k, '_')?>"><?=$k?></label>
				<input type="radio" name="<?=$field['id']?>" id="<?=$field['id']?>_<?=slug($k, '_')?>" value="<?=$v?>"<?=($current_value == $v) ? ' checked="checked"' : ''?> />
				<?php endforeach;?>

			<?php break; case 'checkbox':?>
				<input type="checkbox" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>"<?php echo ( $current_value == 'on' ) ? ' checked="checked"' : ''; ?>>

			<?php break; case 'help':?><!-- Do nothing for help -->
			<?php break; case 'file':
							$document_id = get_post_meta($post->ID, $field['id'], True);
							if ($document_id){
								$document = get_post($document_id);
								$url      = wp_get_attachment_url($document->ID);
							}else{
								$document = null;
							}
							?>
							<?php if($document):?>
							Current file:
							<a href="<?=$url?>"><?=$document->post_title?></a><br /><br />
							<?php endif;?>
							<input type="file" id="file_<?=$post->ID?>" name="<?=$field['id']?>"><br />
			<?php break; default:?>
				<p class="error">Don't know how to handle field of type '<?=$field['type']?>'</p>
			<?php break; endswitch;?>
			<td>
		</tr>
	<?php endforeach;?>
	</table>

	<?php if($meta_box['helptxt']):?>
	<p><?=$meta_box['helptxt']?></p>
	<?php endif;?>
	<?php
}

/**
 * Get a theme option safely.
 *
 * @return $mixed or False
 * @author Chris Conover
 **/
function get_theme_option($key)
{
	$theme_options = get_option(THEME_OPTIONS_NAME);
	return ($theme_options !== FALSE && isset($theme_options[$key])) ? $theme_options[$key] : False;
}

/**
 * Generate HTML for promos on home page.
 *
 * @return string
 * @author Chris Conover
 **/
function get_promo_html()
{
	$promo_count = get_theme_option('promo_post_num');
	if($promo_count === False) {
		$promo_count = 1;
	}

	$exclude_cats = get_theme_option('promo_post_categories');

	$promos = get_posts(
		array(
			'numberposts' => $promo_count,
			'category__not_in' => $exclude_cats,
		)
	);

	ob_start();

	foreach($promos as $promo) {
		$link_url = get_post_meta($promo->ID, '_links_to', True);
		$link_target = get_post_meta($promo->UD, '_links_to_target', True);?>
		<li class="clearfix">
			<? if($link_url != '') {?><a href="<?=$link_url?>" target="<?=$link_target?>"><?}?>
				<?=get_the_post_thumbnail($promo->ID, 'medium')?>
				<h3 class="serif"><?=$promo->post_title?></h3>
				<?=str_replace(']]>', ']]&gt;', apply_filters('the_content', $promo->post_content));?>
			<? if($link_url != '') {?></a><?}?>
		</li>
	<? }
	return ob_get_clean();
}


function return_600( $seconds ) {
	// change the default feed cache recreation period to 10 minutes
	return 600;
}


/**
 * Fetch UCF Today Rosen News
 *
 * @return string
 * @author Chris Conover
 **/
function get_today_news()
{
	global $post, $_wp_additional_image_sizes;

	if( ($feed_url = get_post_meta($post->ID, 'page_feed', True)) == '') {
		$feed_url = get_theme_option('today_rosen_rss');
	}
	if($feed_url !== False && $feed_url != '') {

		$dimensions = '';
		if(isset($_wp_additional_image_sizes['sidebar-rss-thumb'])) {
			$dimensions  = '?thumb='.$_wp_additional_image_sizes['sidebar-rss-thumb']['width'];
			$dimensions .= 'x'.$_wp_additional_image_sizes['sidebar-rss-thumb']['height'];
		}

		add_filter( 'wp_feed_cache_transient_lifetime' , 'return_600' );
		$rss = fetch_feed($feed_url.$dimensions	);
		remove_filter( 'wp_feed_cache_transient_lifetime' , 'return_600' );

		if(!is_wp_error($rss)) {
			$item        = $rss->get_item(0);
			$description = $item->get_description();
			$enclosure   = $item->get_enclosure();

			if(method_exists($enclosure, 'get_thumbnail')) {
				$img_src = $enclosure->get_thumbnail();
				if($img_src != '') {
					$img = '<img src="'.$img_src.'" width="272" alt="'.$item->get_title().' Thumbnail" />';
				}
			}

			ob_start();?>
			<div class="sidebar-pub serif">
				<?=isset($img) ? $img : ''?>
				<h3><a href="<?=$item->get_permalink()?>"><?=$item->get_title()?></a></h3>
				<p><?=strip_tags($description)?></p>
				<a href="<?=preg_replace('/feed\/?$/', '', $feed_url)?>">More Rosen College News &raquo;</a>
			</div>
			<?
			return ob_get_clean();
		}
	}
}

/**
 * Get meta info for a person.
 *
 * @return string
 * @author Chris Conover
 **/
function get_person_meta($post_id)
{
	$img    = get_the_post_thumbnail($post_id, 'full');
	$title  = get_post_meta($post_id, 'person_jobtitle', True);
	$phones = get_post_meta($post_id, 'person_phones', True);
	$phones = ($phones != '') ? explode(',', $phones) : Array();
	$email  = get_post_meta($post_id, 'person_email', True);
	$addtional_info = get_post_meta($post_id, 'person_additional_info', True);

	ob_start()?>
	<div id="person-meta" class="span-15 append-1 last">
		<div id="headshot" class="span-3">
			<? if($img == '') {?>
				<img src="<?=bloginfo('stylesheet_directory')?>/static/img/no-photo.jpg" alt="not photo available"/>
			<? } else {?>
				<?=get_the_post_thumbnail($post_id, 'full')?>
			<? } ?>
		</div>
		<div id="details" class="span-11 last">
			<p><?=$title?></p>
			<ul>
				<? foreach($phones as $phone) { ?>
				<li>
					<?=$phone?>
				</li>
				<? } ?>
			</ul>
			<?=(($email != '') ? '<a href="mailto:'.$email.'">'.$email.'</a>' : '')?>
			<p class="additional-info"><?=$addtional_info?></p>
		</div>
	</div><?
	return ob_get_clean();
}

/**
 * Extract form file extension
 *
 * @return string
 * @author Chris Conover
 **/
function get_form_class($form_id)
{
	$rosen_forms = new Form();
	$url  = get_post_meta($form_id, $rosen_forms->options('name').'_url', True);
	$file = get_post_meta($form_id, 'document_file', true);
	if ($file){
		$url = wp_get_attachment_url(get_post($file)->ID);
	}
	if($url=="#"){
		$class = 'missing';
	} else {
		preg_match('/\.(?<file_ext>[^.]+)$/', $url, $matches);
		$class = isset($matches['file_ext']) ? $matches['file_ext'] : 'file';
	}
	return $class;
}

/**
 * Submit constant contact sign up
 *
 * @return bool
 * @author Chris Conover
 **/
function submit_cc_signup()
{

	if(isset($_POST['cc_email'])) {

		$email = $_POST['cc_email'];

		$base_error = 'Error: Adding your email addresss to the mailing list failed.';

		$username = get_theme_option('constant_contact_username');
		$password = get_theme_option('constant_contact_password');
		$api_key  = get_theme_option('constant_contact_api_key');
		$list     = get_theme_option('constant_contact_list');

		if( ($username === False || $password === False || $api_key === False) ||
					($username == '' || $password == '' || $api_key == '')) {
			$_SESSION['cc_error'] = $base_error.' (misconfiguraiton)';
		} else {
			if($email == '') {
				$_SESSION['cc_error'] = $base_error.' Your email address cannot be blank.';
			} else {
				$xml = '
					<entry xmlns="http://www.w3.org/2005/Atom">
						<title type="text"></title>
						<updated>%s</updated>
						<author></author>
						<id>urn:uuid:E8553C09F4xcvxCCC53F481214230867087</id>
						<summary type="text">Contact</summary>
						<content type="application/vnd.ctct+xml">
							<Contact xmlns="http://ws.constantcontact.com/ns/1.0/">
								<EmailAddress>%s</EmailAddress>
								<OptInSource>ACTION_BY_CUSTOMER</OptInSource>
								<ContactLists>
									<ContactList id="http://api.constantcontact.com/ws/customers/%s/lists/%d"></ContactList>
								</ContactLists>
							</Contact>
						</content>
					</entry>';

					if($list === False || !is_numeric($list)) {
						$list = 2;
					}

					$auth = trim($api_key).'%'.$username.':'.$password;
					$email = html_entity_decode($email, ENT_COMPAT);

					$ch = curl_init();

					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, False);
					curl_setopt($ch, CURLOPT_URL, sprintf(CC_ADD_CONTACT_API_URL, $username));
					curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
					curl_setopt($ch, CURLOPT_USERPWD, $auth);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, str_replace("\t", '', sprintf($xml, date('c'), $email, $username, $list)));
					curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:application/atom+xml"));
					curl_setopt($ch, CURLOPT_HEADER, false); // Do not return headers
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, True); // If you set this to 0, it will take you to a page with the http response

					$response = curl_exec($ch);
					curl_close($ch);
					//$response = http_put_data($url, );

					if($response === False || @simplexml_load_string($response) === False) {
						if(strpos($response, 'Error') == 0 && ($msg = substr($response, strpos($response, ':') + 1)) != '') {
							$_SESSION['cc_error'] = $base_error.' '.$msg.'.';
						} else {
							$_SESSION['cc_error'] = $base_error.' (submission failed)';
						}
					} else {
						$_SESSION['cc_success'] = 'Success: Your email address was added to the mailing list. Thank You.';
					}
			}
		}
	}
}
add_action('wp_loaded', 'submit_cc_signup');

/**
 * Apply Person object name formatting to profile page titles
 *
 * @return string
 * @author Chris Conover
 **/
function get_person_title( $_title='' )
{
	global $post;

	if( $post->post_type === 'person' ) {
		return get_person_name( $post );
	} else if ( $_title !== '' ) {
		return $_title;
	} else {
		return $post->post_title;
	}
}

/**
 * List of Person post objects based on a rosen_org_groups term
 *
 * @return array
 * @author Chris Conover
 **/
function get_term_people($term_id, $order_by = 'menu_order') {
	$posts = get_posts(Array(
		'numberposts' => -1,
		'order'       => 'ASC',
		'orderby'     => $order_by,
		'post_type'   => 'person',
		'meta_key'    => 'person_orderby_name',
		'tax_query'   => Array(
			Array(
				'taxonomy' => 'rosen_org_groups',
				'field' =>  'id',
				'terms' => $term_id))));
	return $posts;
}

/**
 * Formats a Person object's name adding prefix and suffix
 *
 * @return string
 * @author Chris Conover
 **/
function get_person_name($person) {
	$prefix = get_post_meta($person->ID, 'person_title_prefix', True);
	$suffix = get_post_meta($person->ID, 'person_title_suffix', True);
	$name = $person->post_title;
	return $prefix.' '.$name.$suffix;
}

/**
 * Format a list of Person object's phone numbers into an unordered list
 *
 * @return string
 * @author Chris Conover
 **/
function get_person_phones($person_id) {
	$phones = get_post_meta($person_id, 'person_phones', True);
	return ($phones != '') ? explode(',', $phones) : array();
}

/**
 * Adds person meta info to Person profile page
 *
 * @return string
 * @author Chris Conover
 **/
function person_content_filter($content)
{
	global $post;
	if($post->post_type == 'person') {
		return get_person_meta($post->ID).'<div class="span-15 append-1 last clear">'.$content.'</div>';
	} else {
		return $content;
	}
}
add_filter('the_content', 'person_content_filter')
?>
