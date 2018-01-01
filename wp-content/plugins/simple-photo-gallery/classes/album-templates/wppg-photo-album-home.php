<?php
class WPPG_Album_Home
{
    function __construct()
    {
        //NOP
    }

    function render_album_home()
    {
        global $wpdb;
        global $wp_photo_gallery;
        $album_table_name = WPPG_TBL_ALBUM;

        //TODO - add sort order for album home page in future release

        $items = $wpdb->get_results("SELECT * FROM $album_table_name", OBJECT);

        WP_Photo_Gallery_Utility::start_buffer();

        //echo '<link type="text/css" rel="stylesheet" href="'.WP_PHOTO_URL.'/classes/album-templates/css/wppg-photo-album-home.css?ver='.WP_PHOTO_VERSION.'" />';//Load the CSS file for this view
        //we will use the same CSS file for home page as for template 1 for now
        echo '<link type="text/css" rel="stylesheet" href="'.WP_PHOTO_URL.'/classes/album-templates/css/wppg-photo-album-template-1.css?ver='.WP_PHOTO_VERSION.'" />';//Load the CSS file for this view
?>

        <div id="wppg_albumcontainer"> 
<?php 
        if ($wpdb->num_rows > 0 && $items != NULL)
        {
            foreach($items as $item)
            {
                $p = get_post($item->page_id);
                $page_link = get_permalink($p->ID);
                $album_thumb_url=$item->thumbnail_url;
                //Check image size
                $image_info = getimagesize($album_thumb_url);
                if(!$image_info || $image_info[0] > 150 && $image_info[1] > 150){
                    $image_filename = basename($album_thumb_url); //extract filename
                    //First check if standard 150x150 thumb file exists
                    $extension_pos = strrpos($image_filename, '.'); // find position of the last dot, so where the extension starts
                    $standard_thumb = substr($image_filename, 0, $extension_pos) . '-150x150' . substr($image_filename, $extension_pos); //add "-150x150" to original filename
                    $path_without_file_name = dirname($album_thumb_url);
                    $std_thumb_image_url = $path_without_file_name.'/'.$standard_thumb;

                    if (@getimagesize($std_thumb_image_url) !== false){
                        $album_thumb_url = $std_thumb_image_url;
                    }else{
                        $image_id = WP_Photo_Gallery_Utility::get_attachment_id_from_url($album_thumb_url);

                        if($image_id != NULL){
                            $image_meta = wp_get_attachment_metadata($image_id);
                            $upload_dir = wp_upload_dir();
                            $image_path = $upload_dir['basedir'].'/'.$image_meta['file'];
                            //lets make our own 150x150 thumb
                            $resized_file = image_make_intermediate_size($image_path, '150', '150');
                            if ($resized_file === false){
                                $album_thumb_url = ''; //To avoid PHP undeclared variable error. In this case we don't have a default thumb to revert to.
                                $wp_photo_gallery->debug_logger->log_debug("Album Home - Unable to create thumb because image_make_intermediate_size failed and returned false");
                            }else{
                                $album_thumb_url = $path_without_file_name.'/'.$resized_file['file'];
                            }
                        }
                    }
                    $thumb_image_url = $album_thumb_url;
                }else if($image_info[0] <= 150 && $image_info[1] <= 150){
                    $thumb_image_url = $album_thumb_url;
                }

                $album_name = $item->album_name;
                if(strlen($album_name) > 14){
                    $album_name_short = substr($album_name, 0, 14)."...";
                }else{
                    $album_name_short = $album_name;
                }

                ?> 
                <div class="wppg_album_item_container">
                    <div class="wppg_album_item_top">
                        <div class="wppg_album_item_thumbnail">
                        <a href='<?php echo $page_link ?>'>
                                <img class="wppg_album_item_thumb" src='<?php echo $thumb_image_url ?>' alt='<?php echo $album_name; ?>' title="<?php echo $album_name; ?>" />
                        </a>
                        </div>
                    </div>
                    <div class="wppg_album_item_bottom">
                        <div class="wppg_album_item_name"><a href="<?php echo $page_link; ?>" title="<?php echo $album_name; ?>"><?php echo $album_name_short; ?></a></div>
                    </div>
                </div>
<?php 
            } 
        }
        else
        {
            //No galleries found!
            echo '<div class="wppg_red_box_front_end">'.__('There are currently no albums configured!', 'spgallery').'</div>';
        }
?>

	<div class="clear"></div>
        </div>
<?php 
        $output = WP_Photo_Gallery_Utility::end_buffer_and_collect();
        return $output;
    }
    
}