<?php

namespace KnowSchema\Schema\Templates;

class Template_Organization {

	public static function get_publisher_id() {
		$home_url = home_url( '/' );
		
		require_once plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'entities/class-entity-repository.php';
		$repo = new \KnowSchema\Entities\Entity_Repository();
		$entity = $repo->get_primary_entity();

		if ( $entity && $entity['type'] === 'Person' ) {
			return $home_url . '#person';
		}
		
		return $home_url . '#organization';
	}

	public function generate( $post_id = null ) {
		$home_url = home_url( '/' );
		
		require_once plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'entities/class-entity-repository.php';
		$repo = new \KnowSchema\Entities\Entity_Repository();
		$entity = $repo->get_primary_entity();

		// Case 1: Primary Entity is set
		if ( $entity ) {
			$type = ( $entity['type'] === 'Person' ) ? 'Person' : 'Organization';
			$id   = ( $type === 'Person' ) ? $home_url . '#person' : $home_url . '#organization';
			
			$data = array(
				'@type' => $type,
				'@id'   => $id,
				'url'   => ! empty( $entity['url'] ) ? $entity['url'] : $home_url,
				'name'  => $entity['name'],
			);
			
			if ( $type === 'Organization' && ! empty( $entity['image'] ) ) {
				$data['logo'] = array( '@type' => 'ImageObject', 'url' => $entity['image'] );
			} elseif ( $type === 'Person' && ! empty( $entity['image'] ) ) {
				$data['image'] = array( '@type' => 'ImageObject', 'url' => $entity['image'] );
			}

			if ( ! empty( $entity['sameAs'] ) ) {
				$data['sameAs'] = $entity['sameAs'];
			}
			if ( ! empty( $entity['qid'] ) ) {
				if ( ! isset( $data['sameAs'] ) ) { $data['sameAs'] = array(); }
				$data['sameAs'][] = 'https://www.wikidata.org/wiki/' . $entity['qid'];
			}
			return $data;
		}

		// Case 2: Fallback to Global Settings (Organization only)
		$options = get_option( 'knowschema_options' );
		$org_name = ! empty( $options['org_name'] ) ? $options['org_name'] : get_bloginfo( 'name' );

		$data = array(
			'@type' => 'Organization',
			'@id'   => $home_url . '#organization',
			'url'   => $home_url,
			'name'  => $org_name,
			'logo'  => array(
				'@type' => 'ImageObject',
				'url'   => get_site_icon_url( 512 ), // Simple fallback
			)
		);

		if ( ! empty( $options['qid'] ) ) {
			$qid = trim( $options['qid'] );
			if ( strpos( $qid, 'Q' ) === 0 ) {
				$data['sameAs'] = array( 'https://www.wikidata.org/wiki/' . $qid );
			}
		}

		return $data;
	}
}
