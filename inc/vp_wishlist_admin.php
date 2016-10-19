<?php


/**
 ========================
 ADMIN SETTINGS
 ========================
 */

 if (!defined('ABSPATH')) {
	return;
}

function vp_wishlist_menu_settings(){
	add_menu_page('Wishlist Settings', 'Wishlist', 'manage_options', 'vp_wishlist', 'vp_wishlist_settings_page', 'dashicons-heart', 76);
	add_action('admin_init', 'vp_wishlist_custom_settings');
}

add_action('admin_menu', 'vp_wishlist_menu_settings');

// Settings Page

function vp_wishlist_settings_page(){
	require (plugin_dir_path(__FILE__) . 'vp_wishlist_settings_page.php');

}
// Custom Settings
function vp_wishlist_custom_settings(){
	
	register_setting(
		'vp-wishlist-group',
		'vp_wishlist_color_icon'
		);
	register_setting(
		'vp-wishlist-group',
		'vp_wishlist_position_icon'
		);
	register_setting(
		'vp-wishlist-group',
		'vp_wishlist_shortcode_page'
		);
	
	register_setting(
		'vp-wishlist-group',
		'vp_wishlist_single'
		);
	register_setting(
		'vp-wishlist-group',
		'vp_wishlist_pages'
		);
	add_settings_section(
		'vp_wishlist_style_section',
		'',
		'',
		'vp_wishlist'
		);
	add_settings_section(
		'vp_wishlist_settings_section',
		'',
		'',
		'vp_wishlist'
		);
	add_settings_field(
		'vp_wishlist_color_icon',
		'Select Color',
		'vp_wishlist_color_icon_callback',
		'vp_wishlist',
		'vp_wishlist_style_section'
		);
	add_settings_field(
		'vp_wishlist_position_icon',
		'Select Position',
		'vp_wishlist_position_icon_callback',
		'vp_wishlist',
		'vp_wishlist_style_section'
		);
	add_settings_field(
		'vp_wishlist_shortcode_page',
		'Select View Wishlist page',
		'vp_wishlist_shortcode_page_callback',
		'vp_wishlist',
		'vp_wishlist_settings_section'
		);
	
	add_settings_field(
		'vp_wishlist_single',
		'Enable wishlist button on Product page?',
		'vp_wishlist_single_callback',
		'vp_wishlist',
		'vp_wishlist_settings_section'
		);
	add_settings_field(
		'vp_wishlist_pages',
		'Add Wishlist button <br>(For woocommerce product shortcode)',
		'vp_wishlist_pages_callback',
		'vp_wishlist',
		'vp_wishlist_settings_section'
		);
}
// Custom Settings - Icon Style
$vp_wishlist_color_icon_value = sanitize_text_field(get_option('vp_wishlist_color_icon','red'));
if($vp_wishlist_color_icon_value == ''){$vp_wishlist_color_icon_value = 'red';}
function vp_wishlist_color_icon_callback(){
  global $vp_wishlist_color_icon_value;
  
  $vp_wishlist_style_icon_html = '<input type="text" name="vp_wishlist_color_icon" value="'.
                                    $vp_wishlist_color_icon_value.'"/>';
  $vp_wishlist_style_icon_html .= '<span class="dashicons dashicons-heart wishlist-icon" style="color:'                             .$vp_wishlist_color_icon_value.'"></span>';
  $vp_wishlist_style_icon_html .=  '<p class="description"><b>Default Color:</b> RED (#FF0000)</p>';
  echo $vp_wishlist_style_icon_html;
  

}
// Custom Settings - Icon Position
$vp_wishlist_position_icon_value = sanitize_text_field(get_option('vp_wishlist_position_icon','woocommerce_before_shop_loop_item+1'));
function vp_wishlist_position_icon_callback(){
	global $vp_wishlist_position_icon_value;
	?>
	<select name="vp_wishlist_position_icon">

	<?php $first_option = 'woocommerce_before_shop_loop_item+1'; ?>
    <option value="<?php echo $first_option ?>" <?php selected($vp_wishlist_position_icon_value,$first_option)?>>On Top</option>

    <?php $second_option = 'woocommerce_before_shop_loop_item_title+12'; ?>
    <option value="<?php echo $second_option ?>" <?php selected($vp_wishlist_position_icon_value,$second_option)?>>After Product Image</option>

	<?php $third_option = 'woocommerce_after_shop_loop_item_title+1'; ?>
    <option value="<?php echo $third_option ?>" <?php selected($vp_wishlist_position_icon_value,$third_option)?>>After Product Title</option>

    <?php $fourth_option = 'woocommerce_after_shop_loop_item_title+7'; ?>
    <option value="<?php echo $fourth_option ?>" <?php selected($vp_wishlist_position_icon_value,$fourth_option)?>>After Product Ratings</option>

    

    <?php $fifth_option = 'woocommerce_after_shop_loop_item_title+11'; ?>
    <option value="<?php echo $fifth_option ?>" <?php selected($vp_wishlist_position_icon_value,$fifth_option)?>>After Product Price</option>

    

    <?php $sixth_option = 'woocommerce_after_shop_loop_item_title+6'; ?>
    <option value="<?php echo $sixth_option ?>" <?php selected($vp_wishlist_position_icon_value,$sixth_option)?>>Inside Ratings Field</option>

    </select><div class="vp_wishlist_position_message"></div>

<?php
echo '<p class="description"><b>Default Position:</b> ON TOP</p>';
}

// Custom Settings - Product Page
$vp_wishlist_shortcode_page_value = sanitize_text_field(get_option('vp_wishlist_shortcode_page',get_option('vp_wishlist_activate_page')));
function vp_wishlist_shortcode_page_callback(){
	global $vp_wishlist_shortcode_page_value;
    $html = '<select name="vp_wishlist_shortcode_page">';
    $page_ids = get_all_page_ids();
    foreach($page_ids as $page_id){
    	$html .= '<option value="'.$page_id.'"';
        if($vp_wishlist_shortcode_page_value == $page_id){$html .= 'selected';}
    	$html .= '>'.get_the_title($page_id).'</option>';

    }
    $html .= '</select>';
    $html .= '<p class="description"><b>Default</b>: Wishlist</p>';
echo $html;

}

// Custom Settings - Wishlist Pages.
$vp_wishlist_pages_value = sanitize_text_field(get_option('vp_wishlist_pages'));
function vp_wishlist_pages_callback(){
	global $vp_wishlist_pages_value;

	$vp_wishlist_pages_html ='<input type="text" name="vp_wishlist_pages" value="'.$vp_wishlist_pages_value.'">';
	$vp_wishlist_pages_html .='<a href="#" class="vp-wishlist-pages-info">What\'s this?</a><br>';
	$vp_wishlist_pages_html .='<div class="vp-pages-info">If you are using woocommerce product shortcode [product ids] to display product & want to add wishlist button. Just enter the name of page where [product ids] shortcode is.</div>';
	$vp_wishlist_pages_html .= '<p class="description"><b>For eg:</b> page,another-page</p>';
	 echo $vp_wishlist_pages_html;
}
// Custom Settings - Product Page
$vp_wishlist_single_value = sanitize_text_field(get_option('vp_wishlist_single','true'));
function vp_wishlist_single_callback(){
	global $vp_wishlist_single_value;
	echo '<input type="checkbox" name="vp_wishlist_single" value="true" '.checked("true",$vp_wishlist_single_value,false).'><br>';
	
}




?>