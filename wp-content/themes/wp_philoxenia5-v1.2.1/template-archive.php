<?php
/*
Template Name: Archive
*/
?>
<?php get_header(); ?>
	<?php 
		global $paged;
		$arrParams = array(
			'paged' => $paged,
			'ignore_sticky_posts'=>1,
			'showposts' => ci_setting('archive_no'));
		query_posts($arrParams);
	?>

	<div id="main-wrap-outer" class="wrap-outer-page">
		<div id="main-wrap-inner">
			<div id="main-wrap" class="wrap">				
							
				<div id="main" class="inner group">					
					<div id="content" class="group">

						<article class="post listing group">
							<h2><?php _e('Latest posts', 'ci_theme'); ?></h2>
							<ul class="lst archive">
								<?php while (have_posts() ) : the_post(); ?>
									<li><a href="<?php the_permalink(); ?>" title="<?php _e('Permalink to:','ci_theme'); ?> <?php the_title(); ?>"><?php the_title(); ?></a> - <?php echo get_the_date(); ?><?php the_excerpt(); ?></li>
								<?php endwhile; ?>
							</ul>
							
							<?php if (ci_setting('archive_week')=='enabled'): ?>
								<h2 class="hdr"><?php _e('Weekly Archive', 'ci_theme'); ?></h2>
								<ul class="lst archive"><?php wp_get_archives('type=weekly&show_post_count=1') ?></ul>
							<?php endif; ?>
							
							<?php if (ci_setting('archive_month')=='enabled'): ?>
								<h2 class="hdr"><?php _e('Monthly Archive', 'ci_theme'); ?></h2>
								<ul class="lst archive"><?php wp_get_archives('type=monthly&show_post_count=1') ?></ul>
							<?php endif; ?>
							
							<?php if (ci_setting('archive_year')=='enabled'): ?>
								<h2 class="hdr"><?php _e('Yearly Archive', 'ci_theme'); ?></h2>
								<ul class="lst archive"><?php wp_get_archives('type=yearly&show_post_count=1') ?></ul>
							<?php endif; ?>
						</article><!-- /article -->
												
					</div><!-- /content -->
									
					<?php get_sidebar(); ?>				
										
				</div><!-- /main -->
			</div>	
		</div><!-- /main-wrap -->
	</div><!-- /main-wrap-outer -->	

<?php get_footer(); ?>
