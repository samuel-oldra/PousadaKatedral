<?php
/*
Template Name: Homepage
*/
?>

<?php get_header(); ?>

	<div id="main-wrap-outer">
		<div id="main-wrap-inner">
			<div id="main-wrap" class="wrap">

				

				<div id="main" class="group">
					<div class="home-cols cols group">
					
						<?php dynamic_sidebar('homepage'); ?>
					
					</div><!-- /cols -->
										
					<?php get_template_part('inc_newsletter'); ?>					
					
				</div><!-- /main -->
			</div>	
		</div><!-- /main-wrap -->
	</div><!-- /main-wrap-outer -->	

<?php get_footer(); ?>
