<?php

namespace KnowSchema\Schema\Templates;

class Template_Review {

	public function generate( $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$data = get_post_meta( $post_id, '_ks_review_data', true );
		if ( empty( $data ) ) {
			return array();
		}

		$url = get_permalink( $post_id );
		$author_id = get_post_field( 'post_author', $post_id );
		$author_url = get_author_posts_url( $author_id );

		return array(
			'@type' => 'Review',
			'@id'   => $url . '#review',
			'itemReviewed' => array(
				'@type' => ! empty( $data['item_type'] ) ? $data['item_type'] : 'Thing',
				'name'  => ! empty( $data['item_name'] ) ? $data['item_name'] : get_the_title( $post_id ),
			),
			'reviewRating' => array(
				'@type'       => 'Rating',
				'ratingValue' => ! empty( $data['rating'] ) ? $data['rating'] : '5',
				'bestRating'  => '5',
				'worstRating' => '1',
			),
			'author' => array(
				'@id' => $author_url . '#person',
			),
			'reviewBody' => ! empty( $data['body'] ) ? $data['body'] : get_the_excerpt( $post_id ),
			'datePublished' => get_the_date( 'c', $post_id ),
		);
	}
}
