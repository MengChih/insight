<div class="mkdf-related-posts-holder mkdf-post-columns-<?php echo esc_attr($related_posts_number) ?>">
	<?php if ( $related_posts && $related_posts->have_posts() ) : ?>
		<div class="mkdf-related-posts-title">
			<h5 class="mkdf-title-pattern-text"><?php esc_html_e('related posts', 'hashmag' ); ?></h5>
            <div class="mkdf-title-pattern"><div class="mkdf-title-pattern-inner"></div></div>
		</div>
		<div class="mkdf-related-posts-inner clearfix">
			<?php while ( $related_posts->have_posts() ) : $related_posts->the_post(); ?>

                <div class="mkdf-pt-three-item mkdf-post-item">
                    <div class="mkdf-pt-three-item-inner">
                        <?php if (has_post_thumbnail()) { ?>
                            <div class="mkdf-pt-three-image-holder">
                                <?php
                                hashmag_mikado_post_info_category(array(
                                    'category' => 'no'
                                )); ?>
                                <div class="mkdf-pt-three-image-inner-holder">
                                    <a itemprop="url" class="mkdf-pt-three-slide-link mkdf-image-link" href="<?php echo esc_url(get_permalink()); ?>" target="_self">
                                        <?php
                                        if ($related_posts_image_size != 'custom_size') {
                                            echo get_the_post_thumbnail(get_the_ID(), 'hashmag_mikado_square');
                                        } elseif ($thumb_image_width != '' && $thumb_image_height != '') {
                                            echo hashmag_mikado_generate_thumbnail(get_post_thumbnail_id(get_the_ID()), null, $related_posts_image_size, $related_posts_image_size);
                                        }
                                        ?>
                                    </a>
                                </div>
                                <div class="mkdf-pt-three-image-link-holder">
                                </div>
                            </div>
                        <?php } else {
                            hashmag_mikado_post_info_category(array(
                                'category' => 'yes'
                            ));
                        } ?>
                        <div class="mkdf-pt-three-content-holder">
                            <div class="mkdf-pt-three-title-holder">
                                <h4 class="mkdf-pt-three-title">
                                <a itemprop="url" class="mkdf-pt-link" href="<?php echo esc_url(get_permalink()); ?>" target="_self"><?php echo hashmag_mikado_get_title_substring(get_the_title(), 300, $related_posts_title_size) ?></a>
                                </h4>
                            </div>
                            <div class="mkdf-pt-three-excerpt">
                                <?php /*?><?php hashmag_mikado_excerpt(7); ?><?php */?>
                            </div>
                            <div class="mkdf-pt-info-section clearfix">
                                <?php hashmag_mikado_post_info_author(array(
                                    'author' => 'no'
                                )) ?>
                                <?php hashmag_mikado_post_info_date(array(
                                    'date' => 'no',
                                    'date_format' => 'd M Y'
                                )) ?>
                            </div>
                        </div>
                    </div>
                </div>
			<?php endwhile; ?>
		</div>
	<?php endif; 
	wp_reset_postdata();
	?>
</div>




