INSERT
	INTO wp_postmeta (post_id, meta_key, meta_value)
	SELECT 
		ID AS post_id, 
		'_pronamic_subscription_id' AS meta_key,
		'1' AS meta_value /* <-- adjust this value to the subscription post ID */
	FROM 
		wp_posts
	WHERE 
		ID NOT IN (
			SELECT 
				post_id 
			FROM 
				wp_postmeta 
			WHERE 
				meta_key = '_pronamic_subscription_id'
		)
	AND
		post_type = 'post'
;