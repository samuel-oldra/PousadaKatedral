<?php

class WP_Photo_Gallery_Album_Menu extends WP_Photo_Gallery_Admin_Menu
{
    var $menu_page_slug = WP_PHOTO_ALBUM_MENU_SLUG;
    
    /* Specify all the tabs of this menu in the following array */
    var $menu_tabs = array(
        'tab1' => 'Album Management', 
        'tab2' => 'Add/Edit',
        );

    var $menu_tabs_handler = array(
        'tab1' => 'render_tab1', 
        'tab2' => 'render_tab2',
        );

    function __construct() 
    {
        $this->render_menu_page();
    }

    function get_current_tab() 
    {
        $tab_keys = array_keys($this->menu_tabs);
        $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $tab_keys[0];
        return $tab;
    }

    /*
     * Renders our tabs of this menu as nav items
     */
    function render_menu_tabs() 
    {
        $current_tab = $this->get_current_tab();

        echo '<h2 class="nav-tab-wrapper">';
        foreach ( $this->menu_tabs as $tab_key => $tab_caption ) 
        {
            $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
            echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->menu_page_slug . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
        }
        echo '</h2>';
    }
    
    /*
     * The menu rendering goes here
     */
    function render_menu_page() 
    {
        $tab = $this->get_current_tab();
        ?>
        <div class="wrap">
        <div id="poststuff"><div id="post-body">
        <?php 
        $this->render_menu_tabs();
        //$tab_keys = array_keys($this->menu_tabs);
        call_user_func(array(&$this, $this->menu_tabs_handler[$tab]));
        ?>
        </div></div>
        </div><!-- end of wrap -->
        <?php
    }
    
    /*
     * The menu rendering goes here
     */
    function render_tab1() 
    {
        global $wpdb, $wp_photo_gallery;
        $album_table_name = WPPG_TBL_ALBUM;
        $errors = '';
        include_once 'wppg-list-albums.php'; //For rendering the list of existing galleries table
        $album_list = new WPPG_List_Albums();
        
        $wppg_enable_album_feature = $wp_photo_gallery->configs->get_value('wppg_enable_albums');
        
        if(isset($_REQUEST['action'])) //Do list table form row action tasks
        {
            if($_REQUEST['action'] == 'delete_album'){ //"Delete Album" link was clicked for a row in the list table
                $album_id = strip_tags($_GET['id']);
                $album = $wpdb->get_row("SELECT * FROM $album_table_name WHERE id = '$album_id'", OBJECT);

                if($album == NULL){
                    $errors .= '<p>Album with ID '.$album_id.' does not exist</p>';
                }else{
                    //Delete the album page with the shortcode
                    $p = get_post($album->page_id);
                    if($p)
                    {
                        wp_delete_post($p->ID,true);
                    }

                    $delete_result = $wpdb->delete($album_table_name, array('ID' => $album_id));
                    if ($delete_result === false){
                        $errors .= '<p>Unable to delete album with ID '.$album_id.'</p>';
                    }
                }

                if (strlen($errors)> 0){
                    $this->show_msg_error($errors);
                }else{
                    $this->show_msg_updated(__('The album and its page were successfully deleted.','spgallery'));
                }
            }
        }

        if(isset($_REQUEST['wppg_save_general_album_settings'])){
            $wppg_enable_album_feature = isset($_POST['wppg_enable_album_feature'])?'1':'0';
            $wp_photo_gallery->configs->set_value('wppg_enable_albums',$wppg_enable_album_feature);
            $wp_photo_gallery->configs->save_config();
            if($wppg_enable_album_feature == '1')
            {
                WPPGPhotoAlbum::create_root_album_page();
            }
        }
        
        ?>
        <h2><?php _e('Album Management', 'spgallery')?></h2>
        <div class="postbox">
        <h3><label for="title"><?php _e('Creating or Editing an Album', 'spgallery'); ?></label></h3>
        <div class="inside">
            <ul class="wppg_admin_ul_grp1">
                <li><?php _e('To create a new album click the "Create New Album" button below.', 'spgallery'); ?></li>
                <li><?php _e('To edit an existing Album please click the "Edit" link for the applicable album in the table below.', 'spgallery'); ?></li>
            </ul>
        </div></div>

        <div class="postbox">
        <h3><label for="title"><?php _e('General Album Settings', 'spgallery'); ?></label></h3>
        <div class="inside">
            <form action="" method="POST">
                <table class="form-table">
                <tr>
                <th scope="row"><?php _e('Enable Album Feature', 'spgallery');?>:</th>
                <td>
                    <input type="checkbox" id="wppg_enable_album_feature" name="wppg_enable_album_feature" <?php echo ($wppg_enable_album_feature == '1')? 'checked="checked"' : ''; ?>>
                    <span class="description"><?php _e('If you want to use albums on your site then enable this option.', 'spgallery'); ?></span>
                </td>
                </tr>
                </table>
                <input type="submit" name="wppg_save_general_album_settings" value="Save Settings" class="button-primary" />
                </form>
            </div>
        </div>

        <div class="postbox">
        <h3><label for="title"><?php _e( 'Create an Album' , 'spgallery' ); ?></label></h3>
        <div class="inside">
            <?php
            if($wppg_enable_album_feature != '1')
            {
                echo '<p class="wppg_yellow_box">';
                echo __('Please enable the album feature from the settings area above to create new albums.', 'spgallery');
                echo '</p>';
            }
            else
            {
                ?>
                <table class="form-table">
                <tr>
                    <td><input type="submit" name="create_album" value="Create New Album" class="button-primary" onclick="window.location ='?page=wppg_album&tab=tab2'" />
                    <span class="description"><?php _e('Click this button if you wish to create a new album', 'spgallery'); ?></span></td>
                </tr>
                </table>
            <?php } ?>
        </div></div>
        <?php
        //Create a page with the relevant shortcode for each album (if one doesn't exist)
        $results = $wpdb->get_results("SELECT * FROM $album_table_name", OBJECT);
        if( $wpdb->num_rows > 0 && $results != NULL)
        {
            $album_home_page_id = $wp_photo_gallery->configs->get_value('wppg_album_home_page_id');
            if(empty($album_home_page_id)){
                $a_p = get_page_by_path('wppg_photoalbums');
                $parentId = $a_p->ID;
                if($a_p == NULL){
                    //create album parent home page if missing
                    WPPGPhotoAlbum::create_root_album_page();
                }
            }
        ?>
        <div class="postbox">
        <h3><label for="title"><?php _e('Existing Albums', 'spgallery'); ?></label></h3>
        <div class="inside">
        <?php
                $list_albums = new WPPG_List_Albums();
                if(isset($_REQUEST['action'])) //Do list table form row action tasks
                {
                    if(isset($_REQUEST['task']) && $_REQUEST['task'] == 'delete'){ //Delete link was clicked for a row in list table
                        $list_albums->delete_albums(strip_tags($_REQUEST['id']));
                    }
                }
                //Fetch, prepare, sort, and filter our data...
                $list_albums->prepare_items();
                //echo "put table of locked entries here"; 
                ?>
                <form id="tables-filter" method="get" onSubmit="return confirm('Are you sure you want to perform this bulk operation on the selected entries?');">
                <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
                <!-- Now we can render the completed list table -->
                <?php $list_albums->display(); ?>
                </form>                           

        </div></div>
        <?php
        }//End of gallery item listing
    }
    
    function render_tab2()
    {
        global $wp_photo_gallery;

        global $wpdb;
        $album_table_name = WPPG_TBL_ALBUM;
        $gallery_table_name = WPPG_TBL_GALLERY;
        $album_id = '';
        $album_name = '';
        $album_thumb_url = '';
        $album_galleries = array();
        $album_sort_order = 0;
        $album_category = 0; //not being used - set to zero for now
        
        $galleries = $wpdb->get_results("SELECT * FROM $gallery_table_name");
        $number_of_gallery_items = $wpdb->num_rows;

        if(isset($_GET['wppg_album_id'])){
            $album_id = strip_tags($_GET['wppg_album_id']);
            $result = $wpdb->get_row("SELECT * FROM $album_table_name WHERE id = '$album_id'", ARRAY_A);
            if($result == NULL){
                echo '<div id="message" class="error">' .__('No album found with ID: ','spgallery').$album_id. '</div>';
            }else{
                $album_name = $result['album_name'];
                $album_galleries = maybe_unserialize($result['gallery_list']);
                $album_sort_order = $result['sort_order'];
                $album_thumb_url = $result['thumbnail_url'];
            }
        }
        
        
        if(isset($_POST['wppg_save_album']))
        {
            $errors = '';
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'wppg-save-album-nonce'))
            {
                $wp_photo_gallery->debug_logger->log_debug("Nonce check failed on album save!",4);
                die("Nonce check failed on album save!");
            }

            if (empty($_POST['wppg_album_name'])) {
                $errors .= '<p>'.__('Please enter an album name', 'spgallery').'</p>';
            } else{
                $album_name = stripslashes(trim($_POST['wppg_album_name']));
            }
    
            if (isset($_POST['wppg_upload_album_thumb']) && !empty( $_POST['wppg_upload_album_thumb'])) {
                $file_type = wp_check_filetype($_POST['wppg_upload_album_thumb']);
                if (strpos($file_type['type'], 'image') !== false) {
                        $album_thumb_url = trim($_POST['wppg_upload_album_thumb']);
                } else {
                        $errors .= '<p>'.__('The thumbnail URL file must be an image.', 'spgallery').'</p>';
                }

                //Now validate that the entry is a URL
                if(!filter_var($album_thumb_url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED)){
                    $errors .= '<p>'.__('The entry for the thumbnail URL is incorrect.', 'spgallery').'</p>';
                }
            }else{
                $errors .= '<p>'.__('Please choose an album thumbnail image', 'spgallery').'</p>';
            }

            //Let's take care of the gallery checkboxes submissions
            $post_array = $_POST;
            foreach ($post_array as $key=>$value)
            {
                if(strpos($key, 'wppg_gallery_id_') !== false){
                    $album_galleries[] = $value;
                }
            }

            $album_sort_order = $_POST['wppg_album_sort_order'];

            if (strlen($errors)> 0){
                $this->show_msg_error($errors);
            }else{
                $album_id = wp_strip_all_tags(trim($_POST['wppg_album_id']));
                if ($album_id != '' || $album_id != null){
                    //updating an existing gallery
                    $existing_album = $album_id;
                    $album_data = array(
                        'id' => $album_id,
                        'album_name' => $album_name,
                        'thumbnail_url' => $album_thumb_url,
                        'gallery_list' => maybe_serialize($album_galleries),
                        'album_category' => $album_category,
                        'sort_order' => $album_sort_order,
                        'updated' => date('Y-m-d H:i:s')
                    );
                    $condition = array('id' => $album_id);
                    $result = $wpdb->update( $album_table_name, $album_data, $condition);
                    if ($result == false){
                        $wp_photo_gallery->debug_logger->log_debug("DB update of Album ID ".$album_id." failed!",4);
                        $errors .= '<p>'.__('DB update failed!', 'spgallery').'</p>';
                    }
                }else{
                    //creating a new album
                    $album_data = array(
                        'album_name' => $album_name,
                        'thumbnail_url' => $album_thumb_url,
                        'album_category' => $album_category,
                        'gallery_list' => maybe_serialize($album_galleries),
                        'sort_order' => $album_sort_order,
                        'created' => date('Y-m-d H:i:s'),
                        'updated' => date('Y-m-d H:i:s')
                    );
                    $result = $wpdb->insert( $album_table_name, $album_data);
                    $album_id = $wpdb->insert_id;
                    if ($result == false){
                        $wp_photo_gallery->debug_logger->log_debug("DB insert of new album record failed!",4);
                        $errors .= '<p>'.__('DB insert failed!', 'spgallery').'</p>';
                    }
                    $page_created = WPPGPhotoAlbum::create_album_page($album_id); //Create album page with shortcode
                    if(!$page_created){
                        $errors .= '<p>'.__('The page creation process for this album failed!', 'spgallery').'</p>';
                    }
                }
                if (strlen($errors)> 0){
                    $this->show_msg_error($errors);
                }else{
                    $this->show_msg_updated(__('The album was saved successfully.','spgallery'));
                }
            }
        }
        
   ?>
        <h2><?php _e('Create/Edit Album', 'spgallery')?></h2>
        <div class="postbox wppg-album-settings-section">
        <h3><label for="title"><?php _e('Album Settings', 'spgallery'); ?></label></h3>
        <div class="inside">
        <form action="" method="POST">
        <?php wp_nonce_field('wppg-save-album-nonce'); ?>
        <input type="hidden" name="wppg_album_id" value="<?php echo $album_id;?>"/>
        <div class="inside">
            <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Album Name', 'spgallery');?>:</th>
                <td><input type="text" size="25" name="wppg_album_name" value="<?php echo htmlspecialchars($album_name); ?>" />
                <span class="description"><?php _e('This is the name of your album', 'spgallery'); ?></span>
                </td> 
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Album Thumbnail URL', 'spgallery');?>:</th>
                <td>
                <input type="text" id="wppg_upload_album_thumb" size="90" name="wppg_upload_album_thumb" value="<?php echo $album_thumb_url; ?>" />
                <input id="wppg_upload_album_thumb_button" type="button" value="Select Album Image" class="button-secondary" /><br />
                <span class="description"><?php _e('Upload or choose an image to represent your album. This image will be used as a thumbnail for your album.', 'spgallery'); ?></span>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Sort Order Of Album Contents', 'spgallery');?>:</th>
                <td>
                    <select id="wppg_album_sort_order" name="wppg_album_sort_order">
                        <option value="0" <?php selected( $album_sort_order, '0' ); ?>><?php _e( 'By ID Ascending', 'spgallery' ); ?></option>
                        <option value="1" <?php selected( $album_sort_order, '1' ); ?>><?php _e( 'By ID Descending', 'spgallery' ); ?></option>
                        <option value="2" <?php selected( $album_sort_order, '2' ); ?>><?php _e( 'By Date Ascending', 'spgallery' ); ?></option>
                        <option value="3" <?php selected( $album_sort_order, '3' ); ?>><?php _e( 'By Date Descending', 'spgallery' ); ?></option>
                        <option value="4" <?php selected( $album_sort_order, '4' ); ?>><?php _e( 'By Name Ascending', 'spgallery' ); ?></option>
                        <option value="5" <?php selected( $album_sort_order, '5' ); ?>><?php _e( 'By Name Descending', 'spgallery' ); ?></option>
                    </select>
                <span class="description"><?php _e('Choose the order in which the galleries are displayed when someone is viewing this album', 'spgallery'); ?></span>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Select Galleries', 'spgallery');?>:
                    <div class="description"><?php _e('(Select from the available galleries to include in this album)', 'spgallery'); ?></div>
                </th>
                <td>
        <?php 
            if( $number_of_gallery_items > 0 && $galleries != 0)
            {
                $j = 0;
                foreach($galleries as $item)
                {
        ?>
                <ul class="wppg_checkbox_grid">
                    <li><input type="checkbox" <?php echo (in_array($item->id, $album_galleries))? 'checked="checked"' : ''; ?> name="wppg_gallery_id_<?php echo $item->id; ?>" value="<?php echo $item->id; ?>" /><label for="<?php echo $item->name; ?>">&nbsp;<?php echo $item->name; ?></label></li>
                </ul>
        <?php
                    $j++;
                }//End foreach
            }//End if
        ?>

                </td>
            </tr>
            <tr><td colspan="2"><div class="wppg_section_separator_1"></div></td></tr>
        </table>
        <input type="submit" name="wppg_save_album" value="Save Album" class="button-primary" />
        </div>
        </form>   
        </div></div>
            <?php
    }
    
} //end class