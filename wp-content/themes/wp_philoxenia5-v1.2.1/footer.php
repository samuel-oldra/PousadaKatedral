	<div id="footer-wrap">
	
		<?php if (!is_page_template('template-front.php')) ; ?>
	
		<footer class="footer cols wrap group">
			<?php dynamic_sidebar('footer'); ?>
		</footer><!-- /footer -->
		
		<div id="credits-wrap">
			<div id="credits" class="wrap group"><?php echo ci_footer(); ?></div>
		</div>
	</div><!-- /footer-wrap -->
	
</div><!-- /page -->
<?php wp_footer(); ?>
</body>
</html>
