jQuery(document).ready(function($) {
	// first run.
	var template_box = $('#page_template');
	var metabox = $('div#ci_page_room_listing_meta');
	metabox.hide();
	if( template_box.val() == 'template-rooms.php' )
		metabox.show();


	// show only the custom fields we need in the post screen
	$('#page_template').change(function(){
	if( template_box.val() == 'template-rooms.php' )
			metabox.show();
		else
			metabox.hide();
	});


	$('#ci_cpt_room_meta .amenities .inside').sortable();
	$('#amenities-add-field').click( function() {
		$('.amenities .inside').append('<p class="amenities-field"><input type="text" name="ci_cpt_room_amenities[]" /> <a href="#" class="amenities-remove">Remove me</a></p>');
		return false;		
	});
	$('#ci_cpt_room_meta').on('click', '.amenities-remove', function() {
		$(this).parent('p').remove();
		return false;
	});

}); 
