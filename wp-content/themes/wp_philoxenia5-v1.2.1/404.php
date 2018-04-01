<?php get_header(); ?>

	<div id="main-wrap-outer" class="wrap-outer-page">
		<div id="main-wrap-inner">
			<div id="main-wrap" class="wrap">				
							
				<div id="main" class="inner group">					
					<div id="content" class="group">
						
						<article class="post listing group">
							<h2><?php _e('Not found', 'ci_theme'); ?></h2>
							<p><?php _e( 'Oh, no! The page you requested could not be found. Perhaps searching will help...', 'ci_theme' ); ?></p>
	
							<form role="search" method="get" id="search-body" action="<?php echo esc_url(home_url('/')); ?>">
								<div>
									<input type="text" name="s" id="s-body" value="<?php echo (get_search_query()!="" ? get_search_query() : __('Search', 'ci_theme') ); ?>" size="18" onfocus="if (this.value == '<?php _e('Search', 'ci_theme'); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('Search', 'ci_theme'); ?>';}" />
							 		<input type="submit" id="searchsubmit-body" value="<?php esc_attr_e('Search', 'ci_theme'); ?>" />
								</div>
							</form>				
			
							<script type="text/javascript">
								// focus on search field after it has loaded
								document.getElementById('s-body') && document.getElementById('s-body').focus();
							</script>
						</article><!-- /article -->
						
						<?php ci_pagination(); ?>
						
					</div><!-- /content -->
									
					<?php get_sidebar(); ?>
										
				</div><!-- /main -->
			</div>	
		</div><!-- /main-wrap -->
	</div><!-- /main-wrap-outer -->	

<?php get_footer(); ?>
