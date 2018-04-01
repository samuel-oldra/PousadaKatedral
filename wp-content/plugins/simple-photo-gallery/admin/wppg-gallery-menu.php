<?php

class WP_Photo_Gallery_Gallery_Menu extends WP_Photo_Gallery_Admin_Menu
{
    var $menu_page_slug = WP_PHOTO_GALLERY_MENU_SLUG;
    
    /* Specify all the tabs of this menu in the following array */
    var $menu_tabs = array(
        'tab1' => 'Gallery Management', 
        'tab2' => 'Add/Edit',
        'tab3' => 'Selling Your Photos',

        );

    var $menu_tabs_handler = array(
        'tab1' => 'render_tab1', 
        'tab2' => 'render_tab2',
        'tab3' => 'render_tab3',
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
        include_once 'wppg-list-galleries.php'; //For rendering the list of existing galleries table
        $gallery_list = new WPPG_List_Galleries();
        if(isset($_REQUEST['action'])) //Do list table form row action tasks
        {
            if($_REQUEST['action'] == 'delete_gallery'){ //"Delete Gallery" link was clicked for a row in the list table
                $g = new WPPGPhotoGallery($_REQUEST['id']);
                $gallery_list->delete_gallery(strip_tags($_REQUEST['id']));
                
                //Delete gallery page if it exists
                $p = get_post($g->page_id);
                if($p){
                    wp_delete_post($p->ID,true);
                }                
            }
        }
        ?>
        <h2><?php _e('Gallery Management Menu', 'spgallery')?></h2>
        <div class="postbox">
        <h3><label for="title"><?php _e('Creating or Editing a Gallery', 'spgallery'); ?></label></h3>
        <div class="inside">
        <div class="wppg_blue_box">
            <ul class="wppg_admin_ul_grp1">
                <li><?php _e('To create a new gallery click the "Create New Gallery" button below.', 'spgallery'); ?></li>
                <li><?php _e('To edit an existing gallery please click the "Edit" link for the applicable gallery in the "Existing Galleries" table below.', 'spgallery'); ?></li>
            </ul>
        </div>
        </div></div>
        <div class="postbox">
        <h3><label for="title"><?php _e( 'Create a Gallery' , 'spgallery' ); ?></label></h3>
        <div class="inside">
            <table class="form-table">
                <tr>
                    <td><input type="submit" name="create_gallery" value="Create New Gallery" class="button-primary" onclick="window.location ='?page=wppg_gallery&tab=tab2'" />
                    <span class="description"><?php _e('Click this button if you wish to create a new gallery and upload photos', 'spgallery'); ?></span></td>
                </tr>
        </table>
        </div></div>

        <div class="postbox">
        <h3><label for="title"><?php _e('Existing Galleries', 'spgallery'); ?></label></h3>
        <div class="inside">
            <?php 
            //Fetch, prepare, sort, and filter our data...
            $gallery_list->prepare_items();
            //echo "put table of locked entries here"; 
            ?>
            <form id="tables-filter" method="get" onSubmit="return confirm('Are you sure you want to perform this bulk operation on the selected entries?');">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
            <input type="hidden" name="id" value="<?php echo isset($_REQUEST['id'])?$_REQUEST['id']:''; ?>" />
            <!-- Now we can render the completed list table -->
            <?php $gallery_list->display(); ?>
            </form>
        </div></div>
        <?php
    }
    
    function render_tab2()
    {
        global $wp_photo_gallery;
        $gallery_id = '';

        if(isset($_GET['wppg_gallery_id'])){
            $gallery_id = strip_tags($_GET['wppg_gallery_id']);
        }
        //Initialize some variables
        $new_gallery_id = '';
        $existing_gallery_id = '';
        $g = new WPPGPhotoGallery($gallery_id);
        $gallery_name = ($g->name != NULL)?$g->name:'';
        $gallery_watermark = ($g->watermark != NULL)?$g->watermark:'';
        $gallery_watermark_opacity = ($g->watermark_opacity != NULL)?$g->watermark_opacity:'35';
        $gallery_watermark_placement = ($g->watermark_placement != NULL)?$g->watermark_placement:'0';
        $gallery_watermark_width = ($g->watermark_width != NULL)?$g->watermark_width:'';
        $gallery_watermark_font_size = ($g->watermark_font_size != NULL)?$g->watermark_font_size:'';
        $gallery_sort_order = ($g->sort_order != NULL)?$g->sort_order:'0';
        $gallery_thumb_template = ($g->gallery_thumb_template != NULL)?$g->gallery_thumb_template:'0';
        $gallery_photo_preview = ($g->display_image_on_page != NULL)?$g->display_image_on_page:'1';
        $gallery_category = ($g->category != NULL)?$g->category:'';
        $gallery_pagination = ($g->enable_pagination != NULL)?$g->enable_pagination:'';
        $gallery_thumbs_per_page = ($g->thumbs_per_page != NULL)?$g->thumbs_per_page:'';
        $gallery_page_id = ($g->page_id != NULL)?$g->page_id:'0';
        
        include_once 'wppg-list-gallery-images.php'; //For rendering the gallery images table
        $gallery_image_list = new WPPG_List_Gallery_Images();
        
        $gallery_page_url = '';
        $preview_gallery_page_msg = '';
        if($gallery_page_id != 0){
            $gallery_page_url = get_permalink($gallery_page_id);
            $preview_gallery_page_msg .= '<div class="wppg_blue_box wppg_one_third_width">';
            $preview_link = '<a href="'.$gallery_page_url.'" target="_blank">'.__('click here', 'spgallery').'</a>';
            $preview_gallery_page_msg .= '<p>'.sprintf( __('To preview your gallery page on the front end %s', 'spgallery'), $preview_link).'</p>';
            $preview_gallery_page_msg .= '</div>';
        }
        
        if(isset($_REQUEST['action'])) //Do list table form row action tasks
        {
            if($_REQUEST['action'] == 'gallery_saved'){
               $this->show_msg_updated(__('The Gallery was saved successfully.','spgallery'));
            }

            if($_REQUEST['action'] == 'remove_from_gallery'){ //"Remove From Gallery" link was clicked for a row in the list table
                $gallery_image_list->remove_image_from_gallery(strip_tags($_REQUEST['image_id']));
            }
        }
        
        if(isset($_GET['page']) && isset($_GET['task']) && isset($_GET['deleted'])) 
        {
            if($_GET['page'] == WP_PHOTO_GALLERY_MENU_SLUG && $_GET['deleted'] == 1){ 
                echo '<div id="message" class="updated fade"><p><strong>';
                _e('You have successfully deleted the selected gallery image(s) permanently!','spgallery');
                echo '</strong></p></div>';
            }
        }
        
        if(isset($_POST['wppg_save_gallery']))
        {
            $errors = '';
            $num_gallery_images = '';
            $nonce=$_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'wppg-save-gallery'))
            {
                $wp_photo_gallery->debug_logger->log_debug("Nonce check failed on gallery settings save!",4);
                die("Nonce check failed on gallery settings save!");
            }

            if (empty($_POST['wppg_gallery_name'])) {
                $errors .= '<p>'.__('Please enter a gallery name', 'spgallery').'</p>'; //TODO
            } else{
                $gallery_name = wp_strip_all_tags(trim($_POST['wppg_gallery_name']));
            }

            //If images have been added to the gallery
            if (isset($_POST['wppg_img_count'])){
                if (ctype_digit($_POST['wppg_img_count'])){
                    $num_gallery_images = $_POST['wppg_img_count']; //get the number of gallery images from the hidden field
                }
            }

            if (!empty($_POST['wppg_gallery_watermark'])) {
                $gallery_watermark = wp_strip_all_tags(trim($_POST['wppg_gallery_watermark']));
            }else{
                $gallery_watermark = '';
            }
            
            $gallery_watermark_width = sanitize_text_field($_POST['wppg_watermark_width']);
            if(!is_numeric($gallery_watermark_width))
            {
                $errors .= '<p>'.__('You entered a non numeric value for the Watermark Photo Width field. It has been set to the default value.','spgallery').'</p>';
                $gallery_watermark_width = '600';//Set it to the default value for this field
            }

            $gallery_watermark_font_size = sanitize_text_field($_POST['wppg_watermark_font_size']);
            if(!is_numeric($gallery_watermark_font_size))
            {
                $errors .= '<p>'.__('You entered a non numeric value for the Watermark Font Size field. It has been set to the default value.','spgallery').'</p>';
                $gallery_watermark_font_size = '35';//Set it to the default value for this field
            }

            $gallery_watermark_placement = $_POST['wppg_watermark_placement'];

            $gallery_watermark_opacity = sanitize_text_field($_POST['wppg_watermark_opacity']);
            if(!is_numeric($gallery_watermark_opacity))
            {
                $errors .= '<p>'.__('You entered a non numeric value for the Watermark Opacity field. It has been set to the default value.','spgallery').'</p>';
                $gallery_watermark_opacity = '35';//Set it to the default value for this field
            }
            else if($gallery_watermark_opacity > 100 || $gallery_watermark_opacity < 0)
            {
                $errors .= '<p>'.__('The Watermark Opacity value must be between and including 0 and 100. It has been set to the default value.','spgallery').'</p>';
                $gallery_watermark_opacity = '35';//Set it to the default value for this field
            }

            //Check if watermark text has changed - if so delete existing WM image
            if ($g->watermark != $gallery_watermark || $g->watermark_opacity != $gallery_watermark_opacity || $g->watermark_width != $gallery_watermark_width || $g->watermark_font_size != $gallery_watermark_font_size || $g->watermark_placement != $gallery_watermark_placement)
            {
                WPPGPhotoGallery::deleteWatermarkImages($gallery_id);
            }

            $gallery_sort_order = $_POST['wppg_gallery_sort_order'];
            $gallery_thumb_template = $_POST['wppg_gallery_thumb_template'];
            
            $gallery_photo_preview = isset($_POST['wppg_gallery_photo_preview'])?'1':'0';

            $gallery_pagination = isset($_POST['wppg_gallery_pagination'])?'1':'0';
            $gallery_thumbs_per_page = sanitize_text_field($_POST['wppg_thumbs_per_page']);
            
            if (strlen($errors)> 0){
                $this->show_msg_error($errors);
            }else{
                if($gallery_id != NULL){
                    //Update existing gallery
                    $existing_gallery_id = $gallery_id;
                    $gallery_data = array(
                        'id' => $gallery_id,
                        'name' => $gallery_name,
                        'category' => $gallery_category, 
                        'watermark' => $gallery_watermark,
                        'watermark_opacity' => $gallery_watermark_opacity,
                        'watermark_placement' => $gallery_watermark_placement,
                        'watermark_width' => $gallery_watermark_width,
                        'watermark_font_size' => $gallery_watermark_font_size,
                        'sort_order' => $gallery_sort_order,
                        'gallery_thumb_template' => $gallery_thumb_template,
                        'display_image_on_page' => $gallery_photo_preview,
                        'enable_pagination' => $gallery_pagination,
                        'thumbs_per_page' => $gallery_thumbs_per_page,
                        'updated' => date('Y-m-d H:i:s')
                    );
                    $g->create_or_update_gallery($gallery_data);
                }else{
                    //Create new gallery
                    $gallery_data = array(
                        'name' => $gallery_name,
                        'category' => $gallery_category, 
                        'watermark' => $gallery_watermark,
                        'watermark_opacity' => $gallery_watermark_opacity,
                        'watermark_placement' => $gallery_watermark_placement,
                        'watermark_width' => $gallery_watermark_width,
                        'watermark_font_size' => $gallery_watermark_font_size,
                        'sort_order' => $gallery_sort_order,
                        'gallery_thumb_template' => $gallery_thumb_template,
                        'created' => date('Y-m-d H:i:s'),
                        'display_image_on_page' => $gallery_photo_preview,
                        'enable_pagination' => $gallery_pagination,
                        'thumbs_per_page' => $gallery_thumbs_per_page,
                        'updated' => date('Y-m-d H:i:s')
                    );
                    $new_gallery_id = $g->create_or_update_gallery($gallery_data);
                    $gallery_id = $new_gallery_id;
                    if($gallery_id === false){
                        $gallery_id = NULL;
                        $errors .= '<p>'.__('Gallery DB insert or update failed!', 'spgallery').'</p>';
                    }
                    
                    WPPGPhotoGallery::create_gallery_page($gallery_id); //Create gallery page with shortcode
                }
                
                //Now let's process the selected gallery photos
                if (isset($_POST['wppg_img_count']) && $num_gallery_images != NULL){
                    $g->process_gallery_images($num_gallery_images, $gallery_id);
                }
                
                //Now let's rename the temp dir if applicable
                if(!isset($_GET['wppg_gallery_id']) && ($gallery_id != NULL) && isset($_POST['wppg_img_count'])){
                    $upload_dir = wp_upload_dir();
                    $old_dir = $upload_dir['basedir'].'/'.WPPG_UPLOAD_SUB_DIRNAME.'/'.WPPG_UPLOAD_TEMP_DIRNAME;
                    $new_dir = $upload_dir['basedir'].'/'.WPPG_UPLOAD_SUB_DIRNAME.'/'.$gallery_id;

                    //Let's first create an empty index.html file
                    $index_file = $old_dir.'/index.html';
                    $handle = fopen($index_file, 'w') or die('Cannot open file:  '.$index_file);
                    fclose($handle);

                    if(!rename($old_dir, $new_dir)){
                        $wp_photo_gallery->debug_logger->log_debug("Upload directory rename failed upon creation of new gallery with ID: ".$gallery_id,4);
                    }
                    
                    //We must also modify any meta_data for each image which had reference to the temp dir
                    WPPGPhotoGallery::replace_image_meta_info_temp_dir($gallery_id);    
                }
                if (strlen($errors)> 0){
                    //echo '<div id="message" class="error">' . $errors . '</div>';
                    $this->show_msg_error($errors);
                }else{
                    $tab = isset($_GET['tab'])?strip_tags($_GET['tab']):'';
                    $url = "admin.php?page=wppg_gallery&tab=".$tab."&wppg_gallery_id=".$gallery_id."&action=gallery_saved";
                    WP_Photo_Gallery_Utility::redirect_to_url($url);
                }
            }
        }
        
   ?>
        <h2><?php _e('Create/Edit Gallery', 'spgallery')?></h2>
        <?php echo $preview_gallery_page_msg; ?>

        <div class="postbox wppg-gallery-settings-section">
        <h3><label for="title"><?php _e('Gallery Settings', 'spgallery'); ?></label></h3>
        <div class="inside">
        <form action="" method="POST">
        <?php wp_nonce_field('wppg-save-gallery'); ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Gallery Name', 'spgallery');?>:</th>
                <td><input type="text" size="25" name="wppg_gallery_name" value="<?php echo $gallery_name; ?>" />
                <span class="description"><?php _e('This is the name of your gallery', 'spgallery'); ?></span>
                </td> 
            </tr>
            <tr>
                <th scope="row"><?php _e('Gallery Thumbnail Template', 'spgallery');?>:</th>
                <td>
                    <select id="wppg_gallery_thumb_template" name="wppg_gallery_thumb_template">
                        <option value="0" <?php selected( $gallery_thumb_template, '0' ); ?>><?php _e( 'Template 1', 'spgallery' ); ?></option>
                        <option value="1" <?php selected( $gallery_thumb_template, '1' ); ?>><?php _e( 'Template 2', 'spgallery' ); ?></option>
                        <option value="2" <?php selected( $gallery_thumb_template, '2' ); ?>><?php _e( 'Template 3 (Masonry)', 'spgallery' ); ?></option>
                    </select>
                <span class="description"><?php _e('Choose the template style for displaying your gallery thumbnails', 'spgallery'); ?></span>
                <span class="wppg_more_info_anchor"><span class="wppg_more_info_toggle_char">+</span><span class="wppg_more_info_toggle_text"><?php _e('More Info', 'spgallery'); ?></span></span>
                <div class="wppg_more_info_body">
                        <?php 
                        echo '<p class="description">'.__('Template 1: Will display your gallery thumbnails in a grid with thumbnail size 150x150.', 'spgallery').'</p>';
                        echo '<p class="description">'.__('Template 2: Will display your gallery thumbnails using true landscape/portrait proportions.', 'spgallery').'</p>';
                        ?>
                </div>

                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Sort Order Of Gallery Images', 'spgallery');?>:</th>
                <td>
                    <select id="wppg_gallery_sort_order" name="wppg_gallery_sort_order">
                        <option value="0" <?php selected( $gallery_sort_order, '0' ); ?>><?php _e( 'By ID Ascending', 'spgallery' ); ?></option>
                        <option value="1" <?php selected( $gallery_sort_order, '1' ); ?>><?php _e( 'By ID Descending', 'spgallery' ); ?></option>
                        <option value="2" <?php selected( $gallery_sort_order, '2' ); ?>><?php _e( 'By Date Ascending', 'spgallery' ); ?></option>
                        <option value="3" <?php selected( $gallery_sort_order, '3' ); ?>><?php _e( 'By Date Descending', 'spgallery' ); ?></option>
                    </select>
                <span class="description"><?php _e('Choose the sort order of your gallery images when they are displayed on the front end of your site', 'spgallery'); ?></span>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Preview Photo via Page', 'spgallery');?>:</th>
                <td><input name="wppg_gallery_photo_preview" type="checkbox" <?php echo ($gallery_photo_preview == '1')? 'checked="checked"' : ''; ?>/>
                <span class="description"><?php _e('When enabled this setting will show previews of your photos on a separate page. When disabled your photo previews will be via a lightbox. (Activating this option is recommended)', 'spgallery'); ?></span>
                </td> 
            </tr>
            <tr><td colspan="2"><div class="wppg_section_separator_1"></div></td></tr>
            <tr>
                <th scope="row"><?php _e('Watermark Text', 'spgallery');?>:</th>
                <td><input type="text" size="25" name="wppg_gallery_watermark" value="<?php echo $gallery_watermark; ?>" />
                <span class="description"><?php _e('The text which you enter here will appear as a watermark on all photos in this gallery when they are being previewed by your visitors.', 'spgallery'); ?></span>
                <span class="wppg_more_info_anchor"><span class="wppg_more_info_toggle_char">+</span><span class="wppg_more_info_toggle_text"><?php _e('More Info', 'spgallery'); ?></span></span>
                <div class="wppg_more_info_body">
                        <?php 
                        echo '<p class="description">'.__('Leave this field blank if you wish to display the original photo to your customers when they are previewing this gallery.', 'spgallery').'</p>';
                        ?>
                </div>
                </td> 
            </tr>
            <tr>
                <th scope="row"><?php _e('Watermark Text Opacity', 'spgallery');?>:</th>
                <td><input type="text" size="25" name="wppg_watermark_opacity" value="<?php echo $gallery_watermark_opacity; ?>" />
                <span class="description"><?php _e('Enter a value between 0 and 100 where 0 is most transparent and 100 is least transparent.', 'spgallery'); ?></span>
                <span class="wppg_more_info_anchor"><span class="wppg_more_info_toggle_char">+</span><span class="wppg_more_info_toggle_text"><?php _e('More Info', 'spgallery'); ?></span></span>
                <div class="wppg_more_info_body">
                        <?php 
                        echo '<p class="description">'.__('This field enables you to set the transparency of your watermark text.
                                                        A value of 0 will make your watermark text fully transparent and a value of 100 will make it non-transparent.', 'spgallery').'</p>';
                        ?>
                </div>
                </td> 
            </tr>
            <tr>
                <th scope="row"><?php _e('Watermark Text Placement', 'spgallery');?>:</th>
                <td>
                    <select id="wppg_watermark_placement" name="wppg_watermark_placement">
                        <option value="0" <?php selected( $gallery_watermark_placement, '0' ); ?>><?php _e( 'Centered', 'spgallery' ); ?></option>
                        <option value="1" <?php selected( $gallery_watermark_placement, '1' ); ?>><?php _e( 'Repeated Grid', 'spgallery' ); ?></option>
                    </select>
                <span class="description"><?php _e('Choose the placement location of your watermark text', 'spgallery'); ?></span>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Watermarked Max Preview Dimension (px)', 'spgallery');?>:</th>
                <td><input type="text" size="25" name="wppg_watermark_width" value="<?php echo empty($gallery_watermark_width)?'600':$gallery_watermark_width; ?>" />
                <span class="description"><?php _e('Enter a desired max dimension size for your watermarked preview', 'spgallery'); ?></span>
                <span class="wppg_more_info_anchor"><span class="wppg_more_info_toggle_char">+</span><span class="wppg_more_info_toggle_text"><?php _e('More Info', 'spgallery'); ?></span></span>
                <div class="wppg_more_info_body">
                        <?php 
                        echo '<div class="description">'.__('<p>This field enables you to set the maximum desired width or height of the watermarked version of your photos in this gallery depending on whether the photo is a portrait or landscape.</p>
                                                        <p>For instance if your photo is a landscape style image then the value you enter here will represent the maximum desired width of the preview image.</p>
                                                        <p>Conversely, if your photo is a portrait style image then the value you enter here will represent the maximum desired height of the preview image.</p>
                                                        <p>The plugin will automatically scale the watermarked photo\'s other dimension to the appropriate value based on the value entered in this field.<p>', 'spgallery').'</div>';
                        ?>
                </div>
                </td> 
            </tr>
            <tr>
                <th scope="row"><?php _e('Watermark Font Size (px)', 'spgallery');?>:</th>
                <td><input type="text" size="25" name="wppg_watermark_font_size" value="<?php echo empty($gallery_watermark_font_size)?'35':$gallery_watermark_font_size; ?>" />
                <span class="description"><?php _e('Set the font size of the watermark text', 'spgallery'); ?></span>
                <span class="wppg_more_info_anchor"><span class="wppg_more_info_toggle_char">+</span><span class="wppg_more_info_toggle_text"><?php _e('More Info', 'spgallery'); ?></span></span>
                <div class="wppg_more_info_body">
                        <?php 
                        echo '<p class="description">'.__('This field enables you to set the font size of the watermark text for all photos in this gallery. 
                                                        If left blank the plugin will default to 35px.', 'spgallery').'</p>';
                        ?>
                </div>
                </td> 
            </tr>
            <tr><td colspan="2"><div class="wppg_section_separator_1"></div></td></tr>
            <tr>
                <th scope="row"><?php _e('Use Pagination', 'spgallery');?>:</th>
                <td>
                <input id="wppg_gallery_pagination" name="wppg_gallery_pagination" type="checkbox" <?php echo ($gallery_pagination == '1')? 'checked="checked"' : ''; ?>>
                <span class="description"><?php _e('Click this to enable pagination when displaying your gallery\'s photo thumbnails.', 'spgallery'); ?></span>
                </td> 
            </tr>
            <tr>
                <th scope="row"><?php _e('Thumbs Per Page', 'spgallery');?>:</th>
                <td><input type="text" size="25" name="wppg_thumbs_per_page" value="<?php echo empty($gallery_thumbs_per_page)?'20':$gallery_thumbs_per_page; ?>" />
                <span class="description"><?php _e('Set the number of thumbnails to display per page', 'spgallery'); ?></span>
                </td> 
            </tr>
            <tr><td colspan="2"><div class="wppg_section_separator_1"></div></td></tr>
            <tr  class="uploader wppg_upload_button_row">
                <th scope="row"><?php _e('Upload Images', 'spgallery');?>:</th>
                <td><input type="submit" class="wppg_upload_image_button button" name="wppg_upload_image_button" value="Upload"/>
                    <span class="description"><?php _e('To upload or select your images from the media library please click this button', 'spgallery'); ?></span>
                </td>
            </tr>

        </table>
        <input type="submit" name="wppg_save_gallery" value="Save Gallery" class="button-primary" />
        </form>   
        </div></div>
        <div class="postbox">
        <h3><label for="title"><?php _e('Gallery Images', 'spgallery'); ?></label></h3>
        <div class="inside">
            <?php 
            //Fetch, prepare, sort, and filter our data...
            $gallery_image_list->prepare_items();
            //echo "put table of locked entries here"; 
            ?>
            <form id="tables-filter" method="get" onSubmit="return confirm('Are you sure you want to perform this bulk operation on the selected entries?');">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
            <input type="hidden" name="tab" value="<?php echo $_REQUEST['tab']; ?>" />
            <input type="hidden" name="id" value="<?php echo isset($_REQUEST['id'])?$_REQUEST['id']:''; ?>" />
            <!-- Now we can render the completed list table -->
            <?php $gallery_image_list->display(); ?>
            </form>
        </div></div>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                wp.media.controller.Library.prototype.defaults.contentUserSetting=false; //Display the "Upload Files" tab by default when media uploader window is launched
            });
        </script>        
            <?php
    }
    
    function render_tab3() 
    {
        ?>
        <h2><?php _e('Sell Your Digital and Physical Photos', 'spgallery')?></h2>
        <div class="postbox">
        <h3><label for="title"><?php _e('WP Photo Seller Plugin', 'spgallery'); ?></label></h3>
        <div class="inside">
        <div class="wppg_blue_box">
            <div>
            <?php
                $click_here_link = '<a href="http://www.tipsandtricks-hq.com/wordpress-photo-seller-plugin" target="_blank">'.__('Click Here', 'spgallery').'</a>';
                $photoseller_link = '<a href="http://www.tipsandtricks-hq.com/wordpress-photo-seller-plugin" target="_blank">'.__('WP Photo Seller', 'spgallery').'</a>';
                echo '<p>'.sprintf( __('If you are looking for a flexible and professional solution to sell your photos from your WordPress site then you should check out the <strong>%s</strong> plugin.', 'spgallery'), $photoseller_link).'</p>';
                echo '<a href="http://www.tipsandtricks-hq.com/wordpress-photo-seller-plugin" target="_blank"><img src="'.WP_PHOTO_URL.'/images/photo-seller-banner-240-103.png'.'"></a>';
                echo '<p>'.__('Some of the features and highlights of this plugin include:','spgallery').'</p>';
            ?>
            </div>
            <ul class="wppg_admin_ul_grp1">
                <li><?php _e('Ability to sell both digital and physical photos.', 'spgallery'); ?></li>
                <li><?php _e('Sell digital photos with varying size and price options. Pugin will automatically create and deliver secure download link to your customers.', 'spgallery'); ?></li>
                <li><?php _e('Secure digital delivery of photos - the plugin creates and automatically sends encrypted links to your customers.', 'spgallery'); ?></li>
                <li><?php _e('Flexible photo variation options - make as many versions/types of the same (physical or digital) photo and sell for different prices.', 'spgallery'); ?></li>
                <li><?php _e('Inventory control of your photo stocks - set inventory levels for specified photos. Very useful when selling physical prints.', 'spgallery'); ?></li>
                <li><?php _e('Watermarking of photos when previewing.', 'spgallery'); ?></li>
                <li><?php _e('Zoom functionality to allow your customers to examine finer details of your photos without the need to display full resolution image.', 'spgallery'); ?></li>
            </ul>
            <p><?php _e('....and loads more!', 'spgallery'); ?></p>
            <p><?php echo sprintf( __('%s to see more features and a demo.', 'spgallery'), $click_here_link); ?></p>
        </div>
        </div></div>
        <?php
    }
    
    
} //end class