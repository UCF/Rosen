<? if(is_front_page()) {
		$page_posts = query_posts(array(	'numberposts' => 1,
																			'orderby'     => 'rand',
																			'post_type'   => 'page',
																			'meta_key'    => 'page_frontpage',
																			'meta_value'  => 'on'));
		$post = $page_posts[0];
		setup_postdata($post);?>
<? } ?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
		<?=header_()?>
		<!--[if IE]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<style>article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {display: block;}</style>
		<link href="http://cdn.ucf.edu/webcom/-/css/blueprint-ie.css" rel="stylesheet" media="screen, projection">
		<![endif]-->
		<script type="text/javascript">
			<?php if(GA_ACCOUNT):?>
			var GA_ACCOUNT      = '<?=GA_ACCOUNT?>';
			<?php endif;?>
			<?php if(CB_UID):?>
			var CB_UID          = '<?=CB_UID?>';
			var CB_DOMAIN       = '<?=CB_DOMAIN?>';
			<?php endif?>	
			var _sf_startpt     = (new Date()).getTime();
			var _gaq            = _gaq || [];
			<?php if(EVENT_PROXY_URL):?>
			var EVENT_PROXY_URL = '<?=EVENT_PROXY_URL?>';
			<?php endif?>	
			var THEME_IMG_URL     = '<?=THEME_IMG_URL?>';
			var CURRENT_PAGE_NAME = '<?=$post->post_title?>';
		</script>
	</head>
	<body class="<?=body_classes()?>" id="<?=is_front_page() ? 'home' : ''?><?=is_page() ? 'page' : ''?>">
	<div id="blueprint-container" class="container">
	<? if(is_front_page()) { ?>
		<div id="splash-container">
			<?=get_the_post_thumbnail($post->ID, 'full')?>
		</div>
	<? } ?>
		<div id="content-container">
			<div id="header" class="span-15 last">
				<h1 class="span-15 last sans"><a href="<?=bloginfo('url')?>"><?=bloginfo('name')?></a></h1>
			</div>