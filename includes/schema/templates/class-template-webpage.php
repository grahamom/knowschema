<?php

namespace KnowSchema\Schema\Templates;

class Template_WebPage {

	public function generate( $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}
		
		$url = get_permalink( $post_id );
		$home_url = home_url( '/' );

		return array(
			'@type' => 'WebPage',
			'@id'   => $url . '#webpage',
			'url'   => $url,
			'name'  => get_the_title( $post_id ),
			'isPartOf' => array(
				'@id' => $home_url . '#website',
			),
			'datePublished' => get_the_date( 'c', $post_id ),
			'dateModified'  => get_the_modified_date( 'c', $post_id ),
			'breadcrumb'    => array(
				'@id' => $url . '#breadcrumbs',
			),
		);
	}
}
