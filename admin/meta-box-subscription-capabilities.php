<?php

global $post;

$subscription_role = get_post_meta( $post->ID, '_pronamic_subscription_role', true );

$role = get_role( $subscription_role );

if ( ! empty( $role ) ) {
	foreach ( $role->capabilities as $name => $active ) {
		echo $active ? '&#10004;' : '&#10063;';
		echo ' ';
		echo $name;
		echo ' ';
	}
}

?>
<p>
	<?php

	$name = $subscription_role;

	$edit_link = add_query_arg( array(
		'page' => 'roles' ,
		'action' => 'edit' , 
		'role' => $subscription_role
	), admin_url( 'users.php' ) );
	
	$edit_link = wp_nonce_url( $edit_link, 'members-component-action_edit-roles' );

	if ( current_user_can( 'edit_roles' ) ) : ?>
	
		<a href="<?php echo esc_url( $edit_link ); ?>" title="<?php printf( esc_attr__( 'Edit the %s role', 'members' ), $name ); ?>">
			<strong><?php _e( 'Edit Capabilities', 'pronamic_subscriptions' ); ?></strong>
		</a>

	<?php endif; ?>
</p>