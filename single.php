<?php disallow_direct_load('single.php');?>
<?php get_header(); the_post();?>
	<div class="span-24 last page-content" id="<?=$post->post_name?>">
		<div class="span-15 append-1">
			<article>
				<h2><?php echo get_person_title(); ?></h2>
				<?php the_content();?>
			</article>
		</div>
		<div id="sidebar" class="span-8 last">
			<?php get_sidebar(); ?>
		</div>
		<div class="clear"><!-- --></div>
	</div>
<?php get_footer();?>