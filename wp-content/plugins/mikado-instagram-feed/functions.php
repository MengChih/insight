<?php

if(!function_exists('mkdf_instagram_feed_kses_img')) {
	/**
     * @param $content
     *
     * @return string
     */
    function mkdf_instagram_feed_kses_img($content) {
        $img_atts = apply_filters('mkdf_instagram_feed_kses_img_atts', array(
            'src' => true,
            'alt' => true,
            'height' => true,
            'width' => true,
            'class' => true,
            'id' => true,
            'title' => true
        ));

        return wp_kses($content, array(
            'img' => $img_atts
        ));
    }
}