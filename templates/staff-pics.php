<div class="dept">
	<h3><?=$dean_suite->name?></h3>
	<ul class="sans clearfix">
	<?$count = 1;
		$end  = False;
		foreach($people as $person) {
			
			$email = get_post_meta($person->ID, 'person_email', True);
			$img = get_the_post_thumbnail($person->ID, 'full');
			$last = (($count % 4) == 0) ? True : False;
			?>
		<li class="<?=$last ? 'last': ''?><?=$end ? 'clear' : ''?>">
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
	<?
		if($end) $end = False;
		if($last) $end = True;
		$count++; 
		} ?>
	</ul>
</div>