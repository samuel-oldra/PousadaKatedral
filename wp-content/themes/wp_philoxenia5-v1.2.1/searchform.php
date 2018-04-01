<form role="search" method="get" id="search" action="<?php echo esc_url(home_url('/')); ?>">
	<div>
		<input type="text" name="s" id="s" value="<?php echo (get_search_query()!="" ? get_search_query() : __('Search', 'ci_theme') ); ?>" size="18" onfocus="if (this.value == '<?php _e('Search', 'ci_theme'); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('Search', 'ci_theme'); ?>';}" />
		<input type="submit" id="searchsubmit" value="<?php esc_attr_e('Search', 'ci_theme'); ?>" />
	</div>
</form>
