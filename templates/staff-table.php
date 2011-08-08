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
					$job_title = get_post_meta($person->ID, 'person_jobtitle', True);
					$email     = get_post_meta($person->ID, 'person_email', True);
					$phones    = get_person_phones($person->ID);
				?>
					<tr class="sans <?=((($count % 2) == 0) ? 'even' : 'odd')?>">
						<td class="name">
								<?=get_person_name($person)?>
						</td>
						<td class="job_title">
						<?=$job_title?>
						</td> 
						<td class="phones">
							<ul>
								<? foreach($phones as $phone) { ?>
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
		</tbody>
	</table>
</div>