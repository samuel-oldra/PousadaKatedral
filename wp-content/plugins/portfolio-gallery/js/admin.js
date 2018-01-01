jQuery(document).ready(function () {
	jQuery('#arrows-type input[name="params[portfolio_navigation_type]"]').change(function(){
		jQuery(this).parents('ul').find('li.active').removeClass('active');
		jQuery(this).parents('li').addClass('active');
	});
	jQuery('#portfolio-view-tabs > li > a').click(function(){
		jQuery('#portfolio-view-tabs > li').removeClass('active');
		jQuery(this).parent().addClass('active');
		jQuery('#portfolio-view-tabs-contents > li').removeClass('active');
		var liID=jQuery(this).attr('href');
		jQuery(liID).addClass('active');
		jQuery('#adminForm').attr('action',"admin.php?page=Options_portfolio_styles&task=save"+liID);
		return false;
	});
	jQuery('.save-portfolio-options').click(function(){
		alert("General Settings are disabled in free version. If you need those functionalityes, you need to buy the commercial version.");
		return false;
	});
});

	