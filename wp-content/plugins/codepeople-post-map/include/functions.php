<?php 
/**
 * CodePeople Post Map 
 * Version: 1.0.1
 * Author: CodePeople
 * Plugin URI: http://wordpress.dwbooster.com
*/

class CPM {
	//---------- VARIABLES ----------
	
	var $lang_array; // List of supported languages
	var $points = array(); // List of points to set on map
	var $points_str = ''; // List of points as javascript code
	var $flush_map = false; // Determine if the map's code was generated to limit only one map for page
	var $map_id; // ID of map
    var $limit=0; // The number of pins allowed in map zero = unlimited
	var $extended = array();
	
	//---------- CONSTRUCTOR ----------
	
	function __construct(){
		$this->map_id = "cpm_".wp_generate_password(6, false);
		$this->lang_array = array(
							"ar"=>__("ARABIC","codepeople-post-map"),
							"eu"=>__("BASQUE","codepeople-post-map"),
							"bg"=>__("BULGARIAN","codepeople-post-map"),
							"bn"=>__("BENGALI","codepeople-post-map"),
							"ca"=>__("CATALAN","codepeople-post-map"),
							"cs"=>__("CZECH","codepeople-post-map"),
							"da"=>__("DANISH","codepeople-post-map"),
							"de"=>__("GERMAN","codepeople-post-map"),
							"el"=>__("GREEK","codepeople-post-map"),
							"en"=>__("ENGLISH","codepeople-post-map"),
							"en-AU"=>__("ENGLISH (AUSTRALIAN)","codepeople-post-map"),
							"en-GB"=>__("ENGLISH (GREAT BRITAIN)","codepeople-post-map"),
							"es"=>__("SPANISH","codepeople-post-map"),
							"eu"=>__("BASQUE","codepeople-post-map"),
							"fa"=>__("FARSI","codepeople-post-map"),
							"fi"=>__("FINNISH","codepeople-post-map"),
							"fil"=>__("FILIPINO","codepeople-post-map"),
							"fr"=>__("FRENCH","codepeople-post-map"),
							"gl"=>__("GALICIAN","codepeople-post-map"),
							"gu"=>__("GUJARATI","codepeople-post-map"),
							"hi"=>__("HINDI","codepeople-post-map"),
							"hr"=>__("CROATIAN","codepeople-post-map"),
							"hu"=>__("HUNGARIAN","codepeople-post-map"),
							"id"=>__("INDONESIAN","codepeople-post-map"),
							"it"=>__("ITALIAN","codepeople-post-map"),
							"iw"=>__("HEBREW","codepeople-post-map"),
							"ja"=>__("JAPANESE","codepeople-post-map"),
							"kn"=>__("KANNADA","codepeople-post-map"),
							"ko"=>__("KOREAN","codepeople-post-map"),
							"lt"=>__("LITHUANIAN","codepeople-post-map"),
							"lv"=>__("LATVIAN","codepeople-post-map"),
							"ml"=>__("MALAYALAM","codepeople-post-map"),
							"mr"=>__("MARATHI","codepeople-post-map"),
							"nl"=>__("DUTCH","codepeople-post-map"),
							"no"=>__("NORWEGIAN","codepeople-post-map"),
							"or"=>__("ORIYA","codepeople-post-map"),
							"pl"=>__("POLISH","codepeople-post-map"),
							"pt"=>__("PORTUGUESE","codepeople-post-map"),
							"pt-BR"=>__("PORTUGUESE (BRAZIL)","codepeople-post-map"),
							"pt-PT"=>__("PORTUGUESE (PORTUGAL)","codepeople-post-map"),
							"ro"=>__("ROMANIAN","codepeople-post-map"),
							"ru"=>__("RUSSIAN","codepeople-post-map"),
							"sk"=>__("SLOVAK","codepeople-post-map"),
							"sl"=>__("SLOVENIAN","codepeople-post-map"),
							"sr"=>__("SERBIAN","codepeople-post-map"),
							"sv"=>__("SWEDISH","codepeople-post-map"),
							"tl"=>__("TAGALOG","codepeople-post-map"),
							"ta"=>__("TAMIL","codepeople-post-map"),
							"te"=>__("TELUGU","codepeople-post-map"),
							"th"=>__("THAI","codepeople-post-map"),
							"tr"=>__("TURKISH","codepeople-post-map"),
							"uk"=>__("UKRAINIAN","codepeople-post-map"),
							"vi"=>__("VIETNAMESE","codepeople-post-map"),
							"zh-CN"=>__("CHINESE (SIMPLIFIED)","codepeople-post-map"),
							"zh-TW"=>__("CHINESE (TRADITIONAL)","codepeople-post-map")
                                                
        ); 
	} // End __construct
	
	//---------- CREATE MAP ----------
	
	/**
	 * Save a map object in database
	 * called by the action save_post
	 */
	function save_map($post_id){
		// authentication checks

		// make sure data came from our meta box
		if (!isset($_POST['cpm_map_noncename']) || !wp_verify_nonce($_POST['cpm_map_noncename'],__FILE__)) return $post_id;

		// check user permissions
		if (isset($_POST['post_type'] ) && $_POST['post_type'] == 'page'){
			if (!current_user_can('edit_page', $post_id)) return $post_id;
		}
		else{
			if (!current_user_can('edit_post', $post_id)) return $post_id;
		}

		// authentication passed, save data
		
		// Check the existence of a point associated to the post
		$cpm_point = get_post_meta($post_id, 'cpm_point', TRUE);
		
		$new_cpm_point = $_POST['cpm_point'];
		$new_cpm_map = $_POST['cpm_map'];
		$new_cpm_point['icon'] = $_POST['default_icon'];
		
		// The address is required, if address is empty the couple: latitude, longitude must be defined
		if(!(empty($new_cpm_point['address']) || empty($new_cpm_point['latitude']) || empty($new_cpm_point['longitude']))){
			$new_cpm_point['address'] = esc_attr($new_cpm_point['address']);
			$new_cpm_point['name'] = esc_attr($new_cpm_point['name']);
			$new_cpm_point['description'] = esc_attr($new_cpm_point['description']);
			
			
			$new_cpm_map['zoompancontrol'] 	= ($new_cpm_map['zoompancontrol'] == true);
			$new_cpm_map['mousewheel'] 		= ($new_cpm_map['mousewheel'] == true);
			$new_cpm_map['typecontrol'] 	= ($new_cpm_map['typecontrol'] == true);
            $new_cpm_map['single'] 	        = (isset($new_cpm_map['single'])) ? true : false;
			$new_cpm_map['dynamic_zoom'] 	= (isset($new_cpm_map['dynamic_zoom']) && $new_cpm_map['dynamic_zoom']) ? true : false;
            $new_cpm_map['show_default'] 	= (isset($new_cpm_map['show_default']) && $new_cpm_map['show_default']) ? true : false;
            $new_cpm_map['show_window'] 	= (isset($new_cpm_map['show_window']) && $new_cpm_map['show_window']) ? true : false;
			
            if($new_cpm_map['single']){
                if($cpm_point){
                    // Update metadata
                    update_post_meta($post_id,'cpm_map',$new_cpm_map);
                }else{
                    // Create metadata
                    add_post_meta($post_id,'cpm_map',$new_cpm_map,TRUE);
                }
            }else{
                delete_post_meta($post_id,'cpm_map');
            }
            
			
			if($cpm_point){
				// Update metadata
				update_post_meta($post_id,'cpm_point',$new_cpm_point);
			}else{
				// Create metadata
				add_post_meta($post_id,'cpm_point',$new_cpm_point,TRUE);
			}
		}else{
			// Remove metadata
			if($cpm_point) {
				delete_post_meta($post_id,'cpm_point');
				delete_post_meta($post_id,'cpm_map');
			}	
		}
		
	} // End save_map
	
	//---------- OPTIONS FOR CODEPEOPLE POST MAP ----------
	/**
	 * Get default configuration options
	 */
	function _default_configuration(){
		return array(
							'zoom' => '10',
							'dynamic_zoom' => false,
							'width' => '450',
							'height' => '450',
							'margin' => '10',
							'align' => 'center',									
							'language' => 'en',
							'icons' => array(),
							'default_icon' => CPM_PLUGIN_URL.'/images/icons/marker.png',
							'type' => 'ROADMAP',
							'points' => 3,
							'display' => 'map',
							'mousewheel' => true,
							'zoompancontrol' => true,
							'typecontrol' => true,
							'highlight'	=> true,
							'highlight_class' => 'cpm_highlight',
							'show_window' => true,
                            'show_default' => true,
							'windowhtml' => "<div class='cpm-infowindow'>
                                                <div class='cpm-content'>
                                                    <a title='%link%' href='%link%'>%thumbnail%</a>
                                                    <a class='title' href='%link%'>%title%</a>
                                                    <div class='address'>%address%</div>
                                                    <div class='description'>%description%</div>
                                                    <a href='%link%' class='more'>more &raquo;</a>
                                                </div>
                                                <div style='clear:both;'></div>
                                            </div>"
							);
	} // End _default_configuration
	
	/**
	 * Set default system and maps configuration
	 */
	function set_default_configuration($default = false){
		$cpm_default = $this->_default_configuration();
							
    	$options = get_option('cpm_config');
		if ($default || $options === false) {
			update_option('cpm_config', $cpm_default);
			$options = $cpm_default;
		}
		return $options;
	} // End set_default_configuration
	
	/**
	 * Get a part of option variable or the complete array
	 */
	function get_configuration_option($option = null){
	
		$options = get_option('cpm_config');
		$default = $this->_default_configuration();
		
		if(!isset($options)){
			$options = $default;
		}
		
		if(isset($option)){
            return (isset($options[$option])) ? $options[$option] : ((isset($default[$option])) ? $default[$option] : null);
		}else{
			return $options;
		}	
		
	} // End get_configuration_option
	
	//---------- METADATA FORM METHODS ----------
	
	/**
	 * Private method to deploy the list of languages
	 */
	function _deploy_languages($options){
		print '<select name="cpm_map[language]" id="cpm_map_language">';
		foreach($this->lang_array as $key=>$value)
			print '<option value="'.$key.'" '.((isset($options['language']) && $options['language'] == $key) ? 'selected' : '').'>'.$value.'</option>';
		print '</select>';	
	} // End _deploy_languages
	
	/**
	 * Private method to get the list of icons
	 */
	function _deploy_icons($options = null){ 
		$icon_path = CPM_PLUGIN_URL.'/images/icons/';
		$icon_dir = CPM_PLUGIN_DIR.'/images/icons/';	

		$icons_array = array();

		$default_icon = (isset($options) && isset($options['icon'])) ? $options['icon'] : $this->get_configuration_option('default_icon');
		
		if ($handle = opendir($icon_dir)) {
			
			while (false !== ($file = readdir($handle))) {
		
				$file_type = wp_check_filetype($file);
				$file_ext = $file_type['ext'];
				if ($file != "." && $file != ".." && ($file_ext == 'gif' || $file_ext == 'jpg' || $file_ext == 'png') ) {
					array_push($icons_array,$icon_path.$file);
				}
			}
		}
		?>
			<div class="cpm_label">
				<?php _e("Select the marker by clicking on the images", "codepeople-post-map"); ?> 
			</div>    	   
			<div id="cpm_icon_cont">
				<input type="hidden" name="default_icon" value="<?php echo $default_icon ?>" id="default_icon" />			
				<?php foreach ($icons_array as $icon){ ?>
				  <div class="cpm_icon <?php if ($default_icon == $icon) echo "cpm_selected" ?>">
				  <img src="<?php echo $icon ?>" /> 
				  </div>
				<?php } ?>
			</div> 
			<div id="icon_credit">
				<span><?php _e("Powered by","codepeople-post-map"); ?></span>
				<a href="http://mapicons.nicolasmollet.com" target="_blank">
					<img src="<?php echo CPM_PLUGIN_URL ?>/images/miclogo-88x31.gif" />
				</a>
			 </div> 	
		<?php
	} // End _deploy_icons
	
	/**
	 * Private method to insert the map form
	 */
	function _deploy_map_form($options = NULL, $single = false){
		?>
		<h2><?php _e('Maps Configuration', 'codepeople-post-map'); ?></h2>
		<p  style="border:1px solid #E6DB55;margin-bottom:10px;padding:5px;background-color: #FFFFE0;"><?php _e('For any issues with the map, go to our <a href="http://wordpress.dwbooster.com/contact-us" target="_blank">contact page</a> and leave us a message.'); ?></p>
		<table class="form-table">
			<?php
                if($single){
            ?>    
                    <tr valign="top">
                        <th scope="row"><label for="cpm_map_single"><?php _e('Use particular settings for this map:', 'codepeople-post-map')?></label></th>
                        <td>
                            <input type="checkbox" name="cpm_map[single]" id="cpm_map_single" <?php echo ((isset($options['single'])) ? 'CHECKED' : '');?> />
                        </td>
                    </tr>
            <?php        
                }
            ?>
			<tr valign="top">
				<th scope="row"><label for="cpm_map_zoom"><?php _e('Map zoom:', 'codepeople-post-map')?></label></th>
				<td>
					<input type="text" size="4" name="cpm_map[zoom]" id="cpm_map_zoom" value="<?php echo ((isset($options['zoom'])) ? $options['zoom'] : '');?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="cpm_map_dynamic_zoom"><?php _e('Dynamic zoom:', 'codepeople-post-map')?></label></th>
				<td>
					<input type="checkbox" name="cpm_map[dynamic_zoom]" id="cpm_map_dynamic_zoom" <?php echo ( ( isset($options['dynamic_zoom'] ) && $options['dynamic_zoom'] ) ? 'CHECKED' : '' ); ?> /> <?php _e( 'Allows to adjust the zoom dynamically to display all points on map', 'codepeople-post-map' ); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="cpm_map_width"><?php _e('Map width:', 'codepeople-post-map')?></label></th>
				<td>
					<input type="text" size="4" name="cpm_map[width]" id="cpm_map_width" value="<?php echo ((isset($options['width'])) ? $options['width'] : '');?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="cpm_map_height"><?php _e('Map height:', 'codepeople-post-map')?></label></th>
				<td>
					<input type="text" size="4" name="cpm_map[height]" id="cpm_map_height" value="<?php echo ((isset($options['height'])) ? $options['height'] : '');?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="cpm_map_margin"><?php _e('Map margin:', 'codepeople-post-map')?></label></th>
				<td>
					<input type="text" size="4" name="cpm_map[margin]" id="cpm_map_margin" value="<?php echo ((isset($options['height'])) ? $options['margin'] : '');?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="cpm_map_align"><?php _e('Map align:', 'codepeople-post-map')?></label></th>
				<td>
					<select id="cpm_map_align" name="cpm_map[align]">
						<option value="left" <?php echo((isset($options['align']) && $options['align'] == 'left') ? 'selected': ''); ?>><?php _e('left'); ?></option>
						<option value="center" <?php echo((isset($options['align']) && $options['align'] == 'center') ? 'selected': ''); ?>><?php _e('center'); ?></option>
						<option value="right" <?php echo((isset($options['align']) && $options['align'] == 'right') ? 'selected': ''); ?>><?php _e('right'); ?></option>
					</select>	
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="cpm_map_type"><?php _e('Map type:', 'codepeople-post-map'); ?></label></th>
				<td>
					<select name="cpm_map[type]" id="cpm_map_type" >
						<option value="ROADMAP" <?php echo ((isset($options['type']) && $options['type']=='ROADMAP') ? 'selected' : '');?>><?php _e('ROADMAP - Displays a normal street map', 'codepeople-post-map');?></option>
						<option value="SATELLITE" <?php echo ((isset($options['type']) && $options['type']=='SATELLITE') ? 'selected' : '');?>><?php _e('SATELLITE - Displays satellite images', 'codepeople-post-map');?></option>
						<option value="TERRAIN" <?php echo ((isset($options['type']) && $options['type']=='TERRAIN') ? 'selected' : '');?>><?php _e('TERRAIN - Displays maps with physical features such as terrain and vegetation', 'codepeople-post-map');?></option>
						<option value="HYBRID" <?php echo ((isset($options['type']) && $options['type']=='HYBRID') ? 'selected' : '');?>><?php _e('HYBRID - Displays a transparent layer of major streets on satellite images', 'codepeople-post-map');?></option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="cpm_map_language"><?php _e('Map language:', 'codepeople-post-map');?></th>
				<td><?php $this->_deploy_languages($options); ?></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="cpm_map_display"><?php _e('Display map in post/page:', 'codepeople-post-map'); ?></label></th>
				<td>
					<select name="cpm_map[display]" id="cpm_map_display" >
						<option value="icon" <?php echo ((isset($options['display']) && $options['display']=='icon') ? 'selected' : '');?>><?php _e('as icon', 'codepeople-post-map'); ?></option>
						<option value="map" <?php echo ((isset($options['display']) && $options['display']=='map') ? 'selected' : '');?>><?php _e('as full map', 'codepeople-post-map'); ?></option>
					</select>
				</td>
			</tr>
            
            <tr valign="top">
				<th scope="row"><label for="cpm_show_window"><?php _e('Show info bubbles:', 'codepeople-post-map');?></th>
				<td>
                    <input type="checkbox" id="cpm_show_window" name="cpm_map[show_window]" value="true" <?php echo ((isset($options['show_window']) && $options['show_window']) ? 'checked' : '');?>><span> Display the bubbles associated to the points</span>
                </td>
			</tr>
            
            <tr valign="top">
				<th scope="row"><label for="cpm_show_default"><?php _e('Display a bubble by default:', 'codepeople-post-map');?></th>
				<td>
                    <input type="checkbox" id="cpm_show_default" name="cpm_map[show_default]" value="true" <?php echo ((isset($options['show_default']) && $options['show_default']) ? 'checked' : '');?>><span> Display a bubble opened by default</span>
                </td>
			</tr>

            <tr valign="top">
				<th scope="row"><label for="cpm_map_route" style="color:#CCCCCC;"><?php _e('Display route:', 'codepeople-post-map');?></th>
				<td>
                    <input type="checkbox" DISABLED><span> Draws the route between the points in the same post</span><br />
                    <span style="color:#FF0000;">The route between points is available only for the commercial version of plugin. <a href="http://wordpress.dwbooster.com/contact-us/codepeople-post-map">Click Here</a></span>
                </td>
			</tr>
            
            <tr valign="top">
				<th scope="row"><label for="cpm_travel_mode" style="color:#CCCCCC;"><?php _e('Travel mode:', 'codepeople-post-map');?></th>
				<td>
                    <select disabled>
                        <option value="DRIVING">Driving</option>
                    </select>
                </td>
            </tr>
			
            
			<tr valign="top">
				<th scope="row"><label for="wpGoogleMaps_description"><?php _e('Options:')?></label></th>
				<td>
					<input type="checkbox" name="cpm_map[mousewheel]" id="cpm_map_mousewheel" value="true" <?php echo ((isset($options['mousewheel']) && $options['mousewheel']) ? 'checked' : '');?> />
					<label for="cpm_map_mousewheel"><?php _e('Enable mouse wheel zoom', 'codepeople-post-map'); ?></label><br />
					<input type="checkbox" name="cpm_map[zoompancontrol]" id="cpm_map_zoompancontrol" value="true" <?php echo ((isset($options['zoompancontrol']) && $options['zoompancontrol']) ? 'checked' : '');?> />
					<label for="cpm_map_zoompancontrol"><?php _e('Enable zoom/pan controls', 'codepeople-post-map'); ?></label><br />
					<input type="checkbox" name="cpm_map[typecontrol]" id="cpm_map_typecontrol" value="true" <?php echo ((isset($options['typecontrol']) && $options['typecontrol']) ? 'checked' : '');?> />
					<label for="cpm_map_typecontrol"> <?php _e('Enable map type controls (Map, Satellite, or Hybrid)', 'codepeople-post-map'); ?> </label><br />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="cpm_map_points"><?php _e('Enter the number of posts to display on the post/page map:'); ?></th>
				<td><input type="text" name="cpm_map[points]" id="cpm_map_points" value="<?php echo ((isset($options['points'])) ? $options['points'] : '');?>" /></td>
			</tr>
		</table>
	<?php
	} // End _deploy_map_form
	
	/**
	 * Private method to print Maps form
	 */
	function _print_form($options){
		global $post;
		$default_configuration = $this->_default_configuration();
	?>
		<script>
			var cpm_default_marker = "<?php echo $default_configuration['default_icon']; ?>";
			var cpm_point = {};
			
			<?php 
			if(!empty($options['address']) || (!empty($options['latitude']) && !empty($options['longitude']))){ 
				if(!empty($options['address'])) echo 'cpm_point["address"]="'.$options['address'].'";';
				if(!empty($options['latitude']) && !empty($options['longitude'])){
					echo 'cpm_point["latitude"]="'.$options['latitude'].'";';
					echo 'cpm_point["longitude"]="'.$options['longitude'].'";';
				}
				
			} else {
				echo 'cpm_point["address"]="Statue of Liberty, Statue of Liberty National Monument, Statue Of Liberty, New York, NY 10004, USA";'; 
				echo 'cpm_point["latitude"]="40.689848";';
				echo 'cpm_point["longitude"]="-74.044869";';
			} 
			?>
			
		</script>
		<p  style="font-weight:bold;"><?php _e('For more information go to the <a href="http://wordpress.dwbooster.com/content-tools/codepeople-post-map" target="_blank">CodePeople Post Map</a> plugin page'); ?></p>
		<p  style="border:1px solid #E6DB55;margin-bottom:10px;padding:5px;background-color: #FFFFE0;"><?php _e('For any issues with the map, go to our <a href="http://wordpress.dwbooster.com/contact-us" target="_blank">contact page</a> and leave us a message.'); ?></p>
		<div style="border:1px solid #CCC;margin-bottom:10px;min-height:60px;">
			<h3><?php _e('Map points'); ?></h3>
			<div id="points_container" style="padding:10px;">
			<?php _e('Multiple points in the same Post/Page are available only in the <a href="http://wordpress.dwbooster.com/content-tools/codepeople-post-map" target="_blank">advanced version</a>.'); ?>
			</div>
		</div>
		<div class="point_form" style="border:1px solid #CCC;">
			<h3><?php _e('Map point description'); ?></h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="cpm_name"><?php _e('Location name:', 'codepeople-post-map')?></label></th>
					<td>
						<input type="text" size="40" style="width:95%;" name="cpm_point[name]" id="cpm_point_name" value="<?php echo ((isset($options['name'])) ? $options['name'] : '');?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="cpm_point_description"><?php _e('Location description:', 'codepeople-post-map')?></label></th>
					<td>
						<input type="text" size="40" style="width:95%;" name="cpm_point[description]" id="cpm_point_description" value="<?php echo ((isset($options['description'])) ? $options['description'] : '');?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
                        <?php _e("Select an images to attach to the point: ","codepeople-post-map"); ?>
					</th>
                    <td>
                        <input type="text" name="cpm_point[thumbnail]" value="<?php if(isset($options["thumbnail"])){ echo $options["thumbnail"];} ?>" id="cpm_point_thumbnail" />
                        <input class="button" type="button" value="Upload Images" onclick="cpm_thumbnail_selection(this);" />
                    </td>	
				</tr>
				<tr valign="top">
					<td colspan="2">
						<table>
							<tr valign="top">
								<td>
                                    <span style="font-weight:bold;">Address, latitude and longitude are required fields.</span>
									<table>
										<tr valign="top">
											<th scope="row"><label for="cpm_point_address"><?php _e('Address:', 'codepeople-post-map')?></label></th>
											<td  width="100%">
												<input type="text" style="width:100%;" name="cpm_point[address]" id="cpm_point_address" value="<?php echo ((isset($options['address'])) ? $options['address'] : '');?>" />
											</td>
										</tr>	
										<tr valign="top">
											<th scope="row"><label for="cpm_point_latitude"><?php _e('Latitude:', 'codepeople-post-map')?></label></th>
											<td>
												<input type="text" style="width:100%;" name="cpm_point[latitude]" id="cpm_point_latitude" value="<?php echo ((isset($options['latitude'])) ? $options['latitude'] : '');?>" />
											</td>
										</tr>
										<tr valign="top">
											<th scope="row"><label for="cpm_point_longitude"><?php _e('Longitude:', 'codepeople-post-map')?></label></th>
											<td>
												<input type="text" style="width:100%;" name="cpm_point[longitude]" id="cpm_point_longitude" value="<?php echo ((isset($options['longitude'])) ? $options['longitude'] : '');?>" />
											</td>
										</tr>
										<tr valign="top">
											<th scope="row" style="text-align:right;"><p class="submit"><input type="button" name="cpm_point_verify" id="cpm_point_verify" value="<?php _e('Verify', 'codepeople-post-map'); ?>" onclick="cpm_checking_point(this);" /></p></th>
											<td>
												<label for="cpm_point_verify"><?php _e('Verify this latitude and longitude using Geocoding. This could overwrite the point address.', 'codepeople-post-map')?><span style="color:#FF0000">(<?php _e('Required: Press the button "verify" after complete the address.', 'codepeople-post-map'); ?>)</span></label>
											</td>
										</tr>
									</table>
								</td>
								<td width="50%">
									<div id="cpm_map_container" class="cpm_map_container" style="width:400px; height:250px; border:1px dotted #CCC;">
									</div>
								</td>
							</tr>
						</table>	
					</td>
				</tr>
				<tr valign="top">
					<td colspan="2">
						<?php $this->_deploy_icons($options); ?>
					</td>	
				</tr>
			</table>
		</div>
		<p style="border:1px solid #CCC; padding:10px;">
			<?php
			_e( 'To insert this map in a post/page, press the <strong>"insert the map tag"</strong> button and save the post/page modifications.' );
			?>
		</p>	
		<table class="form-table">
			<tr valign="top">
                <th scope="row">
					<label for="cpm_point_bubble"><?php _e('If you want to display the map in page / post:', 'codepeople-post-map')?></label>
				</th>
                <td> 
					<p style="float:left;"><input type="button" class="button-primary" name="cpm_map_shortcode" id="cpm_map_shortcode" value="<?php _e('insert the map tag', 'codepeople-post-map'); ?>" /></p>
				</td>
            </tr>
		</table>	
		<div id="map_data">
			<?php $this->_deploy_map_form($options, true); ?>
		</div>	
        <p class="submit">
            <input type="button" onclick="display_map_form();" value="<?php _e("Show / Hide Map's Options &raquo;", 'codepeople-post-map'); ?>" />
        </p>
        <p>&nbsp;</p>
		<?php
		// create a custom nonce for submit verification later
		echo '<input type="hidden" name="cpm_map_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
	} // End _print_form
	
	/**
	 * Form for maps insertion and update
	 */
	function insert_form(){
		global $post, $wpdb;
	
		$cpm_point = get_post_meta($post->ID, 'cpm_point', TRUE);
		$cpm_map = get_post_meta($post->ID, 'cpm_map', TRUE);
		$general_options = $this->get_configuration_option();
		$options = array_merge((array)$general_options, (array)$cpm_point, (array)$cpm_map);
		$options['post_id'] = $post->ID;
		$this->_print_form($options);
	} // End insert_form
	
	//---------- LOADING RESOURCES ----------
	
	/*
	 * Load the required scripts and styles for ADMIN section of website
	 */
	function load_admin_resources(){
		wp_enqueue_style(
			'admin_cpm_style',
			CPM_PLUGIN_URL.'/styles/cpm-admin-styles.css'
		);
		
		wp_enqueue_script(
			'admin_cpm_script',
			CPM_PLUGIN_URL.'/js/cpm.admin.js',
			array('jquery'),
            null,
            true
		);
	} // End load_admin_resources
	
	/**
	 * Load script and style files required for display google maps on public website
	 */
	function load_resources() {
		wp_enqueue_style( 'cpm_style', CPM_PLUGIN_URL.'/styles/cpm-styles.css');
		wp_enqueue_script( 'cpm_script', CPM_PLUGIN_URL.'/js/cpm.js', array('jquery'));
	} // End load_resources
	
	function load_footer_resources(){
		echo '<style>.cpm-map img{ max-width: none;box-shadow:none;}</style>';
	} // End load_footer_resources
	
	/**
	 * Print the settings page for entering the general setting's data of maps
	 */
	function settings_page(){
		// Check if post exists and save the configuraton options
		if (isset($_POST['cpm_map_noncename']) && wp_verify_nonce($_POST['cpm_map_noncename'],__FILE__)){
			$options = $_POST['cpm_map'];
            $options['windowhtml'] = $this->get_configuration_option('windowhtml');
			update_option('cpm_config', $options);
			echo '<div class="updated"><p><strong>'.__("Settings Updated").'</strong></div>';
		}else{
			$options = $this->get_configuration_option();
		}
		
	?>
		<div class="wrap">
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<?php	
		$this->_deploy_map_form($options);
	?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"  style="color:#CCCCCC;"><label for="cpm_map_multiple"><?php _e('Display a Map by post in non singular pages (like homepage, archives, search results, etc...):', 'codepeople-post-map')?></label></th>
				<td>
					<input type="checkbox" DISABLED />
                    <?php _e('Only one map is allowed by each webpage, but checking this option in pages with multiple posts like homepage, archives,etc, it is possible to display a map for each post', 'codepeople-post-map');?>
                    <br />
                     <span style="color:#FF0000;">The free version of CodePeople Post Map allows only one map by webpage. <a href="http://wordpress.dwbooster.com/contact-us/codepeople-post-map">Click Here</a></span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="cpm_map_search"  style="color:#CCCCCC;"><?php _e('Use points information in search results:', 'codepeople-post-map')?></label></th>
				<td>
					<input type="checkbox" name="cpm_map[search]" id="cpm_map_search" value="true" disabled /> <span style="color:#FF0000;">The search in the maps data is available only in commercial version of plugin. <a href="http://wordpress.dwbooster.com/contact-us/codepeople-post-map">Click Here</a></span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="cpm_map_highlight" style="color:#CCCCCC;"><?php _e('Highlight post when mouse move over related point on map:', 'codepeople-post-map')?></label></th>
				<td>
					<input type="checkbox" name="cpm_map[highlight]" id="cpm_map_highlight" value="true" disabled /> <span style="color:#FF0000;">The post highlight is available only in commercial version of plugin. <a href="http://wordpress.dwbooster.com/contact-us/codepeople-post-map">Click Here</a></span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="cpm_map_highlight_class" style="color:#CCCCCC;"><?php _e('Highlight class:', 'codepeople-post-map')?></label></th>
				<td>
					<input type="input" name="cpm_map[highlight_class]" id="cpm_map_highlight_class" disabled /><span style="color:#FF0000;">The highlight class is available only in commercial version of plugin. <a href="http://wordpress.dwbooster.com/contact-us/codepeople-post-map">Click Here</a></span>
				</td>
			</tr>
            <tr valign="top">
				<th scope="row"><label for="cpm_map_post_type" style="color:#CCCCCC;"><?php _e('Allow to associate a map to the post types:', 'codepeople-post-map')?></label></th>
				<td valign="top">
                <?php
                    $post_types = get_post_types(array('public' => true), 'names');
                ?>
                    <select multiple size="3" DISABLED >
                <?php   
                        foreach($post_types as $post_type){
                            print '<option value="'.$post_type.'" >'.$post_type.'</option>';
                        }
                ?>    
                    </select>
                <?php
                    _e('Posts and Pages are selected by default', 'codepeople-post-map');
                ?>
                <br />    
                <span style="color:#FF0000;">Associate the maps to custom post types is available only in commercial version of plugin. <a href="http://wordpress.dwbooster.com/contact-us/codepeople-post-map">Click Here</a></span>
				</td>
			</tr>
		</table>
        <table cellpadding="10" cellspacing="10" width="500px">
            <tr valign="top">
                <td align="center" width="30%">
                    <a href="http://wordpress.dwbooster.com/content-tools/codepeople-post-map" target="_blank">
                    <img src="<?php echo(CPM_PLUGIN_URL.'/images/routes.jpg'); ?>" width="100px" height="100px" class="cpm_thumbnail_admin" style="border:1px solid #AAA;" />
                    </a>
                    <br />Draws Routes
                </td>
                <td align="center">
                    <a href="http://wordpress.dwbooster.com/content-tools/codepeople-post-map" target="_blank">
                    <img src="<?php echo(CPM_PLUGIN_URL.'/images/custom_post_type.jpg'); ?>" width="100px" height="100px" class="cpm_thumbnail_admin" style="border:1px solid #AAA;" />
                    </a>
                    <br />Associate maps to custom post types
                </td>
                <td align="center" width="30%">
                    <a href="http://wordpress.dwbooster.com/content-tools/codepeople-post-map" target="_blank">
                    <img src="<?php echo(CPM_PLUGIN_URL.'/images/multiple.jpg'); ?>" width="100px" height="100px" class="cpm_thumbnail_admin" style="border:1px solid #AAA;" />
                    </a>
                    <br/>Display a map for each post in pages with multiple posts
                </td>
            </tr>
        </table>
        <script>
            jQuery(function(){
                jQuery('.cpm_thumbnail_admin').mouseover(function(){jQuery(this).width(300).height(300);}).mouseout(function(){jQuery(this).width(100).height(100);});
            });
        </script>
		<p  style="font-weight:bold;"><?php _e('For more information go to the <a href="http://wordpress.dwbooster.com/content-tools/codepeople-post-map" target="_blank">CodePeople Post Map</a> plugin page'); ?></p>
		<div class="submit"><input type="submit" class="button-primary" value="<?php _e('Update Settings', 'codepeople-post-map');?>" /></div>
		<?php 
			// create a custom nonce for submit verification later
			echo '<input type="hidden" name="cpm_map_noncename" value="' . wp_create_nonce(__FILE__) . '" />'; 
		?>
		</form>
		</div>
	<?php
	} // End settings_page
	
	//---------- SHORTCODE METHODS ----------
	
	/*
	 * Static method for ordering an array of posts
	 */
	static function _ordering_array($postA, $postB){
		if ($postA->post_date == $postB->post_date) {
            return 0;
        }
        return ($postA->post_date > $postB->post_date) ? -1 : +1;
	} // End _ordering_array
	
	/*
	 * Static method for remove duplicate posts
	 */
	static function _unique_element( $obj ) {
		static $posts_list = array();
 
		if ( in_array( $obj->ID, $posts_list ) )
			return false;
 
		$posts_list[] = $obj->ID;
		return true;    
	} // End _unique_element
	
	/*
	 * Populate the attribute points
	 */
	function populate_points($post, $force = false){
		$point = get_post_meta($post->ID, 'cpm_point', TRUE);
		if(!empty($point)){
			$point['post_id'] = $post->ID;
			if(!in_array($point, $this->points)){
                if($force){
                    array_unshift($this->points, $point);
                }else{
                    $this->points[] = $point;
                }
			}	
		}	
	} // End populate_points
	
	/*
	 * Generates the javascript code of map points, only called from webpage of multiples posts
	 */
	function print_points(){
        global $id;
        $limit = abs($this->limit);
        $str = '';
        $current = '';
        $count = 0;
        foreach($this->points as $k => $point){
            if(!empty($limit)){
                if($current != $point['post_id']){
                    $current = $point['post_id'];
                    $count++;
                    if( $count > $limit) break;
                }
            }    
            $default = ($id && $id == $current) ? "true" : "false";
            $str .=  $this->_set_map_point($this->points[$k], $k, $default);
        }
        if(strlen($str)) print "<script>if(typeof cpm_global != 'undefined'){".$str."}</script>";
	} // End print_points
	
	/**
	 * Replace each [codepeople-post-map] shortcode by the map
	 */
	function replace_shortcode($atts){
		global $post, $id;
        
        if(is_array($atts)) $this->extended = $atts;
        
        $this->load_resources();
		
        // Limit the publication of map to only one
		if($this->flush_map)
			return '';
		
		$this->flush_map = true;
		
        if($id){
            $cpm_map = get_post_meta($post->ID, 'cpm_map', TRUE);
        }
        
        if(empty($cpm_map)){
            $cpm_map = $this->get_configuration_option();
        }
		
        if(!empty($cpm_map['points'])){
            $this->limit = $cpm_map['points'];
        }
        
		if(is_singular()){ // For maps in a post or page
			$number = (!empty($this->limit)) ? 'numberposts='.$this->limit.'&' : '';
			
			// Set the actual post only to avoid duplicates
			$posts = array($post);
			
			// Get POSTs in the same category
			$categories = get_the_category();
			foreach($categories as $category){
				$posts = array_merge($posts, get_posts($number.'category='. $category->term_id));
			}
			
			// Remove duplicate posts
			$posts = array_filter($posts, array('CPM', '_unique_element'));
			
			// The first post is the actual post, I remove it before sorting the rest of posts in list
			$actual = array_shift($posts);
			usort($posts, array('CPM', '_ordering_array'));
			array_unshift($posts, $actual);
			
			// Obtain only the number of posts configured in the plugin settings
			if(!empty($cpm_map['points']) && $cpm_map['points'] > 0)
				$posts = array_slice($posts, 0, $cpm_map['points']);
			
			foreach($posts as $_post){
				$this->populate_points($_post, true);
			}	
			
			$output  = $this->_set_map_tag($cpm_map);
			$output .= $this->_set_map_config($cpm_map);
			
            $output .= "<noscript>
				codepeople-post-map require JavaScript
			</noscript>
			";	
			return $output;
		}else{ 
			global $id;
            if($id){
                $this->populate_points(get_post($id), true);
            }
			$cpm_map = $this->get_configuration_option();
			$output  = $this->_set_map_tag($cpm_map);
			$output .= $this->_set_map_config($cpm_map);
			return $output;
		}	
	} // End replace_shortcode
	
	/*
	 * Generates the DIV tag where the map will be loaded
	 */
	function _set_map_tag($atts){
		$atts = array_merge($atts, $this->extended);
        extract($atts);				
		
		$output ='<div id="'.$this->map_id.'" class="cpm-map" style="display:none; width:'.$width.'px; height:'.$height.'px; ';
		switch ($align) {
			case "left" :		  
				$output .= 'float:left; margin:'.$margin.'px;"';
			break;
			case "right" :		  
				$output .= 'float:right; margin:'.$margin.'px;"';
			break;
			case "center" :		  
				$output .= 'clear:both; overflow:hidden; margin:'.$margin.'px auto;"';
			break;
			default:
				$output .= 'clear:both; overflow:hidden; margin:'.$margin.'px auto;"';
			break;		  	  
		}
		$output .= "></div>";
		return $output;
	} // End _set_map_tag
	
	/*
	 * Generates the javascript tag with map configuration
	 */
	function _set_map_config($atts){
        $atts = array_merge($atts, $this->extended);
		
		extract($atts);
		$default_language = $this->get_configuration_option('language');
		$output  = "<script type=\"text/javascript\">\n";
	
		if(isset($language)) 
			$output  .= 'var cpm_language = {"lng":"'.$language.'"};';
		elseif(isset($default_language))	
			$output  .= 'var cpm_language = {"lng":"'.$default_language.'"};';
	
		$output .= "var cpm_global = cpm_global || {};\n";
		$output .= "cpm_global['$this->map_id'] = {}; \n";
		$output .= "cpm_global['$this->map_id']['zoom'] = $zoom;\n";
		$output .= "cpm_global['$this->map_id']['dynamic_zoom'] = ".((isset($dynamic_zoom) && $dynamic_zoom) ? 'true' : 'false').";\n";
		$output .= "cpm_global['$this->map_id']['markers'] = new Array();\n";
		$output .= "cpm_global['$this->map_id']['display'] = '$display';\n"; 
		$output .= "cpm_global['$this->map_id']['highlight_class'] = '".$this->get_configuration_option('highlight_class')."';\n"; 
		  
		$highlight = $this->get_configuration_option('highlight');
		$output .= "cpm_global['$this->map_id']['highlight'] = ".(($highlight && !is_singular()) ? 'true' : 'false').";\n"; 
		$output .= "cpm_global['$this->map_id']['type'] = '$type';\n";	
        $output .= "cpm_global['$this->map_id']['show_window'] = ".((isset($show_window) && $show_window) ? 'true' : 'false').";\n";
		$output .= "cpm_global['$this->map_id']['show_default'] = ".((isset($show_default) && $show_default) ? 'true' : 'false').";\n";
        
		  
		// Define controls
		$output .= "cpm_global['$this->map_id']['mousewheel'] = ".((isset($mousewheel) && $mousewheel) ? 'true' : 'false').";\n";	  
		$output .= "cpm_global['$this->map_id']['zoompancontrol'] = ".((isset($zoompancontrol) && $zoompancontrol) ? 'true' : 'false').";\n";	  
		$output .= "cpm_global['$this->map_id']['typecontrol'] = ".((isset($typecontrol) && $typecontrol) ? 'true' : 'false').";\n";	  
		$output .= "</script>";
		
		return $output;
	} // End _set_map_config
	
	/*
	 * Generates the javascript code of map points
	 */
	function _set_map_point($point, $index, $default = "false"){
		return 'cpm_global["'.$this->map_id.'"]["markers"]['.$index.'] = 
							{"address":"'.esc_js(str_replace(array('&quot;', '&lt;', '&gt;', '&#039;', '&amp;'), array('\"', '<', '>', "'", '&'), $point['address'])).'",
							 "lat":"'.$point['latitude'].'",
							 "lng":"'.$point['longitude'].'",
							 "info":"'.esc_js(str_replace(array('&quot;', '&lt;', '&gt;', '&#039;', '&amp;'), array('\"', '<', '>', "'", '&'), $this->_get_windowhtml($point))).'",
							 "open":"'.$default.'",
							 "icon":"'.((!empty($point['icon'])) ? $point['icon'] : $this->get_configuration_option('default_icon')).'",
							 "post":"'.$point['post_id'].'"};';
	} // End _set_map_point
	
    function _get_img_id($url){
        global $wpdb;
        $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM " . $wpdb->prefix . "posts" . " WHERE guid='%s';", $url )); 
        return $attachment[0];
    } // End get_img_id
    
	/**
	 * Get the html info associated to point marker
	 */
 	function _get_windowhtml(&$point) {
    
		$windowhtml = "";
		$windowhtml_frame = $this->get_configuration_option('windowhtml');	
		
		$point_title = (!empty($point['name'])) ? $point['name'] : get_the_title($point['post_id']);
		$point_link = (!empty($point['post_id'])) ? get_permalink($point['post_id']) : '';
		
		$point_thumbnail = "";
        
        if (isset($point['thumbnail']) && $point['thumbnail'] != "") {
            $point_img_url = $point['thumbnail'];
            if(preg_match("/attachment_id=(\d+)/i", $point['thumbnail'], $matches)){
            	$thumb = wp_get_attachment_image_src($matches[1], 'thumbnail');
				if(is_array($thumb))$point_thumbnail = $thumb[0];
			}else{
                $thumb = wp_get_attachment_image_src($this->_get_img_id($point['thumbnail']), 'thumbnail');
				if(is_array($thumb))$point_thumbnail = $thumb[0];
			}
            if($point_thumbnail != "")
                $point_img_url = $point_thumbnail;
		}
		$point_excerpt = $this->_get_excerpt($point['post_id']);

		$point_description = ($point['description'] != "") ? $point['description'] : $point_excerpt;
		$point_address = $point['address'];

		if(isset($point_img_url)) {
			$point_img = "<img src='".$point_img_url."' style='margin:8px 0 0 8px; width:90px; height:90px' align='right' />";
			$html_width = "310px";
		} else {
			$point_img = "";
			$html_width = "auto";
		}				
					
		$find = array("%title%","%link%","%thumbnail%", "%excerpt%","%description%","%address%","%width%","\r\n","\f","\v","\t","\r","\n","\\","\"");
		$replace  = array($point_title,$point_link,$point_img,$point_excerpt,$point_description,$point_address,$html_width,"","","","","","","","'");
		
		$windowhtml = str_replace( $find, $replace, $windowhtml_frame);
					
		return $windowhtml;
		
	} // End _get_windowhtml
	
	/**
	 * Get the excerpt from content
	 */
	function _get_excerpt($post_id) { // Fakes an excerpt if needed

		$content_post = get_post($post_id);
		$content = $content_post->post_content;

		if ( '' != $content ) {
			
			$content = strip_shortcodes( $content ); 
			$content = str_replace(']]>', ']]&gt;', $content);
			$content = strip_tags($content);
			$excerpt_length = 10;
			$words = explode(' ', $content, $excerpt_length + 1);
			if (count($words) > $excerpt_length) {
				array_pop($words);
				array_push($words, '[...]');
				$content = implode(' ', $words);
			}
		}
		return $content;
	} // End _get_excerpt
	
	/*
		Set a link to contact page
	*/
	function customizationLink($links) { 
		$settings_link = '<a href="http://wordpress.dwbooster.com/contact-us" target="_blank">'.__('Request custom changes').'</a>'; 
		array_unshift($links, $settings_link); 
		return $links; 
	} // End customizationLink
} // End CPM class
?>