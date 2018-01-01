<?php

class WP_Photo_Gallery_Utility
{
    function __construct(){
        //NOP
    }
    
    /*
     * Generates a random alpha-numeric number
     */
    static function generate_alpha_numeric_random_string($string_length)
    {
        //Charecters present in table prefix
        $allowed_chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $string = '';
        //Generate random string
        for ($i = 0; $i < $string_length; $i++) {
            $string .= $allowed_chars[rand(0, strlen($allowed_chars) - 1)];
        }
        return $string;
    }
    
    static function set_cookie_value($cookie_name, $cookie_value, $expiry_seconds = 86400, $path = '/', $cookie_domain = '')
    {
        $expiry_time = time() + intval($expiry_seconds);
        if(empty($cookie_domain)){
            $cookie_domain = COOKIE_DOMAIN;
        }
        setcookie($cookie_name, $cookie_value, $expiry_time, $path, $cookie_domain);
    }
    
    static function get_cookie_value($cookie_name)
    {
        if(isset($_COOKIE[$cookie_name])){
            return $_COOKIE[$cookie_name];
        }
        return "";
    }
    
    static function deleteFile($file)
    {
        $delete_result = unlink($file);
        if ($delete_result){
            //TODO - self::log("The following file was successfully deleted: ".$file);
        }else {
            //TODO - self::log("Delete operation of (".$file.") file failed!");
        }
    }

    /*
     * Checks if a directory exists and creates one if it does not
     */
    static function create_dir($dirpath='')
    {
        $res = true;
        if ($dirpath != '')
        {
            //TODO - maybe add some checks to make sure someone is not passing a path with a filename, ie, something which has ".<extenstion>" at the end
            //$path_parts = pathinfo($dirpath);
            //$dirpath = $path_parts['dirname'] . '/' . $path_parts['basename'];
            if (!file_exists($dirpath))
            {
                $res = mkdir($dirpath, 0755);
            }
        }
        return $res;
    }

    static function redirect_to_url($url,$delay='0',$exit='1')
    {
        if(empty($url)){
            echo "<br /><strong>Error! The URL value is empty. Please specify a correct URL value to redirect to!</strong>";
            exit;
        }
        if (!headers_sent()){
            header('Location: ' . $url);
        }
        else{
            echo '<meta http-equiv="refresh" content="'.$delay.';url='.$url.'" />';
        }
        if($exit == '1'){
            exit;
        }
    }
    
    static function get_configuration_value($setting_name)
    {
        
    }
    
    static function start_buffer()
    {
        ob_start();
    }
    
    static function end_buffer_and_collect()
    {
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
    
    static function get_attachment_id_from_url($image_url)
    {
        global $wpdb;
	$prefix = $wpdb->prefix;
	$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM " . $prefix . "posts" . " WHERE guid='%s';", $image_url )); 
        return $attachment[0]; 
    }
    
}
