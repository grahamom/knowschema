<?php

namespace KnowSchema\Schema\Templates;

class Template_Article {

	public function generate( $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$url = get_permalink( $post_id );
		$home_url = home_url( '/' );
		$author_id = get_post_field( 'post_author', $post_id );
		$author_url = get_author_posts_url( $author_id );

		return array(
			'@type' => 'Article',
			'@id'   => $url . '#article',
			'isPartOf' => array(
				'@id' => $url . '#webpage',
			),
			'headline' => get_the_title( $post_id ),
			'datePublished' => get_the_date( 'c', $post_id ),
			'dateModified'  => get_the_modified_date( 'c', $post_id ),
			'mainEntityOfPage' => array(
				'@id' => $url . '#webpage',
			),
			'author' => array(
				'@id' => $author_url . '#person', // Linking to Person node
			),
			'publisher' => array(
				'@id' => $home_url . '#organization',
			),
			'image' => array(
				'@id' => $url . '#primaryimage',
			)
		);
	}
}
