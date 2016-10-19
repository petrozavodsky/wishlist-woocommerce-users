 <?php if (!defined('ABSPATH')) {
	return;
}
?>

<h1>Wishlist WooCommerce Settings</h1>
<hr>
<h3> Use shortcode [vp_wishlist] to display products wishlist.</h3>
<form method="post" action="options.php">
<?php settings_fields('vp-wishlist-group'); ?>
<?php do_settings_sections('vp_wishlist'); ?>
<?php submit_button(); ?>
</form>