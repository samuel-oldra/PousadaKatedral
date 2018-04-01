<?php

/*
Plugin Name: Contact Form 7 - Customfield in mail
Plugin URI: 
Description: This plugin provides a new tag type for the Contact Form 7 Plugin. It allows the use of customfields from a post to be used in the mail section. Requires Contact Form 7
Version: 1
Author: Jeffrey van der Heide
Author URI: http://creativechemistry.net
License: GPL2
*/

/*  Copyright 2010  Jeffrey van der Heide,  (email : jeffrey_vd_heide@hotmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


add_filter( 'wpcf7_special_mail_tags', 'wpcf7_special_mail_tag_for_post_custom_field', 10, 2 );

function wpcf7_special_mail_tag_for_post_custom_field( $output, $name) {
	
	if ( ! isset( $_POST['_wpcf7_unit_tag'] ) || empty( $_POST['_wpcf7_unit_tag'] ) )
		return $output;

	if ( ! preg_match( '/^wpcf7-f(\d+)-p(\d+)-o(\d+)$/', $_POST['_wpcf7_unit_tag'], $matches ) )
		return $output;

	$post_id = (int) $matches[2];

	if ( ! $post = get_post( $post_id ) )
		return $output;

	
	// For backwards compat.
	$name = preg_replace( '/^wpcf7\./', '_', $name );
	

	//Here we get the complete unknown tag i.e. [_customfield__custom_name] and then we split it at the double underscores and save the outcome as $customFieldTag array 
	$customFieldTag = preg_split('/__/',$name, -1);	
	//Here we check if the first part is the _customfield tag
	if( '_customfield' == $customFieldTag[0])  {
		// Here we set the $output to the custom field value using the post ID and the key from the second part after the double underscores
		$output = get_post_meta($post->ID, $customFieldTag[1], false);
		if(is_array($output)){
			$output = implode(",",$output);
		} else {
			$output = $output;
		}
	}
			

	return $output;  
}




?>