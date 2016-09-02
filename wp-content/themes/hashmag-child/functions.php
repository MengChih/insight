<?php

/*** Child Theme Function  ***/

function hashmag_mikado_child_theme_enqueue_scripts() {
	wp_register_style( 'childstyle', get_stylesheet_directory_uri() . '/style.css'  );
	wp_enqueue_style( 'childstyle' );
}

add_action('wp_enqueue_scripts', 'hashmag_mikado_child_theme_enqueue_scripts', 11);