<?php global $ci, $ci_defaults, $load_defaults; ?>
<?php if ($load_defaults===TRUE): ?>
<?php
	/*
		The Social Widget and related options tab has been deprecated in favor of the Socials Ignited plugin.
		You are advised to use that instead. http://wordpress.org/extend/plugins/socials-ignited/
		If you absolutely must use the older social widget, uncomment the following add_filter() line.
	*/
	
	//add_filter('ci_panel_tabs', 'ci_add_tab_social_options', 60);
	
	if( !function_exists('ci_add_tab_social_options') ):
		function ci_add_tab_social_options($tabs) 
		{ 
			$tabs[sanitize_key(basename(__FILE__, '.php'))] = __('Social Options', 'ci_theme'); 
			return $tabs; 
		}
	endif;

	// Default values for options go here.
	// $ci_defaults['option_name'] = 'default_value';
	// or
	// load_panel_snippet( 'snippet_name' );
	load_panel_snippet('social');


?>
<?php else: ?>

	<?php load_panel_snippet('social'); ?>
	
<?php endif; ?>
