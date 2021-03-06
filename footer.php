<?
	$footer_badge_content = get_theme_option('footer_badge_content');
?>
			</div><!-- #blueprint-container -->
		</div>
		<div id="footer" class="sans">
			<div class="container">
				<div class="span-6">
					<a href="http://www.ucf.edu" id="logo">University of Central Florida</a>
					<p id="contact">
						UCF Rosen College of Hospitality Management
						<br />
						9907 Universal Blvd.
						<br />
						Orlando, Florida 32819
						<br />
						407.903.8000
						<br />
						<a href="mailto:rchminfo@ucf.edu">rchminfo@ucf.edu</a>
					</p>
				</div>
				<div class="span-10">
					<div class="span-5">
						<?php echo get_menu('footer-menu-left', 'menu vertical clearfix footer-menu', 'footer-menu-left', True)?>
					</div>
					<div class="span-5 last">
						<?php echo get_menu('footer-menu-right', 'menu vertical clearfix footer-menu', 'footer-menu-right', True)?>
					</div>
				</div>
				<div class="span-8 last">
					<?php if($footer_badge_content): ?>
						<div class="footer-right-content">
							<?php echo $footer_badge_content; ?>
						</div>
					<?php endif; ?>
					<div class="footer-pub" id="search">
						<h3 class="serif">Search Rosen College:</h3>
						<form method="get" action="<?php echo home_url( '/' )?>" role="search" id="search">
							<input type="text" value="<?php echo isset($_GET['s']) ? htmlentities($_GET['s']) : 'Enter Search Term...'?>" name="s"  />
							<input type="submit" value="Search" />
						</form>
					</div>
				</div>
			</div>
		</div><!-- #blueprint-container -->
		<?php echo get_remarketing_code(); ?>
		<?php echo "\n".footer_()."\n"?>
		<?php if ( $post && $post->post_type == 'page' ) : ?>
			<?php echo get_post_meta( $post->ID, 'page_script_snippets', True ); ?>
		<?php endif; ?>
	</body>
</html>
