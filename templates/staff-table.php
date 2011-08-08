<div class="dept">
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
								<?=get_person_name($person)?>
						</td>
						<td class="job_title">
						<?=get_post_meta($person->ID, 'person_jobtitle', True)?>
						</td> 
						<td class="phones">
							<ul>
								<? foreach(get_person_phones($person->ID) as $phone) { ?>
								<li><?=$phone?></li>
								<? } ?>
							</ul>
						</td>
						<td class="email">
							<?=(($email != '') ? '<a href="mailto:'.$email.'">'.$email.'</a>' : '')?>
						</td>
					</tr>
			<? } ?>
		</tbody>
	</table>
</div>