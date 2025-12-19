<?php

namespace KnowSchema\Schema\Templates;

class Template_WebSite {

	public function generate( $post_id = null ) {
		$home_url = home_url( '/' );
		
		return array(
			'@type' => 'WebSite',
			'@id'   => $home_url . '#website',
			'url'   => $home_url,
			'name'  => get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
			'publisher' => array(
				'@id' => $home_url . '#organization', // Linking to Organization node
			),
			'potentialAction' => array(
				'@type' => 'SearchAction',
				'target' => array(
					'@type' => 'EntryPoint',
					'urlTemplate' => $home_url . '?s={search_term_string}'
				),
				'query-input' => 'required name=search_term_string'
			)
		);
	}
}
