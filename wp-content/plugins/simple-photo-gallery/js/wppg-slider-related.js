jQuery(document).ready(function($) {
    var j=1;
    //Set an ID element value for each instance of shortcode slider on the page
    //The below is for the flexslider case
    $('.wppg-slider-container').each(function(){
        $(this).attr('id', 'wppg_slider_'+j);
        print_slider_code('wppg_slider_'+j);
        j++;
    });

    function print_slider_code(slider_id)
    {
        $('#'+slider_id).flexslider({
            animation: "slide",
            smoothHeight: true,
            prevText: '',
            nextText: '',
            controlNav: false,
            slideshow: false
        });

    }

});



