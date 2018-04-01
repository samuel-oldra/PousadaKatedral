<?php

class WPPGPhotoGalleryItem {
    //This the product class that shows how a photo product looks like
    var $id;
    var $name; //Example: My Awesome Image
    var $thumb_url;
    var $alt_text;
    var $description;
    var $date_uploaded;
    var $image_file_name; //Example: my-image.jpg
            
    var $source_dir_url;
    var $source_dir;
    var $image_file_url;
    var $image_file_path;
    
    var $image_width;
    var $image_height;
    
    var $gallery_id;//The ID of the gallery that this product is in
    
    function __construct() {
        //NOP
    }

    function create_photo_item_by_id($image_id)
    {
        $this->id = $image_id;
        $image_post = get_post($image_id);

        $this->name = $image_post->post_title;
        $this->description = $image_post->post_content;
        $this->date_uploaded = $image_post->post_date;
        
        $this->alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true);
        if (empty($this->alt_text)){//if the alt text meta is blank, let's set it to the image name
            $this->alt_text = $image_post->post_name;
        }
        
        $this->gallery_id = get_post_meta($image_id, '_wppg_gallery_id', true);

        $upload_dir = wp_upload_dir();
        $this->source_dir = $upload_dir['basedir'].'/'.WPPG_UPLOAD_SUB_DIRNAME.'/'.$this->gallery_id.'/';
        $this->source_dir_url = $upload_dir['baseurl'].'/'.WPPG_UPLOAD_SUB_DIRNAME.'/'.$this->gallery_id.'/';
        $this->image_file_url = $image_post->guid;
        $this->thumb_url = wp_get_attachment_thumb_url($image_id);
        
        $this->image_file_name = esc_html(wp_basename($this->image_file_url));
        $this->image_file_path = $this->source_dir . $this->image_file_name;

        $attachment_img_src_data = get_post_meta($image_id, '_wp_attachment_metadata', true);     
        $this->image_width = $attachment_img_src_data['width'];
        $this->image_height = $attachment_img_src_data['height'];
    }
    
    static function get_photo_details($image_id){
        $image_info_array = array();
        $thumb_url = wp_get_attachment_thumb_url($image_id);
        $attachment_img = wp_get_attachment_image($image_id);
        $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true);

        $image_post = get_post($image_id);
        $image_desc = $image_post->post_content;
        $upload_date = $image_post->post_date;
        
        $galley_id = get_post_meta($image_id, '_wppg_gallery_id', true);

        //if the alt text meta is blank, let's set it to the image name
        if ($alt_text == '' || $alt_text == NULL){
            $alt_text = $image_post->post_name;
        }
        $image_info = array(
            'id' => $image_id,
            'gallery_id' => $galley_id,
            'thumb_url' => $thumb_url,
            'alt_text' => $alt_text,
            'description' => $image_desc,
            'date_uploaded' => $upload_date
        );

        $image_info_array = $image_info;
        return $image_info_array;
    }

    function calculate_wp_generated_thumbnail_img_name($w, $h)
    {
        $path_info = pathinfo($this->image_file_path);
        $thumb_name = $path_info['filename'].'-'.$w.'x'.$h.'.'.$path_info['extension'];
        return $thumb_name;
    }
}