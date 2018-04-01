<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php
	add_filter('ci_panel_tabs', 'ci_add_tab_site_options', 10);
	if( !function_exists('ci_add_tab_site_options') ):
		function ci_add_tab_site_options($tabs) 
		{ 
			$tabs[sanitize_key(basename(__FILE__, '.php'))] = __('Site Options', 'ci_theme'); 
			return $tabs; 
		}
	endif;

	// Default values for options go here.
	// $ci_defaults['option_name'] = 'default_value';
	// or
	// load_panel_snippet( 'snippet_name' );

	load_panel_snippet('logo');
	load_panel_snippet('favicon');
	load_panel_snippet('touch_favicon');
	load_panel_snippet('footer_text');

?>
<?php else: ?>
	
	<?php load_panel_snippet('logo'); ?>

	<?php load_panel_snippet('favicon'); ?>

	<?php load_panel_snippet('touch_favicon'); ?>

	<?php load_panel_snippet('footer_text'); ?>

	<?php load_panel_snippet('sample_content'); ?>

<?php endif; ?>
