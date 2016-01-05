<?php get_header();?>
<div class="page-content" id="post-list">
	<div class="span-15 append-1">
		<?php while(have_posts()): the_post();?>
		<article>
			<h1><a href="<?php the_permalink();?>"><?php the_title();?></a></h1>
			<div class="meta">
				<span class="date"><?php the_time("F j, Y");?></span>
				<span class="author">by <?php the_author_posts_link();?></span>
			</div>
			<div class="summary">
				<?php the_excerpt();?>
			</div>
		</article>
		<?php endwhile;?>
	</div>
	<div id="sidebar" class="span-8 last">
		<?=get_sidebar();?>
	</div>
	<div class="clear"><!-- --></div>
</div>
<?php get_footer();?>
