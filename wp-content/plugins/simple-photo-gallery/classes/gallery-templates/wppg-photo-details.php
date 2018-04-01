<?php
/*
 * This class will display the photo details page
 */

class WPPG_Gallery_Photo_Details
{
    function __construct()
    {
        //NOP
    }

    function render_photo_details()
    {
        global $wp_photo_gallery;
        WP_Photo_Gallery_Utility::start_buffer();
    
        if(!isset($_GET['image_id']) && !isset($_GET['gallery_id'])){
            echo '<div class="wppg_yellow_box_front_end">'.__('This page is for displaying the details of a selected photo. Please click on a photo from one of the galleries to see the details.').'</div>';
            $output = WP_Photo_Gallery_Utility::end_buffer_and_collect();
            return $output;
        }

        $image_id = strip_tags($_GET['image_id']);
        $gallery_id = strip_tags($_GET['gallery_id']);
        $gallery = new WPPGPhotoGallery($gallery_id);
        $url_param_encoded = '';


        //$c_g = get_post($gallery->page_id);
        //$current_gallery_page = $c_g->guid;
        $current_gallery_page = get_permalink($gallery->page_id);
        
        $wppgPhotoObj = new WPPGPhotoGalleryItem();
        $wppgPhotoObj->create_photo_item_by_id($image_id);
        $photo_name = $wppgPhotoObj->name;

        $image_display_url = '';
        $water_mark_url = '';
        $photo_nav_info = '';
        $file_name = $wppgPhotoObj->image_file_name;
        $full_size_image = $wppgPhotoObj->image_file_url;
        $upload_dir = wp_upload_dir(); 
        $path = $wppgPhotoObj->thumb_url;

        $source_dir = $upload_dir['basedir'].'/'.WPPG_UPLOAD_SUB_DIRNAME.'/'.$gallery_id.'/';

        $gallery_home_page_id = $wp_photo_gallery->configs->get_value('wppg_gallery_home_page_id');
        if(empty($gallery_home_page_id)){
            $g_p = get_page_by_path('wppg_photogallery');
            //$gallery_page = $g_p->guid;
            $gallery_page = get_permalink($g_p->ID);
        }else{
            $gallery_page = get_permalink($gallery_home_page_id);
        }

        $items_to_add = array();
        $display_msg = '';


        $watermark_placement = $gallery->watermark_placement;
        if ($watermark_placement === NULL)
        {
            $watermark_placement = '0';
        }

        $desired_width = $gallery->watermark_width;
        if ($desired_width == 0 || empty($desired_width))
        {
            $desired_width = '600';
        }

        $wm_font_size = $gallery->watermark_font_size;
        if ($wm_font_size == 0 || empty($wm_font_size))
        {
            $wm_font_size = '35';
        }

        $watermark_opacity = $gallery->watermark_opacity;
        if ($watermark_opacity === NULL)
        {
            $watermark_opacity = '35';
        }

        //If the image is a portrait then instead of setting max width, we want to set max height otherwise the preview comes out too big
        if($wppgPhotoObj->image_height > $wppgPhotoObj->image_width){
            $args = array('watermark_height' => $desired_width, 'watermark_font_size' => $wm_font_size, 'watermark_placement' => $watermark_placement, 'watermark_opacity' => $watermark_opacity);
        }else{
            $args = array('watermark_width' => $desired_width, 'watermark_font_size' => $wm_font_size, 'watermark_placement' => $watermark_placement, 'watermark_opacity' => $watermark_opacity);
        }

        if($gallery->watermark != NULL)
        {
            WPPGPhotoGallery::createWatermarkImage($source_dir,$source_dir,$file_name,false,$gallery->watermark,$args);
            $water_mark_url = $upload_dir['baseurl'].'/'.WPPG_UPLOAD_SUB_DIRNAME.'/'.$gallery_id.'/watermark_'.$file_name;
            $image_display_url = $water_mark_url;
        }
        else
        {
            //Don't create a watermark URL if the watermark field was empty in the gallery settings.  Display original image instead
            $image_display_url = $wppgPhotoObj->image_file_url;
        }

        //Get variations
        //$variations_data = WPSPhotoProduct::get_all_photo_variations($image_id);
        //array_shift($variations_data); //remove the first item which is the image title
        //$digital_variations = array();
        //$physical_variations = array();
        //foreach ($variations_data as $variation)
        //{
        //    if(strtolower($variation['type']) == 'digital'){
        //        $digital_variations[] = $variation;
        //    }else if(strtolower($variation['type']) == 'physical'){
        //        $physical_variations[] = $variation;
        //    }
        //}

        //Get gallery items
        //Initialize some variables
        $prev_img_id = '';
        $next_img_id = '';

        $query_params_prev = '';
        $query_params_next = '';

        $preview_url_prev = '';
        $preview_url_next = '';

        $gallery_items = WPPGPhotoGallery::getGalleryItems($gallery_id);
        $gallery_count = count($gallery_items);
        $x=0;
        $img_index = 0;
        foreach($gallery_items as $p)
        {
            $img_id = $p['id'];
            if ($img_id == $image_id){
                $img_index = $x;
                break;
            }
            $x++;
        }

        $details_page_id = $wp_photo_gallery->configs->get_value('wppg_photo_details_page_id');
        if(empty($details_page_id)){
            $photo_details_page = get_page_by_path('wppg_photogallery/wppg_photo_details');
            $preview_page = get_permalink($photo_details_page->ID);
        }else{
            $preview_page = get_permalink($details_page_id);
        }

        //Let's now determine the previous and next gallery image ids
        if ($img_index == 0)
        {
            //Means that current image is the first in the gallery
            $prev_img_id = $gallery_items[$gallery_count-1]['id'];//Last image in the gallery
            if ($img_index == ($gallery_count-1)){
                //If there is a single image in the gallery then the first image will be the last too
                $next_img_id = $gallery_items[$img_index]['id'];
            }else{
                $next_img_id = $gallery_items[$img_index+1]['id'];
            }
            $query_params_prev = array('gallery_id'=>$gallery_id,'image_id'=>$prev_img_id);
            $query_params_next = array('gallery_id'=>$gallery_id,'image_id'=>$next_img_id);
            $preview_url_prev = add_query_arg($query_params_prev, $preview_page);
            $preview_url_next = add_query_arg($query_params_next, $preview_page);
        }
        else if ($img_index == ($gallery_count-1))
        {
            //Means that current image is the last in the gallery
            $prev_img_id = $gallery_items[$img_index-1]['id'];
            $next_img_id = $gallery_items[0]['id'];//First image in the gallery
            $query_params_prev = array('gallery_id'=>$gallery_id,'image_id'=>$prev_img_id);
            $query_params_next = array('gallery_id'=>$gallery_id,'image_id'=>$next_img_id);
            $preview_url_prev = add_query_arg($query_params_prev, $preview_page);
            $preview_url_next = add_query_arg($query_params_next, $preview_page);
        }
        else
        {
            //Current image has a previous and a next image
            $prev_img_id = $gallery_items[$img_index-1]['id'];
            $next_img_id = $gallery_items[$img_index+1]['id'];
            $query_params_prev = array('gallery_id'=>$gallery_id,'image_id'=>$prev_img_id);
            $query_params_next = array('gallery_id'=>$gallery_id,'image_id'=>$next_img_id);
            $preview_url_prev = add_query_arg($query_params_prev, $preview_page);
            $preview_url_next = add_query_arg($query_params_next, $preview_page);
        }

        //Let's add the special auth_key if the gallery is password protected
        if(!empty($gallery->password)){
            $query_params = array('auth_key'=>$url_param_encoded);
            $preview_url_prev = add_query_arg($query_params, $preview_url_prev);
            $preview_url_next = add_query_arg($query_params, $preview_url_next);
        }

        if($gallery_count > 0){
            //Photo navigation info eg, - "Displaying photo 1 of 20"
            $photo_nav_info = sprintf( __('Displaying photo %s of %s', 'spgallery'), $img_index+1, $gallery_count);
        }

        ?>
        <div class="wppg-image-details">
        <h2><?php echo $photo_name; ?></h2>
        <div class="wppg-image-details-watermark-section">
            <img src="<?php echo $image_display_url;?>" alt="<?php echo $wppgPhotoObj->alt_text; ?>" class="wppg-image-details-watermarked-img" />
        </div>
        <div class="wppg-digital-details">
        <div class="wppg-photo-description-text"><?php _e($wppgPhotoObj->description,'spgallery');?></div>
        </div><!-- end of .wppg-digital-details -->

        <div class="wppg-css-clear"></div>
        <div class="wppg_photo_details_navigation_info">
            <?php echo $photo_nav_info; ?>
        </div>

        <div class="wppg-digital-details-prev-next">
        <span class="wppg_photo_details_previous_photo_section wppg_photo_details_navigation_links">
            <?php echo '<a href="'.$preview_url_prev.'" class="wppg_photo_details_previous_photo">&laquo; '.__("Previous Photo", 'spgallery').'</a>'; ?>
        </span>
        <span class="wppg_photo_details_next_photo_section wppg_photo_details_navigation_links">
            <?php echo '<a href="'.$preview_url_next.'" class="wppg_photo_details_next_photo">'.__("Next Photo", 'spgallery').' &raquo;</a>'; ?>
        </span>
        </div>
        <span class="wppg_photo_details_bottom_section"><a href="<?php echo $current_gallery_page; ?>"><span class="wppg_photo_details_back_to_gallery"><?php _e("Back To Gallery Page", 'spgallery');?></span></a></span>
        </div><!-- end of .wppg-image-details -->
<?php
        $output = WP_Photo_Gallery_Utility::end_buffer_and_collect();
        return $output;
    }
}