<?php disallow_direct_load('home.php');?>
<?php get_header(); ?>
	<div class="span-24 last page-content" id="<?=$post->post_name?>">
		<div class="span-15 append-1">
			<article>
				<? $title_color = get_post_meta($post->ID, 'page_titlecolor', True); ?>
				<h2 class="serif">
					<a<?=($title_color != '' ? ' style="color:'.$title_color.'"' : '')?> href="<?=get_permalink($post->ID)?>">
						<?php the_title();?>
					</a>
				</h2>
				<p <?=($title_color != '' ? ' style="color:'.$title_color.'"' : '')?> id="subtitle" class="serif">
					<?=get_post_meta($post->ID, 'page_subtitle', True)?>
				</p>
				<ul id="promos">
				  <?=get_promo_html()?>
				</ul>
				<div class="span-7 append-1">
				  <div class="widget">
					<h3 class="serif">Events</h3>
						<ul class="events clearfix"></ul>
				</div>
				</div>
				<div class="span-7 last">
				  <div class="widget">
						<h3 class="serif">Student Resources</h3>
						<?=get_menu('student-resources', 'menu vertical sans')?>
						<h3 class="serif">Academic Degress</h3>
							<?=get_menu('academic-degrees', 'menu vertical sans last')?>
 					</div>
				</div>
			</article>
		</div>
		<div id="sidebar" class="span-8 last">
			<?=get_sidebar();?>
		</div>
		<div class="clear"><!-- --></div>
	</div>
<?php get_footer();?>