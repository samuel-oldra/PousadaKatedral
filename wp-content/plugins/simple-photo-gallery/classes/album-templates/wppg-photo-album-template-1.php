<?php
class WPPG_Album_Template_1
{
    function __construct()
    {
        //NOP
    }

    function render_album($album_id)
    {
        global $wp_photo_gallery;
        global $wpdb;
        $album_table_name = WPPG_TBL_ALBUM;
        $album_object = $wpdb->get_row("SELECT * FROM $album_table_name WHERE id = '$album_id'", OBJECT);

        if($album_object == NULL){
           $wp_photo_gallery->debug_logger->log_debug('[' . basename(__FILE__) . ' - line ' . __LINE__ . "] wppg_photo_album shortcode - No album found with ID ".$album_id);
            return '<div class="wppg_red_box_front_end">No album was found with ID '.$album_id.'</div>';
        }
        
        WP_Photo_Gallery_Utility::start_buffer();
?>
        <link type="text/css" rel="stylesheet" href="<?php echo WP_PHOTO_URL.'/classes/album-templates/css/wppg-photo-album-template-1.css?ver='.WP_PHOTO_VERSION ?>" />
        
<?php
        $gallery_array_list = maybe_unserialize($album_object->gallery_list); //Get the galleries associated with this album
        
        if(!empty($gallery_array_list)){
            //
            $gallery_id_list = "(" .implode(",",$gallery_array_list) .")"; //Create comma separate list for DB operation
            $album_contents_sort_order = $album_object->sort_order;
            switch($album_contents_sort_order) {
                case 0: //Sort By ID Ascending
                        $orderBy = 'ORDER BY id ASC';
                        break;
                case 1: //Sort By ID Descending
                        $orderBy = 'ORDER BY id DESC';
                        break;
                case 2: //Sort By Created Date Ascending
                        $orderBy = 'ORDER BY created ASC';
                        break;
                case 3: //Sort By Created Date Descending
                        $orderBy = 'ORDER BY created DESC';
                        break;
                case 4: //Sort By Name Ascending
                        $orderBy = 'ORDER BY name ASC';
                        break;
                case 5: //Sort By Name Descending
                        $orderBy = 'ORDER BY name DESC';
                        break;
                default: //Default to ID ascending
                        $orderBy = 'ORDER BY id DESC';
                        break;
            }
            $gallery_table_name = WPPG_TBL_GALLERY;
            $query = "Select * from ".$gallery_table_name." WHERE id IN ".$gallery_id_list;
            
            $items = $wpdb->get_results($query);
?>

            <div id="wppg_albumcontainer"> 
<?php 
            if (!empty($items))
            {
                foreach($items as $item)
                {
                    $p = get_post($item->page_id);
                    $page_link = get_permalink($p->ID);
                    $thumb_image_url='#';

                    $gallery_items = WPPGPhotoGallery::getGalleryItems($item->id);
                    if($gallery_items){
                        $thumb_image_url = $gallery_items[0]['thumb_url']; //Get the thumb URL of the first gallery item
                    }
                    $gallery_name = $item->name;
                    if(strlen($gallery_name) > 14){
                        $gallery_name_short = substr($gallery_name, 0, 14)."...";
                    }else{
                        $gallery_name_short = $gallery_name;
                    }

                    ?> 
                    <div class="wppg_album_item_container">
                        <div class="wppg_album_item_top">
                            <div class="wppg_album_item_thumbnail">
                            <a href='<?php echo $page_link ?>'>
                                    <img class="wppg_album_item_thumbnail" src='<?php echo $thumb_image_url ?>' alt='<?php echo $gallery_name; ?>' title="<?php echo $gallery_name; ?>" />
                            </a>
                            </div>
                        </div>
                        <div class="wppg_album_item_bottom">
                            <div class="wppg_album_item_name"><a href="<?php echo $page_link; ?>" title="<?php echo $gallery_name; ?>"><?php echo $gallery_name_short; ?></a></div>
                        </div>
                    </div>
    <?php 
                } 
            }
            else
            {
                //No galleries found for the whole site!
                echo '<div class="wppg_red_box_front_end">'.__('There are currently no galleries configured in the system!', 'spgallery').'</div>';
            }

        }
        $output = WP_Photo_Gallery_Utility::end_buffer_and_collect();
        return $output;
    }
}