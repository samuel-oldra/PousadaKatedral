<?php
/* 
 * Inits the admin dashboard side of things.
 * Main admin file which loads all settings panels and sets up admin menus. 
 */
class WP_Photo_Gallery_Admin_Init
{
    var $main_menu_page;
    var $gallery_menu;
    var $settings_menu;
    var $album_menu;

    function __construct()
    {
        $this->admin_includes();
        add_action('admin_print_scripts', array(&$this, 'admin_menu_page_scripts'));
        add_action('admin_print_styles', array(&$this, 'admin_menu_page_styles'));
        add_action('admin_menu', array(&$this, 'create_admin_menus'));        
    }
    
    function admin_includes()
    {
        include_once('wppg-photo-admin-menu.php');
    }

    function admin_menu_page_scripts() 
    {
        //make sure we are on the appropriate menu page
        if (isset($_GET['page']) && strpos($_GET['page'], WP_PHOTO_MENU_SLUG_PREFIX ) !== false ) {
            wp_enqueue_script('postbox');
            wp_enqueue_script('dashboard');
            wp_enqueue_script('thickbox');
            wp_enqueue_script('media-upload');
            //media uploader stuff
            wp_enqueue_media(); // This function loads in the required media files for the media manager.
            // Register, localize and enqueue our custom JS.
            wp_register_script( 'wppg-gallery-media', WP_PHOTO_URL.'/js/wppg_media_uploader.js', array( 'jquery' ));
            wp_enqueue_script( 'wppg-gallery-media' );
            
            
            wp_register_script('wppg-admin-js', WP_PHOTO_URL. '/js/wppg-gallery-admin-script.js', array('jquery'));
            wp_enqueue_script('wppg-admin-js');
            
        }
    }
    
    function admin_menu_page_styles() 
    {
        //make sure we are on the appropriate menu page
        if (isset($_GET['page']) && strpos($_GET['page'], WP_PHOTO_MENU_SLUG_PREFIX ) !== false ) {
            wp_enqueue_style('dashboard');
            wp_enqueue_style('thickbox');
            wp_enqueue_style('global');
            wp_enqueue_style('wp-admin');
            wp_enqueue_style('wppg-admin-css', WP_PHOTO_URL. '/css/wppg-photo-admin-styles.css');
        }
    } 
    
    function create_admin_menus()
    {
        $menu_icon_url = WP_PHOTO_URL.'/images/plugin-icon.png';
        $this->main_menu_page = add_menu_page(__('Photo Gallery', WP_PHOTO_TEXT_DOMAIN), __('Photo Gallery', WP_PHOTO_TEXT_DOMAIN), WP_PHOTO_MANAGEMENT_PERMISSION, WP_PHOTO_SETTINGS_MENU_SLUG , array(&$this, 'handle_settings_menu_rendering'), $menu_icon_url);
        add_submenu_page(WP_PHOTO_SETTINGS_MENU_SLUG, __('Settings', WP_PHOTO_TEXT_DOMAIN),  __('Settings', WP_PHOTO_TEXT_DOMAIN) , WP_PHOTO_MANAGEMENT_PERMISSION, WP_PHOTO_SETTINGS_MENU_SLUG, array(&$this, 'handle_settings_menu_rendering'));
        add_submenu_page(WP_PHOTO_SETTINGS_MENU_SLUG, __('Gallery', WP_PHOTO_TEXT_DOMAIN),  __('Gallery', WP_PHOTO_TEXT_DOMAIN) , WP_PHOTO_MANAGEMENT_PERMISSION, WP_PHOTO_GALLERY_MENU_SLUG, array(&$this, 'handle_gallery_menu_rendering'));
        add_submenu_page(WP_PHOTO_SETTINGS_MENU_SLUG, __('Albums', WP_PHOTO_TEXT_DOMAIN),  __('Albums', WP_PHOTO_TEXT_DOMAIN) , WP_PHOTO_MANAGEMENT_PERMISSION, WP_PHOTO_ALBUM_MENU_SLUG, array(&$this, 'handle_album_menu_rendering'));
        do_action('wppg_admin_menu_created');
    }
    
    function handle_gallery_menu_rendering()
    {
        include_once('wppg-gallery-menu.php');
        $this->gallery_menu = new WP_Photo_Gallery_Gallery_Menu();
    }

    function handle_settings_menu_rendering()
    {
        include_once('wppg-photo-settings-menu.php');
        $this->settings_menu = new WP_Photo_Gallery_Settings_Menu();
        
    }

    function handle_album_menu_rendering()
    {
        include_once('wppg-album-menu.php');
        $this->album_menu = new WP_Photo_Gallery_Album_Menu();
    }
    
}//End of class
