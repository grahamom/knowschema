<?php

namespace KnowSchema\Schema\Templates;

class Template_Person {

	public function generate( $user_id = null ) {
		// If used for Author node
		if ( ! $user_id ) {
			return array();
		}

		$author_url = get_author_posts_url( $user_id );
		$name = get_the_author_meta( 'display_name', $user_id );
		$description = get_the_author_meta( 'description', $user_id );
		$website = get_the_author_meta( 'url', $user_id );

		$data = array(
			'@type' => 'Person',
			'@id'   => $author_url . '#person',
			'name'  => $name,
			'url'   => $author_url,
		);

		if ( ! empty( $description ) ) {
			$data['description'] = $description;
		}
		
		if ( ! empty( $website ) ) {
			$data['sameAs'] = array( $website );
		}
		
		return $data;
	}
}
