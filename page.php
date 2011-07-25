<?php disallow_direct_load('page.php');?>
<?php get_header(); the_post();?>
	
	<div class="span-24 last page-content" id="<?=$post->post_name?>">
		<div class="span-15 append-1">
			<article class="serif">
				<h2><?php the_title();?></h2>
				<?php the_content();?>
			</article>
		</div>
		<div id="sidebar" class="span-8 last">
			<?=get_sidebar();?>
		</div>
		
		<div class="clear"></div>
		<?php get_template_part('templates/below-the-fold'); ?>
		
	</div>

<?php get_footer();?>