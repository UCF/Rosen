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
						<?=get_menu('footer-menu-left', 'menu vertical clearfix footer-menu', 'footer-menu-left', True)?>
					</div>
					<div class="span-5 last">
						<?=get_menu('footer-menu-right', 'menu vertical clearfix footer-menu', 'footer-menu-right', True)?>
					</div>
				</div>
				<div class="span-8 last">
					<div class="best-colleges-container">
						<img src="<?php echo THEME_IMG_URL ?>/best-colleges-most-innovative-2016.png" width="129" height="125" alt="Best Colleges Most Innovative 2016" class="best-colleges-badge">
					</div>
					<div class="footer-pub" id="search">
						<h3 class="serif">Search Rosen College:</h3>
						<form method="get" action="<?=home_url( '/' )?>" role="search" id="search">
							<input type="text" value="<?=isset($_GET['s']) ? htmlentities($_GET['s']) : 'Enter Search Term...'?>" name="s"  />
							<input type="submit" value="Search" />
						</form>
					</div>
				</div>
			</div>
		</div><!-- #blueprint-container -->
		<?php echo get_remarketing_code(); ?>
		<?="\n".footer_()."\n"?>
	</body>
</html>
