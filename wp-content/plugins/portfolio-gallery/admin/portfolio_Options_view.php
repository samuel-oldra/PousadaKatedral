<?php
if (function_exists('current_user_can'))
    if (!current_user_can('manage_options')) {
        die('Access Denied');
    }
if (!function_exists('current_user_can')) {
    die('Access Denied');
}

function      html_showStyles($param_values, $op_type)
{
    ?>	
<script>

jQuery(document).ready(function () {
	var strliID=$(location).attr('hash');
	//alert(strliID);
	jQuery('#portfolio-view-tabs li').removeClass('active');
	if(jQuery('#portfolio-view-tabs li a[href="'+strliID+'"]').length>0){
		jQuery('#portfolio-view-tabs li a[href="'+strliID+'"]').parent().addClass('active');
	}else {
		jQuery('#portfolio-view-tabs li a[href="#portfolio-view-options-0"]').parent().addClass('active');
	}
	jQuery('#portfolio-view-tabs-contents li').removeClass('active');
	strliID=strliID;
	//alert(strliID);
	if(jQuery(strliID).length>0){
		jQuery(strliID).addClass('active');
	}else {
		jQuery('#portfolio-view-options-0').addClass('active');
	}
	$('input[data-slider="true"]').bind("slider:changed", function (event, data) {
		 $(this).parent().find('span').html(parseInt(data.value)+"%");
		 $(this).val(parseInt(data.value));
	});	
});
</script>
<div class="wrap">

<?php $path_site2 = plugins_url("../images", __FILE__); ?>
		<?php $path_site = plugins_url("Front_images", __FILE__); ?>
			<div class="slider-options-head">
		<div style="float: left;">
			<div><a href="http://huge-it.com/wordpress-plugins-portfolio-gallery-user-manual/" target="_blank">User Manual</a></div>
			<div>This section allows you to configure the Portfolio/Gallery options. <a href="http://huge-it.com/wordpress-plugins-portfolio-gallery-user-manual/" target="_blank">More...</a></div>
			<div>This options are disabled in free version. Get full version to customize them. <a href="http://huge-it.com/wordpress-plugins-portfolio-gallery-user-manual/" target="_blank">Get full Version</a></div>
		</div>
		<div style="float: right;">
			<a class="header-logo-text" href="http://huge-it.com/portfolio-gallery/" target="_blank">
				<div><img width="250px" src="<?php echo $path_site2; ?>/huge-it1.png" /></div>
				<div>Get the full version</div>
			</a>
		</div>
	</div>
	<div style="clear:both;"></div>
<div id="poststuff">
<img src="<?php echo $path_site2; ?>/portfolio_options.jpg" />
</div>
</div>
<input type="hidden" name="option" value=""/>
<input type="hidden" name="task" value=""/>
<input type="hidden" name="controller" value="options"/>
<input type="hidden" name="op_type" value="styles"/>
<input type="hidden" name="boxchecked" value="0"/>

<?php
}