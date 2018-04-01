<?php 
	// This global is needed so that we won't need a duplicate of the form
	// just for a few classes.
	// Out of the box, the file template-front.php defines this global.
	global $booking_form_classes;
	if(empty($booking_form_classes))
	{
		// These are the default classes
		$booking_form_classes = 'bbooking wrap group';
	}
?>
<div id="booking" class="<?php echo $booking_form_classes; ?>">

	<form action="<?php echo get_permalink(ci_setting('booking_form_page')); ?>" method="post">
		<p>
			<label for="arrive"><?php _ex('Arrive', 'verb', 'ci_theme'); ?></label>
			<input name="arrive" type="text" class="date" />
		</p>
		
		<p>	
			<label for="depart"><?php _ex('Depart', 'verb', 'ci_theme'); ?></label>
			<input name="depart" type="text" class="date" />
		</p>
			
		<p>	
			<label for="adults"><?php _e('Adults', 'ci_theme'); ?></label>
			<select name="adults">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
			</select>
		</p>
	
		<p>	
			<label for="children"><?php _e('Children', 'ci_theme'); ?></label>
			<select name="children">
				<option value="0">0</option>
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
			</select>
		</p>
			
		<p>	
			<input type="submit" name="send_to_form" value="<?php _e('Check Availability', 'ci_theme'); ?>" />
		</p>
	</form>

</div><!-- /booking -->
