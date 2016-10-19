<?php
/**
 * Plugin name: Wishlist WooCommerce users
 * Plugin URI: https://alkoweb.ru
 * Author: petrozavodsky
 * Autor URI: https://alkoweb.ru
 * Version: 1.0.0
 * Description: Очень не бета версия (НОГАМИ НЕ ПИНАТЬ , СРАЗУ ПРЕДУПРЕЖДАЮ)
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

include( plugin_dir_path( __FILE__ ) . 'inc/vp_wishlist_enqueue.php' );
include( plugin_dir_path( __FILE__ ) . 'inc/vp_wishlist_admin.php' );
require_once( plugin_dir_path( __FILE__ ) . 'vp_wishlist_core.php' );
require_once( plugin_dir_path( __FILE__ ) . 'inc/vp_add_filter.php' );
new vp_add_filter();
new vp_wishlist_core( __FILE__ );

//Dialog Box
function vp_wishlist_dialogbox() {
	global $vp_wishlist_shortcode_page_value;

	$vp_dialogbox = '<div class="vp-wishlist-dialog">';
	$vp_dialogbox .= '<div class="vp-dialog-icon icon-heart vp-middle"></div>';
	$vp_dialogbox .= '<div class="vp-dialog-close icon-cross"></div>';
	$vp_dialogbox .= '<div class="vp-dialog-info vp-middle">Product added to wishlist <br> <a href ="' . get_permalink( $vp_wishlist_shortcode_page_value ) . '" style = "text-decoration: underline">View Wishlist</a></div>';
	$vp_dialogbox .= '</div>';
	echo $vp_dialogbox;

}

add_action( 'wp_head', 'vp_wishlist_dialogbox' );

//Creating page on first time activation
function vp_wishlist_activate_option() {

	add_option( 'vp_wishlist_activate', 'vp_wishlist' );
	add_option( 'vp_wishlist_activate_page', '' );


}

register_activation_hook( __FILE__, 'vp_wishlist_activate_option' );
function vp_activate_function() {
	if ( get_option( 'vp_wishlist_activate_page' ) == null ) {
		$vp_page = array(

			'post_status'  => 'publish',
			'post_title'   => 'Wishlist',
			'post_type'    => 'page',
			'post_content' => '[vp_wishlist]'
		);
		//insert page and save the id
		$pageid = wp_insert_post( $vp_page, false );
		update_option( 'vp_wishlist_activate_page', $pageid );
	}
}

function vp_wishlist_activate() {

	if ( is_admin() && get_option( 'vp_wishlist_activate' ) == 'vp_wishlist' ) {

		delete_option( 'vp_wishlist_activate' );


		add_action( 'admin_head', 'vp_activate_function' );
	}
}

add_action( 'admin_init', 'vp_wishlist_activate' );


//Enqueue Scripts
function vp_enqueue_scripts() {
	if ( isset( $_COOKIE['vp_wishlist_cookie'] ) ) {
		$vp_set_wishlist_cookie = $_COOKIE['vp_wishlist_cookie'];
	} else {
		$vp_set_wishlist_cookie = '';
	}

	wp_enqueue_style( 'vp_wishlist_style_font', 'https://fonts.googleapis.com/css?family=Raleway:600,400' );
	wp_enqueue_style( 'vp_wishlist_style', plugins_url( 'assets/css/wishlist_style.css', __FILE__ ) );
	wp_enqueue_script( 'vp_wishlist_js', plugins_url( 'assets/js/wishlist_js.js', __FILE__ ), array(
		'jquery'
	), '1.0.0', true );
	wp_localize_script( 'vp_wishlist_js', 'vp_data', array(
		'adminurl'  => admin_url() . 'admin-ajax.php',
		// Generating Product ids For jquery (adding active class)
		'button_id' => str_replace( ',', ',#vp-wishlist-', $vp_set_wishlist_cookie )
	) );

}

add_action( 'wp_enqueue_scripts', 'vp_enqueue_scripts' );
//Closing woocommerce product link
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 11 );

function vp_wishlist_button_html() {

	$count            = get_post_meta( get_the_ID(), '_wlvp_popularity', true );
	$class_count_null = '';
	if ( $count < 1 ) {
		$class_count_null = 'vp-wishlist-count-null';
	}

	if ( is_user_logged_in() ) {
		$class_user_logeded_in = 'vp_wishlist_login';
	}else{
		$class_user_logeded_in = 'vp_wishlist_no-login';
	}

	$class                   = apply_filters( 'vp_wishlist_add_class_active', $class_user_logeded_in, get_the_ID() );

	$vp_wishlist_button_html = '<div id = "vp-wishlist-' . get_the_ID() . '" data-post-id= "' . get_the_ID() . '" class="vp_wishlist_add vp_wishlist_button icon-heart ' . $class . '">';

	$vp_wishlist_button_html .= "<div class='vp-wishlist-count {$class_count_null}'>{$count}</div>";

	if ( is_single() ) {
		$vp_wishlist_button_html .= '<div class="vp-wishlist-text">Add to Wishlist</div>';
	}
	$vp_wishlist_button_html .= '</div>';

	return $vp_wishlist_button_html;
}

// Displaying Wishlist button
function vp_wishlist_button() {
	global $vp_wishlist_pages_value; //vp_wishlist_admin.php 

	if ( ! is_single() && ( is_shop() || is_product_category() || is_page( explode( ',', $vp_wishlist_pages_value ) ) ) ) {
		echo vp_wishlist_button_html();


	}
	remove_action( 'vp_wishlist_button', 'woocommerce_template_loop_product_link_close', 1 );
}

// Changing Wishlist Button Positions.
global $vp_wishlist_position_icon_value; //vp_wishlist_admin.php
$vp_wishlist_woocommerce_hook_position = substr( $vp_wishlist_position_icon_value, strpos( $vp_wishlist_position_icon_value, '+' ) + 1 );
$vp_wishlist_woocommerce_hook          = substr( $vp_wishlist_position_icon_value, 0, strpos( $vp_wishlist_position_icon_value, '+' ) );

// $vp_wishlist_woocommerce_hook_position == Position (Setting Positions)
add_action( $vp_wishlist_woocommerce_hook, 'vp_wishlist_button', $vp_wishlist_woocommerce_hook_position );

//Wishlist on single-page
function vp_wishlist_button_singlepage() {
	global $vp_wishlist_single_value; //vp_wishlist_admin.php 

	if ( is_single() && $vp_wishlist_single_value == "true" ) {
		echo vp_wishlist_button_html();
	}


}

add_action( 'woocommerce_single_product_summary', 'vp_wishlist_button_singlepage', 6 );

// Adding Front end CSS to Head.
function vp_wishlist_head_style() {
	global $vp_wishlist_woocommerce_hook;
	global $vp_wishlist_color_icon_value;
	global $vp_wishlist_woocommerce_hook_position;
	$style = '<style>
          .active{color: ' . $vp_wishlist_color_icon_value . ';}';

	// Inside Rating Style.
	$style .= $vp_wishlist_woocommerce_hook == 'woocommerce_after_shop_loop_item_title' && $vp_wishlist_woocommerce_hook_position == 6 ? '.star-rating{display:inline-block!important;}.vp_wishlist_button{display: inline-block;}' : '';

// On Top Style.
	$style .= $vp_wishlist_woocommerce_hook == 'woocommerce_before_shop_loop_item' && $vp_wishlist_woocommerce_hook_position == 1 ? '.vp_wishlist_button{text-align: right;}' : '';
	$style .= '</style>';
	echo $style;
}

add_action( 'wp_head', 'vp_wishlist_head_style' );

?>