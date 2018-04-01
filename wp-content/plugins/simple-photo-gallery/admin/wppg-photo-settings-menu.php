<?php

class WP_Photo_Gallery_Settings_Menu extends WP_Photo_Gallery_Admin_Menu
{
    var $menu_page_slug = WP_PHOTO_SETTINGS_MENU_SLUG;
    
    /* Specify all the tabs of this menu in the following array */
    var $menu_tabs = array(
        'tab1' => 'General Settings', 
        'tab2' => 'Advanced Settings', 
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
        global $wp_photo_gallery;
        //Do form submission tasks
        if(isset($_POST['wppg_save_general_gallery_settings']))
        {
            $errors = '';
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'wppg-save-general-gallery-settings-nonce'))
            {
                $wp_photo_gallery->debug_logger->log_debug("Nonce check failed on gallery general settings save!",4);
                die("Nonce check failed on gallery settings save!");
            }


            $gallery_selection_sort_order = $_POST['wppg_gallery_selection_sort_order'];
            $wp_photo_gallery->configs->set_value('wppg_gallery_home_sort_order', $gallery_selection_sort_order);

            $gallery_home_page = $_POST['wppg_gallery_home_page'];
            $wp_photo_gallery->configs->set_value('wppg_gallery_home_page_id', $gallery_home_page);

            $album_home_page = $_POST['wppg_album_home_page'];
            $wp_photo_gallery->configs->set_value('wppg_album_home_page_id', $album_home_page);

            $wp_photo_gallery->configs->save_config();

            echo '<div id="message" class="updated fade"><p><strong>';
            _e('Gallery general settings were successfully saved.','spgallery');
            echo '</strong></p></div>';
        }
        $gallery_selection_sort_order = $wp_photo_gallery->configs->get_value('wppg_gallery_home_sort_order');
        ?>
        <div class="wppg_grey_box">
 	<p>For information, updates and documentation, please visit the <a href="http://photography-solutions.tipsandtricks-hq.com/simple-wordpress-photo-gallery-plugin" target="_blank">Simple Photo Gallery Plugin</a> Page.</p>
        <p><a href="https://www.tipsandtricks-hq.com/development-center" target="_blank">Follow us</a> on Twitter, Google+ or via Email to stay up to date regarding new features and improvements to this plugin.</p>
        <p>If you like the plugin, please <a href="http://wordpress.org/support/view/plugin-reviews/simple-photo-gallery" target="_blank">give us a good rating</a>.</p>
        </div>
        
        <form action="" method="POST">
        <?php wp_nonce_field('wppg-save-general-gallery-settings-nonce'); ?>
        <div class="postbox">
        <h3><label for="title"><?php _e('Getting Started', 'spgallery'); ?></label></h3>
        <div class="inside">
            <div class="wppg_blue_box">
                <?php
                echo '<p>'.__('Using the <strong>Simple Photo Gallery</strong> Plugin is easy.', 'spgallery').'</p>'; 
                $gallery_link = '<a href="admin.php?page='.WP_PHOTO_GALLERY_MENU_SLUG.'">gallery settings</a>';
                $info_msg = '<p>'.sprintf( __('Just go to the %s and upload your photos and create your gallery.', 'spgallery'), $gallery_link).'</p>';
                echo $info_msg;
                echo '<p>'.__('After uploading your photos and saving your gallery the plugin will automatically create the required gallery pages on the front end of your site. It really is that simple!', 'spgallery').'</p>'; 
                ?>
            </div>
        </div></div>
        
        <div class="postbox">
            <h3><label for="title"><?php _e('General Gallery Settings', 'spgallery'); ?></label></h3>
            <div class="inside">
                <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Sort Order Of Gallery Selection', 'spgallery');?>:</th>
                    <td>
                        <select id="wppg_gallery_selection_sort_order" name="wppg_gallery_selection_sort_order">
                            <option value="0" <?php selected( $gallery_selection_sort_order, '0' ); ?>><?php _e( 'By ID Ascending', 'spgallery' ); ?></option>
                            <option value="1" <?php selected( $gallery_selection_sort_order, '1' ); ?>><?php _e( 'By ID Descending', 'spgallery' ); ?></option>
                            <option value="2" <?php selected( $gallery_selection_sort_order, '2' ); ?>><?php _e( 'By Date Ascending', 'spgallery' ); ?></option>
                            <option value="3" <?php selected( $gallery_selection_sort_order, '3' ); ?>><?php _e( 'By Date Descending', 'spgallery' ); ?></option>
                            <option value="4" <?php selected( $gallery_selection_sort_order, '4' ); ?>><?php _e( 'By Name Ascending', 'spgallery' ); ?></option>
                            <option value="5" <?php selected( $gallery_selection_sort_order, '5' ); ?>><?php _e( 'By Name Descending', 'spgallery' ); ?></option>
                        </select>
                    <span class="description"><?php _e('Choose the sort order of your gallery selection images when they are displayed on the gallery page of the front end of your site', 'spgallery'); ?></span>
                    </td>
                </tr>
                </table>
            </div>
        </div>

        <div class="postbox">
            <?php
            $pages = get_pages();
            ?>
            <h3><label for="title"><?php _e('Page Settings', 'spgallery'); ?></label></h3>
            <div class="inside">
                <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Gallery Home Page', 'spgallery');?>:</th>
                    <td>
                        <select id="wppg_gallery_selection_sort_order" name="wppg_gallery_home_page">
              <?php 
                        foreach($pages as $page)
                        {
                            if($wp_photo_gallery->configs->get_value('wppg_gallery_home_page_id') == $page->ID)
                            {
                                echo '<option value="'.$page->ID.'" selected="selected">'.$page->post_title.'</option>';   
                            }
                            else
                            {
                                echo '<option value="'.$page->ID.'">'.$page->post_title.'</option>';
                            } 
                        }
                ?>
                        </select>
                    <span class="description"><?php _e('Select the page that you want to set as the main gallery home page.', 'spgallery'); ?></span>
                    <span class="wppg_more_info_anchor"><span class="wppg_more_info_toggle_char">+</span><span class="wppg_more_info_toggle_text"><?php _e('More Info', 'spgallery'); ?></span></span>
                    <div class="wppg_more_info_body">
                        <?php 
                        echo '<p class="description">'.__('Note: This plugin will initially automatically create a default gallery home page with the correct shortcode for you.', 'spgallery').'</p>';
                        echo '<p class="description">'.__('If you decide to change this page to another one then you will need to make sure that you also insert the following shortcode:', 'spgallery').'</p>';
                        echo '<p><strong>[wppg_photo_gallery_home]</strong></p>';
                        ?>
                    </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Album Home Page', 'spgallery');?>:</th>
                    <td>
                        <select id="wppg_gallery_selection_sort_order" name="wppg_album_home_page">
              <?php 
                        foreach($pages as $page)
                        {
                            if($wp_photo_gallery->configs->get_value('wppg_album_home_page_id') == $page->ID)
                            {
                                echo '<option value="'.$page->ID.'" selected="selected">'.$page->post_title.'</option>';   
                            }
                            else
                            {
                                echo '<option value="'.$page->ID.'">'.$page->post_title.'</option>';
                            } 
                        }
                ?>
                        </select>
                    <span class="description"><?php _e('Select the page that you want to set as the main album home page.', 'spgallery'); ?></span>
                    <span class="wppg_more_info_anchor"><span class="wppg_more_info_toggle_char">+</span><span class="wppg_more_info_toggle_text"><?php _e('More Info', 'spgallery'); ?></span></span>
                    <div class="wppg_more_info_body">
                        <?php 
                        echo '<p class="description">'.__('Note: This plugin will initially automatically create a default album home page with the correct shortcode for you.', 'spgallery').'</p>';
                        echo '<p class="description">'.__('If you decide to change this page to another one then you will need to make sure that you also insert the following shortcode:', 'spgallery').'</p>';
                        echo '<p><strong>[wppg_photo_albums_home]</strong></p>';
                        ?>
                    </div>
                    </td>
                </tr>
                </table>
            </div>
        </div>
        <input type="submit" name="wppg_save_general_gallery_settings" value="Save Settings" class="button-primary" />
        </form>
        
        <?php
    }
    
    function render_tab2()
    {
        global $wp_photo_gallery;
        //Do form submission tasks
        if(isset($_POST['wppg_management_permission_update']))
        {
            $wp_photo_gallery->configs->set_value('wppg_management_permission', $_POST['wppg_management_permission']);
            
            $wp_photo_gallery->configs->save_config();
            echo '<div id="message" class="updated fade"><p><strong>';
            _e('Admin dashboard access permission settings were successfully saved.','spgallery');
            echo '</strong></p></div>';
        }
        ?>
        
        <div class="wppg_grey_box">
 	<p>For information, updates and documentation, please visit the <a href="http://photography-solutions.tipsandtricks-hq.com/simple-wordpress-photo-gallery-plugin" target="_blank">Simple Photo Gallery Plugin</a> Page.</p>
        <p><a href="https://www.tipsandtricks-hq.com/development-center" target="_blank">Follow us</a> on Twitter, Google+ or via Email to stay up to date regarding new features and improvements to this plugin.</p>
        <p>If you like the plugin, please <a href="http://wordpress.org/support/view/plugin-reviews/simple-photo-gallery" target="_blank">give us a good rating</a>.</p>
        </div>
        
        
        <div class="postbox">
        <h3><label for="title">Admin Dashboard Access Permission</label></h3>
        <div class="inside">

            <p>
                Simple Photo Gallery plugin's admin dashboard is accessible to admin users only (just like any other plugin).
                You can allow users with other WP role to access the plugin's admin dashboard by selecting a value below.
                <br /><br />
                <strong>If don't know what this is for then leave it as it is.</strong>
            </p>
            <?php
            $selected_permission = $wp_photo_gallery->configs->get_value('wppg_management_permission');
            ?>
            <form method="post" action="">
                <select name="wppg_management_permission">
                    <option <?php echo ($selected_permission == 'manage_options') ? "selected='selected'" : ""; ?> value="manage_options">Admin</option>
                    <option <?php echo ($selected_permission == 'edit_pages') ? "selected='selected'" : ""; ?> value="edit_pages">Editor</option>
                    <option <?php echo ($selected_permission == 'edit_published_posts') ? "selected='selected'" : ""; ?> value="edit_published_posts">Author</option>
                    <option <?php echo ($selected_permission == 'edit_posts') ? "selected='selected'" : ""; ?> value="edit_posts">Contributor</option>
                </select>
                <input type="submit" name="wppg_management_permission_update" class="button" value="Save Permission &raquo" />
            </form>

        </div></div>     
        <?php
    }
    
} //end class