<div id="slider">

	<?php 
		global $post;
		$q = new WP_Query( array(
			'post_type'=>'slider',
			'posts_per_page' => -1
		)); 
	?>

	<?php while ( $q->have_posts() ) : $q->the_post(); ?>

		<?php 
			global $post;
			$img_id = false;
			$img_url = '';
			if(has_post_thumbnail())
			{
				$img_id = get_post_thumbnail_id($post->ID);
				$img_info = wp_get_attachment_image_src($img_id, 'ci_home_slider');
				if(!empty($img_info))
				{
					$img_url = $img_info[0];
				}
	
			}
		?>
		<?php if(!empty($img_url)): ?>
			<div class="slide" style="background:url(<?php echo $img_url; ?>) no-repeat top center;">
				<div class="slide-content wrap">
					<div class="slide-badge">
						<?php $link = get_post_meta($post->ID, 'ci_cpt_slider_url', true); ?>
						<?php if(!empty($link)): ?>
							<h2><a href="<?php echo $link; ?>"><?php the_title(); ?></a></h2>
						<?php else: ?>
							<h2><?php the_title(); ?></h2>
						<?php endif; ?>
						<?php the_excerpt(); ?>
					</div><!-- /slide-badge -->
				</div><!-- /slide-content -->
			</div><!-- /slide -->
		<?php endif; ?>

	<?php endwhile; ?>	
	<?php wp_reset_postdata(); ?>

</div>
<div id="header-trans"></div>
