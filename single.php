<?php disallow_direct_load('single.php');?>
<?php get_header(); the_post();?>
	
	<div class="span-24 last page-content" id="<?=$post->post_name?>">
		<div class="span-15 append-1">
			<article>
				<h1><?php the_title();?></h1>
				<? if($post->post_type == 'person') echo get_person_meta($post->ID); ?>
				<?php the_content();?>
			</article>
		</div>
		<div id="sidebar" class="span-8 last">
			<?=get_sidebar();?>
		</div>
	</div>

<?php get_footer();?>