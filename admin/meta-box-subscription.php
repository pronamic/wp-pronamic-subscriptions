<?php 

$id = get_the_ID();

wp_nonce_field( 'pronamic_subscription_save', 'pronamic_subscriptions_nonce' );

?>
<p>
	<label for="pronamic_subscription_id">
		<?php _e( 'Subscription', 'pronamic_subscriptions' ) ?>
	</label>

	<?php 
	
	$subscription_id = get_post_meta( $id, '_pronamic_subscription_id', true );

	$subscriptions = get_posts( array(
		'post_type' => 'pronamic_subs' ,
		'nopaging'  => true
	) );
	
	?>
	<select id="pronamic_subscription_id" name="pronamic_subscription_id">
		<option value=""><?php _e( '&mdash; No Subscription &mdash;', 'pronamic_subscriptions' ); ?></option>

		<?php foreach ( $subscriptions as $subscription ) : ?>
			<option value="<?php echo $subscription->ID; ?>" <?php selected( $subscription->ID, $subscription_id ); ?>><?php echo $subscription->post_title; ?></option>
		<?php endforeach; ?>
	</select>
</p>