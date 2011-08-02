<?php disallow_direct_load('sidebar.php');?>

<?php if(!function_exists('dynamic_sidebar') or !dynamic_sidebar('Sidebar')):?>
<? if(is_page()) {?>
	<?=get_the_post_thumbnail($post->ID, 'sidebar-feature')?>
<? } ?>
<?=get_menu('sidebar-nav-menu', 'menu vertical sans', 'sidebar-nav-menu', (is_front_page() ? True : False))?>
<div class="sidebar-pub" id="search">
	<h3 class="serif">Search Rosen College:</h3>
	<form>
	  <input type="text" name="q" value="Enter Search Term..."/>
		<input type="submit" value="Search" />
	</form>
</div>
<?=get_today_news()?>
<div class="sidebar-pub">
	<h3 class="serif">Let us Cater to You</h3>
	<p class="serif">
		Choose our Mediterranean-style venue for your next meeting or event.  
		Our professional staff will add a personal touch and demonstrate 
		service that is beyond compare
	</p>
</div>
<div id="sidebar-social">
	<h3>Get Social:</h3>
	<?=get_menu('sidebar-social-menu', 'menu vertical sans', 'sidebar-social-menu')?>
</div>
<?php endif;?>