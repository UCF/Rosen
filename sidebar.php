<?php disallow_direct_load('sidebar.php');?>

<?php if(!function_exists('dynamic_sidebar') or !dynamic_sidebar('Sidebar')):?>
<? if(is_page()) {?>
	<?=get_the_post_thumbnail($post->ID, 'sidebar-feature')?>
<? } ?>
<?=get_menu('sidebar-nav-menu', 'menu vertical sans', 'sidebar-nav-menu', (is_front_page() ? True : False))?>
<div id="sidebar-social">
	<h3><?=get_menu_title('sidebar-social-menu')?></h3>
	<?=get_menu('sidebar-social-menu', 'menu vertical sans', 'sidebar-social-menu')?>
</div>
<?
	$home_page_title = get_theme_option('home_page_spotlight_title'); 
	$home_page_content = get_theme_option('home_page_spotlight_content');
	if($home_page_title !== FALSE && $home_page_content !== FALSE &&
			$home_page_title != '' && $home_page_content != ''):
?>
<div class="sidebar-pub" id="theme-shoutout">
	<h3 class="serif"><?=$home_page_title?></h3>
	<p class="serif">
		<?=$home_page_content?>
	</p>
</div>
<div class="sidebar-pub" id="newsletter_signup">
	<h3 class="serif">Sign Up for  the Newsletter:</h3>
	<form action="<?=bloginfo('url')?>" method="post">
		<input type="text" name="cc_email" value="Enter Email Address..." />
		<input type="submit" value="Submit" />
	</form>
</div>
<?=get_today_news()?>
<? endif; ?>
<?php endif;?>
