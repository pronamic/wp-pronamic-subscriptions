<?php

/**
 * Gravity Forms - Field advanced settings
 *
 * @param int $position
 * @param int $form_id
 */
function pronamic_subscriptions_gform_field_advanced_settings( $position, $form_id ) {
	if ( $position == 450 ) : ?>

		<li class="product_field_type_setting field_setting" style="display: list-item;">
			<input type="checkbox" id="pronamic_populate_subscriptions" onclick="SetFieldProperty('populateSubscriptions', this.checked); ToggleInputName();" />

			<label for="pronamic_populate_subscriptions" class="inline">
				<?php _e( 'Populate with Subscriptions', 'pronamic_subscriptions' ); ?>
			</label>
		</li>

	<?php endif;
}

add_action( 'gform_field_advanced_settings', 'pronamic_subscriptions_gform_field_advanced_settings', 10, 2 );

/**
 * Gravity Forms - Editor JavaScript
 */
function pronamic_subscriptions_gform_editor_js() {
	?>
	<script type="text/javascript">
		jQuery(document).bind("gform_load_field_settings", function(event, field, form) {
			if(field.type == "product") {
				var populateSubscriptions = typeof field.populateSubscriptions == "boolean" ? field.populateSubscriptions : false;
				jQuery("#pronamic_populate_subscriptions").prop("checked", populateSubscriptions);
			}
		});
	</script>
	<?php
}

add_action( 'gform_editor_js', 'pronamic_subscriptions_gform_editor_js' );

/**
 * We search the Gravity Forms form for subscription fields
 *
 * @param array $form
 * @return array
 */
function pronamic_subscriptions_gform_find_subscription_fields( $form ) {
	$form['pronamic_subscription_fields'] = array();

	foreach ( $form['fields'] as &$field ) {
		if ( isset( $field['populateSubscriptions'] ) ) {
			$populate_subscriptions = filter_var( $field['populateSubscriptions'], FILTER_VALIDATE_BOOLEAN );

			if ( $populate_subscriptions ) {
				// We adjust the field type so 'Gravity Forms' handles it correctly
				// The 'Gravity Forms Update Post' plugin also requires this
				if ( ! is_admin() ) {
					$field['type']                  = 'post_custom_field';
				}

				$field['postCustomFieldName']   = '_pronamic_subscription_id';
				$field['postCustomFieldUnique'] = true;

				$form['pronamic_subscription_fields'][] = $field;
			}
		}
	}

	return $form;
}

add_filter( 'gform_pre_submission_filter', 'pronamic_subscriptions_gform_find_subscription_fields' );
add_filter( 'gform_admin_pre_render',      'pronamic_subscriptions_gform_find_subscription_fields' );
add_filter( 'gform_pre_render',            'pronamic_subscriptions_gform_find_subscription_fields' );

/**
 * Gravity Forms - Populate subscription
 *
 * @param array $form
 */
function pronamic_subscriptions_gform_populate_subscriptions( $form ) {
	foreach ( $form['pronamic_subscription_fields'] as &$field ) {
		// Make sure we only get subscriptions once
		if ( ! isset( $subscriptions ) ) {
			$subscriptions = get_posts( array(
				'post_type' => 'pronamic_subs',
				'nopaging'  => true,
				'orderby'   => 'menu_order',
				'order'     => 'ASC'
			) );
		}

		// Build new choices array
		$field['choices'] = array();

		foreach ( $subscriptions as $subscription ) {
			$field['choices'][] = array(
				'text'       => $subscription->post_title,
				'value'      => '' . $subscription->ID,
				'price'      => pronamic_subscription_get_price( $subscription->ID ),
				'isSelected' => false
			);
		}
	}

	return $form;
}

add_filter( 'gform_admin_pre_render', 'pronamic_subscriptions_gform_populate_subscriptions' );
add_filter( 'gform_pre_render',       'pronamic_subscriptions_gform_populate_subscriptions' );

/**
 * Gravity Forms - Post data
 *
 * @param array $post_data
 * @param array $form
 * @param array $lead
 */
function pronamic_subscriptions_gform_post_data( $post_data, $form, $lead ) {
	foreach ( $form['pronamic_subscription_fields'] as $field ) {
		$value = RGFormsModel::get_field_value( $field );

		// Value is in '$value|$price' notation (for example: 2074|15)
		$separator_position = strpos( $value, '|' );
		if ( $separator_position !== false ) {
			$value = substr( $value, 0, $separator_position );
			$price = substr( $value, $separator_position + 1 );
		}

		$post_data['post_custom_fields']['_pronamic_subscription_id'] = $value;
	}

	return $post_data;
}

add_filter( 'gform_post_data', 'pronamic_subscriptions_gform_post_data', 10, 3 );
