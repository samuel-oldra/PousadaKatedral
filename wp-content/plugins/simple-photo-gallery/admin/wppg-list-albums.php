<?php
class WPPG_List_Albums extends WP_Photo_Gallery_List_Table {
    
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
        $page = WP_PHOTO_ALBUM_MENU_SLUG;
        $album_id = $item['id'];
        $tab = 'tab2';
        $actions = array(
            'edit' => sprintf('<a href="admin.php?page=%s&tab=%s&wppg_album_id=%s">Edit</a>',$page,$tab,$album_id),
            'delete' => sprintf('<a href="admin.php?page=wppg_album&action=delete_album&id=%s" onclick="return confirm(\'Are you sure you want to delete this item?\')">Delete Album</a>',$album_id),
        );
        return sprintf('%1$s <span style="color:silver"></span>%2$s',
            /*$1%s*/ $item['id'],
            /*$2%s*/ $this->row_actions($actions)
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
            'album_name' => 'Gallery Name',
            'created' => 'Created',
            'updated' => 'Last Updated'
        );
        return $columns;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'id' => array('id',false),
            'album_name' => array('album_name',false),
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
                $this->delete_albums(($_REQUEST['item']));
            }
        }
    }
    
    /*
     * This function will delete selected albums.
     * The function accepts either an array of IDs or a single ID
     */
    function delete_albums($entries)
    {
        global $wpdb, $wp_photo_gallery;
        $album_table = WPPG_TBL_ALBUM;
        if (is_array($entries))
        {
            //Delete multiple records
            $id_list = "(" .implode(",",$entries) .")"; //Create comma separate list for DB operation
            $delete_command = "DELETE FROM ".$album_table." WHERE id IN ".$id_list;
            $result = $wpdb->query($delete_command);
            if($result != NULL)
            {
                $success_msg = '<div id="message" class="updated"><p><strong>';
                $success_msg .= __('The selected entries were deleted successfully!','WPS');
                $success_msg .= '</strong></p></div>';
                _e($success_msg);
            }else{
                $wp_photo_gallery->debug_logger->log_debug("There was an error deleting one or more albums!",4);
            }
        } 
        elseif ($entries != NULL)
        {
            //Delete single record
            $delete_command = "DELETE FROM ".$album_table." WHERE id = '".absint($entries)."'";
            $result = $wpdb->query($delete_command);
            if($result != NULL)
            {
                $success_msg = '<div id="message" class="updated"><p><strong>';
                $success_msg .= __('The selected entry was deleted successfully!','WPS');
                $success_msg .= '</strong></p></div>';
                _e($success_msg);
            } else{
                $wp_photo_gallery->debug_logger->log_debug("There was an error deleting album with ID: ".absint($entries),4);
            }
        }
    }
    
    
    function prepare_items() {
        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 25;
        $album_id = 0;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->process_bulk_action();
    	
    	global $wpdb;
        $album_table = WPPG_TBL_ALBUM;

	/* -- Ordering parameters -- */
	    //Parameters that are going to be used to order the result
	$orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'id';
	$order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : 'ASC';

	$data = $wpdb->get_results("SELECT * FROM $album_table ORDER BY $orderby $order", ARRAY_A);
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