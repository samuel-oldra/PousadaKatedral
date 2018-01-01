<?php get_header(); ?>

	<div id="main-wrap-outer" class="wrap-outer-page">
		<div id="main-wrap-inner">
			<div id="main-wrap" class="wrap">				
							
				<div id="main" class="inner group">					
					<div id="content" class="group">

						<article class="post listing group">
							<?php 
								global $wp_query;
						
								$found = $wp_query->post_count > $wp_query->found_posts ? $wp_query->post_count : $wp_query->found_posts;
								$none = __('No results found. Please broaden your terms and search again.', 'ci_theme');
								$one = __('Just one result found. We either nailed it, or you might want to broaden your terms and search again.', 'ci_theme');
								$many = sprintf(__("%d results found.", 'ci_theme'), $found);
							?>
							<h2><?php _e('Search results', 'ci_theme'); ?></h2>
							<p><?php ci_e_inflect($found, $none, $one, $many); ?></p>

							<?php if($found==0) get_search_form(); ?>

						</article><!-- /article -->

						
						<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
						<article id="post-<?php the_ID(); ?>" <?php post_class('post listing group'); ?>>
							<h2><a href="<?php the_permalink(); ?>" title="<?php echo __('Permalink to', 'ci_theme').' '.get_the_title(); ?>"><?php the_title(); ?></a></h2>
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
