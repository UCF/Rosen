			</div><!-- #blueprint-container -->
		</div>
		<div id="footer" class="sans">
			<div class="container">
				<div class="span-6">
					<a href="http://www.ucf.edu" id="logo">University of Central Florida</a>
					<p id="contact">
						UCF Rosen College of Hospitality Management
						<br />
						9007 Universal Blvd.
						<br />
						Orlando, Florida 32819
						<br />
						407.903.8000
					</p>
				</div>
				<div class="span-10">
					<?=get_menu('footer-menu', 'menu vertical clearfix', 'footer-menu', NULL, 1)?>
				</div>
				<div class="span-7 last" id="newsletter_signup">
					<h3 class="serif">Sign Up for  the Newsletter:</h3>
					<form>
						<input type="text" value="Enter Email Address..." />
						<input type="submit" value="Submit" />
					</form>
				</div>
			</div>
		</div>
		<?=footer_()?>
	</body>
</html>