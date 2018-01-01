<?php
/*
Template Name: Booking form
*/
?>


<?php

	// Sanitize data, or initialize if they don't exist.
	$clientname = isset($_POST['clientname']) ? esc_html(trim($_POST['clientname'])) : '';
	$email = isset($_POST['email']) ? esc_html(trim($_POST['email'])) : '';
	$arrive = isset($_POST['arrive']) ? esc_html(trim($_POST['arrive'])) : '';
	$depart = isset($_POST['depart']) ? esc_html(trim($_POST['depart'])) : '';
	$adults = isset($_POST['adults']) ? intval($_POST['adults']) : '1';
	$children = isset($_POST['children']) ? intval($_POST['children']) : '0';
	$comments = isset($_POST['comments']) ? esc_html(trim($_POST['comments'])) : '';
	
	$errorString = '';
	$emailSent = false;
	
	if(isset($_POST['send_booking']))
	{
		// We are here because the form was submitted. Let's validate!

		
		if(empty($clientname) or mb_strlen($clientname) < 2)
			$errorString .= '<li>'.__('Your name is required', 'ci_theme').'</li>';

		if(empty($email) or !is_email($email))
			$errorString .= '<li>'.__('A valid email is required', 'ci_theme').'</li>';

		if(empty($arrive) or strlen($arrive) != 10)
			$errorString .= '<li>'.__('A complete arrival date is required', 'ci_theme').'</li>';

		if(!checkdate(substr($arrive, 5, 2), substr($arrive, 8, 2), substr($arrive, 0, 4)))
			$errorString .= '<li>'.__('The arrival date must be in the form yyyy/mm/dd', 'ci_theme').'</li>';

		if(empty($depart) or strlen($depart) != 10)
			$errorString .= '<li>'.__('A complete departure date is required', 'ci_theme').'</li>';

		if(!checkdate(substr($depart, 5, 2), substr($depart, 8, 2), substr($depart, 0, 4)))
			$errorString .= '<li>'.__('The departure date must be in the form yyyy/mm/dd', 'ci_theme').'</li>';

		if(empty($adults) or !is_numeric($adults) or intval($adults) < 1)
			$errorString .= '<li>'.__('A number of one or more adults is required', 'ci_theme').'</li>';

		if(!is_numeric($children) or intval($children) < 0)
			$errorString .= '<li>'.__('A number of zero or more children is required', 'ci_theme').'</li>';


		// Alright, lets send the email already!
		if(empty($errorString))
		{
			$mailbody  = __("Name:", 'ci_theme') . " " . $clientname . "\n";
			$mailbody .= __("Email:", 'ci_theme') . " " . $email . "\n";
			$mailbody .= __("Date of arrival:", 'ci_theme') . " " . $arrive . "\n";
			$mailbody .= __("Date of departure:", 'ci_theme') . " " . $depart . "\n";
			$mailbody .= __("Adults:", 'ci_theme') . " " . $adults . "\n";
			$mailbody .= __("Children:", 'ci_theme') . " " . $children . "\n";
			$mailbody .= __("Comments:", 'ci_theme') . " " . $comments . "\n";

			$emailSent = wp_mail(ci_setting('booking_form_email'), ci_setting('logotext').' - '. __('Booking Enquiry', 'ci_theme'), $mailbody);
		}
		
	}

?>




<?php get_header(); ?>

	<div id="main-wrap-outer" class="wrap-outer-page">
		<div id="main-wrap-inner">
			<div id="main-wrap" class="wrap">				
							
				<div id="main" class="inner group">					
					<div id="content" class="group">

						<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
							<article id="post-<?php the_ID(); ?>" <?php post_class('post listing group'); ?>>
								<h2><?php the_title(); ?></h2>

								<?php ci_e_content(); ?>
	
								<?php if(!empty($errorString)): ?>
									<ul id="formerrors">
										<?php echo $errorString; ?>
									</ul>
								<?php endif; ?>
	
								<?php if($emailSent===true): ?>
									<p id="formsuccess"><?php _e('You booking inquiry has been sent. We will contact you as soon as possible.', 'ci_theme'); ?></p>
								<?php endif; ?>
	
								<?php if(  !isset($_POST['send_booking'])  or  (isset($_POST['send_booking']) and !empty($errorString))  ): ?>
									<form action="<?php the_permalink(); ?>" id="booking-form" method="post" class="group">
										
										<p class="half">
											<label for="clientname"><?php _e('Name', 'ci_theme'); ?></label>
											<input name="clientname" type="text" value="<?php echo $clientname;?>" />
										</p>
										
										<p class="half">
											<label for="email"><?php _e('Email', 'ci_theme'); ?></label>
											<input name="email" type="text" class="email" value="<?php echo $email; ?>" />
										</p>										
		
										<p class="half">
											<label for="arrive"><?php _ex('Arrive', 'verb', 'ci_theme'); ?></label>
											<input name="arrive" type="text" class="date" value="<?php echo $arrive; ?>" />
										</p>
										
										<p class="half">
											<label for="depart"><?php _ex('Depart', 'verb', 'ci_theme'); ?></label>
											<input name="depart" type="text" class="date" value="<?php echo $depart; ?>" />
										</p>
										
										<p class="half">
											<label for="adults"><?php _e('Adults', 'ci_theme'); ?></label>
											<select name="adults">
												<option value="1" <?php selected($adults, '1'); ?>>1</option>
												<option value="2" <?php selected($adults, '2'); ?>>2</option>
												<option value="3" <?php selected($adults, '3'); ?>>3</option>
												<option value="4" <?php selected($adults, '4'); ?>>4</option>
											</select>
										</p>
		
										<p class="half">
											<label for="children"><?php _e('Children', 'ci_theme'); ?></label>
											<select name="children">
												<option value="0" <?php selected($children, '0'); ?>>0</option>
												<option value="1" <?php selected($children, '1'); ?>>1</option>
												<option value="2" <?php selected($children, '2'); ?>>2</option>
												<option value="3" <?php selected($children, '3'); ?>>3</option>
												<option value="4" <?php selected($children, '4'); ?>>4</option>
											</select>
										</p>
										
										<p>
											<label for="comments"><?php _e('Comments', 'ci_theme'); ?></label>
											<textarea name="comments" rows="5" cols="50"><?php echo $comments; ?></textarea>
										</p>		
																				
										<p><input type="submit" name="send_booking" value="<?php _e('Send','ci_theme'); ?>" /></p>
									</form>
								<?php endif; ?>
							
							</article><!-- /article -->
						<?php endwhile; endif; ?>
												
					</div><!-- /content -->
									
					<section id="sidebar">
						<?php dynamic_sidebar('sidebar-room'); ?>				
					</section><!-- /sidebar -->	
										
				</div><!-- /main -->
			</div>	
		</div><!-- /main-wrap -->
	</div><!-- /main-wrap-outer -->	

<?php get_footer(); ?>
