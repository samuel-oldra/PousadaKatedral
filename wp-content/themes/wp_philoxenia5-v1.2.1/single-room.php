<?php get_header(); ?>
	<div id="main-wrap-outer" class="wrap-outer-page">
		<div id="main-wrap-inner">
			<div id="main-wrap" class="wrap">				
							
				<div id="main" class="inner group">
					
					<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
					
						<?php 
							// Exclude the featured image from the slider, as it appears on the header.
							if(get_post_meta($post->ID, 'ci_cpt_room_featured_header', true) == 'disabled')
								$exclude_id = array();
							else
								$exclude_id = array(get_post_thumbnail_id());
															
							$args = array(
								'post__not_in' => $exclude_id,
								'post_type' => 'attachment',
								'posts_per_page' => -1,
								'order' => 'ASC',
								'orderby' => 'menu_order ID',  
								'post_parent' => $post->ID,
								'post_mime_type' => 'image',
								'post_status' => null
							);
							$attachments = get_posts($args);
							$image_count = count($attachments);
				
							$slider_enabled = get_post_meta($post->ID, 'ci_cpt_room_slider', true)!='disabled' ? true : false;
						?>
						<?php if($slider_enabled): ?>
							<?php if($image_count > 0):?>
								<div id="room-gallery" class="group">
									<div id="room-photos">
										<?php 
											$attachment = $attachments[0];
											$attr = array(
												'alt'   => '',
												'title' => ''
											);
											$img_attrs = wp_get_attachment_image_src( $attachment->ID, 'large' );
											echo '<a href="'.$img_attrs[0].'" id="room-photo-medium" rel="prettyPhoto['.$post->ID.']" title="">'.wp_get_attachment_image( $attachment->ID, 'ci_room_slider', false, $attr ).'</a>';
											//echo '<a href="" id="room-photo-medium" title=""><img src="" /></a>';
										?>
										<ul id="room-carousel" class="jcarousel-skin-tango">
											<?php
												//
												// The following commented lines (within this php block) make the carousel
												// fill with images (by repeating), if less than 6 are uploaded.
												// If you want such a behavior, uncomment these lines.
												// You should uncomment 7 lines in total.
												//
											
												//$thumbs_count=0;
												//while(true)
												//{
													foreach ( $attachments as $attachment )
													{
														$attr = array(
															'alt'   => trim(strip_tags( get_post_meta($attachment->ID, '_wp_attachment_image_alt', true) )),
															'title' => trim(strip_tags( $attachment->post_title ))
														);
														$img_attrs = wp_get_attachment_image_src( $attachment->ID, 'ci_room_slider' );													
														$img_attrf = wp_get_attachment_image_src( $attachment->ID, 'full' );
														echo '<li><a href="'. $img_attrf[0] .'" rel="'. $img_attrs[0] .'" title="">'.wp_get_attachment_image( $attachment->ID, 'ci_room_slider_thumb', false, $attr ).'</a></li>';
														echo '<li style="display: none;"><a href="'. $img_attrf[0] .'" rel=prettyPhoto['.$post->ID.'] title="">'.wp_get_attachment_image( $attachment->ID, 'ci_room_slider_thumb', false, $attr ).'</a></li>';
														//$thumbs_count++;
														//if ($thumbs_count >= 6 and count($attachments) < 6) break;
													}
													//if ($thumbs_count >= 6) break;
												//}
											?>	
										</ul>
									</div><!-- /photos -->
									<div id="room-amenities">
										<h3><?php _e('Amenities', 'ci_theme'); ?></h3>
										<ul>
											<?php
												$amenities = get_post_meta($post->ID, 'ci_cpt_room_amenities', true);
												if(is_array($amenities) and count($amenities)>0)
												{
													foreach($amenities as $am)
													{
														echo '<li>'.$am.'</li>';
													}
												}
												else
												{
													echo _e('No amenities available','ci_theme');
												}
											?>
										</ul>
									</div><!-- /room-amenities -->
								</div><!-- /room-gallery -->
							<?php endif; ?>
						<?php endif; ?>

						
						<div id="content" class="group">
	
							<article id="post-<?php the_ID(); ?>" <?php post_class('post group'); ?>>
								<?php ci_e_content(); ?>
							</article><!-- /article -->
	
						</div><!-- /content -->
					<?php endwhile; endif; ?>
					
					<section id="sidebar">
						<?php dynamic_sidebar('sidebar-room'); ?>				
					</section><!-- /sidebar -->	
					
				</div><!-- /main -->
			</div>	
		</div><!-- /main-wrap -->
	</div><!-- /main-wrap-outer -->	
	
<?php get_footer(); ?>
