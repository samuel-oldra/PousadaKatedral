<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php
	add_filter('ci_panel_tabs', 'ci_add_tab_homepage_options', 20);
	if( !function_exists('ci_add_tab_homepage_options') ):
		function ci_add_tab_homepage_options($tabs) 
		{ 
			$tabs[sanitize_key(basename(__FILE__, '.php'))] = __('Homepage Options', 'ci_theme'); 
			return $tabs; 
		}
	endif;

	// Default values for options go here.
	// $ci_defaults['option_name'] = 'default_value';
	// or
	// load_panel_snippet( 'snippet_name' );

	$ci_defaults['booking_form_page'] 	= '';
	$ci_defaults['booking_form_email'] 	= get_option('admin_email');

	load_panel_snippet('slider_cycle');

?>
<?php else: ?>

	<fieldset class="set">
		<p class="guide"><?php _e('Select your booking form page, that you have created and assigned the "Booking Form" page template. This is to redirect properly when checking availability from any page on the site. If blank, the booking form will be hidden. The booking form e-mail address is where the e-mails will be send.' , 'ci_theme'); ?></p>
		<fieldset>
			<label for="<?php echo THEME_OPTIONS; ?>[booking_form_page]"><?php _e('Select the Booking Form page', 'ci_theme'); ?></label>
			<?php wp_dropdown_pages(array(
				'show_option_none' => '&nbsp;',
				'selected'=>$ci['booking_form_page'],
				'name'=>THEME_OPTIONS.'[booking_form_page]'
			)); ?>
		</fieldset>

		<fieldset class="mt10">
			<?php ci_panel_input('booking_form_email', __('Booking form E-mail address', 'ci_theme')); ?>
		</fieldset>
	</fieldset>

	<?php load_panel_snippet('slider_cycle'); ?>
		
<?php endif; ?>
