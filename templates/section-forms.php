<?php
	$rosen_forms = new Form();
	if(!isset($categories)) {
		$categories    = get_categories(array(
			'orderby' => 'name',
			'order'   => 'ASC',
			'parent'  => get_category_by_slug('forms')->term_id,
		));
	}
?>

<div class="forms">
	<?php foreach($categories as $category):?>
	<h3><?=$category->name?></h3>
	<ul>
		<?php
			$forms = get_posts(array(
				'numberposts' => -1,
				'orderby'     => 'title',
				'order'       => 'ASC',
				'post_type'   => 'form',
				'category'    => $category->term_id,
			));
		?>
		<?php foreach($forms as $form):?>
		<?php
			$url  = get_post_meta($form->ID, $rosen_forms->options('name').'_url', True);
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
	<?php endforeach;?>
</div>