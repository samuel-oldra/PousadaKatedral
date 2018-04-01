<?php

/*
 * This will display the gallery home page
 */
class WPPG_Gallery_Home
{
    function __construct()
    {
        //NOP
    }
    
    function render_gallery_home()
    {
        global $wpdb, $wp_photo_gallery;
        $gallery_table = WPPG_TBL_GALLERY;

        $gallery_selection_sort_order = $wp_photo_gallery->configs->get_value('wppg_gallery_home_sort_order');
        switch($gallery_selection_sort_order) {
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

        //Get galleries
        $galleries = $wpdb->get_results("SELECT * FROM $gallery_table $orderBy", OBJECT);

        WP_Photo_Gallery_Utility::start_buffer();
        echo '<link type="text/css" rel="stylesheet" href="'.WP_PHOTO_URL.'/classes/gallery-templates/css/wppg-gallery-home.css?ver='.WP_PHOTO_VERSION.'" />';//Load the CSS file for this view
        ?>
        <div id="wppg_gallery_container"> 
        <?php 
        if (!empty($galleries))
        {
            foreach($galleries as $item)
            {
                //$p = get_page_by_path('photogallery/gallery' . $item->id);
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
                <div class="wppg_gallery_item_container">
                    <div class="wppg_gallery_item_top">
                        <div class="wppg_gallery_item_thumbnail">
                        <a href='<?php echo $page_link ?>'>
                                <img class="wppg_gallery_item_thumb" src='<?php echo $thumb_image_url ?>' alt='<?php echo $gallery_name; ?>' title="<?php echo $gallery_name; ?>" />
                        </a>
                        </div>
                    </div>
                    <div class="wppg_gallery_item_bottom">
                        <div class="wppg_gallery_item_name"><a href="<?php echo $page_link; ?>" title="<?php echo $gallery_name; ?>"><?php echo $gallery_name_short; ?></a></div>
                    </div>
                </div>
    <?php 
            } 
        }
        else
        {
            //No galleries found!
            echo '<div class="wppg_yellow_box_front_end">'.__("There are currently no galleries!","WPPG").'</div>';
        }
    ?>

                <div class="clear"></div>
        </div>
    <?php
    $output = WP_Photo_Gallery_Utility::end_buffer_and_collect();
    return $output;
    }
}
