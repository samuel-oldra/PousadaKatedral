<?php

class WP_Photo_Gallery_Shortcode_Utility {

    function __construct() {
        //NOP
    }

    //Handles output for the slider shortcode
    static function wppg_slider_output_sc($ids, $attrs) {
        if (is_array($ids)) {
            //get photos for each gallery id
            $image_data = array();
            foreach ($ids as $id) {
                $image_data_temp = WPPGPhotoGallery::getGalleryItems($id);
                $image_data = array_merge($image_data, $image_data_temp);
            }
        } else {
            //get photos for single gallery
            $image_data = WPPGPhotoGallery::getGalleryItems($ids);
        }

        $slider_id = uniqid();
        $carousel_id = uniqid();

        $carousel_js_code = '';
        $show_carousel = false;
        if (isset($attrs['show_carousel']) && $attrs['show_carousel'] == '1') {
            $show_carousel = true;
            $carousel_js_code = <<<EOT
            $('#$carousel_id').flexslider({
                animation: "slide",
                controlNav: false,
                animationLoop: false,
                slideshow: false,
                itemWidth: 150,
                itemMargin: 5,
                asNavFor: '#$slider_id'
              });
EOT;
        }

        WP_Photo_Gallery_Utility::start_buffer();
        ?>
        <div class="wppg-slider-container flexslider" id="<?php echo $slider_id; ?>">
            <ul class="wppg-slides slides">
                <?php
                foreach ($image_data as $image) {
                    echo '<li><img src="' . $image['image_url'] . '"/></li>';
                }
                ?>
            </ul>
        </div>

        <?php
        if($show_carousel){
        ?>
        <div class="wppg-slider-container flexslider" id="<?php echo $carousel_id; ?>">
            <ul class="wppg-slides slides">
                <?php
                foreach ($image_data as $image) {
                    echo '<li><img src="' . $image['image_url'] . '"/></li>';
                }
                ?>
            </ul>
        </div>        
        <?php
        }
        
        
        $slider_js_code = <<<EOT
<script type="text/javascript" charset="utf-8">
/* <![CDATA[ */
jQuery(document).ready(function($) {
    $carousel_js_code
    $('#$slider_id').flexslider({
        animation: "slide",
        smoothHeight: true,
        prevText: '',
        nextText: '',
        controlNav: false,
        slideshow: false
    });
});      
/* ]]> */
</script> 
EOT;
        echo $slider_js_code;

        $output = WP_Photo_Gallery_Utility::end_buffer_and_collect();
        return $output;
    }

}