<?php 

global $post;

wp_nonce_field( 'pronamic_subscription_save_details', 'pronamic_subscriptions_nonce' );

?>

<div>
	<label for="pronamic_subscription_price"><?php _e( 'Price:', 'pronamic_subscriptions' ); ?></label> <br />
	<input type="text" id="pronamic_subscription_price" name="pronamic_subscription_price" value="<?php echo get_post_meta( $post->ID, '_pronamic_subscription_price', true ); ?>" size="25" />
</div>

<div>
	<label for="pronamic_subscription_role"><?php _e( 'Role:', 'pronamic_subscriptions' ); ?></label> <br />
	
	<select id="pronamic_subscription_role" name="pronamic_subscription_role">
		<option value=""></option>

		<?php 
		
		$subscription_role = get_post_meta( $post->ID, '_pronamic_subscription_role', true );

		$editable_roles = get_editable_roles(); 
		
		foreach ( $editable_roles as $role => $details ) {
			$name = translate_user_role( $details['name'] );

			printf(
				'<option value="%s" %s>%s</option>' , 
				esc_attr( $role ) , 
				selected( $role, $subscription_role ) , 
				$name
			);
		}

		?>
	</select>

	<?php if ( current_user_can( 'create_roles' ) ) : ?>
	
		<a href="<?php echo admin_url( 'users.php?page=role-new' ); ?>" target="_blank">
			<?php  _e( 'Add New Role', 'pronamic_subscriptions' ); ?>
		</a>
	
	<?php endif; ?>
</div>