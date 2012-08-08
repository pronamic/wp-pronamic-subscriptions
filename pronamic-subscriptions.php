<?php
/*
Plugin Name: Pronamic Subscriptions
Plugin URI: http://pronamic.eu/wordpress/subscriptions/
Description: This plugin add some basic company directory functionality to WordPress

Version: 0.1
Requires at least: 3.0

Author: Pronamic
Author URI: http://pronamic.eu/

Text Domain: pronamic_subscriptions
Domain Path: /languages/

License: GPL
*/

/**
 * Register post type
 */
function pronamic_subscriptions_init() {
	$relPath = dirname( plugin_basename( __FILE__ ) ) . '/languages/';

	load_plugin_textdomain( 'pronamic_subscriptions', false, $relPath );

	register_post_type( 'pronamic_subs', array(
		'labels' => array(
			'name' => _x( 'Subscriptions', 'post type general name', 'pronamic_subscriptions' ) , 
			'singular_name' => _x( 'Subscription', 'post type singular name', 'pronamic_subscriptions' ) , 
			'add_new' => _x( 'Add New', 'pronamic_subscription', 'pronamic_subscriptions' ) , 
			'add_new_item' => __( 'Add New Subscription', 'pronamic_subscriptions' ) , 
			'edit_item' => __( 'Edit Subscription', 'pronamic_subscriptions' ) , 
			'new_item' => __( 'New Subscription', 'pronamic_subscriptions' ) , 
			'view_item' => __( 'View Subscription', 'pronamic_subscriptions' ) , 
			'search_items' => __( 'Search Subscriptions', 'pronamic_subscriptions' ) , 
			'not_found' =>  __( 'No subscriptions found', 'pronamic_subscriptions' ) , 
			'not_found_in_trash' => __( 'No subscriptions found in Trash', 'pronamic_subscriptions' ) ,  
			'parent_item_colon' => __( 'Parent Subscription:', 'pronamic_subscriptions' ) , 
			'menu_name' => _x( 'Subscriptions', 'menu_name', 'pronamic_subscriptions' )
		) , 
		'public' => true ,
		'publicly_queryable' => true ,
		'show_ui' => true ,
		'show_in_menu' => true ,
		'query_var' => true ,
		'capability_type' => 'post' ,
		'has_archive' => true ,
		'rewrite' => array( 'slug' => _x( 'subscriptions', 'slug', 'pronamic_subscriptions' ) ) , 
		'menu_icon' => plugins_url( 'admin/icons/subscription.png', __FILE__ ) , 
		'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'custom-fields' ) 
	));
}

add_action( 'init', 'pronamic_subscriptions_init' );
