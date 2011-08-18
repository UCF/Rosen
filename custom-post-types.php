<?php

abstract class CustomPostType{
	public 
		$name              = 'custom_post_type',
		$plural_name       = 'Custom Posts',
		$singular_name     = 'Custom Post',
		$add_new_item      = 'Add New Custom Post',
		$edit_item         = 'Edit Custom Post',
		$new_item          = 'New Custom Post',
		$public            = True,  # I dunno...leave it true
		$use_title         = True,  # Title field
		$use_editor        = True,  # WYSIWYG editor, post content field
		$use_revisions     = True,  # Revisions on post content and titles
		$use_thumbnails    = False, # Featured images
		$use_order         = False, # Wordpress built-in order meta data
		$use_metabox       = False, # Enable if you have custom fields to display in admin
		$use_shortcode     = False, # Auto generate a shortcode for the post type (see also toHTML method)
		$use_hierarchical  = False, # Must be true to see Parent field in Page Attributes
		$_builtin          = False, # True when extending built-in post type (post, page, etc.)
		$taxonomies        = Array(); # Taxonomies associated with this post type (e.g. post_tag, category)

	/**
	 * Wrapper for get_posts function, that predefines post_type for this
	 * custom post type.  Any options valid in get_posts can be passed as an
	 * option array.  Returns an array of objects.
	 **/
	public function get_objects($options=array()){
		$defaults = array(
			'numberposts'   => -1,
			'orderby'       => 'title',
			'order'         => 'ASC',
			'post_type'     => $this->options('name'),
		);
		$options = array_merge($defaults, $options);
		$objects = get_posts($options);
		return $objects;
	}
	
	
	/**
	 * Similar to get_objects, but returns array of key values mapping post
	 * title to id if available, otherwise it defaults to id=>id.
	 **/
	public function get_objects_as_options($options=array()){
		$objects = $this->get_objects($options);
		$opt     = array();
		foreach($objects as $o){
			switch(True){
				case $this->options('use_title'):
					$opt[$o->post_title] = $o->ID;
					break;
				default:
					$opt[$o->ID] = $o->ID;
					break;
			}
		}
		return $opt;
	}
	
	
	/**
	 * Return the instances values defined by $key.
	 **/
	public function options($key){
		$vars = get_object_vars($this);
		return $vars[$key];
	}
	
	
	/**
	 * Additional fields on a custom post type may be defined by overriding this
	 * method on an descendant object.
	 **/
	public function fields(){
		return array();
	}
	
	
	/**
	 * Using instance variables defined, returns an array defining what this
	 * custom post type supports.
	 **/
	public function supports(){
		#Default support array
		$supports = array();
		if ($this->options('use_title')){
			$supports[] = 'title';
		}
		if ($this->options('use_order')){
			$supports[] = 'page-attributes';
		}
		if ($this->options('use_thumbnails')){
			$supports[] = 'thumbnail';
		}
		if ($this->options('use_editor')){
			$supports[] = 'editor';
		}
		if ($this->options('use_revisions')){
			$supports[] = 'revisions';
		}
		return $supports;
	}
	
	
	/**
	 * Creates labels array, defining names for admin panel.
	 **/
	public function labels(){
		return array(
			'name'          => __($this->options('plural_name')),
			'singular_name' => __($this->options('singular_name')),
			'add_new_item'  => __($this->options('add_new_item')),
			'edit_item'     => __($this->options('edit_item')),
			'new_item'      => __($this->options('new_item')),
		);
	}
	
	
	/**
	 * Creates metabox array for custom post type. Override method in
	 * descendants to add or modify metaboxes.
	 **/
	public function metabox(){
		if ($this->options('use_metabox')){
			return array(
				'id'       => $this->options('name').'_metabox',
				'title'    => __($this->options('singular_name').' Fields'),
				'page'     => $this->options('name'),
				'context'  => 'normal',
				'priority' => 'high',
				'fields'   => $this->fields(),
			);
		}
		return null;
	}
	
	
	/**
	 * Registers metaboxes defined for custom post type.
	 **/
	public function register_metaboxes(){
		if ($this->options('use_metabox')){
			$metabox = $this->metabox();
			add_meta_box(
				$metabox['id'],
				$metabox['title'],
				'show_meta_boxes',
				$metabox['page'],
				$metabox['context'],
				$metabox['priority']
			);
		}
	}
	
	
	/**
	 * Registers the custom post type and any other ancillary actions that are
	 * required for the post to function properly.
	 **/
	public function register(){
		$registration = array(
			'labels'       => $this->labels(),
			'supports'     => $this->supports(),
			'public'       => $this->options('public'),
			'taxonomies'   => $this->options('taxonomies'),
			'_builtin'     => $this->options('_builtin'),
			'hierarchical' => $this->options('hierarchical')
		);
		
		if ($this->options('use_order')){
			$regisration = array_merge($registration, array('hierarchical' => True,));
		}
		
		register_post_type($this->options('name'), $registration);
		
		if ($this->options('use_shortcode')){
			add_shortcode($this->options('name').'-list', array($this, 'shortcode'));
		}
	}
	
	
	/**
	 * Shortcode for this custom post type.  Can be overridden for descendants.
	 * Defaults to just outputting a list of objects outputted as defined by
	 * toHTML method.
	 **/
	public function shortcode($attr){
		$default = array(
			'type' => $this->options('name'),
		);
		if (is_array($attr)){
			$attr = array_merge($default, $attr);
		}else{
			$attr = $default;
		}
		return sc_object_list($attr);
	}
	
	
	/**
	 * Handles output for a list of objects, can be overridden for descendants.
	 * If you want to override how a list of objects are outputted, override
	 * this, if you just want to override how a single object is outputted, see
	 * the toHTML method.
	 **/
	public function objectsToHTML($objects, $tax_queries){
		if (count($objects) < 1){ return '';}
		
		$class = get_custom_post_type($objects[0]->post_type);
		$class = new $class;
		
		ob_start();
		?>
		<ul class="<?=$class->options('name')?>-list">
			<?php foreach($objects as $o):?>
			<li>
				<?=$class->toHTML($o)?>
			</li>
			<?php endforeach;?>
		</ul>
		<?php
		$html = ob_get_clean();
		return $html;
	}
	
	
	/**
	 * Outputs this item in HTML.  Can be overridden for descendants.
	 **/
	public function toHTML($object){
		$html = '<a href="'.get_permalink($object->ID).'">'.$object->post_title.'</a>';
		return $html;
	}
}


class Page extends CustomPostType{
	public 
		$name           = 'page',
		$plural_name    = 'pages',
		$singular_name  = 'Page',
		$add_new_item   = 'Add New Page',
		$edit_item      = 'Edit Page',
		$new_item       = 'New Page',
		$public         = True,
		$use_thumbnails = True,
		$use_editor     = True,
		$use_order      = True,
		$use_title      = True,
		$use_shortcode  = True,
		$use_metabox    = True,
		$_builtin       = True,
		$hierarchical   = True,
		
		$taxonomies     = Array('categories');
		
	
	
	public function objectsToHTML($objects, $tax_queries){
		$class = get_custom_post_type($objects[0]->post_type);
		$class = new $class;
		
		$outputs = array();
		foreach($objects as $o){
			$outputs[] = $class->toHTML($o);
		}
		
		return implode(', ', $outputs);
	}
	
	
	public function toHTML($object){
		return $object->post_title;
	}
	
	public function fields(){
		return array(
			array(
				'name'  => 'Front Page:',
				'desc'  => 'Put this page into the rotation for the front page.',
				'id'    => $this->options('name').'_frontpage',
				'type'  => 'checkbox',
			),
			array(
				'name'  => 'Subtitle:',
				'desc'  => 'Appears below H2 on front page.',
				'id'    => $this->options('name').'_subtitle',
				'type'  => 'text',
			),
			array(
				'name'  => 'Quote:',
				'desc'  => 'Appears at the bottom of the page in a highlighed section.',
				'id'    => $this->options('name').'_quote',
				'type'  => 'textarea',
			),
			array(
				'name'  => 'Alternate New Feed URL:',
				'desc'  => 'Override theme options news feed URL.',
				'id'    => $this->options('name').'_feed',
				'type'  => 'text',
			),
			array(
				'name'  => 'Front Page Title Color:',
				'desc'  => 'Override title and subtitle color on front page only. Example: #FFFFFF',
				'id'    => $this->options('name').'_titlecolor',
				'type'  => 'text',
			),
		);
	}
}

abstract class Link extends CustomPostType{
	public
		$name           = 'link',
		$plural_name    = 'Forms',
		$singular_name  = 'Form',
		$add_new_item   = 'Add Form',
		$edit_item      = 'Edit Form',
		$new_item       = 'New Form',
		$public         = True,
		$use_title      = True,
		$use_metabox    = True;
	
	public function fields(){
		return array(
			array(
				'name' => __('url'),
				'desc' => __('URL'),
				'id'   => $this->options('name').'_url',
				'type' => 'text',
			),
		);
	}
}

class Form extends Link{
	public
		$name           = 'form',
		$plural_name    = 'Forms',
		$singular_name  = 'Form',
		$add_new_item   = 'Add Form',
		$edit_item      = 'Edit Form',
		$new_item       = 'New Form',
		$public         = True,
		$use_shortcode  = True,
		$taxonomies     = Array('post_tag', 'category');
	
	public function fields(){
		$fields   = parent::fields();
		$fields[] = array(
			'name'    => __('Document'),
			'desc'    => __('Define an external url or upload a new file.  Uploaded files will override any url set.'),
			'id'      => $this->options('name').'_file',
			'type'    => 'file',
		);
		return $fields;
	}
	
	static function get_url($form){
		$x = get_post_meta($form->ID, 'form_url', True);
		$y = wp_get_attachment_url(get_post_meta($form->ID, 'form_file', True));
		
		return ($y) ? $y : $x;
	}
	
	public function objectsToHTML($objects, $tax_queries) {
		$categories = array();
		foreach($tax_queries as $query) {
			if($query['taxonomy'] == 'category') {
				foreach($query['terms'] as $term_slug) {
					if( ($term = get_term_by('slug', $term_slug, 'category')) !== False) {
						array_push($categories, $term);
					}
				}
			}
		}
		if(count($categories) == 0) {
			$categories    = get_categories(array(
				'orderby' => 'name',
				'order'   => 'ASC',
				'parent'  => get_category_by_slug('forms')->term_id,
			));
		}
		
		ob_start();?>
		<div class="forms">
			<? foreach($categories as $category): ?>
				<h3><?=$category->name?></h3>
				<ul>
					<?php
						$forms = get_posts(array(
							'numberposts' => -1,
							'orderby'     => 'date', // Newest first always
							'order'       => 'desc', //
							'post_type'   => 'form',
							'category'    => $category->term_id,
						));
					?>
					<?php foreach($forms as $form):?>
					<?php
						$url  = get_post_meta($form->ID, 'form_url', True);
						$file = get_post_meta($form->ID, 'form_file', true);
						if ($file){
							$url = wp_get_attachment_url(get_post($file)->ID);
						}
						if($url=="#"){
							$class = 'missing';
						} else {
							preg_match('/\.(?<file_ext>[^.]+)$/', $url, $matches);
							$class = isset($matches['file_ext']) ? $matches['file_ext'] : 'file';
						}	
					?>
					<li class="document <?=$class?>">
						<a href="<?=$url?>"><?=$form->post_title?></a>
					</li>
					<?php endforeach;?>
				</ul>
			<? endforeach;?>
		</div>
		<?
		return ob_get_clean();
	}
}

/**
 * Describes a staff member of the Rosen College
 *
 * @author Chris Conover
 **/
class Person extends CustomPostType
{
	/*
	The following query will pre-populate the person_orderby_name
	meta field with a guess of the last name extracted from the post title.
	
	>>>BE SURE TO REPLACE wp_<number>_... WITH THE APPROPRIATE SITE ID<<<
	
	INSERT INTO wp_29_postmeta(post_id, meta_key, meta_value) 
	(	SELECT	id AS post_id, 
						'person_orderby_name' AS meta_key, 
						REVERSE(SUBSTR(REVERSE(post_title), 1, LOCATE(' ', REVERSE(post_title)))) AS meta_value
		FROM		wp_29_posts AS posts
		WHERE		post_type = 'person' AND
						(	SELECT meta_id 
							FROM wp_29_postmeta 
							WHERE post_id = posts.id AND
										meta_key = 'person_orderby_name') IS NULL)
	*/
	
	public
		$name           = 'person',
		$plural_name    = 'People',
		$singular_name  = 'Person',
		$add_new_item   = 'Add Person',
		$edit_item      = 'Edit Person',
		$new_item       = 'New Person',
		$public         = True,
		$use_shortcode  = True,
		$use_metabox    = True,
		$use_thumbnails = True,
		$use_order      = True,
		$taxonomies     = Array('rosen_org_groups', 'category');
		
		public function fields(){
			$fields = array(
				array(
					'name'    => __('Title Prefix'),
					'desc'    => '',
					'id'      => $this->options('name').'_title_prefix',
					'type'    => 'text',
				),
				array(
					'name'    => __('Title Suffix'),
					'desc'    => __('Be sure to include leading comma or space if neccessary.'),
					'id'      => $this->options('name').'_title_suffix',
					'type'    => 'text',
				),
				array(
					'name'    => __('Job Title'),
					'desc'    => __(''),
					'id'      => $this->options('name').'_jobtitle',
					'type'    => 'text',
				),
				array(
					'name'    => __('Phone'),
					'desc'    => __('Separate multiple entries with commas.'),
					'id'      => $this->options('name').'_phones',
					'type'    => 'text',
				),
				array(
					'name'    => __('Email'),
					'desc'    => __(''),
					'id'      => $this->options('name').'_email',
					'type'    => 'text',
				),
				array(
					'name'    => __('Order By Name'),
					'desc'    => __('Name used for sorting. Leaving this field blank may lead to an unexpected sort order.'),
					'id'      => $this->options('name').'_orderby_name',
					'type'    => 'text',
				),
			);
			return $fields;
		}
	
	public function objectsToHTML($objects, $tax_queries) {
		# We could try to use the objects passed here but it simpler
		# to just look them up again already split up into sections 
		# based on the tax_queries array
		
		ob_start();
		if(count($tax_queries) ==0) {
			// Dean's Suite is always first if everything is being displayed
			$dean_suite_name = get_theme_option('aboutus_featured_group');
			$dean_suite = False;
			if($dean_suite_name !== False) {
				$dean_suite = get_term_by('name', $dean_suite_name, 'rosen_org_groups');
				if($dean_suite !== False) {
					$people = get_term_people($dean_suite->term_id, 'menu_order'); 
					include('templates/staff-pics.php');
				}
			}
		}
		
		if(count($tax_queries) == 0) {
			$terms = get_terms('rosen_org_groups', Array('orderby' => 'name'));
		} else {
			$terms = array();
			foreach($tax_queries as $key=>$query) {
				foreach($query['terms'] as $term_slug) {
					$term = get_term_by('slug', $term_slug, $query['taxonomy']);
					if($term !== False) {
						array_push($terms, $term);
					}
				}
			}
		}
		foreach($terms as $term) {
			if(count($tax_queries) > 0 || ($dean_suite_name === False || $dean_suite === False || $term->term_id != $dean_suite->term_id)) {
				$people = get_term_people($term->term_id, 'meta_value');
				include('templates/staff-table.php');
			}
		}
		return ob_get_clean();
	}
} // END class 

/**
 * Desribes a Rosen College venue
 *
 * @author Chris Conover
 **/
class Venue extends CustomPostType
{
	public
		$name           = 'venue',
		$plural_name    = 'Venues',
		$singular_name  = 'Venue',
		$add_new_item   = 'Add Venue',
		$edit_item      = 'Edit Venue',
		$new_item       = 'New Venue',
		$public         = True,
		$use_shortcode  = True,
		$use_metabox    = True,
		$use_thumbnails = True,
		$use_order      = True,
		$taxonomies     = Array('category');
		
		public function fields(){
			$fields = array();
			return $fields;
		}
		
		public function objectsToHTML($objects, $tax_queries) {
			if(count($objects) > 0) {
				ob_start();?>
				<ul class="venue-list clearfix">
				<?
					$count = 0;
					$end = False;
					foreach($objects as $object) { 
						$attach_id = get_post_thumbnail_id($object->ID);
						if($attach_id !== False && 
								($small_img_atts = wp_get_attachment_image_src($attach_id, 'thumbnail')) !== False &&
									($full_img_atts = wp_get_attachment_image_src($attach_id, 'large'))) {
							$last = (($count + 1) % 3) == 0 ? True : False;
				?>
					<li<?=$last ? ' class="last"' : ''?><?=$end ? ' class="clear"' : ''?>>
						<a href="<?=$full_img_atts[0]?>">
							<img src="<?=$small_img_atts[0]?>" height="<?=$small_img_atts[1]?>" width="<?=$small_img_atts[2]?>" alt="<?=$post->title?> Venue Thumbnail" />
						</a>
						<h4 class="sans"><?=$object->post_title?></h4>
						<?=apply_filters('the_content', $object->post_content) ?>
					</li>
				<?	$end = ($last) ? True : False;
						}
					$count++;
				} 
				?>
				</ul><?
				return ob_get_clean();
			}
		}
} // END class 

/**
 * Desribes a Issuu publication
 *
 * @author Chris Conover
 **/
class Publication extends CustomPostType
{
	public
		$name           = 'publication',
		$plural_name    = 'Publications',
		$singular_name  = 'Publication',
		$add_new_item   = 'Add Publication',
		$edit_item      = 'Edit Publication',
		$new_item       = 'New Publication',
		$public         = True,
		$use_shortcode  = False,
		$use_metabox    = False,
		$use_thumbnails = False,
		$use_order      = False,
		$taxonomies     = Array();
		
		public function fields(){
			$fields = array();
			return $fields;
		}
		
} // END class

?>