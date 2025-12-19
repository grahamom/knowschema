<?php

namespace KnowSchema\Entities;

class Entity_Repository {

	public function get_entity( $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post || $post->post_type !== 'ks_entity' ) {
			return null;
		}

		return array(
			'id'      => $post->ID,
			'name'    => $post->post_title,
			'type'    => get_post_meta( $post->ID, '_ks_entity_type', true ),
			'url'     => get_post_meta( $post->ID, '_ks_entity_url', true ),
			'qid'     => get_post_meta( $post->ID, '_ks_entity_qid', true ),
			'sameAs'  => get_post_meta( $post->ID, '_ks_entity_sameas', true ),
			'image'   => get_the_post_thumbnail_url( $post->ID, 'full' ),
		);
	}

	public function get_primary_entity() {
		$options = get_option( 'knowschema_options' );
		$primary_id = isset( $options['primary_entity_id'] ) ? intval( $options['primary_entity_id'] ) : 0;
		
		if ( $primary_id ) {
			return $this->get_entity( $primary_id );
		}

		return null;
	}
}
