<?php 
	get_template_part('panel/constants');

	load_theme_textdomain( 'ci_theme', get_template_directory() . '/lang' );

	// This is the main options array. Can be accessed as a global in order to reduce function calls.
	$ci = get_option(THEME_OPTIONS);
	$ci_defaults = array();

	// The $content_width needs to be before the inclusion of the rest of the files, as it is used inside of some of them.
	if ( ! isset( $content_width ) ) $content_width = 649;

	//
	// Let's bootstrap the theme.
	//
	get_template_part('panel/bootstrap');


	//
	// Define our various image sizes.
	//
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 292, 150, true );
	add_image_size('ci_blog_thumb', 192, 146, true);
	add_image_size('ci_home_slider', 1920, 485, true);
	add_image_size('ci_featured_header', 1920, 245, true);
	add_image_size('ci_room_slider', 710, 323, true);
	add_image_size('ci_room_slider_thumb', 87, 69, true);


	// Let the user choose a color scheme on each post individually.
	add_ci_theme_support('post-color-scheme', array('page', 'post', 'room'));


	// Inform the user that a hi-res picture should be used. This is displayed on the Featured Image meta box.
	add_filter('admin_post_thumbnail_html', 'ci_post_thumbnail_meta_box');
	function ci_post_thumbnail_meta_box($content)
	{
		if(strpos($content, 'id="remove-post-thumbnail"')===FALSE)
		{
			$content .= __('<p>For best results, use an image that is at least 1920 pixels wide.</p>', 'ci_theme');
		}
		return $content;
	}

	// Register prettyPhoto to open images
	add_filter('the_content', 'prettyphotorel', 12);
	add_filter('get_comment_text', 'prettyphotorel');
	function prettyphotorel ($content)
	{   global $post;
		$pattern = "/<a(.*?)href=('|\")([^>]*).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>(.*?)<\/a>/i";
	    $replacement = '<a$1href=$2$3.$4$5 rel="prettyPhoto['.$post->ID.']"$6>$7</a>';
	    $content = preg_replace($pattern, $replacement, $content);
	    return $content;
	}

	
?>
