<?php

namespace KnowSchema\Schema;

class Breadcrumb_Provider {

	public function get_items( $post_id ) {
		$items = array();
		
		// 1. Home
		$items[] = array(
			'name' => __( 'Home', 'knowschema' ),
			'item' => home_url( '/' ),
		);

		if ( is_front_page() ) {
			return $items;
		}

		// 2. Ancestors
		$ancestors = get_post_ancestors( $post_id );
		if ( ! empty( $ancestors ) ) {
			$ancestors = array_reverse( $ancestors );
			foreach ( $ancestors as $ancestor ) {
				$items[] = array(
					'name' => get_the_title( $ancestor ),
					'item' => get_permalink( $ancestor ),
				);
			}
		}

		// 3. Current Page
		$items[] = array(
			'name' => get_the_title( $post_id ),
			'item' => get_permalink( $post_id ),
		);

		return apply_filters( 'knowschema_breadcrumb_items', $items, $post_id );
	}
}
