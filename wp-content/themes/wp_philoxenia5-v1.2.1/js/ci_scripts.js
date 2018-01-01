jQuery.fn.exists = function(){ return this.length>0; }

jQuery(document).ready( function($) {	
	// helping main nav look a bit better
	$('ul.nav').superfish({ 
		delay:       500, 
		animation:   {opacity:'show'},
		speed:       'fast',
		autoArrows:  true,
		dropShadows: false
	});
	
	// we only need this in the room page so let's check if there's a carousel anyway
	if ($('#room-carousel').exists()) {
		$('#room-carousel').jcarousel({
			wrap: 'circular'
		});
	}
	
	// update the medium photo from carousel
	$('#room-carousel a').click( function(){
		var rel = $(this).attr('rel');
		var href = $(this).attr('href');
		var target_img = $('#room-photo-medium img').attr('src',rel);
		var target_href = $('#room-photo-medium').attr('href',href); 
		return false;
	});
	
	
	// if we have a slider well, slide
	if ($('#slider').exists()) {
		$('#slider').cycle({
			fx: ThemeOption.slider_effect,
			timeout: Number(ThemeOption.slider_timeout),
			sync: ThemeOption.slider_sync,
			speed: Number(ThemeOption.slider_speed)
		});
	}
	
	// datepicker
	$( ".date" ).datepicker({dateFormat: 'yy/mm/dd'});
	
	$('#switch').click(function(){
		var c = $(this).attr('rel');
		console.log(c);
		$('#c').attr('href','colors/'+c);
	});
	
	$("a[rel^='prettyPhoto']").prettyPhoto({
		theme: 'dark_rounded',
		social_tools: ''
	});

});
