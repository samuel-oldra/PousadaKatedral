<?php
class WPPG_List_Galleries extends WP_Photo_Gallery_List_Table {
    
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
        $page = WP_PHOTO_GALLERY_MENU_SLUG;
        $gallery_id = $item['id'];
        $tab = 'tab2';
        $actions = array(
            'edit' => sprintf('<a href="admin.php?page=%s&tab=%s&wppg_gallery_id=%s">Edit</a>',$page,$tab,$gallery_id),
            'delete' => sprintf('<a href="admin.php?page=wppg_gallery&action=delete_gallery&id=%s" onclick="return confirm(\'Are you sure you want to delete this item?\')">Delete Gallery</a>',$gallery_id),
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
            'id' => 'ID',
            'name' => 'Gallery Name',
            'created' => 'Created',
            'updated' => 'Last Updated'
        );
        return $columns;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'id' => array('id',false),
            'name' => array('name',false),
            'created' => array('created',false),
            'updated' => array('updated',false)
        );
        return $sortable_columns;
    }
    
    function get_bulk_actions() {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    function process_bulk_action() {
        if('delete'===$this->current_action()) 
        {
            //Process delete bulk actions
            if(!isset($_REQUEST['item']))
            {
                $error_msg = '<p>'.__('Error - Please select some records using the checkboxes', 'spgallery').'</p>';
                echo '<div id="message" class="error fade">'.$error_msg.'</div>';
            }else 
            {            
                $this->delete_gallery(($_REQUEST['item']));
            }
        }
    }
    
    /*
     * This function will delete selected galleries.
     * The function accepts either an array of IDs or a single ID
     */
    function delete_gallery($entries)
    {
        global $wpdb;
        $errors = '';
        $gallery_table = WPPG_TBL_GALLERY;
        if (is_array($entries))
        {
            foreach($entries as $entry)//Delete multiple records
            {
                //Delete all attachment posts
                $gallery_delete_result = WPPGPhotoGallery::deleteGalleryItems($entry);
                if(!$gallery_delete_result){
                    $errors .= '<p>Unable to delete gallery images</p>'; //TODO
                }

                //Delete the gallery folder
                $gallery_folder_delete_result = WPPGPhotoGallery::deleteGalleryFolder($entry);
                if(!$gallery_folder_delete_result){
                    $errors .= '<p>Unable to delete gallery folder</p>'; //TODO
                }
                
                //Delete gallery page if it exists
                $g = new WPPGPhotoGallery($entry);
                $p = get_post($g->page_id);
                if($p){
                    wp_delete_post($p->ID,true);
                }                
            }
            //Now delete the gallery row in the gallery table
            $id_list = "(" .implode(",",$entries) .")"; //Create comma separate list for DB operation
            $delete_command = "DELETE FROM ".$gallery_table." WHERE id IN ".$id_list;
            $result = $wpdb->query($delete_command);
            if($result != NULL)
            {
                $success_msg = '<div id="message" class="updated fade"><p><strong>';
                $success_msg .= __('The selected entries were deleted successfully!','spgallery');
                $success_msg .= '</strong></p></div>';
                _e($success_msg);
            }else{
                $wp_photo_gallery->debug_logger->log_debug("There was an error deleting one or more of the selected galleries with ids: ".print_r($entries, true),4);
            }
        }
        elseif ($entries != NULL) //Delete gallery single record
        {
            //Delete all attachment posts
            $gallery_delete_result = WPPGPhotoGallery::deleteGalleryItems($entries);
            if(!$gallery_delete_result){
                $errors .= '<p>Unable to delete gallery images</p>'; //TODO
            }

            //Delete the gallery folder
            $gallery_folder_delete_result = WPPGPhotoGallery::deleteGalleryFolder($entries);
            if(!$gallery_folder_delete_result){
                $errors .= '<p>Unable to delete gallery folder</p>'; //TODO
            }

            //Delete gallery page if it exists
            $g = new WPPGPhotoGallery($entries);
            $p = get_post($g->page_id);
            if($p){
                wp_delete_post($p->ID,true);
            }                
            
            $delete_command = "DELETE FROM ".$gallery_table." WHERE id = '".absint($entries)."'";
            $result = $wpdb->query($delete_command);
            if($result != NULL)
            {
                $success_msg = '<div id="message" class="updated fade"><p><strong>';
                $success_msg .= __('The selected entry was deleted successfully!','spgallery');
                $success_msg .= '</strong></p></div>';
                _e($success_msg);
            } else{
                $wp_photo_gallery->debug_logger->log_debug("There was an error deleting the gallery with id: ".$entries,4);
            }
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
        $gallery_table = WPPG_TBL_GALLERY;

	/* -- Ordering parameters -- */
	    //Parameters that are going to be used to order the result
	$orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'id';
	$order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : 'ASC';

	$data = $wpdb->get_results("SELECT * FROM $gallery_table ORDER BY $orderby $order", ARRAY_A);
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
    
}