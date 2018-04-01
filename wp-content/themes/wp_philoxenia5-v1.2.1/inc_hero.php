<?php 
	global $post;

	$img_url = ci_setting('default_header_bg');
	$img_id = ci_setting('default_header_bg_hidden');

	// Assign first the fallback image. It will be replaced next if another featured image exists.
	if(!empty($img_url) and !empty($img_id))
	{
		$img_info = wp_get_attachment_image_src($img_id, 'ci_featured_header');
	}

	// Replace the header image if the post/page has a featured image assigned
	if(is_single() or is_page())
	{
		if(has_post_thumbnail() and get_post_meta($post->ID, 'ci_cpt_room_featured_header', true) != 'disabled')
		{
			$img_id = get_post_thumbnail_id($post->ID);
			$img_info = wp_get_attachment_image_src($img_id, 'ci_featured_header');
			if(!empty($img_info))
			{
				$img_url = $img_info[0];
			}

		}
	}


?>
<div id="hero" style="background:url(<?php echo $img_url; ?>) center;">
	<div class="hero-content wrap">
		<?php if (get_post_type()=='room' and !is_post_type_archive()): ?>
			<h2><?php single_post_title(); ?></h2>
		<?php elseif (is_post_type_archive('room')): ?>
			<h2><?php post_type_archive_title(); ?></h2>
		<?php elseif (is_404()): ?>
			<h2><?php _e('Not found', 'ci_theme'); ?></h2>
		<?php elseif (is_single()): ?>
			<h2><?php single_post_title(); ?></h2>
		<?php elseif (is_search()): ?>
			<h2><?php _e('Search', 'ci_theme'); ?></h2>
		<?php elseif (is_page_template()): ?>
			<h2><?php single_post_title(); ?></h2>
		<?php elseif (is_page()): ?>
			<h2><?php single_post_title(); ?></h2>
		<?php else: ?>
			<h2><?php _e('From the blog', 'ci_theme'); ?></h2>
			<a href="<?php echo ci_rss_feed(); ?>" class="btn-book"><?php _e('Subscribe to RSS','ci_theme'); ?></a>
		<?php endif; ?>
	</div>
</div><!-- /hero -->	
