<?php
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die (__('Please do not load this page directly. Thanks!', 'ci_theme'));
	if ( post_password_required() ) {
		echo '<p class="nocomments">' . __('This post is password protected. Enter the password to view comments.', 'ci_theme') . '</p>';
		return;
	}
?>

<?php if (have_comments()): ?>
	<div class="post-comments">
		<h2 id="comments"><?php comments_number(__('No comments', 'ci_theme'), __('1 comment', 'ci_theme'), __('% comments', 'ci_theme')); ?></h2>
		<div class="comments-pagination"><?php paginate_comments_links(); ?></div>
		<ol id="comment-list">
			<?php wp_list_comments(array(
				'callback' => 'ci_comment'
			)); ?>
		</ol>
		<div class="comments-pagination"><?php paginate_comments_links(); ?></div>
	</div><!-- .post-comments -->
<?php else: ?>
	<?php if(!comments_open() and ci_setting('comments_off_message')=='enabled'): ?>
		<div class="post-comments innerbox">
			<p><?php _e('Comments are closed.', 'ci_theme'); ?></p>
		</div>
	<?php endif; ?>
<?php endif; ?>

<?php if(comments_open()): ?>
	<section id="respond">
		<div id="form-wrapper">
			<?php get_template_part('comment-form'); ?>
		</div><!-- #form-wrapper -->
	</section>
<?php endif; ?>


