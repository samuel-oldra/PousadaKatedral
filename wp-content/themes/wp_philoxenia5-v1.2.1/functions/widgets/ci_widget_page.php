<?php 
if( !class_exists('CI_Page_Widget') ):
class CI_Page_Widget extends WP_Widget {

	function CI_Page_Widget(){
		$widget_ops = array('description' => __('Displays a single page with a featured image', 'ci_theme'));
		$control_ops = array('width' => 300, 'height' => 400);
		parent::WP_Widget('ci_page_widget', $name='-= CI Page =-', $widget_ops, $control_ops);
	}
	
	function widget($args, $instance) {
		global $post;
		$old_post = $post;
				
		extract($args);
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$ci_post_id = $instance['post_id'];

		$post = get_post($ci_post_id);

		echo $before_widget;
		echo '<div class="ci_widget_portfolio">';

		if($post)
		{
			setup_postdata($post);
			
			if ($title) 
				echo $before_title . $title . $after_title;
			else 
				echo $before_title . '<a href="'.get_permalink().'">'. get_the_title() . '</a>' . $after_title;

			echo '<a href="'.get_permalink().'">'.get_the_post_thumbnail().'</a>';
			the_excerpt();
			echo '<p><a href="'.get_permalink().'">'. __('Learn more &raquo;','ci_theme') .'</a></p>';
		}

		echo "</div>";
		echo $after_widget;

		$post = $old_post;
		setup_postdata($post);
	}
	
	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = stripslashes($new_instance['title']);
		$instance['post_id'] = intval($new_instance['post_id']);
		return $instance;
	}
	 
	function form($instance){
		$instance = wp_parse_args( (array) $instance, array('post_id' => 0, 'title'=>'') );
		$ci_post_id = intval($instance['post_id']);
		$title = htmlspecialchars($instance['title']);
		echo '<p><label for="'.$this->get_field_id('title').'">' . __('Title (leave empty to use the page\'s title):', 'ci_theme') . '</label><input id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . esc_attr($title) . '" class="widefat" /></p>';
		echo '<p><label for="'.$this->get_field_id('post_id').'">'.__('Page to show:', 'ci_theme').'</label></p>';
		wp_dropdown_pages( array(
			'selected' => $ci_post_id,
			'id' => $this->get_field_id('post_id'),
			'name' => $this->get_field_name('post_id')
		));
	}

} // class

register_widget('CI_Page_Widget');

endif; //class_exists
?>
