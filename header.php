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
		<?="\n".header_()."\n"?>
		<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!--[if IE]>
		<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<style>article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {display: block;}</style>
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
			var THEME_IMG_URL     = '<?=THEME_IMG_URL?>';
			var CURRENT_PAGE_NAME = '<?=$post->post_title?>';
		</script>
	</head>
	<body class="<?=body_classes()?>" id="<?=is_front_page() ? 'home' : 'page'?>">
	<div id="blueprint-container" class="container">
	<? if(is_front_page()) { ?>
		<div id="splash-container">
			<?=get_the_post_thumbnail($post->ID, 'full')?>
		</div>
	<? } ?>
		<div id="content-container">
			<? if(isset($_SESSION['cc_error'])) {?>
				<div class="error">
					<? echo $_SESSION['cc_error']; unset($_SESSION['cc_error']);?>
				</div>
			<? } ?>
			<? if(isset($_SESSION['cc_success'])) {?>
				<div class="success">
					<? echo $_SESSION['cc_success']; unset($_SESSION['cc_success']);?>
				</div>
			<? } ?>
			<div id="header" class="span-15 last">
				<h1 class="span-15 last sans"><a href="<?=bloginfo('url')?>"><?=bloginfo('name')?></a></h1>
			</div>
			<div id="mobile-nav" class="span-24 last">
				<a id="mobile-nav-toggle" href="#">
					<span class="hamburger"></span>
					Menu
				</a>
				<?php echo get_menu( 'sidebar-nav-menu', 'menu vertical sans', 'mobile-nav-menu', ( is_front_page() ? True : False ) ); ?>
			</div>
