<?php
if(!defined('ABSPATH')) exit;

class MikadofInstagramWidget extends WP_Widget {

	protected $params;

	public function __construct() {
		parent::__construct(
			'mkdf_instagram_widget',
			'Mikado Instagram Widget',
			array( 'description' => __( 'Display instagram images', 'mkd' ) )
		);

		$this->setParams();
	}

	protected function setParams() {
		$this->params = array(
			array(
				'name' => 'title',
				'type' => 'textfield',
				'title' => 'Title'
			),
			array(
				'name' => 'tag',
				'type' => 'textfield',
				'title' => 'Tag'
			),
			array(
				'name' => 'number_of_photos',
				'type' => 'textfield',
				'title' => 'Number of photos'
			),
			array(
				'name' => 'number_of_cols',
				'type' => 'dropdown',
				'title' => 'Number of columns',
				'options' => array(
					'2' => 'Two',
					'3' => 'Three',
					'4' => 'Four',
                    '5' => 'Four with Featured Image',
					'6' => 'Six',
					'7' => 'Seven',
					'9' => 'Nine',
				)
			),
			array(
				'name' => 'image_size',
				'type' => 'dropdown',
				'title' => 'Image Size',
				'options' => array(
					'thumbnail'             => 'Small',
					'low_resolution'        => 'Medium',
					'standard_resolution'   => 'Large'
				)
			),
			array(
				'name'    => 'space_between_cols',
				'type'    => 'dropdown',
				'title'   => 'Space Between Columns?',
				'options' => array(
					'yes' => 'Yes',
					'no'  => 'No'
				)
			),
			array(
				'name'    => 'show_overlay',
				'type'    => 'dropdown',
				'title'   => 'Show Overlay?',
				'options' => array(
					'yes' => 'Yes',
					'no'  => 'No'
				)
			),
			array(
				'name' => 'transient_time',
				'type' => 'textfield',
				'title' => 'Images Cache Time'
			),
		);
	}

	public function getParams() {
		return $this->params;
	}

	public function widget($args, $instance) {
		extract($instance);

		$title              = !empty($title) ? $title : '';
		$number_of_photos   = !(empty($number_of_photos)) ? $number_of_photos : '';
		$tag                = !empty($tag) ? $tag : '';
		$transient_time     = !empty($transient_time) ? $transient_time : '';
		$number_of_cols     = !empty($number_of_cols) ? $number_of_cols : '';
		$show_overlay       = !empty($show_overlay) ? $show_overlay : '';
		$space_between_cols = !empty($space_between_cols) ? $space_between_cols : '';
		$image_size         = !empty($image_size) ? $image_size : '';

		print $args['before_widget'];
		print $args['before_title'].$title.$args['after_title'];

		$instagram_api = MikadofInstagramApi::getInstance();
		$images_array = $instagram_api->getImages($number_of_photos, $tag, array(
			'use_transients' => true,
			'transient_name' => $args['widget_id'],
			'transient_time' => $transient_time
		));

		$number_of_cols     = $number_of_cols == '' ? 3 : $number_of_cols;
		$space_between_cols = $space_between_cols == 'yes' ? '' : 'without-space';
		$show_overlay       = $show_overlay == 'yes' ? true : false;

		if(is_array($images_array) && count($images_array)) {

            if ($number_of_cols == '5') { $i = 0; ?>

                <ul class="mkdf-instagram-feed clearfix mkdf-featured-instagram <?php echo esc_attr($space_between_cols); ?>">
                    <?php
                    foreach ($images_array as $image) { ?>
                        <li>
                            <div class="mkdf-instagram-item-holder">

                                <a href="<?php echo esc_url($instagram_api->getHelper()->getImageLink($image)); ?>"
                                   target="_blank">
                                    <?php if ($show_overlay) : ?>

                                        <div class="mkdf-instagram-overlay">
                                            <div class="mkdf-instagram-overlay-inner">
                                                <div class="mkdf-instagram-overlay-inner2">
                                                    <span class="overlay-icon social-instagram"></span>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endif;

                                    if($i++ == 0) {
                                        echo mkdf_instagram_feed_kses_img($instagram_api->getHelper()->getImageHTML($image, 'standard_resolution'));
                                    } else {
                                        echo mkdf_instagram_feed_kses_img($instagram_api->getHelper()->getImageHTML($image, 'low_resolution'));
                                    } ?>
                                </a>
                            </div>
                        </li>
                    <?php } ?>
                </ul>


            <?php } else { ?>

                <ul class="mkdf-instagram-feed clearfix mkdf-col-<?php echo esc_attr($number_of_cols); ?> <?php echo esc_attr($space_between_cols); ?>">
                    <?php
                    foreach ($images_array as $image) { ?>
                        <li>
                            <div class="mkdf-instagram-item-holder">

                                <a href="<?php echo esc_url($instagram_api->getHelper()->getImageLink($image)); ?>"
                                   target="_blank">
                                    <?php if ($show_overlay) : ?>

                                        <div class="mkdf-instagram-overlay">
                                            <div class="mkdf-instagram-overlay-inner">
                                                <div class="mkdf-instagram-overlay-inner2">
                                                    <span class="overlay-icon social-instagram"></span>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endif; ?>
                                    <?php echo mkdf_instagram_feed_kses_img($instagram_api->getHelper()->getImageHTML($image, $image_size)); ?>
                                </a>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            <?php }
        }

		print $args['after_widget'];
	}

	public function form($instance) {
		foreach ($this->params as $param_array) {
			$param_name = $param_array['name'];
			${$param_name} = isset( $instance[$param_name] ) ? esc_attr( $instance[$param_name] ) : '';
		}

		//user has connected with instagram. Show form
		if(get_option('mkdf_instagram_code')) {
			foreach ($this->params as $param) {
				switch($param['type']) {
					case 'textfield':
						?>
						<p>
							<label for="<?php echo esc_attr($this->get_field_id( $param['name'] )); ?>"><?php echo
								esc_html($param['title']); ?></label>
							<input class="widefat" id="<?php echo esc_attr($this->get_field_id( $param['name'] )); ?>" name="<?php echo esc_attr($this->get_field_name( $param['name'] )); ?>" type="text" value="<?php echo esc_attr( ${$param['name']} ); ?>" />
						</p>
						<?php
						break;
					case 'dropdown':
						?>
						<p>
							<label for="<?php echo esc_attr($this->get_field_id( $param['name'] )); ?>"><?php echo
								esc_html($param['title']); ?></label>
							<?php if(isset($param['options']) && is_array($param['options']) && count($param['options'])) { ?>
								<select class="widefat" name="<?php echo esc_attr($this->get_field_name( $param['name'] )); ?>" id="<?php echo esc_attr($this->get_field_id( $param['name'] )); ?>">
									<?php foreach ($param['options'] as $param_option_key => $param_option_val) {
										$option_selected = '';
										if(${$param['name']} == $param_option_key) {
											$option_selected = 'selected';
										}
										?>
										<option <?php echo esc_attr($option_selected); ?> value="<?php echo esc_attr($param_option_key); ?>"><?php echo esc_attr($param_option_val); ?></option>
									<?php } ?>
								</select>
							<?php } ?>
						</p>

						<?php
						break;
				}
			}
		} else { ?>
			<p><?php esc_html_e('You haven\'t connected with Instagram. Please go to Mikado Options -> Social page and connect with your Instagram account.', 'mkd_instagram_feed'); ?></p>
		<?php }
	}
}

function mkdf_instagram_widget_load(){
	register_widget('MikadofInstagramWidget');
}

add_action('widgets_init', 'mkdf_instagram_widget_load');