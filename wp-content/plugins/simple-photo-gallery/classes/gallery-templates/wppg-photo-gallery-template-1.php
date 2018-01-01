<?php
class WPPG_Gallery_Template_1
{
    function __construct()
    {
        //NOP
    }

    function render_gallery($gallery_id)
    {
        global $wp_photo_gallery;
        $pagination = false; //Initialize
        $gallery = new WPPGPhotoGallery($gallery_id);
        $display_photo_details_page = $gallery->display_image_on_page;
        $gallery_items = WPPGPhotoGallery::getGalleryItems($gallery_id);
        WP_Photo_Gallery_Utility::start_buffer();
?>
        <link type="text/css" rel="stylesheet" href="<?php echo WP_PHOTO_URL.'/classes/gallery-templates/css/wppg-photo-gallery-template-1.css?ver='.WP_PHOTO_VERSION ?>" />
        <div id="wppg-gallery-display">
<?php
        //Add Pagination if applicable
        if ($gallery->enable_pagination == 1){
?>
            <link type="text/css" rel="stylesheet" href="<?php echo WP_PHOTO_URL.'/classes/gallery-templates/css/wppg-pagination.css?ver='.WP_PHOTO_VERSION; ?>" />
<?php            
            $pagination = WPPGPhotoGallery::apply_gallery_pagination($gallery, $gallery_items);
            if ($pagination !== false){
                $gallery_items = $pagination['array'];
            }
        }

        foreach($gallery_items as $p)
        {
            $image_id = $p['id'];

            //Now let's create a PhotoProduct object for this item
            $wppgPhotoObj = new WPPGPhotoGalleryItem();        
            $wppgPhotoObj->create_photo_item_by_id($image_id);

            $water_mark_url = '';
            $file_name = $wppgPhotoObj->image_file_name;
            $upload_dir = wp_upload_dir(); 
            $path = $wppgPhotoObj->thumb_url;

            if ($display_photo_details_page == 1)
            {
                $details_page_id = $wp_photo_gallery->configs->get_value('wppg_photo_details_page_id');
                if(empty($details_page_id)){
                    $photo_details_page = get_page_by_path('wppg_photogallery/wppg_photo_details');
                    if($photo_details_page == NULL){
                        $wp_photo_gallery->debug_logger->log_debug('Gallery template 1: get_page_by_path returned NULL!',4);
                    }
                    $preview_page = $photo_details_page->guid;
                }else{
                    $preview_page = get_permalink($details_page_id);
                }
                //Check if this gallery is password protected
                if(!empty($gallery->password)){
                    //This gallery is password protected - so let's add an encoded string
                    $encoded_str = base64_encode(WPPS_PHOTO_VIEW_AUTH_STRING);
                    $query_params = array('gallery_id'=>$gallery_id,'image_id'=>$image_id, 'auth_key'=>$encoded_str);
                }else{
                    $query_params = array('gallery_id'=>$gallery_id,'image_id'=>$image_id);
                }
                $preview_url = add_query_arg($query_params, $preview_page);
                $button_html = '<span class="wppg_popup_view_details_section"><a href="'.$preview_url.'"><span class="wppg-button">'.__("View Photo", "spgallery").'</span></a></span>';
            }
            else
            {
                $source_dir = $upload_dir['basedir'].'/'.WPPG_UPLOAD_SUB_DIRNAME.'/'.$gallery_id.'/';

                if($gallery->watermark != NULL)
                {
                    //Get the gallery settings values for watermark placement, width and font size
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
                        $wm_font_size = '14';
                    }

                    $args = array('watermark_width' => $desired_width, 'watermark_font_size' => $wm_font_size, 'watermark_placement' => $watermark_placement);

                    WPPGPhotoGallery::createWatermarkImage($source_dir,$source_dir,$file_name,false,$gallery->watermark, $args);
                    $water_mark_url = $upload_dir['baseurl'].'/'.WPPG_UPLOAD_SUB_DIRNAME.'/'.$gallery_id.'/watermark_'.$file_name;
                    $preview_url = $water_mark_url;
                }
                else
                {
                    //Don't create a watermark URL if the watermark field was empty in the gallery settings. Display original image instead
                    $preview_url = $wppgPhotoObj->image_file_url;
                }
                //$button_html = '<input type="button" value="'.__("View Image", "spgallery").'" class="gallerybutton wppg-template1-view-button" id="viewPhotoDetails_'.$wppgPhotoObj->id.'" />';
                $button_html = '<a href="'.$preview_url.'" class="wppg_popup wppg-template1-lb-view" id="viewPhotoDetails_'.$wppgPhotoObj->id.'">'.__("View Image", "spgallery").'</a>';
            }

    ?>
    <div class="item">
        <div class="wppg-gallery-template1-top">
            <div class="wppg-gallery-template1-thumb">
                <a class="wppg_popup" title="<?php echo $wppgPhotoObj->name ?>" href="<?php echo $preview_url ?>">
                    <img alt="<?php echo $wppgPhotoObj->alt_text ?>" src="<?php echo $path ?>">
                </a>
            </div>
        </div>
        <div class="wppg-gallery-template1-bottom">
            <div class="wppg-gallery-template1-view-button">
                <?php echo $button_html; ?>
            </div>
        </div>
    </div><!-- end of .item -->
    <?php 
         }//End foreach loop
    ?>
    <div class="clear"></div>
    <?php
         //Insert pagination nav bar at bottom
        if ($gallery->enable_pagination == 1 && $pagination !== false){
?>
            <div class="wppg_photo_gallery_pagination"><?php echo $pagination['panel']; ?></div>    
<?php    
        }        
?>            
        </div> <!--end wppg-gallery-display div --> 
<?php
        if($display_photo_details_page == 0){
            //Load lightbox css file
            wp_enqueue_style('jquery-lightbox-css', WP_PHOTO_URL . '/js/jquery-lightbox/css/jquery.lightbox-0.5.css');
            
            //Load lightbox js files
            wp_enqueue_script('wppg-lb-script-js', WP_PHOTO_URL.'/js/simple_photo_gallery_js.js', array('jquery'));
            wp_localize_script('wppg-lb-script-js', 'WPPG_LIGHTBOX_JS', 
                                    array('imgLoading'=>WP_PHOTO_URL.'/js/jquery-lightbox/images/lightbox-ico-loading.gif',
                                        'imgbtnPrev'=>WP_PHOTO_URL.'/js/jquery-lightbox/images/lightbox-btn-prev.gif',
                                        'imgbtnNext'=>WP_PHOTO_URL.'/js/jquery-lightbox/images/lightbox-btn-next.gif',
                                        'imgBlank'=>WP_PHOTO_URL.'/js/jquery-lightbox/images/lightbox-blank.gif',
                                        'imgbtnClose'=>WP_PHOTO_URL.'/js/jquery-lightbox/images/lightbox-btn-close.gif'));
        }
        $output = WP_Photo_Gallery_Utility::end_buffer_and_collect();
        return $output;
    }
}