/**
 * CodePeople Post Map 
 * Version: 1.0.1
 * Author: CodePeople
 * Plugin URI: http://wordpress.dwbooster.com
*/

(function ($) {
	var _latlng_btn,
    thumbnail_field;
	
    // thumbnail selection
    window["cpm_send_to_editor"] = function(html) {

        var file_url = jQuery(html).attr('href');
        if (file_url) {
            jQuery(thumbnail_field).val(file_url);
        }
        tb_remove();
        window.send_to_editor = window.cpm_send_to_editor_default;

    };
		
    window["cpm_send_to_editor_default"] = window.send_to_editor;
    
    window["cpm_thumbnail_selection"] = function(e){
        thumbnail_field = $(e).parent().find('input[type="text"]');
        window.send_to_editor = window.cpm_send_to_editor;
        tb_show('', 'media-upload.php?TB_iframe=true');
        return false;
    };
    
    //---------------------------------------------------------
    
    function _get_latlng(request, callback){
		var g = new google.maps.Geocoder();
		g.geocode(request, callback);
	};
	
	window['cpm_get_latlng'] = function (){
		var f 			= _latlng_btn.parents('.point_form'),
			a 			= $('#cpm_point_address').val(),
			longitude 	= $('#cpm_point_longitude').val(),
			latitude 	= $('#cpm_point_latitude').val(),
			language	= $('#cpm_map_language').val(),
			request		= {};
		
		// Remove unnecessary spaces characters
		longitude = longitude.replace(/^\s+/, '').replace(/\s+$/, '');
		latitude  = latitude.replace(/^\s+/, '').replace(/\s+$/, '');
		a = a.replace(/^\s+/, '').replace(/\s+$/, '');
		
		if(longitude.length && latitude.length){
			request['location'] = new google.maps.LatLng(latitude, longitude);
		}else if(a.length){
			request['address'] = a.replace(/[\n\r]/g, '');
		}else{
			return false;
		}	
		
		_get_latlng(request, function(result, status){
			if(status && status == "OK"){
				// Update fields
				var address   = result[0]['formatted_address'],
					latitude  = result[0]['geometry']['location'].lat(),
					longitude = result[0]['geometry']['location'].lng();
				
				if(address && latitude && longitude){
					$('#cpm_point_address').val(address);
					$('#cpm_point_longitude').val(longitude);
					$('#cpm_point_latitude').val(latitude);
					
					// Load Map
					cpm_load_map(f.find('.cpm_map_container'),latitude, longitude);
				}
			}else{
				alert('The point is not located');
			}
			
		});
	};		
	
	// Show/Hide the information related to the map 
	window['display_map_form'] = function (){
		$('#map_data').slideToggle();
	};
	
	// Check the point or address existence
	window['cpm_checking_point'] = function (e){
		var language = 'en';
		_latlng_btn = $(e);
		
		if(typeof google != 'undefined' && google.maps){
			cpm_get_latlng();
		}else{
			$('<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false'+((language) ? '&language='+language: '')+'&callback=cpm_get_latlng"></script>').appendTo('body');
		}
	};
	
	window['cpm_load_map'] = function(container, latitude, longitude){
		var c = container,
			f = c.parents('.point_form'),
			p = new google.maps.LatLng(latitude, longitude),
			m = new google.maps.Map(c[0], {
								zoom: 5,
								center: p,
								mapTypeId: google.maps.MapTypeId['ROADMAP'],
								
								// Show / Hide controls
								panControl: true,
								scaleControl: true,
								zoomControl: true,
								mapTypeControl: true,
								scrollWheel: true
						}),
			mk = new google.maps.Marker({
							  position: p,
							  map: m,
							  icon: new google.maps.MarkerImage(cpm_default_marker),
							  draggable: true
						 });
			
			google.maps.event.addListener(mk, 'position_changed', function(){
				f.find('#cpm_point_latitude').val(mk.getPosition().lat());
				f.find('#cpm_point_longitude').val(mk.getPosition().lng());
			});				
	};
	
	window['cpm_set_map_flag'] = function(){
		var request = {};
		if(cpm_point['longitude'] && cpm_point['latitude']){
			request['location'] = new google.maps.LatLng(cpm_point['latitude'], cpm_point['longitude']);
		}else if(cpm_point['address']){
			request['address'] = cpm_point['address'].replace(/[\n\r]/g, '');
		}
		
		_get_latlng(request, function(result, status){
			if(status && status == "OK"){
				// Update fields
				var address   = result[0]['formatted_address'],
					latitude  = result[0]['geometry']['location'].lat(),
					longitude = result[0]['geometry']['location'].lng();
				
				if(address && latitude && longitude){
					// Load Map
					cpm_load_map($('.cpm_map_container'),latitude, longitude);
				}
			}
		});
	};
	
    function enable_disable_fields(f, v){
        var p = f.parents('#map_data');
        p.find('input[type="text"]').attr({'DISABLED':v,'READONLY':v});
        p.find('select').attr({'DISABLED':v,'READONLY':v});
        p.find('input[type="checkbox"]').filter('[id!="cpm_map_single"]').attr({'DISABLED':v,'READONLY':v});
    };
        
	$(function(){
		// Actions for icons
		$(".cpm_icon").click(function(){
			var  i = $(this);
			$('.cpm_icon.cpm_selected').removeClass('cpm_selected');
			i.addClass('cpm_selected');
			$('#default_icon').val($('img', i).attr('src'));
		}).mouseover(function(){
			$(this).css({"border":"solid #BBBBBB 1px"})
		}).mouseout(function(){
			$(this).css({"border":"solid #F9F9F9 1px"})
		});
		
		// Action for insert shortcode 
		$('#cpm_map_shortcode').click(function(){
            if(window.cpm_send_to_editor_default)
                window.send_to_editor = window.cpm_send_to_editor_default;
        	if(send_to_editor){
        		send_to_editor('[codepeople-post-map]');
			}
            var t = $('#content');
            if(t.length){
                var v= t.val()
                if(v.indexOf('codepeople-post-map') == -1)
                    t.val(v+'[codepeople-post-map]');
            }
        });
		
		// Create the script tag and load the maps api
		if($('.cpm_map_container').length){
			$('<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&callback=cpm_set_map_flag"></script>').appendTo('body');
		}
        
        $('#cpm_map_single').each(function(){
            var f = $(this);
            enable_disable_fields(f, !f[0].checked);
            f.click(function(){
                enable_disable_fields(f,!f[0].checked);
            });
        });
	});
})(jQuery);