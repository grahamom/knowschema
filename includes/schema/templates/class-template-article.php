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

		// Ensure Template_Organization is loaded for the helper
		if ( ! class_exists( 'KnowSchema\Schema\Templates\Template_Organization' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'class-template-organization.php';
		}
		$publisher_id = \KnowSchema\Schema\Templates\Template_Organization::get_publisher_id();

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
				'@id' => $publisher_id,
			),
			'image' => array(
				'@id' => $url . '#primaryimage',
			)
		);
	}
}
