<?php

namespace KnowSchema\Schema\Templates;

class Template_WebSite {

	public function generate( $post_id = null ) {
		$home_url = home_url( '/' );
		
		// Ensure Template_Organization is loaded for the helper
		if ( ! class_exists( 'KnowSchema\Schema\Templates\Template_Organization' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'class-template-organization.php';
		}
		$publisher_id = \KnowSchema\Schema\Templates\Template_Organization::get_publisher_id();

		return array(
			'@type' => 'WebSite',
			'@id'   => $home_url . '#website',
			'url'   => $home_url,
			'name'  => get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
			'publisher' => array(
				'@id' => $publisher_id,
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
