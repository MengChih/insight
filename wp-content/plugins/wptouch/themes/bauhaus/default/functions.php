<?php

add_action( 'foundation_enqueue_scripts', 'bauhaus_enqueue_scripts' );
add_filter( 'amp_should_show_featured_image_in_header', 'bauhaus_should_show_thumbnail' );
add_filter( 'foundation_featured_show', 'bauhaus_show_featured_slider', 10, 2 );

function bauhaus_enqueue_scripts() {
	wp_enqueue_script(
		'bauhaus-js',
		BAUHAUS_URL . '/default/bauhaus.js',
		array( 'jquery' ),
		BAUHAUS_THEME_VERSION,
		true
	);
}

function bauhaus_should_show_thumbnail() {
	$settings = bauhaus_get_settings();

	switch( $settings->bauhaus_use_thumbnails ) {
		case 'none':
			return false;
		case 'index':
			return is_home();
		case 'index_single':
			return is_home() || is_single();
		case 'index_single_page':
			return is_home() || is_single() || is_page();
		case 'all':
			return is_home() || is_single() || is_page() || is_archive() || is_search();
		default:
			// in case we add one at some point
			return false;
	}
}

function bauhaus_should_show_taxonomy() {
	$settings = bauhaus_get_settings();

	if ( $settings->bauhaus_show_taxonomy ) {
		return true;
	} else {
		return false;
	}
}

function bauhaus_should_show_date(){
	$settings = bauhaus_get_settings();

	if ( $settings->bauhaus_show_date ) {
		return true;
	} else {
		return false;
	}
}

function bauhaus_should_show_author(){
	$settings = bauhaus_get_settings();

	if ( $settings->bauhaus_show_author ) {
		return true;
	} else {
		return false;
	}
}

function bauhaus_should_show_search(){
	$settings = bauhaus_get_settings();

	if ( $settings->bauhaus_show_search ) {
		return true;
	} else {
		return false;
	}
}

function bauhaus_should_show_comment_bubbles(){
	$settings = bauhaus_get_settings();

	if ( $settings->bauhaus_show_comment_bubbles ) {
		return true;
	} else {
		return false;
	}
}

function bauhaus_is_menu_position_default(){
	$settings = bauhaus_get_settings();

	if ( $settings->bauhaus_menu_position == 'left-side' ) {
		return true;
	} else {
		return false;
	}
}

function bauhaus_show_featured_slider( $show_featured_slider, $featured_slider_enabled ) {
	if ( bauhaus_allow_featured_slider_override() ) {
		$settings = bauhaus_get_settings();

		global $post;
		if ( $settings->featured_slider_page !== false && $post->ID == $settings->featured_slider_page ) {
			$show_featured_slider = true;
		} elseif ( $settings->featured_slider_page == true ) {
			$show_featured_slider = false;
		}
	}

	return $show_featured_slider;
}
