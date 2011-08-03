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
</div>