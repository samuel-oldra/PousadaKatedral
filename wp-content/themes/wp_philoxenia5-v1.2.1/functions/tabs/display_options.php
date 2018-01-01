<?php global $ci, $ci_defaults, $load_defaults, $content_width; ?>
<?php if ($load_defaults===TRUE): ?>
<?php
	add_filter('ci_panel_tabs', 'ci_add_tab_display_options', 30);
	if( !function_exists('ci_add_tab_display_options') ):
		function ci_add_tab_display_options($tabs) 
		{ 
			$tabs[sanitize_key(basename(__FILE__, '.php'))] = __('Display Options', 'ci_theme'); 
			return $tabs; 
		}
	endif;
	
	// Default values for options go here.
	// $ci_defaults['option_name'] = 'default_value';
	// or
	// load_panel_snippet( 'snippet_name' );
	load_panel_snippet('excerpt');
	load_panel_snippet('seo');
	load_panel_snippet('comments');

	$ci_defaults['default_header_bg'] 			= ''; // Holds the URL of the image file to use as header background
	$ci_defaults['default_header_bg_hidden'] 	= ''; // Holds the attachment ID of the image file to use as header background
	
?>
<?php else: ?>
		
	<fieldset class="set">
		<p class="guide"><?php _e('Upload or select an image to be used as the default header background on your blog section. This will be displayed only on listing pages and when the currently showing post has no featured image attached. For best results, use a high resolution image, more than 1920 pixels wide.', 'ci_theme'); ?></p>
		<fieldset>
			<?php ci_panel_upload_image('default_header_bg', __('Upload a header image', 'ci_theme')); ?>
			<input id="default_header_bg_hidden" type="hidden" name="<?php echo THEME_OPTIONS; ?>[default_header_bg_hidden]" value="<?php echo $ci['default_header_bg_hidden']; ?>" />
		</fieldset>
	</fieldset>
	
	<?php load_panel_snippet('excerpt'); ?>	

	<?php load_panel_snippet('seo'); ?>	

	<?php load_panel_snippet('comments'); ?>	

<?php endif; ?>
