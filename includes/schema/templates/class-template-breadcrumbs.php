<?php

namespace KnowSchema\Schema\Templates;

class Template_BreadcrumbList {

	public function generate( $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$url = get_permalink( $post_id );
		
		$items = array();
		$position = 1;

		// Home
		$items[] = array(
			'@type' => 'ListItem',
			'position' => $position,
			'name' => 'Home',
			'item' => home_url( '/' )
		);
		$position++;

		// Ancestors
		$ancestors = get_post_ancestors( $post_id );
		if ( $ancestors ) {
			$ancestors = array_reverse( $ancestors );
			foreach ( $ancestors as $ancestor ) {
				$items[] = array(
					'@type' => 'ListItem',
					'position' => $position,
					'name' => get_the_title( $ancestor ),
					'item' => get_permalink( $ancestor )
				);
				$position++;
			}
		}

		// Current Page
		$items[] = array(
			'@type' => 'ListItem',
			'position' => $position,
			'name' => get_the_title( $post_id ),
			// 'item' => $url // Google recommends omitting 'item' for the last item
		);

		return array(
			'@type' => 'BreadcrumbList',
			'@id'   => $url . '#breadcrumbs',
			'itemListElement' => $items,
		);
	}
}
