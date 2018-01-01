<?php
add_action( 'widgets_init', 'ci_widgets_init' );
if( !function_exists('ci_widgets_init') ):
function ci_widgets_init() {

	register_sidebar(array(
		'name' => __( 'Homepage', 'ci_theme'),
		'id' => 'homepage',
		'description' => __( '3 widgets under the slider. Use the CI Page widget.', 'ci_theme'),
		'before_widget' => '<div id="%1$s" class="col three %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	));		

	register_sidebar(array(
		'name' => __( 'Right Sidebar', 'ci_theme'),
		'id' => 'sidebar-right',
		'description' => __( 'Sidebar on the right', 'ci_theme'),
		'before_widget' => '<aside id="%1$s" class="widget %2$s group">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));			

	register_sidebar(array(
		'name' => __( 'Room Sidebar', 'ci_theme'),
		'id' => 'sidebar-room',
		'description' => __( 'Sidebar on the right', 'ci_theme'),
		'before_widget' => '<aside id="%1$s" class="widget %2$s group">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	));
		
	register_sidebar(array(
		'name' => __( 'Footer', 'ci_theme'),
		'id' => 'footer-sidebar',
		'description' => __( '3 widgets in the footer', 'ci_theme'),
		'before_widget' => '<div id="%1$s" class="col three %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	));			

}
endif;
?>
