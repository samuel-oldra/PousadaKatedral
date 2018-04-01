<?php
/*
Template Name: Rooms listing
*/
?>
<?php get_header(); ?>

	<?php 
		global $paged;
	
		$paged = get_query_var('paged') ? get_query_var('paged') : 1;
		$base_category = get_post_meta($post->ID, 'base_rooms_category', true);
	
		$params = array(
			'paged' => $paged,
			'post_type' => 'room'
		);
	
		$args_tax = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'room_category',
					'field' => 'id',
					'terms' => $base_category,
					'include_children' => true
				)
			)
		);
	
		if(empty($base_category) or $base_category < 1)
			query_posts($params);
		else
			query_posts(array_merge($params, $args_tax));
	?>	

	<div id="main-wrap-outer" class="wrap-outer-page">
		<div id="main-wrap-inner">
			<div id="main-wrap" class="wrap">				
							
				<div id="main" class="inner group">					
					<div id="content" class="group">

						<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
						<article id="post-<?php the_ID(); ?>" <?php post_class('post listing group'); ?>>
							<h2><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(sprintf(__('Permalink to: %s', 'ci_theme'), get_the_title())); ?>"><?php the_title(); ?></a></h2>
							<?php the_post_thumbnail('ci_blog_thumb', array('class' => 'alignleft')); ?>
							<?php ci_e_content(); ?>
						</article><!-- /article -->
						<?php endwhile; endif; ?>
						
						<?php ci_pagination(); ?>
						<?php wp_reset_query(); ?>
						
					</div><!-- /content -->
									
					<section id="sidebar">
						<?php dynamic_sidebar('sidebar-room'); ?>				
					</section><!-- /sidebar -->	
										
				</div><!-- /main -->
			</div>	
		</div><!-- /main-wrap -->
	</div><!-- /main-wrap-outer -->	

<?php get_footer(); ?>
