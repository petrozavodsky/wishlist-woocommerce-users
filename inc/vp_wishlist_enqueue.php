<?php

/**
==========================
     Enqueue Scripts
==========================
*/
if (!defined('ABSPATH')) {
	return;
}
function vp_wishlist_admin_script($hook){
	if('toplevel_page_vp_wishlist' != $hook){
		return;
	}
	wp_enqueue_style('vp_wishlist_admin_css',plugins_url('/css/vp_wishlist_admin_css.css',__FILE__));
	wp_enqueue_script('vp_wishlist_admin_js',plugins_url('/js/vp_wishlist_admin_js.js',__FILE__),array('jquery'),'1.0.0',true);
	wp_localize_script('vp_wishlist_admin_js','vp_admin_localize',
		array(
			'ajaxurl' =>admin_url(). 'admin-ajax.php'
		));
}
add_action( 'admin_enqueue_scripts', 'vp_wishlist_admin_script' );
?>