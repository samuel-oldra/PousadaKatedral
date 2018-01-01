<?php

class WP_Photo_Gallery_Installer
{
    static function run_installer()
    {	
        global $wpdb;
        WP_Photo_Gallery_Installer::create_db_tables();
        WP_Photo_Gallery_Installer::create_photo_gallery_pages();
        WP_Photo_Gallery_Installer::create_upload_directory();
        
    }
    
    static function create_db_tables()
    {
        //global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        //Table name variables
        $gallery_tbl_name = WPPG_TBL_GALLERY;
	$downloads_tbl_name = WPPG_TBL_DOWNLOADS;
        $photogallery_settings_tbl_name = WPPG_TBL_SETTINGS;
        $album_tbl_name = WPPG_TBL_ALBUM;
        $global_meta_tbl_name = WPPG_TBL_GLOBAL_META_DATA;
        
	$dl_tbl_sql = "CREATE TABLE " . $downloads_tbl_name . " (
        id int(10) unsigned NOT NULL AUTO_INCREMENT,
        duid varchar(100),
        downloaded_on datetime null,
        ip varchar(50) not null,
        PRIMARY KEY  (id)
        )ENGINE=MyISAM DEFAULT CHARSET=utf8;";
	dbDelta($dl_tbl_sql);

        $cs_tbl_sql = "CREATE TABLE " . $photogallery_settings_tbl_name . " (
        wppg_key varchar(128) NOT NULL,
        value text NOT NULL,
        PRIMARY KEY  (wppg_key)
        )ENGINE=MyISAM DEFAULT CHARSET=utf8;";
        dbDelta($cs_tbl_sql);

        $gallery_tbl_sql = "CREATE TABLE " . $gallery_tbl_name . " (
        id int(11) NOT NULL AUTO_INCREMENT,
        name varchar(200) NOT NULL,
        created datetime NOT NULL,
        updated datetime NOT NULL,
        category int(11) NOT NULL,
        watermark varchar(500) NOT NULL,
        watermark_opacity varchar(32) NOT NULL,
        watermark_placement int(11) NOT NULL,
        watermark_width varchar(32) NOT NULL,
        watermark_font_size varchar(16) NOT NULL,
        sort_order int(11) NOT NULL,
        gallery_thumb_template int(11) NOT NULL,
        display_image_on_page tinyint(1) NOT NULL DEFAULT '1',
        enable_pagination tinyint(1) NOT NULL DEFAULT '0',
        thumbs_per_page varchar(32) NOT NULL,
        password varchar(500) NOT NULL,
        page_id bigint(20) NOT NULL,
        PRIMARY KEY  (id)
        )ENGINE=MyISAM DEFAULT CHARSET=utf8;";
        dbDelta($gallery_tbl_sql);
        
        $album_tbl_sql = "CREATE TABLE " . $album_tbl_name . " (
        id int(11) NOT NULL AUTO_INCREMENT,
        album_name varchar(200) NOT NULL,
        created datetime NOT NULL,
        updated datetime NOT NULL,
        album_category int(11) NOT NULL,
        thumbnail_url text NOT NULL,
        page_id bigint(20) NOT NULL,
        gallery_list text NOT NULL,
        sort_order int(11) NOT NULL,
        PRIMARY KEY  (id)
        )ENGINE=MyISAM DEFAULT CHARSET=utf8;";
        dbDelta($album_tbl_sql);
        
        $gm_tbl_sql = "CREATE TABLE " . $global_meta_tbl_name . " (
        meta_id bigint(20) NOT NULL auto_increment,
        date_time datetime NOT NULL default '0000-00-00 00:00:00',
        meta_key1 varchar(255) NOT NULL,
        meta_key2 varchar(255) NOT NULL,
        meta_key3 varchar(255) NOT NULL,
        meta_key4 varchar(255) NOT NULL,
        meta_key5 varchar(255) NOT NULL,
        meta_value1 varchar(255) NOT NULL,
        meta_value2 text NOT NULL,
        meta_value3 text NOT NULL,
        meta_value4 longtext NOT NULL,
        meta_value5 longtext NOT NULL,
        PRIMARY KEY  (meta_id)
        )ENGINE=MyISAM DEFAULT CHARSET=utf8;";
        dbDelta($gm_tbl_sql);
        

	update_option("wp_photo_db_version", WP_PHOTO_DB_VERSION);
    }
    
    static function create_upload_directory()
    {
        //Create our folder in the "uploads" directory
	$upload_dir = wp_upload_dir();
	$wppg_dir = $upload_dir['basedir'] . '/'.WPPG_UPLOAD_SUB_DIRNAME;           
        if(!is_dir($wppg_dir)) {
            $mkdir_res = mkdir($wppg_dir , 0755, true);
            if($mkdir_res === false){
                $wp_photo_gallery->debug_logger->log_debug("WP_Photo_Gallery_Installer::create_upload_directory(): Could not create upload directory!",4);
                return;
            }else{
                //Let's also create an empty index.html file in this folder
                $index_file = $wppg_dir.'/index.html';
                $handle = fopen($index_file, 'w'); //or die('Cannot open file:  '.$index_file);
                fclose($handle);
            }
        }
        //Let's now create a .htaccess file to protect browsing of images
        WP_Photo_Gallery_Installer::createGalleryHTaccessFile();

    }
    
    static function createGalleryHTaccessFile()
    {
        //TODO
    }
    
    static function create_photo_gallery_pages()
    {
        global $wp_photo_gallery;
        // Create gallery home page
        $g_page = array(
          'post_title' => __('Galleries', 'spgallery'),
          'post_name' => 'wppg_photogallery',
          'post_content' => '[wppg_photo_gallery_home]',
          'post_parent' => 0,
          'post_status' => 'publish',
          'post_type' => 'page',
          'comment_status' => 'closed',
          'ping_status' => 'closed'
        );


        $g_p = get_page_by_path('wppg_photogallery');
        if(!$g_p) {
          $g_pageId = wp_insert_post($g_page);
          $g_parentId = $g_pageId;
        }else {
          $g_parentId = $g_p->ID;
        }
        
        //Save the page ID in the settings
        $wp_photo_gallery->configs->set_value('wppg_gallery_home_page_id',$g_parentId);
                
        // Create the photo details page which will display an individual image and its informaton etc
        $details_page = array(
          'post_title' => __('Photo Details', 'spgallery'),
          'post_name' => 'wppg_photo_details',
          'post_content' => '[wppg_photo_details]',
          'post_parent' => $g_parentId,
          'post_status' => 'publish',
          'post_type' => 'page',
          'comment_status' => 'closed',
          'ping_status' => 'closed'
        );

        // Create the top level photo details page
        $d_p = get_page_by_path('wppg_photogallery/wppg_photo_details');
        if(!$d_p) {
          $d_pageId = wp_insert_post($details_page);
          $d_parentId = $d_pageId;
        }
        else {
          $d_parentId = $d_p->ID;
        }
        
        //Save the page ID in the settings
        $wp_photo_gallery->configs->set_value('wppg_photo_details_page_id',$d_parentId); 
        $wp_photo_gallery->configs->save_config();
    }
}
