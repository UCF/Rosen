<?php 

/**
 * Empty shortcode
 **/
function sc_empty_shortcode(){
	return 'shortcode';
}
add_shortcode('empty-shortcode', 'sc_empty_shortcode');


/**
 * Fetches objects defined by arguments passed, outputs the objects according
 * to the toHTML method located on the object.
 **/
function sc_object($attr){
	if (!is_array($attr)){return '';}
	
	$defaults = array(
		'tags'       => '',
		'categories' => '',
		'type'       => '',
		'limit'      => -1,
	);
	$options = array_merge($defaults, $attr);
	
	$tax_query = array(
		'relation' => 'OR',
	);
	
	if ($options['tags']){
		$tax_query[] = array(
			'taxonomy' => 'post_tag',
			'field'    => 'slug',
			'terms'    => explode(' ', $options['tags']),
		);
	}
	
	if ($options['categories']){
		$tax_query[] = array(
			'taxonomy' => 'category',
			'field'    => 'slug',
			'terms'    => explode(' ', $options['categories']),
		);
	}
	
	$query_array = array(
		'tax_query'      => $tax_query,
		'post_status'    => 'publish',
		'post_type'      => $options['type'],
		'posts_per_page' => $options['limit'],
	);
	$query = new WP_Query($query_array);
	
	global $post;
	ob_start();
	?>
	
	<ul class="object-list <?=$options['type']?>">
		<?php while($query->have_posts()): $query->the_post();
		$class = get_custom_post_type($post->post_type);
		$class = new $class;?>
		<li>
			<?=$class->toHTML($post->ID)?>
		</li>
		<?php endwhile;?>
	</ul>
	
	<?php
	$results = ob_get_clean();
	wp_reset_postdata();
	return $results;
}
add_shortcode('sc-object', 'sc_object');

/**
 * Outputs forms, organized by the sub-category of 'Forms' they are related to.
 * Uncategorized forms will not display.
 *
 * Example:
 * [sc-forms]
 **/
function sc_forms(){
	ob_start();
	include('templates/section-forms.php');
	return ob_get_clean();
}
add_shortcode('sc-forms', 'sc_forms');

/**
 * Builds staff list based on parameters
 *
 * @return string
 * @author Chris Conover
 **/
function sc_staff($atts = Array())
{
	$dept = isset($atts['dept']) ? $atts['dept'] : False;
	
	if(!function_exists('get_term_people')) {
		function get_term_people($term_id, $order_by = 'menu_order') {
			$posts = get_posts(Array(
													'numberposts' => -1,
													'order' => 'ASC',
													'order_by' => $order_by,
													'post_type' => 'rosen_person',
													'tax_query' => Array(
																						Array(
																								'taxonomy' => 'rosen_org_groups',
																								'field' =>  'id',
																								'terms' => $term_id))));
			return $posts;
		}
	}
	if(!function_exists('get_person_name')) {
		function get_person_name($person) {
			$prefix = get_post_meta($person->ID, 'rosen_person_title_prefix', True);
			$suffix = get_post_meta($person->ID, 'rosen_person_title_suffix', True);
			$name = $person->post_title;
			return $prefix.$name.$suffix;
		}
	}
	if(!function_exists('get_person_phones'))	{
		function get_person_phones($person_id) {
			$phones = get_post_meta($person_id, 'rosen_person_phones', True);
			return explode(',', $phones);
		}
	}
	if($dept !== False) {
		$term = get_term_by('name', $dept, 'rosen_org_groups');
		if($term !== False) {
			$people = get_term_people($term->term_id);
			ob_start();?>
			<div class="dept">
				<h3><?=$term->name?></h3>
				<ul>
				<?$count = 0; 
					foreach($people as $person) {
						$email = get_post_meta($person->ID, 'rosen_person_email', True);
						?>
					<li>
						<a href="<?=get_permalink($person->ID)?>">
							<?=get_the_post_thumbnail($post->ID, 'profile')?>
							<?=get_person_name($person)?>
						</a>
						<p class="title"><?=get_post_meta($person->ID, 'rosen_person_jobtitle', True)?></p>
					</li>
				<?$count++; 
					} ?>
				</ul>
			</div>
			<?
			return ob_get_clean();
		}
	} else {
		$terms = get_terms('rosen_org_groups', Array('orderby' => 'name'));
		ob_start();
		foreach($terms as $term) {?>
		<div class="dept">
			<h3><?=$term->name?></h3>
			<table>
				<thead class="sans">
					<tr>
						<th scope="col">Name</th>
						<th scope="col">Title</th>
						<th scope="col">Phone(s)</th>
						<th scope="col">E-Mail</th>
					</tr>
				</thead>
				<tbody class="serif">
					<?$count = 0;
						$people = get_term_people($term->term_id, 'title'); 
						foreach($people as $person) {
							$count++;
							$email = get_post_meta($person->ID, 'rosen_person_email', True);
						?>
							<tr class="<?=((($count % 2) == 0) ? 'even' : 'odd')?>">
								<td class="name"><?=get_person_name($person)?></td>
								<td class="job_title"><?=get_post_meta($person->ID, 'rosen_person_jobtitle', True)?></td>
								<td class="phones">
									<ul>
										<? foreach(get_person_phones($person->ID) as $phone) { ?>
										<li><?=$phone?></li>
										<? } ?>
									</ul>
								</td>
								<td class="email"><?=(($email != '') ? '<a href="mailto:'.$email.'">'.$email.'</a>' : '')?></td>
							</tr>
					<? } ?>
				</body>
			</table>
		</div>
		<? }
		return ob_get_clean();
	}
}
add_shortcode('sc-staff', 'sc_staff');
?>