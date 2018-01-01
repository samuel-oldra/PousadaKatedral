jQuery(document).ready(function ($) {
	// Uploading files
	var file_frame;
	//var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
	//var set_to_post_id = 10; // Set this
        var gallery_settings_page = $('.wppg-gallery-settings-section'); //this contains all of the elements inside the gallery settings page
        var settings_save_button = $('input[name=wppg_save_gallery]');
        var hidden_info_block = "";
        //var image_description = "";
        //var escaped_descr = "";
        var img_count = 0;


	 
	jQuery('.wppg_upload_image_button').live('click', function( event ){
	 
	event.preventDefault();
	 
	// If the media frame already exists, reopen it.
	if (file_frame) {
	// Set the post ID to what we want
	//file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
	// Open frame
	file_frame.open();
	return;
	} else {
	// Set the wp.media post id so the uploader grabs the ID we want when initialised
	//wp.media.model.settings.post.id = set_to_post_id;
	}
	 
	// Create the media frame.
	file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Choose Your Images For Your PhotoSeller Gallery',//jQuery( this ).data( 'uploader_title' ),
            button: {
                text: 'Insert Into Gallery',//jQuery( this ).data( 'uploader_button_text' ),
            },
            multiple: true // Set to true to allow multiple files to be selected
	});
	 
	// When an image is selected, run a callback.
	file_frame.on( 'select', function() {
            var selection = file_frame.state().get('selection');
            selection.map( function( attachment ) {
                attachment = attachment.toJSON();
                hidden_info_block = hidden_info_block + "<input class='wppg_gallery_img_info' type='hidden' name='wppg_img_id_row_"+img_count+"' value='" + attachment.id + "'/>";
                img_count = img_count + 1;
            });
            settings_save_button.after(hidden_info_block);
            if ((img_count > 0) && (gallery_settings_page.find('.wppg_gallery_images').length == 0)){
                //if there are images specified let's display a <tr> row before the images stating how many were selected
                var upload_button_row = gallery_settings_page.find('.wppg_upload_button_row');
                upload_button_row.after('<tr class="wppg_gallery_images"><th scope="row"><label>Number of Images Selected:</label></th><td><span class="wppg_yellow_box">'+img_count+' images were selected</span></td></tr>');
            }
            //add hidden input element which stores the number of images selected
            if ($('#post-body').find('input[name=wppg_img_count]').length == 0)
            {
                settings_save_button.after("<input type='hidden' name='wppg_img_count' value='" + img_count + "'/>");
            }else{
                $('input[name=wppg_img_count]').attr('value',(img_count));
            }
	});
	
	// Finally, open the modal
	file_frame.open();
	});
        
	// Restore the main ID when the add media button is pressed
//	jQuery('a.add_media').on('click', function() {
//            wp.media.model.settings.post.id = wp_media_post_id;
//	});
    //Triggers the more info toggle link
    $(".wppg_more_info_body").hide();//hide the more info on page load
    $(".wppg_more_info_anchor").click(function(){
        $(this).next(".wppg_more_info_body").animate({ "height": "toggle"});
        var toogle_char_ref = $(this).find(".wppg_more_info_toggle_char");
        var toggle_char_value = toogle_char_ref.text();
        if(toggle_char_value === "+"){
            toogle_char_ref.text("-");
        }
        else{
             toogle_char_ref.text("+");
        }
    });
    //End of more info toggle

});