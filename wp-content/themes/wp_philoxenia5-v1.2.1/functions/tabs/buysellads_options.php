<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php
	add_filter('ci_panel_tabs', 'ci_add_tab_buysellads_options', 90);
	if( !function_exists('ci_add_tab_buysellads_options') ):
		function ci_add_tab_buysellads_options($tabs) 
		{ 
			$tabs[sanitize_key(basename(__FILE__, '.php'))] = __('BuySellAds Options', 'ci_theme'); 
			return $tabs; 
		}
	endif;

	// Default values for options go here.
	// $ci_defaults['option_name'] = 'default_value';
	// or
	// load_panel_snippet( 'snippet_name' );
	load_panel_snippet('buysellads');

?>
<?php else: ?>

	<?php load_panel_snippet('buysellads'); ?>

<?php endif; ?>
