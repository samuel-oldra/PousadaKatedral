<?php
//
// Room listing template meta box
//
add_action('admin_init', 'ci_add_page_room_listing_meta');
add_action('save_post', 'ci_update_page_room_listing_meta');
function ci_add_page_room_listing_meta(){
	add_meta_box("ci_page_room_listing_meta", __('Base Rooms Category', 'ci_theme'), "ci_add_page_room_listing_meta_box", "page", "normal", "high");
}

function ci_update_page_room_listing_meta($post_id){
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
	if (isset($_POST['post_view']) and $_POST['post_view']=='list') return;

	if (isset($_POST['post_type']) && $_POST['post_type'] == "page")
	{
		update_post_meta($post_id, "base_rooms_category", (isset($_POST["base_rooms_category"]) ? $_POST["base_rooms_category"] : '') );
	}
}

function ci_add_page_room_listing_meta_box(){
	global $post;
	$category = get_post_meta($post->ID, 'base_rooms_category', true);
	?>
	<p><?php _e('Select the base rooms category. Only rooms of the selected category and sub-categories will be displayed. If you don\'t select one (i.e. empty) all room categories will be shown. You need to select a <strong>Rooms Listing</strong> template for this option to work.', 'ci_theme'); ?></p>
	<?php wp_dropdown_categories(array(
		'selected'=>$category,
		'name' => 'base_rooms_category',
		'show_option_none' => ' ',
		'taxonomy' => 'room_category',
		'hierarchical' => 1,
		'show_count' => 1,
		'hide_empty' => 0
	)); ?>
	<?php
}

?>
