UPDATE
	wp_posts AS post
		LEFT JOIN
	wp_postmeta AS postmeta
			ON postmeta.post_id = post.ID
SET
	postmeta.meta_value = '1' /* <-- adjust this value to the subscription post ID */
WHERE
	postmeta.meta_key = '_pronamic_subscription_id'
		AND
	post.post_type = 'pronamic_company'
;