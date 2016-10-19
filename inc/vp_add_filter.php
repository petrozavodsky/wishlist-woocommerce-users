<?php

class vp_add_filter {

	private $meta_field = "_wlvp_popularity";

	function __construct() {

		add_filter( 'woocommerce_catalog_orderby', array( $this, 'woocommerce_catalog_orderby' ) );
		add_action( 'save_post', array( $this, 'add_default_popularity' ) );

		add_action( 'vp_wishlist_post_remove', array( $this, 'remove_popularity' ) );
		add_action( 'vp_wishlist_add', array( $this, 'add_popularity' ) );

		add_action( 'woocommerce_product_query', array( $this, 'woocommerce_product_query' ) );
	}

	function woocommerce_product_query( $q, $class ) {
		if ( $_REQUEST['orderby'] == "_wlvp_popularity" ) {
			$q->set( 'orderby', array(
				'wlvp_popularity_exist'     => 'DESC',
				'wlvp_popularity_not_exist' => 'DESC',
				'post_date'                 => 'DESC',
				'ID'                        => 'DESC',

			) );

			$q->set( 'order', 'DESC' );

			$q->set( 'meta_query', array(
				'relation'                  => 'OR',
				'wlvp_popularity_exist'     => array(
					'key'     => $this->meta_field,
					'type'    => 'NUMERIC',
					'compare' => 'EXISTS',
				),
				'wlvp_popularity_not_exist' => array(
					'key'     => $this->meta_field,
					'type'    => 'NUMERIC',
					'compare' => 'NOT EXISTS',
				),

			) );
		}

	}

	function get_popularity( $post_id ) {
		$out = get_post_meta( $post_id, $this->meta_field, true );

		return $out;
	}

	function add_popularity( $post_id ) {
		$old = $this->get_popularity( $post_id );
		$old = intval( $old );
		$new = $old + 1;
		update_post_meta( $post_id, $this->meta_field, $new );
	}

	function remove_popularity( $post_id ) {
		$old = $this->get_popularity( $post_id );
		$new = $old - 1;
		if ( $new < 0 ) {
			$new = 0;
		}
		update_post_meta( $post_id, $this->meta_field, $new );
	}

	function woocommerce_catalog_orderby( $var ) {
		unset( $var['rating'] );
		$add_arr = array( $this->meta_field => "По рейтингу" );

		return $add_arr + $var;
	}

	function add_default_popularity( $post_id ) {
		if ( $parent_id = wp_is_post_revision( $post_id ) ) {
			$post_id = $parent_id;
		}
		remove_action( 'save_post', array( $this, 'add_default_popularity' ) );
		if ( get_post_meta( $post_id, $this->meta_field, true ) == '' ) {
			update_post_meta( $post_id, $this->meta_field, 0 );
		}
		add_action( 'save_post', array( $this, 'add_default_popularity' ) );
	}
}