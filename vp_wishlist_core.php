<?php

if (!defined('ABSPATH')) {
    return;
}

class vp_wishlist_core
{
    private $file;
    private $field = 'vp_wishlist';

    public function __construct($file)
    {
        add_action('wp_ajax_wishlist_trigger', array($this, 'ajax_add_item_to_wishlist'));
        add_action('wp_ajax_nopriv_wishlist_trigger', array($this, 'redirect_to_register'));


        add_action('wp_ajax_wishlist_trigger_remove', array($this, 'ajax_add_item_to_wishlist'));

        add_shortcode('vp_wishlist', array($this, 'shortcode'));
        add_shortcode('xoo_wishlist', array($this, 'shortcode'));

        add_filter('vp_wishlist_add_class_active', array($this, 'hthml_class_active'), 10, 3);

        add_action('template_redirect', array($this, 'redirect_target_wishlist_action'));
    }

    function redirect_to_register()
    {

        $arr['redirect_url'] = apply_filters('vp_wishlist_register_page', get_post_permalink(get_option('woocommerce_myaccount_page_id')));

        wp_send_json($arr);
    }

    function hthml_class_active($var, $post_id)
    {

        if (wp_get_current_user()->exists()) {
            $current_user = wp_get_current_user();
            $user_id = $current_user->ID;
            $list = $this->get_user_wishlist($user_id);
            if (is_array($list) && array_key_exists($post_id, $list)) {
                return $var.' active';
            }

        }

        return $var;
    }

    function ajax_add_item_to_wishlist()
    {
        $request = $_REQUEST;
        $item_id = $request['post_id'];
        $out_arr = array();
        $current = false;

        if ($request['type'] == 'update') {
            $result_update = $this->update_user_wishlist($item_id);
            if (array_key_exists($item_id, $result_update)) {
                $current = true;
            }
            $out_arr = array('items' => $result_update, 'current' => $current);
        } elseif ($request['type'] == 'remove') {
            $result_update = $this->remove_user_wishlist($item_id);

            if (array_key_exists($item_id, $result_update)) {
                $current = true;
            }
            $out_arr = array('items' => $result_update, 'current' => $current);
        }

        wp_send_json($out_arr);
    }

    function remove_user_wishlist($value)
    {

        if (wp_get_current_user()->exists()) {
            $current_user = wp_get_current_user();
            $user_id = $current_user->ID;
            $old = $this->get_user_wishlist($user_id);

            if (is_array($old)) {
                unset($old[$value]);
            }
            do_action('vp_wishlist_post_remove', $value);
            update_user_meta($user_id, $this->field, $old);

            return $old;
        } else {
            return 'login';
        }

    }

    function update_user_wishlist($value)
    {

        if (wp_get_current_user()->exists()) {
            $current_user = wp_get_current_user();
            $user_id = $current_user->ID;
            $old = $this->get_user_wishlist($user_id);

            if (array_key_exists($value, $old)) {
                unset($old[$value]);
                do_action('vp_wishlist_post_remove', $value);
            } else {
                $old[$value] = $value;
                do_action('vp_wishlist_add', $value);
            }

            update_user_meta($user_id, $this->field, $old);

            return $old;
        } else {
            return 'login';
        }

    }

    public function get_user_wishlist($user_id)
    {
        $user_id = intval($user_id);
        $result = get_user_meta($user_id, $this->field, true);
        if ($result == '') {
            return false;
        } elseif (is_array($result) && count($result) < 1) {
            return false;
        }

        return $result;
    }

    public function get_user_wishlist_shorcode_ids($user_id)
    {
        $arr = $this->get_user_wishlist($user_id);
        if (is_array($arr) && count($arr) > 0) {
            return implode($arr, ',');
        }

        return false;
    }


    function vp_wishlist_remove_button()
    {
        if (is_user_logged_in()) {

            $current_user = wp_get_current_user();
            $user_id = $current_user->ID;
            if ($user_id == $_REQUEST['uid']) {
                echo '<div class="vp_wishlist_remove vp-wishlist-remove-cross vp_wishlist_button icon-cross" remove-id =  "' . get_the_ID() . '" ></div>';
            }
        }
    }

    function shortcode()
    {
        add_action('woocommerce_before_shop_loop_item', array($this, 'vp_wishlist_remove_button'), 1);
        remove_action('woocommerce_after_shop_loop_item_title', 'vp_wishlist_button');
        $ids = false;

        if ($_REQUEST['uid'] != null) {
            $ids = $this->get_user_wishlist_shorcode_ids(intval($_REQUEST['uid']));
        } else {
            if (wp_get_current_user()->exists()) {
                $current_user = wp_get_current_user();
                $ids = $this->get_user_wishlist_shorcode_ids($current_user->ID);
            }
        }

        if ($ids === false) {
            return 'No Product Found';

        } else {
            $ids = esc_attr($ids);
            $vp_wishlist_elements = '<div class="vp_wishlist_products">';
            $vp_wishlist_elements .= '<div class ="vp_wishlist_message"></div>';
            $vp_wishlist_elements .= do_shortcode("[products ids='{$ids}']");
            $vp_wishlist_elements .= '</div>';

            return $vp_wishlist_elements;

        }

    }


    function redirect_target_wishlist_action()
    {
        global $post;


        if (wp_get_current_user()->exists()) {
            $current_user = wp_get_current_user();
            if ($_REQUEST['uid'] == null) {
                if (has_shortcode($post->post_content, 'vp_wishlist') || has_shortcode($post->post_content, 'xoo_wishlist')) {
                    $post_link = get_post_permalink($post->ID);
                    $post_link = add_query_arg(array('uid' => $current_user->ID,), $post_link);
                    wp_redirect($post_link, 301);
                }
            }
        }

    }

}




