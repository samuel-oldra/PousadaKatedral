<?php

class WPPGPhotoAlbum 
{
    //This class is for albums & any tasks associated with that
    function __construct($id=null) 
    {
        //NOP
    }

    static function create_root_album_page()
    {
        global $wp_photo_gallery;
        $album_home_id = $wp_photo_gallery->configs->get_value('wppg_album_home_page_id');
        if(!empty($album_home_id)){
            return;
        }
        //First check if a page has already been set
        //
        // Page for displaying the Album list
        $a_page = array(
          'post_title' => __('Photo Albums', 'spgallery'),
          'post_name' => 'wppg_photoalbums',
          'post_content' => '[wppg_photo_albums_home]',
          'post_parent' => 0,
          'post_status' => 'publish',
          'post_type' => 'page',
          'comment_status' => 'closed',
          'ping_status' => 'closed'
        );

        $a_p = get_page_by_path('wppg_photoalbums');
        if(!$a_p){
          $a_pageId = wp_insert_post($a_page);
          $a_parentId = $a_pageId;
        }
        else {
          $a_parentId = $a_p->ID;
        }
        
        //Save the page ID in the settings
        $wp_photo_gallery->configs->set_value('wppg_album_home_page_id',$a_parentId); 
        $wp_photo_gallery->configs->save_config();

    }

    static function create_album_page($album_id)
    {
        global $wp_photo_gallery, $wpdb;
        $page = array();
        $album_table_name = WPPG_TBL_ALBUM;
        $sql = 'SELECT * from ' . $album_table_name . " WHERE id='$album_id'";
        
        $album_obj = $wpdb->get_row($sql);
        $album_home_page_id = $wp_photo_gallery->configs->get_value('wppg_album_home_page_id');
        if(empty($album_home_page_id)){
            $g_p = get_page_by_path('wppg_photoalbums');
            $parentId = $g_p->ID;
        }else{
            $parentId = $album_home_page_id;
        }
        
        $p = '';
        if($album_obj->page_id != 0 || !empty($album_obj->page_id))
        {
            $p = get_post($album_obj->page_id);
        }
        
        if(empty($p) || $album_obj->page_id == 0) {
            $page['post_title'] = $album_obj->album_name;
            $page['post_name'] = 'album' . $album_id;
            $page['post_content'] = '[wppg_photo_album id="'. $album_id .'"]';
            $page['post_parent'] = $parentId;
            $page['post_status'] = 'publish';
            $page['post_type'] = 'page';
            $new_page_id = wp_insert_post($page);
            if($new_page_id == 0){
                $wp_photo_gallery->debug_logger->log_debug("WPPGPhotoAlbum::create_album_page: Failed to create a page for album with ID: ".$album_id,4);
                return false;
            }
            //update this album's row with the page id
            $album_data = array(
                'page_id' => $new_page_id
            );
            $condition = array('id' => $album_id); //album row to update
            $result = $wpdb->update($album_table_name, $album_data, $condition);
            if ($result == false){
                $wp_photo_gallery->debug_logger->log_debug("WPPGPhotoAlbum::create_album_page: DB update failed for album with ID: ".$album_id,4);
                return false;
            }else{
                return true;
            }
         }

    }
    
}