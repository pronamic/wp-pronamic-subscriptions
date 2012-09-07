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

class Pronamic_Subscriptions_Plugin {
	/**
	 * Bootstrap
	 */
	public static function bootstrap() {
		add_action( 'init',           array( __CLASS__, 'init' ) );
		add_action( 'admin_init',     array( __CLASS__, 'admin_init' ) );
	}

	/**
	 * Initialize
	 */
	function init() {
		// Text domain
		$rel_path = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
	
		load_plugin_textdomain( 'pronamic_subscriptions', false, $rel_path );
	
		// Includes
		require_once 'pronamic-subscriptions-template.php';
	
		// Post type
		// http://codex.wordpress.org/Function_Reference/register_post_type
		// max. 20 characters, can not contain capital letters or spaces
		register_post_type( 'pronamic_subs', array(
			'labels'             => array(
				'name'               => _x( 'Subscriptions', 'post type general name', 'pronamic_subscriptions' ) , 
				'singular_name'      => _x( 'Subscription', 'post type singular name', 'pronamic_subscriptions' ) , 
				'add_new'            => _x( 'Add New', 'pronamic_subscription', 'pronamic_subscriptions' ) , 
				'add_new_item'       => __( 'Add New Subscription', 'pronamic_subscriptions' ) , 
				'edit_item'          => __( 'Edit Subscription', 'pronamic_subscriptions' ) , 
				'new_item'           => __( 'New Subscription', 'pronamic_subscriptions' ) , 
				'view_item'          => __( 'View Subscription', 'pronamic_subscriptions' ) , 
				'search_items'       => __( 'Search Subscriptions', 'pronamic_subscriptions' ) , 
				'not_found'          => __( 'No subscriptions found', 'pronamic_subscriptions' ) , 
				'not_found_in_trash' => __( 'No subscriptions found in Trash', 'pronamic_subscriptions' ) , 
				'parent_item_colon'  => __( 'Parent Subscription:', 'pronamic_subscriptions' ) , 
				'menu_name'          => _x( 'Subscriptions', 'menu_name', 'pronamic_subscriptions' ) 
			) , 
			'public'             => true ,
			'publicly_queryable' => true ,
			'show_ui'            => true ,
			'show_in_menu'       => true ,
			'query_var'          => true ,
			'capability_type'    => 'post' ,
			'has_archive'        => true ,
			'rewrite'            => array( 'slug' => _x( 'subscriptions', 'slug', 'pronamic_subscriptions' ) ) , 
			'menu_icon'          => plugins_url( 'admin/icons/subscription.png', __FILE__ ) , 
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'custom-fields' ) 
		));
	}

	/**
	 * Admin initialize
	 */
	public static function admin_init() {
		add_filter( 'manage_pronamic_subs_posts_columns',       array( __CLASS__, 'manage_posts_columns' ) );
		add_filter( 'manage_pronamic_subs_posts_custom_column', array( __CLASS__, 'manage_custom_column' ), 10, 2 );

		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
	
		add_action( 'save_post',      array( __CLASS__, 'save_post' ) );
	}

	/**
	 * Add meta boxes
	 */
	public static function add_meta_boxes() {
		add_meta_box( 
			'pronamic_subscription_meta_box_details', // id
			__( 'Subscription Details', 'pronamic_subscriptions' ), // title
			array( __CLASS__, 'meta_box_subscription_details' ), // callback
			'pronamic_subs', // post_type
			'side' , // context
			'high' // priority
    	);

		add_meta_box( 
			'pronamic_subscription_meta_box_capabilities', // id
			__( 'Subscription Capabilities', 'pronamic_subscriptions' ), // title
			array( __CLASS__, 'meta_box_subscription_capabilities' ), // callback
			'pronamic_subs', // post_type 
			'normal', // context
			'high' // priority
    	);
	}

	/**
	 * Meta box subscription details
	 */
	public static function meta_box_subscription_details() {
		include 'admin/meta-box-subscription-details.php';
	}

	/**
	 * Meta box subscription details
	 */
	public static function meta_box_subscription_capabilities() {
		include 'admin/meta-box-subscription-capabilities.php';
	}

	/**
	 * Save post
	 */
	function save_post( $post_id ) {
		global $post;
	
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;
	
		if( ! isset( $_POST['pronamic_subscriptions_nonce'] ) )
			return;
	
		if( ! wp_verify_nonce( $_POST['pronamic_subscriptions_nonce'], 'pronamic_subscriptions_save_details' ) )
			return;
	
		if( ! current_user_can( 'edit_post', $post_id ) )
			return;
			
		// Go
		$price = filter_input( INPUT_POST, 'pronamic_subscription_price', FILTER_SANITIZE_NUMBER_FLOAT );
		$role = filter_input(  INPUT_POST, 'pronamic_subscription_role',  FILTER_SANITIZE_STRING );
		
		// Save data
		update_post_meta( $post_id, '_pronamic_subscription_price', $price );
		update_post_meta( $post_id, '_pronamic_subscription_role',  $role );
	}
	
	/**
	 * Add admin columns
	 */
	public static function manage_posts_columns( $column ) {
	    $column['pronamic_subscription_role'] = __( 'Role', 'pronamic_subscriptions' );
	    $column['pronamic_subscription_price'] = __( 'Price', 'pronamic_subscriptions' );
	 
	    return $column;
	}
	
	/**
	 * Add admin rows
	 */
	public static function manage_custom_column( $column_name, $post_id ) {
	    switch ( $column_name ) {
	        case 'pronamic_subscription_role' :
	        	global $wp_roles;

	        	$role = get_post_meta( $post_id, '_pronamic_subscription_role', true );
	        	
	        	if ( ! empty( $role ) ) {
		        	$role_name = isset( $wp_roles->role_names[$role] ) ? translate_user_role( $wp_roles->role_names[$role] ) : __( 'None', 'pronamic_subscriptions' );
	
		        	if ( current_user_can( 'edit_roles' ) ) {
						$edit_link = add_query_arg( array(
							'page' => 'roles' ,
							'action' => 'edit' , 
							'role' => $role
						), admin_url( 'users.php' ) );
		
						$edit_link = wp_nonce_url( $edit_link, 'members-component-action_edit-roles' );
	
		        		printf(
		        			'<a href="%s" title="%s" target="_blank">%s</a>', 
		        			esc_url( $edit_link ) , 
		        			sprintf( esc_attr__( 'Edit the %s role', 'pronamic_subscriptions' ), $role_name ),
		        			$role_name
		        		);
		        	} else {
		        		echo $role_name;
		        	}
	        	}
	
	            break;
	        case 'pronamic_subscription_price' :
	            $price = get_post_meta( $post_id, '_pronamic_subscription_price', true );

	            if ( ! empty( $price ) ) {
	            	echo number_format( $price, 2 );
	            }

	            break;
	    }
	}
}

Pronamic_Subscriptions_Plugin::bootstrap();
