<?php

namespace KnowSchema\Schema\Templates;

class Template_BreadcrumbList {

	public function generate( $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'class-breadcrumb-provider.php';
		$provider = new \KnowSchema\Schema\Breadcrumb_Provider();
		$breadcrumb_items = $provider->get_items( $post_id );

		$url = get_permalink( $post_id );
		$items = array();
		$position = 1;

		foreach ( $breadcrumb_items as $index => $breadcrumb ) {
			$list_item = array(
				'@type'    => 'ListItem',
				'position' => $position,
				'name'     => $breadcrumb['name'],
			);

			// Don't add 'item' for the last one (current page) per some recommendations, 
			// though Google often accepts it. Briefing said "derived from breadcrumbs provider".
			if ( $index < count( $breadcrumb_items ) - 1 ) {
				$list_item['item'] = $breadcrumb['item'];
			}

			$items[] = $list_item;
			$position++;
		}

		return array(
			'@type'           => 'BreadcrumbList',
			'@id'             => $url . '#breadcrumbs',
			'itemListElement' => $items,
		);
	}
}
