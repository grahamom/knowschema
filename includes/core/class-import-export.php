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
			return array( __( 'Error: Invalid JSON data', 'knowschema' ) );
		}

		$log = array();

		// Detect Import Type: Raw Schema Entity
		if ( isset( $data['@context'] ) && isset( $data['@type'] ) ) {
			$name = isset( $data['name'] ) ? $data['name'] : __( 'Imported Entity', 'knowschema' );
			$log[] = sprintf( __( 'Detected raw Schema.org Entity: "%s" (%s)', 'knowschema' ), $name, $data['@type'] );
			
			if ( ! $dry_run ) {
				$post_data = array(
					'post_title'   => $name,
					'post_status'  => 'publish',
					'post_type'    => 'ks_entity',
				);
				$post_id = wp_insert_post( $post_data );

				if ( $post_id && ! is_wp_error( $post_id ) ) {
					// Map Basic Fields
					$type = $data['@type'];
					if ( $type !== 'Person' && $type !== 'Organization' ) {
						// Fallback or mapping? We only support P/O specifically in UI, but allowing others is fine for JSON data
					}
					update_post_meta( $post_id, '_ks_entity_type', $type );

					if ( isset( $data['url'] ) ) {
						update_post_meta( $post_id, '_ks_entity_url', esc_url_raw( $data['url'] ) );
					}
					
					if ( isset( $data['sameAs'] ) ) {
						$sameas = is_array( $data['sameAs'] ) ? $data['sameAs'] : array( $data['sameAs'] );
						update_post_meta( $post_id, '_ks_entity_sameas', array_map( 'esc_url_raw', $sameas ) );
					}
					
					// Save Full JSON for advanced fields
					update_post_meta( $post_id, '_ks_entity_json_data', $data );

					$log[] = __( 'Successfully created new Schema Entity.', 'knowschema' );
				} else {
					$log[] = __( 'Error creating entity post.', 'knowschema' );
				}
			} else {
				$log[] = __( 'Dry Run: Would create new Entity with raw JSON data.', 'knowschema' );
			}
			
			return $log;
		}

		// Standard Backup Import
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
