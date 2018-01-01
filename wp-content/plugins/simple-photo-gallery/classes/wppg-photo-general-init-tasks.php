<?php

class WPPG_General_Init_Tasks
{
    var $add_slider_script; //For detecting if a particular shortcode is being used (before a page loads)
    function __construct()
    {
        global $wp_photo_gallery;
        $this->init_shortcode_tasks();
        $this->load_scripts_and_styles();
        add_filter('wp_list_pages_excludes', array($this, 'hide_plugin_created_pages'));
        add_action('wp_head',array($this, 'do_wppg_head_tasks'));
        add_action('wp_footer', array($this, 'do_wppg_footer_tasks'));
        //Add more tasks that need to be executed at init time
    }
    
    
    function init_shortcode_tasks()
    {
        add_shortcode('wppg_photo_gallery', array(&$this, 'wppg_photo_gallery_sc_handler'));
        add_shortcode('wppg_photo_gallery_home', array(&$this, 'wppg_photo_gallery_home_sc_handler'));
        add_shortcode('wppg_photo_details', array(&$this, 'wppg_photo_details_sc_handler'));
        add_shortcode('wppg_photo_slider', array(&$this, 'wppg_photo_slider_sc_handler'));

        add_shortcode('wppg_photo_album', array(&$this, 'wppg_photo_album_sc_handler'));
        add_shortcode('wppg_photo_albums_home', array(&$this, 'wppg_photo_albums_home_sc_handler'));
        
    }
    
    function load_scripts_and_styles()
    {
        if (is_admin()){
            //Load any admin side scripts here
            //Album page
            if(strpos($_SERVER['QUERY_STRING'], 'page=wppg') !== false) {
                if ($_GET['page'] == 'wppg_album'){
                    //media upload stuff
                    wp_enqueue_script('media-upload');
                    wp_enqueue_script('thickbox');
                    wp_register_script('wppg-media-upload', WP_PHOTO_URL.'/js/wppg_album_thumb_uploader.js', array('jquery','media-upload','thickbox'));
                    wp_enqueue_style( 'dialogStylesheet', includes_url().'css/jquery-ui-dialog.css' );
                    wp_enqueue_script('wppg-media-upload');	    	
                    wp_enqueue_style('thickbox'); //style sheet for thickbox
                }
            }   
        }else{
            //Load front end side scripts here
            wp_enqueue_style('wppg-photo-css', WP_PHOTO_URL . '/css/wppg-photo.css', null, WP_PHOTO_VERSION, 'all');

            wp_register_style('wppg-slider-custom-css', WP_PHOTO_URL . '/js/flexslider/flexslider-custom.css', null, WP_PHOTO_VERSION, 'all');
            wp_register_style('wppg-slider-css', WP_PHOTO_URL . '/js/flexslider/flexslider.css', null, WP_PHOTO_VERSION, 'all');
            
            wp_enqueue_script('jquery-lightbox', WP_PHOTO_URL . '/js/jquery-lightbox/js/jquery.lightbox-0.5.js', array('jquery'), WP_PHOTO_VERSION);
            
            wp_register_script('wppg-flex-slider', WP_PHOTO_URL . '/js/flexslider/jquery.flexslider-min.js', array('jquery'), WP_PHOTO_VERSION, true); //Load this script only for pages with the slider shortcode
            //wp_register_script('wppg-slider-related', WP_PHOTO_URL . '/js/wppg-slider-related.js', array('jquery'), WP_PHOTO_VERSION, true); //Load this script only for pages with the slider shortcode
        }
    }
    
    function wppg_photo_gallery_sc_handler($attrs)
    {
        global $wpdb, $wp_photo_gallery;
        $gallery_id = $attrs['id'];
        $gallery_table = WPPG_TBL_GALLERY;
        //Let's get the gallery associated with this ID
        $gallery_object = $wpdb->get_row("SELECT * FROM $gallery_table WHERE id = '".$gallery_id."'");
        if ($gallery_object === NULL)
        {
            //No result found
            $wp_photo_gallery->debug_logger->log_debug("wppg_photo_gallery_sc_handler: Could not find gallery with ID: ".$gallery_id,4);
            echo '<div class="wppg_red_box_front_end">'.__("Error: No gallery found with the following ID: ","spgallery").$gallery_id.'</div>';
        }

        isset($gallery_object->gallery_thumb_template)? $template = $gallery_object->gallery_thumb_template: $template = '0';

        switch($template){
            case '0':
                require_once 'gallery-templates/wppg-photo-gallery-template-1.php';
                $template1 = new WPPG_Gallery_Template_1();
                $output = $template1->render_gallery($gallery_id);
                break;
            case '1': 
                require_once 'gallery-templates/wppg-photo-gallery-template-2.php';
                $template2 = new WPPG_Gallery_Template_2();
                $output = $template2->render_gallery($gallery_id);
                break;
            case '2': 
                require_once 'gallery-templates/wppg-photo-gallery-template-3.php';
                $template3 = new WPPG_Gallery_Template_3();
                $output = $template3->render_gallery($gallery_id);
                break;            
            default:
                require_once 'gallery-templates/wppg-photo-gallery-template-1.php';
                $template1 = new WPPG_Gallery_Template_1();
                $output = $template1->render_gallery($gallery_id);
       }
       return $output;
    }

    function wppg_photo_gallery_home_sc_handler($attrs)
    {
        require_once 'gallery-templates/wppg-gallery-home.php';
        $gallery_home = new WPPG_Gallery_Home();
        $output = $gallery_home->render_gallery_home();
        return $output;
    }

    function wppg_photo_details_sc_handler($attrs)
    {
        require_once 'gallery-templates/wppg-photo-details.php';
        $photo_details = new WPPG_Gallery_Photo_Details();
        $output = $photo_details->render_photo_details();
        return $output;
    }
    
    function wppg_photo_albums_home_sc_handler($attrs)
    {
        require_once 'album-templates/wppg-photo-album-home.php';
        $album_home = new WPPG_Album_Home();
        $output = $album_home->render_album_home();
        return $output;
 
    }
    
    function wppg_photo_album_sc_handler($attrs)
    {
        global $wpdb, $wp_photo_gallery;
        $album_id = $attrs['id'];
        $album_table = WPPG_TBL_ALBUM;
        //Let's get the gallery associated with this ID
        $album_object = $wpdb->get_row("SELECT * FROM $album_table WHERE id = '".$album_id."'");
        if ($album_object === NULL)
        {
            //No result found
            $wp_photo_gallery->debug_logger->log_debug("wppg_photo_album_sc_handler: Could not find album with ID: ".$album_id,4);
            echo '<div class="wppg_red_box_front_end">'.__("Error: No album found with the following ID: ","spgallery").$album_id.'</div>';
        }

/* 
 * TODO - we'll add templates later    
     isset($album_object->album_thumb_template)? $template = $album_object->album_thumb_template: $template = '0';

     switch($template){
         case '0':
             require_once 'album-templates/wppg-photo-album-template-1.php';
             $template1 = new WPPG_Gallery_Template_1();
             $output = $template1->render_album($album_id);
             break;
         case '1': 
             require_once 'album-templates/wppg-photo-album-template-2.php';
             $template2 = new WPPG_Gallery_Template_2();
             $output = $template2->render_album($album_id);
             break;
         default:
             require_once 'album-templates/wppg-photo-album-template-1.php';
             $template1 = new WPPG_Gallery_Template_1();
             $output = $template1->render_album($album_id);
    }
 * 
 */
        require_once 'album-templates/wppg-photo-album-template-1.php';
        $template1 = new WPPG_Album_Template_1();
        $output = $template1->render_album($album_id);
        return $output;
    }
    
    
    function wppg_photo_slider_sc_handler($attrs)
    {
        extract(shortcode_atts(array(
            'id' => 'not specified',
            'show_carousel' => '',
        ), $attrs));

        if($id == 'not specified'){
            echo '<div class="wppg_red_box_front_end""><strong>'.__('Simple Photo Gallery: Please specify a gallery ID or multiple IDs separated by commas','spgallery').'</strong></div>';
            return;
        }
        $this->add_slider_script = true; //For detecting if this shortcode is being used (before a page loads)
        wp_enqueue_style('wppg-slider-custom-css');
        wp_enqueue_style('wppg-slider-css');
        //wp_enqueue_style('wppg-slider-css-theme');
        $gallery_ids = strip_tags($id);
        if(strrpos($gallery_ids, ',')){
            //Multiple galleries specified
            $id_array = explode(',', $gallery_ids);
            $slider_output = WP_Photo_Gallery_Shortcode_Utility::wppg_slider_output_sc($id_array, $attrs);
        }else{
            //Single gallery specified
            $slider_output = WP_Photo_Gallery_Shortcode_Utility::wppg_slider_output_sc($gallery_ids, $attrs);
        }
        return $slider_output;
    }

    function hide_plugin_created_pages($excludes)
    {
        //Hide any plugin-created pages which should not be clicked directly
        $photo_details = get_page_by_path('wppg_photogallery/wppg_photo_details');
        $excludes[] = $photo_details->ID;    

        if(is_array(get_option('exclude_pages'))){
            $excludes = array_merge(get_option('exclude_pages'), $excludes );
        }
        sort($excludes);

        return $excludes;
    }

    function do_wppg_head_tasks()
    {
        //Check if Photo details page
        $page_id = get_the_ID();
        if(!empty($page_id))
        {
            $current_page = get_post($page_id);
            if ($current_page->post_name == 'wppg_photo_details'){
                //Only load stylesheet when on the photo details page
                wp_enqueue_style('wppg-photo-details-css', WP_PHOTO_URL . '/classes/gallery-templates/css/wppg-photo-details.css', null, WP_PHOTO_VERSION, 'all');
                if(isset($_GET['gallery_id'])){
                    $gallery_id = strip_tags($_GET['gallery_id']);
                    $gallery = new WPPGPhotoGallery($gallery_id);
                }
            }
        }
        
    }

    function do_wppg_footer_tasks()
    {
        //Check if slider scripts need loading
        if ($this->add_slider_script){
            //wp_enqueue_script('wppg-responsive-slides');
            wp_enqueue_script('wppg-flex-slider');
            wp_enqueue_script('wppg-slider-related');
        }
    }
    
}