<?php 

/**
 * Empty shortcode
 **/
function sc_empty_shortcode(){
	return 'shortcode';
}
add_shortcode('empty-shortcode', 'sc_empty_shortcode');


/**
 * Create a javascript slideshow of each top level element in the
 * shortcode.  All attributes are optional, but may default to less than ideal
 * values.  Available attributes:
 * 
 * height     => css height of the outputted slideshow, ex. height="100px"
 * width      => css width of the outputted slideshow, ex. width="100%"
 * transition => length of transition in milliseconds, ex. transition="1000"
 * cycle      => length of each cycle in milliseconds, ex cycle="5000"
 *
 * Example:
 * [slideshow height="500px" transition="500" cycle="2000"]
 * <img src="http://some.image.com" .../>
 * <div class="robots">Robots are coming!</div>
 * <p>I'm a slide!</p>
 * [/slideshow]
 **/
function sc_slideshow($attr, $content=null){
	$content = cleanup(str_replace('<br />', '', $content));
	$content = DOMDocument::loadHTML($content);
	$html    = $content->childNodes->item(1);
	$body    = $html->childNodes->item(0);
	$content = $body->childNodes;
	
	# Find top level elements and add appropriate class
	$items = array();
	foreach($content as $item){
		if ($item->nodeName != '#text'){
			$classes   = explode(' ', $item->getAttribute('class'));
			$classes[] = 'slide';
			$item->setAttribute('class', implode(' ', $classes));
			$items[] = $item->ownerDocument->saveXML($item);
		}
	}
	
	$height    = ($attr['height']) ? $attr['height'] : '100px';
	$width     = ($attr['width']) ? $attr['width'] : '100%';
	$tran_len  = ($attr['transition']) ? $attr['transition'] : 1000;
	$cycle_len = ($attr['cycle']) ? $attr['cycle'] : 5000;
	
	ob_start();
	?>
	<div 
		class="slideshow"
		data-tranlen="<?=$tran_len?>"
		data-cyclelen="<?=$cycle_len?>"
		style="height: <?=$height?>; width: <?=$width?>;"
	>
		<?php foreach($items as $item):?>
		<?=$item?>
		<?php endforeach;?>
	</div>
	<?php
	$html = ob_get_clean();
	
	return $html;
}
add_shortcode('slideshow', 'sc_slideshow');


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
	if(!function_exists('get_term_people')) {
		function get_term_people($term_id, $order_by = 'menu_order') {
			$posts = get_posts(Array(
													'numberposts' => -1,
													'order' => 'ASC',
													'orderby' => $order_by,
													'post_type' => 'person',
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
			$prefix = get_post_meta($person->ID, 'person_title_prefix', True);
			$suffix = get_post_meta($person->ID, 'person_title_suffix', True);
			$name = $person->post_title;
			return $prefix.$name.$suffix;
		}
	}
	if(!function_exists('get_person_phones'))	{
		function get_person_phones($person_id) {
			$phones = get_post_meta($person_id, 'person_phones', True);
			return explode(',', $phones);
		}
	}
	
	ob_start();
	// Dean's Suite is always first
	$dean_suite_name = get_theme_option('aboutus_featured_group');
	$dean_suite = False;
	if($dean_suite_name !== False) {
		$dean_suite = get_term_by('name', 'Dean\'s Suite', 'rosen_org_groups');
		if($dean_suite !== False) {
			$people = get_term_people($dean_suite->term_id, 'menu_order'); 
			?>
			<div class="dept">
				<h3><?=$term->name?></h3>
				<ul class="sans clearfix">
				<?$count = 1; 
					foreach($people as $person) {
						
						$email = get_post_meta($person->ID, 'person_email', True);
						$img = get_the_post_thumbnail($person->ID, 'full');
						?>
					<li class="<?=((($count % 4) == 0) ? 'last':'')?>">
						<a href="<?=get_permalink($person->ID)?>">
							<? if($img == '') {?>
								<img src="<?=bloginfo('stylesheet_directory')?>/static/img/no-photo.jpg" alt="not photo available"/>
							<? } else {?> 
								<?=$img?>
							<? } ?>
						</a>
							<p class="name">
								<strong>
									<a href="<?=get_permalink($person->ID)?>">
										<?=get_person_name($person)?>
									</a>
								</strong>
							</p>
							<p class="title">
								<a href="<?=get_permalink($person->ID)?>">
									<?=get_post_meta($person->ID, 'person_jobtitle', True)?>
								</a>
							</p>
					</li>
				<?$count++; 
					} ?>
				</ul>
			</div><?
		}
	}
	$terms = get_terms('rosen_org_groups', Array('orderby' => 'name'));
	foreach($terms as $term) {
		if($dean_suite_name === False || $dean_suite === False || $term->term_id != $dean_suite->term_id) {
			$people = get_term_people($term->term_id, 'title'); ?>
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
							foreach($people as $person) {
								$count++;
								$email = get_post_meta($person->ID, 'person_email', True);
							?>
								<tr class="sans <?=((($count % 2) == 0) ? 'even' : 'odd')?>">
									<td class="name">
										<a href="<?=get_permalink($person->ID)?>">
											<?=get_person_name($person)?>
										</a>
									</td>
									<td class="job_title">
										<a href="<?=get_permalink($person->ID)?>">
											<?=get_post_meta($person->ID, 'person_jobtitle', True)?>
											</a>
										</td>
									<td class="phones">
										<ul>
											<? foreach(get_person_phones($person->ID) as $phone) { ?>
											<li>
												<a href="<?=get_permalink($person->ID)?>">
													<?=$phone?>
												</a>
											</li>
											<? } ?>
										</ul>
									</td>
									<td class="email">
										<?=(($email != '') ? '<a href="mailto:'.$email.'">'.$email.'</a>' : '')?>
									</td>
								</tr>
						<? } ?>
					</body>
				</table>
			</div><?
		}
	}
	return ob_get_clean();
}
add_shortcode('sc-staff', 'sc_staff');
?>