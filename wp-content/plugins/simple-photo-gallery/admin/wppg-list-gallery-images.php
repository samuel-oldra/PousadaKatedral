<?php
class WPPG_List_Gallery_Images extends WP_Photo_Gallery_List_Table {
    
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'item',     //singular name of the listed records
            'plural'    => 'items',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }

    function column_default($item, $column_name){
    	return $item[$column_name];
    }
        
    function column_id($item){
        $gallery_id = strip_tags($_GET['wppg_gallery_id']);
        $image_id = $item['id'];
        $tab = strip_tags($_REQUEST['tab']);
        $actions = array(
            'edit' => sprintf('<a href="post.php?post=%s&action=edit" target="_blank">Edit</a>',$image_id),
            'remove' => sprintf('<a href="admin.php?page=%s&tab=%s&wppg_gallery_id=%s&action=%s&image_id=%s" onclick="return confirm(\'Are you sure you want to remove this image from this gallery?\')">Remove From Gallery</a>',WP_PHOTO_GALLERY_MENU_SLUG,$tab,$gallery_id,'remove_from_gallery',$image_id),
            'delete' => "<a class='submitdelete' onclick='return showNotice.warn();' href='" . wp_nonce_url( "post.php?action=delete&amp;post=$image_id&amp;gallery_img_delete=1", 'delete-post_' . $image_id ) . "'>" . __( 'Delete Permanently' ) . "</a>",
        );
        return sprintf('%1$s <span style="color:silver"></span>%2$s',
            /*$1%s*/ $item['id'],
            /*$2%s*/ $this->row_actions($actions)
        );
    }

    function column_thumb_url($item){
        return sprintf(
            '<img src="%1$s"  height="75" width="75">',
            /*$1%s*/ $item['thumb_url']
       );
    }

    
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
       );
    }
    
    function get_columns(){
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox
            'id' => 'Image ID',
            'thumb_url' => 'Thumbnail',
            'alt_text' => 'Alt Text',
            'description' => 'Description',
            'date_uploaded' => 'Upload Date'
        );
        return $columns;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'id' => array('id',false),
            'thumb_url' => array('thumb_url',false),
            'alt_text' => array('alt_text',false),
            'description' => array('description',false),
            'date_uploaded' => array('date_uploaded',false)
        );
        return $sortable_columns;
    }
    
    function get_bulk_actions() {
        $actions = array(
            'remove' => 'Remove',
            'delete' => 'Delete'
        );
        return $actions;
    }

    function process_bulk_action() {
        //TODO
        if('delete'===$this->current_action()) 
        {
            //Process delete bulk actions
            if(!isset($_REQUEST['item']))
            {
                $error_msg = '<p>'.__('Error - Please select some records using the checkboxes', 'spgallery').'</p>';
                echo '<div id="message" class="error fade">'.$error_msg.'</div>';
            }else 
            {            
                $this->delete_images_permanently(($_REQUEST['item']));
            }
        }

        if('remove'===$this->current_action()) 
        {
            //Process remove bulk actions
            if(!isset($_REQUEST['item']))
            {
                $error_msg = '<p>'.__('Error - Please select some records using the checkboxes', 'spgallery').'</p>';
                echo '<div id="message" class="error fade">'.$error_msg.'</div>';
            }else 
            {            
                $this->remove_image_from_gallery(($_REQUEST['item']));
            }
        }
    }
    
    /*
     * This function will delete selected images (the posts of type "attachment") permanently.
     * The function accepts either an array of IDs or a single ID
     */
    function delete_images_permanently($entries)
    {
        $meta_key = WPPG_ATTACHMENT_META_TAG;
        $errors = '';
        if (is_array($entries))
        {
            //Remove multiple images from gallery
            foreach($entries as $entry){
                $result = wp_delete_attachment($entry, true);
                if ($result == NULL) {
                    $errors .= '<p><strong>'.sprintf(__('The deletion of attachment with image ID %s failed!', 'spgallery'), $entry).'</strong></p>';
                }
            }
        } elseif ($entries != NULL)
        {
            //Remove a single image from the gallery
            $result = wp_delete_attachment($entries, true);
            if ($result == NULL) {
                $errors .= '<p><strong>'.sprintf(__('The deletion of attachment with image ID %s failed!', 'spgallery'), $entries).'</strong></p>';
            }
        }
        
        if($errors == '')
        {
            $success_msg = '<div id="message" class="updated fade"><p><strong>';
            $success_msg .= __('The selected images were successfully deleted from the system permanently!','spgallery');
            $success_msg .= '</strong></p></div>';
            _e($success_msg);
        }else{
            $error_msg = '<div id="message" class="error">';
            $error_msg .= $errors;
            $error_msg .= '</div>';
        }
    }
    
    /*
     * This function will remove selected images from a gallery.
     * The function accepts either an array of IDs or a single ID
     */
    function remove_image_from_gallery($entries)
    {
        $meta_key = WPPG_ATTACHMENT_META_TAG;
        $errors = '';
        if (is_array($entries))
        {
            //Remove multiple images from gallery
            foreach($entries as $entry){
                $result = delete_post_meta($entry, $meta_key);
                if ($result == NULL) {
                    $errors .= '<p><strong>'.sprintf(__('Removal of image ID %s from gallery failed!', 'spgallery'), $entry).'</strong></p>';
                }
            }
        } elseif ($entries != NULL)
        {
            //Remove a single image from the gallery
            $result = delete_post_meta($entries, $meta_key);
            if ($result == NULL) {
                $errors .= '<p><strong>'.sprintf(__('Removal of image ID %s from gallery failed!', 'spgallery'), $entries).'</strong></p>';
            }
        }
        
        if($errors == '')
        {
            $success_msg = '<div id="message" class="updated fade"><p><strong>';
            $success_msg .= __('The selected images were removed from the gallery successfully!','spgallery');
            $success_msg .= '</strong></p></div>';
            _e($success_msg);
        }else{
            $error_msg = '<div id="message" class="error">';
            $error_msg .= $errors;
            $error_msg .= '</div>';
        }
    }
    
    function prepare_items() {
        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 25;
        $gallery_id = 0;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->process_bulk_action();
    	
    	global $wpdb;
        $gallery_items_table = WPPG_TBL_GALLERY;
        
        //Let's get the gallery id
        if(isset($_GET['wppg_gallery_id'])){
            $gallery_id = strip_tags($_GET['wppg_gallery_id']);
        }

	/* -- Ordering parameters -- */
	    //Parameters that are going to be used to order the result
	$orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'id';
	$order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : 'DESC';

        $data = $this->fetch_gallery_images($gallery_id, $orderby, $order);
	//$data = $wpdb->get_results("SELECT * FROM $gallery_items_table WHERE gallery_id=$gallery_id ORDER BY $orderby $order", ARRAY_A);
        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        $this->items = $data;
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
    
    function fetch_gallery_images($gallery_id, $orderby, $order)
    {
        global $wpdb;
        //$posts = $wpdb->get_col( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wppg_gallery_id' AND meta_value = $gallery_id" );
        $gallery_image_ids_array = WPPGPhotoGallery::get_gallery_image_ids($gallery_id);
        $gallery_images_array = array();
        
        foreach($gallery_image_ids_array as $image_id){
            $thumb_url = wp_get_attachment_thumb_url($image_id);
            $attachment_img = wp_get_attachment_image($image_id);
            $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true);
            
            $image_post = get_post($image_id);
            $image_desc = $image_post->post_content;
            $upload_date = $image_post->post_date;
            
            //if the alt text meta is blank, let's set it to the image name
            if ($alt_text == '' || $alt_text == NULL){
                $alt_text = $image_post->post_name;
                update_post_meta($image_id, '_wp_attachment_image_alt', $alt_text); //update the post_meta table
            }
            $image_info = array(
                'id' => $image_id,
                'thumb_url' => $thumb_url,
                'alt_text' => $alt_text,
                'description' => $image_desc,
                'date_uploaded' => $upload_date
            );
            
            $gallery_images_array[] = $image_info;
        }
        
        //Let's take care of sorting
        $sortArray = array();

        foreach($gallery_images_array as $g_img)
        {
            $sortArray[] = $g_img[$orderby];
        }
        //$sort_order = strtoupper('SORT_'.$order);
        if (strtoupper($order) == 'DESC')
            array_multisort($sortArray,SORT_DESC,$gallery_images_array);
        else
            array_multisort($sortArray,SORT_ASC,$gallery_images_array);

        return $gallery_images_array;
    }
}