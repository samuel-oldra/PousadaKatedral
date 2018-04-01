<?php
//
// Uncomment one of the following two. Their functions are in panel/generic.php
//
add_action('wp_enqueue_scripts', 'ci_enqueue_modernizr');
//add_action('wp_enqueue_scripts', 'ci_print_html5shim');


// This function lives in panel/generic.php
add_action('wp_footer', 'ci_print_selectivizr', 100);



add_action('init', 'ci_register_theme_scripts');
if( !function_exists('ci_register_theme_scripts') ):
function ci_register_theme_scripts()
{
	//
	// Register all scripts here, both front-end and admin. 
	// There is no need to register them conditionally, as the enqueueing can be conditional.
	//

	wp_register_script('jquery-superfish', get_child_or_parent_file_uri('/panel/scripts/superfish.js'), array('jquery'), false, true);
	wp_register_script('jquery-ui-custom', get_child_or_parent_file_uri('/js/jquery-ui-1.8.17.custom.min.js'), array('jquery'), false, true);
	wp_register_script('jquery-prettyphoto', get_child_or_parent_file_uri('/js/jquery.prettyPhoto.js'), array('jquery'), false, true);
	wp_register_script('jquery-carousel', get_child_or_parent_file_uri('/js/jquery.jcarousel.min.js'), array('jquery'), false, true);			

	wp_register_script('ci-front-scripts', get_child_or_parent_file_uri('/js/ci_scripts.js'),
		array(
			'jquery',
			'jquery-prettyphoto',
			'jquery-superfish',
			'jquery-cycle-all',
			'jquery-ui-custom'
		),
		CI_THEME_VERSION, true);
}
endif;



add_action('wp_enqueue_scripts', 'ci_enqueue_theme_scripts');
if( !function_exists('ci_enqueue_theme_scripts') ):
function ci_enqueue_theme_scripts()
{
	//
	// Enqueue all (or most) front-end scripts here.
	// They can be also enqueued from within template files.
	//	
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );


	// carousel script is only needed in the room template
	if (get_post_type()=='room')
		wp_enqueue_script('jquery-carousel');

	$isAuto = ci_setting('slider_autoslide');
	$params['slider_effect'] = ci_setting('slider_effect');
	$params['slider_timeout'] = empty($isAuto) ? "0" : (string)ci_setting('slider_timeout');
	$params['slider_sync'] = ci_setting('slider_sync')=='enabled' ? 'true' : 'false';
	$params['slider_speed'] = (string)ci_setting('slider_speed');

	wp_enqueue_script('ci-front-scripts');
	wp_localize_script('ci-front-scripts', 'ThemeOption', $params);

}
endif;


if( !function_exists('ci_enqueue_admin_theme_scripts') ):
add_action('admin_enqueue_scripts','ci_enqueue_admin_theme_scripts');
function ci_enqueue_admin_theme_scripts() 
{
	global $pagenow;

	//
	// Enqueue here scripts that are to be loaded on all admin pages.
	//

	if(is_admin() and $pagenow=='themes.php' and isset($_GET['page']) and $_GET['page']=='ci_panel.php')
	{
		//
		// Enqueue here scripts that are to be loaded only on CSSIgniter Settings panel.
		//

	}
}
endif;

?>
