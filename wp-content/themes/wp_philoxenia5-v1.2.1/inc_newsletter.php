<?php if(ci_setting('newsletter_action')!=''): ?>
	<aside class="newsletter hn">
		<h3><span><?php ci_e_setting('newsletter_heading'); ?></span></h3>
		<form method="post" action="<?php ci_e_setting('newsletter_action'); ?>" class="group">
			<p class="newsletter-title"><?php ci_e_setting('newsletter_description'); ?></p>
			<p><input id="<?php ci_e_setting('newsletter_name_id'); ?>" name="<?php ci_e_setting('newsletter_name_name'); ?>" type="text" placeholder="<?php _e('Enter your name', 'ci_theme'); ?>" /></p>
			<p><input id="<?php ci_e_setting('newsletter_email_id'); ?>" name="<?php ci_e_setting('newsletter_email_name'); ?>" type="text" placeholder="<?php _e('Enter your email', 'ci_theme'); ?>" /></p>
			<p class="newsletter-action"><input type="submit" value="<?php _e('Submit', 'ci_theme'); ?>" /></p>
			<?php
				$fields = ci_setting('newsletter_hidden_fields');
				if(is_array($fields) and count($fields) > 0)
				{
					for( $i = 0; $i < count($fields); $i+=2 )
					{
						if(empty($fields[$i]))
							continue;
						echo '<input type="hidden" name="'.esc_attr($fields[$i]).'" value="'.esc_attr($fields[$i+1]).'" />';
					}
				}
			?>
		</form>
	</aside><!-- /newsletter -->
<?php endif; ?>
