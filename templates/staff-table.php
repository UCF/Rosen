<div class="dept clear">
	<h3><?=$term->name?></h3>
	<table>
		<thead class="sans">
			<tr>
				<th scope="col" class="name">Name</th>
				<th scope="col" class="job_title">Title</th>
				<th scope="col" class="phones">Phone(s)</th>
				<th scope="col" class="email">E-Mail</th>
			</tr>
		</thead>
		<tbody class="serif">
			<?$count = 0;
				foreach($people as $person) {
					$count++;
					$email     = get_post_meta($person->ID, 'person_email', True);
				?>
					<tr class="sans <?=((($count % 2) == 0) ? 'even' : 'odd')?>" data-profile-url="<?=get_permalink($person->ID);?>">
						<td class="name">
							<a href="<?=get_permalink($person->ID)?>"><?=get_person_name($person)?></a>
						</td>
						<td class="job_title">
							<a href="<?=get_permalink($person->ID)?>"><?=get_post_meta($person->ID, 'person_jobtitle', True)?></a>
						</td> 
						<td class="phones">
							<a href="<?=get_permalink($person->ID)?>">
								<ul>
									<? foreach(get_person_phones($person->ID) as $phone) { ?>
									<li><?=$phone?></li>
									<? } ?>
								</ul>
							</a>
						</td>
						<td class="email">
							<?=(($email != '') ? '<a href="mailto:'.$email.'">'.$email.'</a>' : '')?>
						</td>
					</tr>
			<? } ?>
		</tbody>
	</table>
</div>