<?php

class WPPGPhotoGallery 
{
    //This class is for galleries & any tasks associated with creation/manipulation of galleries etc
    var $id;
    var $name; 
    var $created;
    var $watermark;
    var $watermark_width;
    var $watermark_opacity;
    var $watermark_font_size;
    var $watermark_placement;
    var $sort_order;
    var $gallery_thumb_template;
    var $page_id;
    var $display_image_on_page;
    var $enable_pagination;
    var $thumbs_per_page;
    var $password;
    var $category;
    
    function __construct($id=null) 
    {
        $this->get_gallery_settings($id);
    }
    
    function get_gallery_settings($id)
    {
        global $wpdb;
        $sql = 'SELECT * from ' . WPPG_TBL_GALLERY . " WHERE id='$id'";
        $data = $wpdb->get_row($sql, ARRAY_A);
        if($data) {
            $this->id = $data['id'];
            $this->name = $data['name'];
            $this->created = $data['created'];
            $this->watermark = $data['watermark'];
            $this->watermark_width = $data['watermark_width'];
            $this->watermark_placement = $data['watermark_placement'];
            $this->watermark_opacity = $data['watermark_opacity'];
            $this->watermark_font_size = $data['watermark_font_size'];
            $this->sort_order = $data['sort_order'];
            $this->gallery_thumb_template = $data['gallery_thumb_template'];
            $this->page_id = $data['page_id'];
            $this->display_image_on_page = $data['display_image_on_page'];
            $this->enable_pagination = $data['enable_pagination'];
            $this->thumbs_per_page = $data['thumbs_per_page'];
            return true;
      }
      else{
          return false;
      }
    }

    function create_or_update_gallery($gallery_data)
    {
        global $wpdb, $wp_photo_gallery;
        $table = WPPG_TBL_GALLERY;
        $gallery_id = $this->id;
        $result = false;
        if ($gallery_id != NULL){
            //Update existing gallery
            $condition = array('id' => $gallery_id);
            $result = $wpdb->update( $table, $gallery_data, $condition);
            if ($result == false){
                $wp_photo_gallery->debug_logger->log_debug("Update failed for Gallery ID: ".$gallery_id,4);
                echo "<br /> Gallery update failed!!!";
                return $result;
            }
        }else{
            //Create new gallery
            $result = $wpdb->insert( $table, $gallery_data);
            $gallery_id = $wpdb->insert_id;
            if ($result == false){
                $wp_photo_gallery->debug_logger->log_debug("New gallery DB insert failed!",4);
                echo "<br /> New gallery insert failed!!!";
                return $result;
            }
            $this->id = $gallery_id; //set the object id variable for the newly created gallery
        }
        return $gallery_id;
    }
    
    static function get_gallery_image_ids($gallery_id){
        global $wpdb;
        $image_ids = $wpdb->get_col( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wppg_gallery_id' AND meta_value = $gallery_id" );
        return $image_ids;
    }
    
    function process_gallery_images($num_gallery_images, $existing_gallery_id)
    {
        //let's loop through the POST data and get the image details and insert into DB
        $j=0;
        while ($j<$num_gallery_images)
        {
            //Get the names of the hidden input elements
            $current_element_img_id = "wppg_img_id_row_".$j;

            $current_image_id = $_POST[$current_element_img_id];

            $new_img_upload = false;
            $image_meta = wp_get_attachment_metadata($current_image_id);
            $img_file = $image_meta['file'];
            
            $pos = strpos($img_file, 'spgallery');
            
            if ($pos !== false) {
                $new_img_upload = true;
            }
            //Let's first check if this image is already being used by another gallery - if so, copy the image and create a new attachment post
            $src_img_gallery_id = get_post_meta($current_image_id, WPPG_ATTACHMENT_META_TAG, true);

            if($src_img_gallery_id != ''){
                //this means the image is already being used by another gallery - so let's copy the applicable files over to the tmp dir
                $res = WPPGPhotoGallery::copy_existing_images_to_gallery_dir($src_img_gallery_id, $current_image_id, $existing_gallery_id);
                if ($res) {
                    //Let's create a new post of type attachment
                    $new_image_id = WPPGPhotoGallery::create_new_post_attachment_and_meta_data($current_image_id, $existing_gallery_id);
                    if($new_image_id !== false){
                        //Let's add our special meta key for this image
                        update_post_meta($new_image_id, WPPG_ATTACHMENT_META_TAG, $existing_gallery_id);
                    }                        
                }
            }
            else if($new_img_upload)
            {
                update_post_meta($current_image_id, WPPG_ATTACHMENT_META_TAG, $existing_gallery_id);
            }
            else
            {
                //This is an existing image somewhere on this site.
                $res = WPPGPhotoGallery::copy_media_library_images_to_gallery_dir($current_image_id, $existing_gallery_id);
                if ($res) {
                    //Let's create a new post of type attachment if necessary
                    $new_image_id = WPPGPhotoGallery::create_new_post_attachment_and_meta_data($current_image_id, $existing_gallery_id);
                    if($new_image_id === true){
                        //For case where media library image is already part of this gallery's directory
                        //Let's add our special meta key for this image
                        update_post_meta($current_image_id, WPPG_ATTACHMENT_META_TAG, $existing_gallery_id);
                    }else if($new_image_id !== false){
                        //Case where a new image post was created after copying original image
                        //Let's add our special meta key for this image
                        update_post_meta($new_image_id, WPPG_ATTACHMENT_META_TAG, $existing_gallery_id);
                    }                        
                }
            }
            $j++;
        }
        return;
    }
    
    function rename_gallery_temp_dir()
    {
        
    }
    
    //Returns an array of image items for a particular gallery
    static function getGalleryItems($gallery_id)
    {
        $gallery = new WPPGPhotoGallery($gallery_id);
        $sort_order = $gallery->sort_order; //0 = by ID ascending, 1 = by ID descending, 2 = by Date ascending, 3 = by Date descending
        
        $gallery_image_ids_array = WPPGPhotoGallery::get_gallery_image_ids($gallery_id);
        $gallery_images_array = array();

        foreach($gallery_image_ids_array as $image_id){
            $thumb_url = wp_get_attachment_thumb_url($image_id);
            $attachment_img = wp_get_attachment_image($image_id);
            $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true);

            $image_post = get_post($image_id);
            $image_desc = $image_post->post_content;
            $upload_date = $image_post->post_date;
            $image_url = wp_get_attachment_url($image_id);
            //if the alt text meta is blank, let's set it to the image name
            if ($alt_text == '' || $alt_text == NULL){
                $alt_text = $image_post->post_name;
            }
            $image_info = array(
                'id' => $image_id,
                'thumb_url' => $thumb_url,
                'alt_text' => $alt_text,
                'description' => $image_desc,
                'date_uploaded' => $upload_date,
                'image_url' => $image_url
            );

            $gallery_images_array[] = $image_info;
        }
        
        return $gallery_images_array;
    }
    
    //Returns an array of gallery images and details according to the chosen sort order
    static function sortGalleryItems($gallery_images_array, $sort_order='0')
    {
        $sortArrayKey = array();//An array to hold the column we want to sort by

        //Now let's sort the array according to the chosen setting
        //0 = by ID ascending, 1 = by ID descending, 2 = by Date ascending, 3 = by Date descending
        if ($sort_order == '0')
        {
            //Nothing to do - the original array is already sorted by ID ascending
        }
        else if ($sort_order == '1')
        {
            foreach ($gallery_images_array as $item){
                $sortArrayKey[] = $item['id'];
            }
            array_multisort($sortArrayKey, SORT_DESC, $gallery_images_array); //Sort by id descending
        }
        else if ($sort_order == '2')
        {
            foreach ($gallery_images_array as $item){
                $sortArrayKey[] = $item['date_uploaded'];
            }
            array_multisort($sortArrayKey, SORT_ASC, $gallery_images_array); //Sort by date ascending
        }
        else if ($sort_order == '3')
        {
            foreach ($gallery_images_array as $item){
                $sortArrayKey[] = $item['date_uploaded'];
            }
            array_multisort($sortArrayKey, SORT_DESC, $gallery_images_array); //Sort by date descending
        }
        return $gallery_images_array; //return the sorted array
    }
    
    
    //This function will update the meta info for those images which have the temp dirname in their details.
    //It will replace all instances of the dir name "wpps_tmp" with the gallery id for that particular image.
    static function replace_image_meta_info_temp_dir($gallery_id) 
    {
        //Get the image IDs so we can start updating the wp_post and wp_post_meta tables
        $image_ids = WPPGPhotoGallery::get_gallery_image_ids($gallery_id);
        //Now cycle through each image_id (ie, post_id)
        foreach ($image_ids as $post_id){
          $wp_attachment_meta_data = get_post_meta($post_id, '_wp_attachment_metadata');
          $old_string = $wp_attachment_meta_data[0]['file'];
          $new_string = preg_replace('/'.WPPG_UPLOAD_TEMP_DIRNAME.'/', $gallery_id, $old_string, 1);
          $wp_attachment_meta_data[0]['file'] = $new_string;
          update_post_meta($post_id, '_wp_attachment_metadata', $wp_attachment_meta_data[0]);

          $wp_attached_file = get_post_meta($post_id, '_wp_attached_file');
          $old_string = $wp_attached_file[0];
          $new_string = preg_replace('/'.WPPG_UPLOAD_TEMP_DIRNAME.'/', $gallery_id, $old_string, 1);
          $wp_attached_file[0] = $new_string;
          update_post_meta($post_id, '_wp_attached_file', $wp_attached_file[0]);

          $post_info = get_post($post_id, ARRAY_A);
          $old_string = $post_info['guid'];
          $new_string = preg_replace('/'.WPPG_UPLOAD_TEMP_DIRNAME.'/', $gallery_id, $old_string, 1);
          $post_info['guid'] = $new_string;
          wp_update_post($post_info);
        }
    }
    
    /**
     * This function will copy existing gallery images to another gallery's folder.
     * Note: It will copy all versions of the image files such as 150x150 thumb, original etc 
     * (Mainly used when someone selects an image from the media uploader library which has already been uploaded and is already being used by another gallery)
     * 
     * @return true if successful, false if failed to copy
     */
    static function copy_existing_images_to_gallery_dir($src_img_gallery_id, $image_id, $current_gallery_id)
    {
        global $wp_photo_gallery;
        $result = '';
        $upload_dir = wp_upload_dir();
        $original_image_file = '';
        $thumb_150_by_150_file = '';
        $med_300_by_225_file = '';
        $large_624_by_468_file = '';
        if ($current_gallery_id == ''){
            $dest_dir = WPPG_UPLOAD_TEMP_DIRNAME;
        }else{
            $dest_dir = $current_gallery_id;
        }
        
        $image_files_to_copy = array();
        
        $image_meta = wp_get_attachment_metadata($image_id);//get_post_meta($image_id, '_wp_attachment_metadata', false);

        if (isset($image_meta['file']))
            $image_files_to_copy[] = array('orig_file_path'=>$upload_dir['basedir'].'/'.$image_meta['file'], 'filename'=>basename($image_meta['file']));
        if (isset($image_meta['sizes']['thumbnail']['file']))
            $image_files_to_copy[] = array('orig_file_path'=>$upload_dir['basedir'].'/'.WPPG_UPLOAD_SUB_DIRNAME.'/'.$src_img_gallery_id.'/'.$image_meta['sizes']['thumbnail']['file'], 'filename'=>$image_meta['sizes']['thumbnail']['file']);
        if (isset($image_meta['sizes']['medium']['file']))
            $image_files_to_copy[] = array('orig_file_path'=>$upload_dir['basedir'].'/'.WPPG_UPLOAD_SUB_DIRNAME.'/'.$src_img_gallery_id.'/'.$image_meta['sizes']['medium']['file'], 'filename'=>$image_meta['sizes']['medium']['file']);
        if (isset($image_meta['sizes']['post-thumbnail']['file']))
            $image_files_to_copy[] = array('orig_file_path'=>$upload_dir['basedir'].'/'.WPPG_UPLOAD_SUB_DIRNAME.'/'.$src_img_gallery_id.'/'.$image_meta['sizes']['post-thumbnail']['file'], 'filename'=>$image_meta['sizes']['post-thumbnail']['file']);

        if(empty($current_gallery_id)){
            $dirpath = $upload_dir['basedir'].'/'.WPPG_UPLOAD_SUB_DIRNAME.'/'.WPPG_UPLOAD_TEMP_DIRNAME;
            WP_Photo_Gallery_Utility::create_dir($dirpath);
        }else{
            //Check if the existing gallery has its own upload dir...create one if necessary
            $dirpath = $upload_dir['basedir'].'/'.WPPG_UPLOAD_SUB_DIRNAME.'/'.$dest_dir;
            if(!is_dir($dirpath)){
                WP_Photo_Gallery_Utility::create_dir($dirpath);
            }
        }
        
        foreach ($image_files_to_copy as $image_file){
            $result = copy($image_file['orig_file_path'], $upload_dir['basedir'].'/'.WPPG_UPLOAD_SUB_DIRNAME.'/'.$dest_dir.'/'.$image_file['filename']);
            if ($result === FALSE){
                $wp_photo_gallery->debug_logger->log_debug("Image copy failed from " . $image_file['orig_file_path'] ." to ". $upload_dir['basedir'].'/wp_photo_seller/'.$dest_dir.'/'.$image_file['filename'],4);
                return FALSE;
            }
        }
        
        return TRUE;
    }

    /**
     * This function will copy existing media library images to a gallery's folder.
     * Note: It will copy all versions of the image files such as 150x150 thumb, original etc 
     * (Mainly used when someone selects an image from the media uploader library which has already been uploaded and is not currently being used by another gallery)
     * 
     * @return true if successful, false if failed to copy
     */
    static function copy_media_library_images_to_gallery_dir($image_id, $current_gallery_id)
    {
        global $wp_photo_gallery;
        $result = '';
        $upload_dir = wp_upload_dir();
        if ($current_gallery_id == ''){
            $dest_dir = WPPG_UPLOAD_TEMP_DIRNAME;
        }else{
            $dest_dir = $current_gallery_id;
        }
        
        $image_files_to_copy = array();
        
        $image_meta = wp_get_attachment_metadata($image_id);//get_post_meta($image_id, '_wp_attachment_metadata', false);
        $path_parts = pathinfo($image_meta['file']);
        $image_orig_dir = $path_parts['dirname'];

        //Let's first check if this image has already been uploaded to this gallery's directory
        $wp_attached_file = get_post_meta($image_id, '_wp_attached_file');
        $lib_image_path_info = pathinfo($wp_attached_file[0]);
        $lib_image_dir = $lib_image_path_info['dirname'];
        $this_gallery_dirpath = WPPG_UPLOAD_SUB_DIRNAME .'/'.$current_gallery_id;
        if ($lib_image_dir == $this_gallery_dirpath){
            //image aready resides in the correct flder
            return true;
        }
        if (isset($image_meta['file']))
            $image_files_to_copy[] = array('orig_file_path'=>$upload_dir['basedir'].'/'.$image_meta['file'], 'filename'=>basename($image_meta['file']));
        if (isset($image_meta['sizes']['thumbnail']['file']))
            $image_files_to_copy[] = array('orig_file_path'=>$upload_dir['basedir'].'/'.$image_orig_dir.'/'.$image_meta['sizes']['thumbnail']['file'], 'filename'=>$image_meta['sizes']['thumbnail']['file']);
        if (isset($image_meta['sizes']['medium']['file']))
            $image_files_to_copy[] = array('orig_file_path'=>$upload_dir['basedir'].'/'.$image_orig_dir.'/'.$image_meta['sizes']['medium']['file'], 'filename'=>$image_meta['sizes']['medium']['file']);
        if (isset($image_meta['sizes']['post-thumbnail']['file']))
            $image_files_to_copy[] = array('orig_file_path'=>$upload_dir['basedir'].'/'.$image_orig_dir.'/'.$image_meta['sizes']['post-thumbnail']['file'], 'filename'=>$image_meta['sizes']['post-thumbnail']['file']);

        if(empty($current_gallery_id)){
            $dirpath = $upload_dir['basedir'].'/'.WPPG_UPLOAD_SUB_DIRNAME.'/'.WPPG_UPLOAD_TEMP_DIRNAME;
            WP_Photo_Gallery_Utility::create_dir($dirpath);
        }else{
            //Check if the existing gallery has its own upload dir...create one if necessary
            $dirpath = $upload_dir['basedir'].'/'.WPPG_UPLOAD_SUB_DIRNAME.'/'.$dest_dir;
            if(!is_dir($dirpath)){
                WP_Photo_Gallery_Utility::create_dir($dirpath);
            }
        } 
        
        foreach ($image_files_to_copy as $image_file){
            $result = copy($image_file['orig_file_path'], $upload_dir['basedir'].'/'.WPPG_UPLOAD_SUB_DIRNAME.'/'.$dest_dir.'/'.$image_file['filename']);
            if ($result === FALSE){
                $wp_photo_gallery->debug_logger->log_debug("Image copy failed from " . $image_file['orig_file_path'] ." to ". $upload_dir['basedir'].'/wp_photo_seller/'.$dest_dir.'/'.$image_file['filename'],4);
                return FALSE;
            }
        }
        
        return TRUE;
    }
    
    /**
     * This function will create a new post of type "attachment" by copying details from an existing image (ie, post of type attachment) which is being used by another gallery.
     * (Mainly used when someone selects an image from the media uploader library which has already been uploaded and is already being used by another gallery)
     * 
     * @return post ID if successful, FALSE on failure
     */
    static function create_new_post_attachment_and_meta_data($image_id, $existing_gallery_id)
    {
        if ($existing_gallery_id == ''){
            $dest_dir = WPPG_UPLOAD_TEMP_DIRNAME;
        }else{
            $dest_dir = $existing_gallery_id;
        }
        
        //Let's first check if this image was originally uploaded to this gallery's directory - if so no need to to create new post
        $wp_attached_file = get_post_meta($image_id, '_wp_attached_file');
        $lib_image_path_info = pathinfo($wp_attached_file[0]);
        $lib_image_dir = $lib_image_path_info['dirname'];
        $this_gallery_dirpath = WPPG_UPLOAD_SUB_DIRNAME .'/'.$existing_gallery_id;
        if ($lib_image_dir == $this_gallery_dirpath){
            //image already resides in the correct flder
            return true;
        }
        
        $upload_dir = wp_upload_dir();
        $new_post_array = get_post($image_id, ARRAY_A); //Let's copy the post info from the existing image
        unset($new_post_array['ID']);
        $image_file_name = esc_html(wp_basename($new_post_array['guid']));
        $new_post_array['guid'] = $upload_dir['baseurl'] . '/'.WPPG_UPLOAD_SUB_DIRNAME.'/'.$dest_dir.'/'.$image_file_name; //Set the image url to reflect the correct path
        $new_image_id = wp_insert_post($new_post_array, true);
        if($new_image_id == 0){
           WPSCommon::log('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] New post insert failed: " . print_r($new_image_id, true));
           return false;
       }
       
       //Now let's create the various meta data for this newly copied post (attachment)
       $new_image_attachment_meta = wp_get_attachment_metadata($image_id); //copy the attachment meta data from existing image
       $new_image_attachment_meta['file'] = WPPG_UPLOAD_SUB_DIRNAME.'/'.$dest_dir.'/'.$image_file_name;
       update_post_meta($new_image_id, '_wp_attachment_metadata', $new_image_attachment_meta);
       
       update_post_meta($new_image_id, '_wp_attached_file', $new_image_attachment_meta['file']);
       
       $new_image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
       update_post_meta($new_image_id, '_wp_attachment_image_alt', $new_image_alt);
       return $new_image_id;
    }
    
    static function createWatermarkImage($source_dir, $destination_dir,$file_name,$delete_existing_file = false, $watermark_text='',$args=array()) 
    {
        global $wp_photo_gallery;
        if($watermark_text===''){
            $watermark_text = ' ';//get_site_url();
        }

        $watermark_image_name =  'watermark_'.$file_name;
        $dest = $destination_dir.$watermark_image_name;

        if(file_exists($dest))
        {
            if($delete_existing_file === false)
            {
                return;
            }
        }

        $sourch_file = $source_dir.$file_name;
        $image_info = getimagesize($sourch_file);
        list($width, $height) = $image_info;
        $mime_type = strtolower($image_info['mime']);

        $desired_height = isset($args['watermark_height'])?$args['watermark_height']:'';
        $desired_width = isset($args['watermark_width'])?$args['watermark_width']:'';
        $font_size = isset($args['watermark_font_size'])?$args['watermark_font_size']:'';
        $watermark_placement = isset($args['watermark_placement'])?$args['watermark_placement']:'0';
        $watermark_opacity = isset($args['watermark_opacity'])?$args['watermark_opacity']:'35';
        //$watermark_colour = isset($args['watermark_colour'])?$args['watermark_colour']:'ffffff'; //TODO - introduce in settings using a colour picker

        if($desired_height != ''){
            //we have a portrait image so use the height as the maximum dimension
            if($desired_height > $height){$desired_height = $height;}//Check to make sure the watermarked image is not larger than the original
            $desired_width = floor($width * ($desired_height / $height));                

        }else{
            //we have a landscape image so use the width as the maximum dimension
            if(empty($desired_width)){
                $desired_width = 600;
            }
            if($desired_width > $width){$desired_width = $width;}//Check to make sure the watermarked image is not larger than the original
            $desired_height = floor($height * ($desired_width / $width));                
        }

        if(empty($font_size)){
            $font_size = 35;
        }

        if ($mime_type == 'image/jpeg' || $mime_type == 'image/pjpeg'){
            $image = imagecreatefromjpeg($sourch_file);
        }else if($mime_type == 'image/png'){
            $image = imagecreatefrompng($sourch_file);
        }

        $font = WP_PHOTO_PATH.'/fonts'.DIRECTORY_SEPARATOR.'arial.ttf';
        
        $TextSize = ImageTTFBBox($font_size, 0, $font, $watermark_text) or die;

        $TextWidth = abs($TextSize[2]) + abs($TextSize[0]) + 8; //Added an extra 8 pixels because otherwise the watermark text appeared slightly cut-off on the RHS
        $TextHeight = abs($TextSize[7]) + abs($TextSize[1]);
        // Create Image for Text
        $image_p = ImageCreateTrueColor($TextWidth, $TextHeight);
        ImageSaveAlpha($image_p, true);
        ImageAlphaBlending($image_p, false);
        $bgText = imagecolorallocatealpha($image_p, 255, 255, 255, 127);
        imagefill($image_p, 0, 0, $bgText);
        $watermark_transparency = 127 - ( $watermark_opacity * 1.27 );
        $color = 'ffffff'; //TODO - introduce in settings using a colour picker
        $rgb = WPPGPhotoGallery::hex2rgb($color,false);
        $TextColor = imagecolorallocatealpha($image_p, $rgb[0], $rgb[1], $rgb[2], $watermark_transparency);

        // Create Text on image
        imagettftext($image_p, $font_size, 0, 0, abs($TextSize[5]), $TextColor, $font, $watermark_text);


        $watermark_img_path = $image_p;
        imagealphablending($image_p, false);
        imagesavealpha($image_p, true);
        $sourcefile_width=imageSX($image);
        $sourcefile_height=imageSY($image);
        $watermarkfile_width=imageSX($image_p);
        $watermarkfile_height=imageSY($image_p);

        $dest_y = ($sourcefile_height / 2) - ($watermarkfile_height / 2);
        $dest_x = ($sourcefile_width / 2) - ($watermarkfile_width / 2);

        if($watermark_placement == '0')
        {
            //Display watermark text in centre of image
            imagecopy($image, $image_p, $dest_x, $dest_y, 0, 0,$watermarkfile_width, $watermarkfile_height);
        }
        else 
        {
            //Display watermark text as repeated grid
            $top = 20;
            while($top<$sourcefile_height)
            {
                $left = 10;
                while($left<$sourcefile_width){
                    imagecopy($image,$image_p,$left,$top,0,0,$TextWidth,$TextHeight);
                    $left = $left + $TextWidth + 50;
                } 
                $top =$top+$TextHeight + 50; 
            }

        }

        /* create the physical watermarked image to its destination */
        imagejpeg($image, $dest,100);
        imagedestroy($image); //clean up some memory
        $resized = image_make_intermediate_size($source_dir.$watermark_image_name, $desired_width, $desired_height); //Use the WP function to resize the watermarked image to that specified in the settings
        if ($resized === false){
            $wp_photo_gallery->debug_logger->log_debug('WPPGPhotoGallery::createWatermarkImage - image_make_intermediate_size failed and returned false!',4);
        }else{
            rename($source_dir.$resized['file'], $source_dir.$watermark_image_name); //Since the above WP function uses a different naming convention we will change the name back to our convention
        }
    }
    
	/**
	 * Converts hexidecimal color value to rgb values and returns as array/string
	 *
	 * @param string $hex
	 * @param bool $asString
	 * @return array|string
	 */
	static function hex2rgb($hex, $asString = false) {
        // strip off any leading #
        if (0 === strpos($hex, '#')) {
           $hex = substr($hex, 1);
        } else if (0 === strpos($hex, '&H')) {
           $hex = substr($hex, 2);
        }

        // break into hex 3-tuple
        $cutpoint = ceil(strlen($hex) / 2)-1;
        $rgb = explode(':', wordwrap($hex, $cutpoint, ':', $cutpoint), 3);

        // convert each tuple to decimal
        $rgb[0] = (isset($rgb[0]) ? hexdec($rgb[0]) : 0);
        $rgb[1] = (isset($rgb[1]) ? hexdec($rgb[1]) : 0);
        $rgb[2] = (isset($rgb[2]) ? hexdec($rgb[2]) : 0);

        return ($asString ? "{$rgb[0]} {$rgb[1]} {$rgb[2]}" : $rgb);
    }

    //This function will delete watermark images in a galley's folder.
    //Useful when we need to create new watermark image
    static function deleteWatermarkImages($gallery_id)
    {
        $upload_dir = wp_upload_dir(); 
        $gallery_mark_dir = $upload_dir['basedir'].'/'.WPPG_UPLOAD_SUB_DIRNAME.'/'.$gallery_id;
        
        $dir_contents = scandir($gallery_mark_dir);
        foreach($dir_contents as $filename){
            if (stripos($filename,'watermark') !== false)
            {
                WP_Photo_Gallery_Utility::deleteFile($gallery_mark_dir."/".$filename);
            }
        }
    }
    
    static function deleteGalleryItems($gallery_id)
    {
        global $wp_photo_gallery;
        $result = TRUE;
        $gallery_items = WPPGPhotoGallery::getGalleryItems($gallery_id);
        foreach ($gallery_items as $item){
            $result = wp_delete_post($item['id'], true);
            if (!$result){
                $wp_photo_gallery->debug_logger->log_debug("WPPGPhotoGallery::deleteGalleryItems: Error deleting post ID: ".$item['id'],4);
                break;
            }
        }
        return $result;
    }

    static function deleteGalleryFolder($gallery_id)
    {
          $upload_dir = wp_upload_dir(); 
          $gallery_folder = $upload_dir['basedir'].'/'.WPPG_UPLOAD_SUB_DIRNAME.'/'.$gallery_id;

          //chmod($gallery_folder, 0755); //change permissions of folder in case

          if (is_dir($gallery_folder) === true)
          {
              $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($gallery_folder), RecursiveIteratorIterator::CHILD_FIRST);

              foreach ($files as $file)
              {
                  if (in_array($file->getBasename(), array('.', '..')) !== true)
                  {
                      if ($file->isDir() === true)
                      {
                          rmdir($file->getPathName());
                      }

                      else if (($file->isFile() === true) || ($file->isLink() === true))
                      {
                          unlink($file->getPathname());
                      }
                  }
              }
              return rmdir($gallery_folder);
          }
          else if ((is_file($gallery_folder) === true) || (is_link($gallery_folder) === true))
          {
              return unlink($gallery_folder);
          }
          //This gallery does not have a folder
          return true;
    }

    //This function will apply pagination to a gallery
    static function apply_gallery_pagination($gallery, $gallery_items)
    {
        $thumbs_per_page = ($gallery->thumbs_per_page != NULL)?$gallery->thumbs_per_page:'20';
        $page_query_param = 'g_page';
        $page = isset($_GET[$page_query_param])?$_GET[$page_query_param]:''; //Get current page from query var
        global $wp;
        $current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) ); //Get current url

        $pagination = WPPGPhotoGallery::pagination_array($gallery_items, $page, $page_query_param, $current_url, $thumbs_per_page);
        return $pagination;
    }
    
    static function pagination_array($array, $page = 1, $link_prefix = false, $current_uri = '', $limit_page = 10)
    {
            if (empty($page) or !$limit_page){
                $page = 1;
            }
            $panel = '';
            $num_rows = count($array);
            if (!$num_rows or $limit_page >= $num_rows){
                return false;
            }
            $num_pages = ceil($num_rows / $limit_page);
            $page_offset = ($page - 1) * $limit_page;

            $nav_panel = '<div class="wppg_pagination_prev_next">';
            
            if ($page == 1){
                //First page
                $next_page = add_query_arg( $link_prefix, ($page + 1), $current_uri );
                $prev_page = add_query_arg( $link_prefix, $num_pages, $current_uri );
                
                $nav_panel .= '<span class="wppg_pagination_navigation_links">';
                $nav_panel .= '<a href="'.$prev_page.'">« Previous</a>';
                $nav_panel .= '</span>';
                $nav_panel .= '<span class="wppg_pagination_text">'.__('Displaying Page','spgallery').' '.$page.' '.__('of','spgallery').' '.$num_pages.'</span>';
                $nav_panel .= '<span class="wppg_pagination_navigation_links">';
                $nav_panel .= '<a href="'.$next_page.'">Next »</a>';
                $nav_panel .= '</span>';
            }else if($page == $num_pages){
                //Last page
                $next_page = add_query_arg( $link_prefix, 1, $current_uri );
                $prev_page = add_query_arg( $link_prefix, ($page - 1), $current_uri );
                
                $nav_panel .= '<span class="wppg_pagination_navigation_links">';
                $nav_panel .= '<a href="'.$prev_page.'">« Previous</a>';
                $nav_panel .= '</span>';
                $nav_panel .= '<span class="wppg_pagination_text">'.__('Displaying Page','spgallery').' '.$page.' '.__('of','spgallery').' '.$num_pages.'</span>';
                $nav_panel .= '<span class="wppg_pagination_navigation_links">';
                $nav_panel .= '<a href="'.$next_page.'">Next »</a>';
                $nav_panel .= '</span>';
                
            }else{
                $next_page = add_query_arg( $link_prefix, ($page + 1), $current_uri );
                $prev_page = add_query_arg( $link_prefix, ($page - 1), $current_uri );
                
                $nav_panel .= '<span class="wppg_pagination_navigation_links">';
                $nav_panel .= '<a href="'.$prev_page.'">« Previous</a>';
                $nav_panel .= '</span>';
                $nav_panel .= '<span class="wppg_pagination_text">'.__('Displaying Page','spgallery').' '.$page.' '.__('of','spgallery').' '.$num_pages.'</span>';
                $nav_panel .= '<span class="wppg_pagination_navigation_links">';
                $nav_panel .= '<a href="'.$next_page.'">Next »</a>';
                $nav_panel .= '</span>';
            }
            
            $nav_panel .= '</div>';
            
            $output['panel'] = $nav_panel; //Panel HTML source.
            $output['offset'] = $page_offset; //Current page number.
            $output['limit'] = $limit_page; //Number of resuts per page.
            $output['array'] = array_slice($array, $page_offset, $limit_page, true); //Array of current page results.

            return $output;
    }    
    
    static function create_gallery_page($gallery_id)
    {
        global $wpdb, $wp_photo_gallery;
        $table = WPPG_TBL_GALLERY;
        $gallery_obj = new WPPGPhotoGallery($gallery_id);
        $gallery_home_page_id = $wp_photo_gallery->configs->get_value('wppg_gallery_home_page_id');
        if(empty($gallery_home_page_id)){
            $g_p = get_page_by_path('wppg_photogallery');
            $parentId = $g_p->ID;
        }else{
            $parentId = $gallery_home_page_id;
        }
        
        $p = '';
        if($gallery_obj->page_id != 0 || !empty($gallery_obj->page_id))
        {
            $p = get_post($gallery_obj->page_id);
        }
        if(empty($p) || $gallery_obj->page_id == 0) {
            //Create a post(page) if one does not exist for this gallery
            $page['post_title'] = $gallery_obj->name;
            $page['post_name'] = 'gallery' . $gallery_obj->id;
            $page['post_content'] = '[wppg_photo_gallery id="'. $gallery_obj->id .'"]';
            $page['post_parent'] = $parentId;
            $page['post_status'] =  'publish';
            $page['post_type'] =  'page';
            $page['post_password'] =  $gallery_obj->password;
            $new_page_id = wp_insert_post($page);
            //update this gallery's row in the gallery table
            $gallery_data = array(
                'page_id' => $new_page_id
            );
            $condition = array('id' => $gallery_obj->id); //gallery row to update
            $result = $wpdb->update( $table, $gallery_data, $condition);
            if ($result == false){
                $wp_photo_gallery->debug_logger->log_debug("WPPGPhotoGallery::create_gallery_page: DB update failed for gallery with ID: ".$gallery_id,4);
            }
        }
        
    }
}