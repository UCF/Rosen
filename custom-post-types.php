<?php

abstract class CustomPostType{
	public 
		$name           = 'custom_post_type',
		$plural_name    = 'Custom Posts',
		$singular_name  = 'Custom Post',
		$add_new_item   = 'Add New Custom Post',
		$edit_item      = 'Edit Custom Post',
		$new_item       = 'New Custom Post',
		$public         = True,  # I dunno...leave it true
		$use_title      = True,  # Title field
		$use_editor     = True,  # WYSIWYG editor, post content field
		$use_revisions  = True,  # Revisions on post content and titles
		$use_tags       = True,  # Tags taxonomy
		$use_categories = False, # Categories taxonomy
		$use_thumbnails = False, # Featured images
		$use_order      = False, # Wordpress built-in order meta data
		$use_metabox    = False, # Enable if you have custom fields to display in admin
		$use_shortcode  = False, # Auto generate a shortcode for the post type (see also toHTML method)
	  $_builtin       = False; # True when extending built-in post type (post, page, etc.)
	
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
			'labels'   => $this->labels(),
			'supports' => $this->supports(),
			'public'   => $this->options('public'),
			'_builtin' => $this->options('_builtin'),
		);
		
		if ($this->options('use_order')){
			$regisration = array_merge($registration, array('hierarchical' => True,));
		}
		
		register_post_type($this->options('name'), $registration);
		
		if ($this->options('use_categories')){
			register_taxonomy_for_object_type('category', $this->options('name'));
		}
		
		if ($this->options('use_tags')){
			register_taxonomy_for_object_type('post_tag', $this->options('name'));
		}
		
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
		return sc_object($attr);
	}
	
	
	/**
	 * Outputs this item in HTML.  Can be overridden for descendants.
	 **/
	public function toHTML($post){
		if (is_int($post)){
			$post = get_post($post);
		}
		
		$html = '<a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a>';
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
		$use_categories = True,
		$use_thumbnails = True,
		$use_editor     = True,
		$use_order      = True,
		$use_title      = True,
		$use_shortcode  = True,
		$use_metabox    = True,
		$_builtin       = True;
	
	public function fields(){
		return array(
			array(
				'name'  => 'Appear on Front Page:',
				'desc'  => '',
				'id'    => $this->options('name').'_frontpage',
				'type'  => 'checkbox',
			),
			array(
				'name'  => 'Subtitle:',
				'desc'  => 'Appears below H2 on front page.',
				'id'    => $this->options('name').'_subtitle',
				'type'  => 'text',
			),
		);
	}
}

?>