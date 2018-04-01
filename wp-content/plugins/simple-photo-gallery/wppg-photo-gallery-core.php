<?php

if (!class_exists('WP_Photo_Gallery')){

class WP_Photo_Gallery{
    var $version = '1.7.3';
    var $db_version = '1.3';
    var $plugin_url;
    var $plugin_path;
    var $configs;
    var $admin_init;
    var $debug_logger;

    function __construct() {
        $this->load_configs();
        $this->define_constants();
        $this->includes();
        $this->loader_operations();

        add_action('init', array( &$this, 'wp_photo_plugin_init' ), 0 );
        do_action('wps_scanner_loaded');
    }
    
    function plugin_url() { 
        if ( $this->plugin_url ) return $this->plugin_url;
        return $this->plugin_url = plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) );
    }

    function plugin_path() { 	
        if ( $this->plugin_path ) return $this->plugin_path;		
        return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
    }
    
    function load_configs(){
        include_once('classes/wppg-photo-config.php');
        $this->configs = WP_Photo_Gallery_Config::get_instance();
    }
    
    function define_constants(){
        
        define('WP_PHOTO_VERSION', $this->version);
        define('WP_PHOTO_URL', $this->plugin_url());
        define('WP_PHOTO_PATH', $this->plugin_path());
        define('WP_PHOTO_DB_VERSION', $this->db_version);
        define('WP_PHOTO_TEXT_DOMAIN', 'spgallery');
  
        $selected_permission = $this->configs->get_value('wppg_management_permission');
        if(empty($selected_permission)){
            define('WP_PHOTO_MANAGEMENT_PERMISSION', 'manage_options');
        }
        else{
            define('WP_PHOTO_MANAGEMENT_PERMISSION', $selected_permission);
        }

        define('WP_PHOTO_MENU_SLUG_PREFIX', 'wppg');
        define('WP_PHOTO_MAIN_MENU_SLUG', 'wppg_main');
        define('WP_PHOTO_SETTINGS_MENU_SLUG', 'wppg_settings');
        define('WP_PHOTO_GALLERY_MENU_SLUG', 'wppg_gallery');
        define('WP_PHOTO_ALBUM_MENU_SLUG', 'wppg_album');

        define('WPPG_UPLOAD_SUB_DIRNAME', 'simple_photo_gallery');
        define('WPPG_UPLOAD_TEMP_DIRNAME', 'wppg_gallery_tmp_dir');
        define('WPPG_ATTACHMENT_META_TAG', '_wppg_gallery_id');
  
        global $wpdb;
        define('WPPG_TBL_GALLERY', $wpdb->prefix . 'wppg_gallery');
        define('WPPG_TBL_ALBUM', $wpdb->prefix . 'wppg_album');
        define('WPPG_TBL_SETTINGS', $wpdb->prefix . 'wppg_settings');
        define('WPPG_TBL_DOWNLOADS', $wpdb->prefix . 'wppg_downloads');
        define('WPPG_TBL_GLOBAL_META_DATA', $wpdb->prefix . 'wppg_global_meta');
    }

    function includes() {
        //Load common files
        include_once('classes/wppg-photo-debug-logger.php');
        include_once('classes/wppg-photo-utility.php');
        include_once('classes/wppg-photo-gallery-item-class.php');
        include_once('classes/wppg-photo-general-init-tasks.php');
        include_once('classes/wppg-shortcode-utility.php');
        include_once('classes/wppg-photo-gallery-class.php');
        include_once('classes/wppg-photo-album-class.php');

        if (is_admin()){ //Load admin side only files
            include_once('admin/wppg-photo-admin-init.php');
            include_once('admin/general/wppg-list-table.php');
        }
        else{ //Load front end side only files
        }
    }

    function loader_operations(){
        add_action('plugins_loaded',array(&$this, 'plugins_loaded_handler'));//plugins loaded hook
        $this->debug_logger = new WP_Photo_Gallery_Logger();
        if(is_admin()){
            register_activation_hook( __FILE__, array(&$this, 'activate_handler'));//activation hook
            $this->admin_init = new WP_Photo_Gallery_Admin_Init();
        }
    }
    
    function activate_handler(){
        //Only runs when the plugin activates
        include_once ('classes/wppg-photo-installer.php');
        WP_Photo_Gallery_Installer::run_installer();
    }
    
    function do_db_upgrade_check(){
        if(is_admin()){//Check if DB needs to be updated
            if (get_option('wp_photo_db_version') != WP_PHOTO_DB_VERSION) {
                include_once ('classes/wppg-photo-installer.php');
                WP_Photo_Gallery_Installer::run_installer();
            }
        }
    }
    
    function plugins_loaded_handler(){//Runs when plugins_loaded action gets fired
        if(is_admin()){//Do admin side plugins_loaded operations
            $this->do_db_upgrade_check();
            //$this->settings_obj = new WP_Security_Settings_Page();//Initialize settins menus
            
            add_filter('wp_handle_upload_prefilter', array( &$this, 'custom_upload_filter'));
            add_filter('wp_handle_upload', array( &$this, 'handle_upload') );
            //add_filter('media_upload_tabs', array( &$this, 'remove_medialibrary_tab'), 10, 1); //This is to remove the media library tab on LHS from the uploader popup
        }
    }
    
    function wp_photo_plugin_init(){//Lets run... Main plugin operation code goes here
        //Set up localisation.
        $locale = apply_filters( 'plugin_locale', get_locale(), 'spgallery' );
        load_textdomain( 'spgallery', WP_LANG_DIR . "/spgallery-$locale.mo" );
	load_plugin_textdomain('spgallery', false, dirname(plugin_basename(__FILE__ )) . '/languages/');

        new WPPG_General_Init_Tasks();
        //Plugin into code goes here... actions, filters, shortcodes goes here
        //add_action(....);
        //$this->debug_logger->log_debug("WP Security pluign init");
    }
    
    //TODO - perhaps we should create a separate class for plugins loaded tasks and move the functions below there??
    function custom_upload_filter($file)
    {
        add_filter('upload_dir', array( &$this, 'wppg_set_upload_dir')); //Fire wp filter which allows us to manipulate upload_dir
        return $file;
    }
    
    
    function handle_upload($fileinfo)
    {
        remove_filter('upload_dir', array( &$this, 'wppg_set_upload_dir' ) );
        return $fileinfo;
    }
    
    function remove_medialibrary_tab($tabs)
    {
      unset($tabs['library']);
      return $tabs;
    }
    
    //This function will dynamically change the upload directory based on the gallery id or if a new gallery is being created
    function wppg_set_upload_dir($path_data)
    {
        $output = array();
        $originating_url_parsed = parse_url($_SERVER['HTTP_REFERER']);
        if(isset($originating_url_parsed['query'])){
            parse_str($originating_url_parsed['query'], $output);
            if (isset($output['page'])){
                if ($output['page'] == WP_PHOTO_GALLERY_MENU_SLUG) {
                    $gallery_subdir = WPPG_UPLOAD_SUB_DIRNAME;
                    if (isset($output['wppg_gallery_id']))
                    {
                        $gallery_subdir .= '/'.$output['wppg_gallery_id'];
                    }
                    else
                    {
                        $gallery_subdir .= '/'.WPPG_UPLOAD_TEMP_DIRNAME;
                    }

                    $path_data['path'] = $path_data['basedir'] . "/" . $gallery_subdir;
                    $path_data['url'] = $path_data['baseurl'] . "/" . $gallery_subdir;
                }
            }
        }
        return $path_data;
    }
    
}//End of class

}//End of class not exists check

$GLOBALS['wp_photo_gallery'] = new WP_Photo_Gallery();
