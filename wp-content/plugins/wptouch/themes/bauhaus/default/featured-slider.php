<?php
	$settings = foundation_get_settings();
?>
<?php
	$settings = foundation_get_settings();
?>
	<a href='<?php echo the_permalink(); ?>' class='needsclick'>
		<?php if ( $settings->featured_style == 'enhanced' && wptouch_get_comment_count() != 0 ) { ?>
			<div class='comments-number'><span><?php echo wptouch_get_comment_count(); ?></span></div>
		<?php } ?>
		<img src='<?php echo foundation_featured_get_image(); ?>' alt='<?php the_title(); ?>' / >
		<p class='featured-date'>
			<?php wptouch_the_time(); ?>
			<?php if ( $settings->featured_style == 'normal' && $settings->featured_comments ) { ?>
				&bull; <?php echo wptouch_get_comment_count() . ' comments'; ?></p>
			<?php } ?>
		<p class='featured-title'><?php the_title(); ?></p>
	</a>