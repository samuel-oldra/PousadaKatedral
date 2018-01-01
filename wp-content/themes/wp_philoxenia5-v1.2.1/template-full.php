<?php
/*
Template Name: Full Width, No Sidebar
*/
?>
<?php get_header(); ?>

	<div id="main-wrap-outer" class="wrap-outer-page">
		<div id="main-wrap-inner">
			<div id="main-wrap" class="wrap">				
							
				<div id="main" class="inner group">					
					<div id="content" class="full group">
						
						<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
							<article id="post-<?php the_ID(); ?>" <?php post_class('post group'); ?>>
								<?php ci_e_content(); ?>
								<?php wp_link_pages(); ?>							
							</article><!-- /article -->
						<?php endwhile; endif; ?>
						<?php comments_template(); ?>

					</div><!-- /content -->
				</div><!-- /main -->

			</div>	
		</div><!-- /main-wrap -->
	</div><!-- /main-wrap-outer -->	

<?php get_footer(); ?>
