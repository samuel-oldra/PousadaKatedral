<?php

class WP_Photo_Gallery_Configure_Settings
{    
    function __construct(){
        
    }
    
    static function set_default_settings()
    {
        global $wp_photo_gallery;

        //General settings
        $wp_photo_gallery->configs->set_value('wppg_gallery_home_sort_order','1'); //Default: sort by ID descending
        $wp_photo_gallery->configs->set_value('wppg_photo_details_page_id','');
        $wp_photo_gallery->configs->set_value('wppg_gallery_home_page_id','');
        $wp_photo_gallery->configs->set_value('wppg_album_home_page_id','');
        
        //Enable Albums
        $wp_photo_gallery->configs->set_value('wppg_enable_albums',''); //checkbox
        
        //TODO - keep adding default options for any fields that require it
        
        //Save it
        $wp_photo_gallery->configs->save_config();
    }
    
    static function add_option_values()
    {
        global $wp_photo_gallery;

        //General settings
        $wp_photo_gallery->configs->add_value('wppg_gallery_home_sort_order','1'); //Default: sort by ID descending        
        $wp_photo_gallery->configs->add_value('wppg_photo_details_page_id','');
        $wp_photo_gallery->configs->add_value('wppg_gallery_home_page_id','');
        $wp_photo_gallery->configs->add_value('wppg_album_home_page_id','');

        //Enable Albums
        $wp_photo_gallery->configs->add_value('wppg_enable_albums',''); //checkbox

        //TODO - keep adding default options for any fields that require it
        
        //Save it
        $wp_photo_gallery->configs->save_config();
    }
}
