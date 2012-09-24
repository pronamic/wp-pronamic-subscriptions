<?php

function pronamic_subscription_can( $capability, $post_id = null ) {
	$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;

	$subscription_role = get_post_meta( $post_id, '_pronamic_subscription_role', true );

	$role = get_role( $subscription_role );

	if ( ! empty( $role ) ) {
		return $role->has_cap( $capability );
	} else {
		return false;
	}
}

function pronamic_subscription_the_price( ) {
	$price = get_post_meta( get_the_ID(), '_pronamic_subscription_price', true );

	if ( !empty( $price ) ) {
		echo '&euro; ', number_format( $price, 2, ',', '.' );
	}
}
