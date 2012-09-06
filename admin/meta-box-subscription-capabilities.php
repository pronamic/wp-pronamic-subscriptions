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

if ( pronamic_subscription_can( 'manage_options' ) ) {
	echo '<p>Jep, kan opties beheren.</p>';
} else {
	echo '<p>Nope, kan geen opties beheren.</p>';
}
