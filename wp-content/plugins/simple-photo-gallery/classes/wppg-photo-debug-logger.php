<?php
/* 
 * Logs debug data to a file. Here is an example usage
 * global $wp_photo_gallery;
 * $wp_photo_gallery->debug_logger->log_debug("Log messaged goes here");
 */
class WP_Photo_Gallery_Logger
{
    var $log_folder_path;
    var $default_log_file = 'wppg-log.txt';
    var $debug_enabled = false;
    var $debug_status = array('SUCCESS','STATUS','NOTICE','WARNING','FAILURE','CRITICAL');
    var $section_break_marker = "\n----------------------------------------------------------\n\n";
    var $log_reset_marker = "-------- Log File Reset --------\n";
    
    function __construct()
    {
        $this->log_folder_path = WP_PHOTO_PATH . '/logs';
        //TODO - check config and if debug is enabled then set the enabled flag to true
        $this->debug_enabled = true;
    }
    
    function get_debug_timestamp()
    {
        return '['.date('m/d/Y g:i A').'] - ';
    }
    
    function get_debug_status($level)
    {
        $size = count($this->debug_status);
        if($level >= $size){
            return 'UNKNOWN';
        }
        else{
            return $this->debug_status[$level];
        }
    }
    
    function get_section_break($section_break)
    {
        if ($section_break) {
            return $this->section_break_marker;
        }
        return "";
    }
    
    function append_to_file($content,$file_name)
    {
        if(empty($file_name))$file_name = $this->default_log_file;
        $debug_log_file = $this->log_folder_path.'/'.$file_name;
        $fp=fopen($debug_log_file,'a');
        fwrite($fp, $content);
        fclose($fp);
    }
    
    function reset_log_file($file_name='')
    {
        if(empty($file_name))$file_name = $this->default_log_file;
        $debug_log_file = $this->log_folder_path.'/'.$file_name;
        $content = $this->get_debug_timestamp().$this->log_reset_marker;
        $fp=fopen($debug_log_file,'w');
        fwrite($fp, $content);
        fclose($fp);
    }
    
    function log_debug($message,$level=0,$section_break=false,$file_name='')
    {
        if (!$this->debug_enabled) return;
        $content = $this->get_debug_timestamp();//Timestamp
        $content .= $this->get_debug_status($level);//Debug status
        $content .= ' : ';
        $content .= $message . "\n";
        $content .= $this->get_section_break($section_break);
        $this->append_to_file($content, $file_name);
    }
    
}