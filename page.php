<?php disallow_direct_load('page.php');?>
<?php get_header(); the_post();?>
	
	<div class="span-24 last page-content" id="<?=$post->post_name?>">
		<div class="span-15 append-1">
			<article class="serif">
				<h2><?php the_title();?></h2>
				<?php the_content();?>
				<? if( ($_quote = get_post_meta($post->ID, 'page_quote', True)) != '') {?>
					<p class="quote clear"><?=$_quote?></p>
				<? } ?>
			</article>
		</div>
		<div id="sidebar" class="span-8 last">
			<?=get_sidebar();?>
		</div>
		
		<div id="below-the-fold" class="clear">
			<?php get_template_part('templates/below-the-fold'); ?>
		</div>
	</div>

<?php get_footer();?>