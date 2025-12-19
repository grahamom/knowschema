<?php

namespace KnowSchema\Core;

class Import_Export {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function get_export_data() {
		$data = array(
			'version'  => $this->version,
			'date'     => date( 'c' ),
			'settings' => get_option( 'knowschema_options' ),
			'entities' => array(),
			'post_meta' => array(),
		);

		// Export Entities (CPT)
		$entities = get_posts( array(
			'post_type'      => 'ks_entity',
			'posts_per_page' => -1,
			'post_status'    => 'any',
		) );

		foreach ( $entities as $entity ) {
			$data['entities'][] = array(
				'title'   => $entity->post_title,
				'content' => $entity->post_content,
				'meta'    => get_post_meta( $entity->ID ),
			);
		}

		// Export Post Meta (Schema assignments)
		// This can be heavy, so we limit to posts that actually have our meta
		global $wpdb;
		$meta_results = $wpdb->get_results( "
			SELECT post_id, meta_key, meta_value 
			FROM $wpdb->postmeta 
			WHERE meta_key LIKE '_ks_%'
		" );

		foreach ( $meta_results as $row ) {
			if ( ! isset( $data['post_meta'][ $row->post_id ] ) ) {
				$data['post_meta'][ $row->post_id ] = array();
			}
			$data['post_meta'][ $row->post_id ][ $row->meta_key ] = $row->meta_value;
		}

		return $data;
	}

	public function process_import( $json_data, $dry_run = true ) {
		$data = json_decode( $json_data, true );
		if ( ! $data || ! is_array( $data ) ) {
			return new \WP_Error( 'invalid_json', __( 'Invalid JSON data', 'knowschema' ) );
		}

		$log = array();

		// Settings
		if ( ! empty( $data['settings'] ) ) {
			if ( ! $dry_run ) {
				update_option( 'knowschema_options', $data['settings'] );
			}
			$log[] = __( 'Settings prepared for import.', 'knowschema' );
		}

		// Entities
		if ( ! empty( $data['entities'] ) ) {
			foreach ( $data['entities'] as $entity ) {
				$log[] = sprintf( __( 'Entity "%s" found.', 'knowschema' ), $entity['title'] );
				if ( ! $dry_run ) {
					// Logic to insert/update entity would go here
					// For MVP we might just create new ones to avoid complex reconciliation
					$post_data = array(
						'post_title'   => $entity['title'],
						'post_content' => $entity['content'],
						'post_type'    => 'ks_entity',
						'post_status'  => 'publish',
					);
					$id = wp_insert_post( $post_data );
					if ( ! empty( $entity['meta'] ) ) {
						foreach ( $entity['meta'] as $k => $v ) {
							update_post_meta( $id, $k, maybe_unserialize( $v[0] ) );
						}
					}
				}
			}
		}

		return $log;
	}
}
