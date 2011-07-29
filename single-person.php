<?php disallow_direct_load('single.php');?>
<?php get_header(); the_post();?>
	
	<div class="span-24 last page-content" id="<?=$post->post_name?>">
		<? 
			$img = get_the_post_thumbnail($post->ID, 'full');
			$title = get_post_meta($post->ID, 'person_jobtitle', True);
			$phones = get_post_meta($post->ID, 'person_phones', True);
			$phones = ($phones != '') ? explode(',', $phones) : Array();
			$email = get_post_meta($post->ID, 'person_email', True);
		?>
		<div class="span-18">
			<article>
				<h1><?php the_title();?></h1>
				<div id="person-meta">
					<? if($img == '') {?>
						<img src="<?=bloginfo('stylesheet_directory')?>/static/img/no-photo.jpg" alt="not photo available"/>
					<? } else {?> 
						<?=get_the_post_thumbnail($post->ID, 'full')?>
					<? } ?>
					<p class="title"><?=$title?></p>
					<ul class="phones">
						<? foreach($phones as $phone) { ?>
						<li>
							<?=$phone?>
						</li>
						<? } ?>
					</ul>
					<?=(($email != '') ? '<a href="mailto:'.$email.'">'.$email.'</a>' : '')?>
				</div>
				<?php the_content();?>
			</article>
		</div>
		<div id="sidebar" class="span-6 last">
			<?=get_sidebar();?>
		</div>
	</div>

<?php get_footer();?>