<?php

namespace KnowSchema\Schema\Templates;

class Template_Organization {

	public function generate( $post_id = null ) {
		$home_url = home_url( '/' );
		
		require_once plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'entities/class-entity-repository.php';
		$repo = new \KnowSchema\Entities\Entity_Repository();
		$entity = $repo->get_primary_entity();

		if ( $entity && $entity['type'] === 'Organization' ) {
			$data = array(
				'@type' => 'Organization',
				'@id'   => $home_url . '#organization',
				'url'   => ! empty( $entity['url'] ) ? $entity['url'] : $home_url,
				'name'  => $entity['name'],
			);
			if ( ! empty( $entity['image'] ) ) {
				$data['logo'] = array( '@type' => 'ImageObject', 'url' => $entity['image'] );
			}
			if ( ! empty( $entity['sameAs'] ) ) {
				$data['sameAs'] = $entity['sameAs'];
			}
			if ( ! empty( $entity['qid'] ) ) {
				$data['sameAs'][] = 'https://www.wikidata.org/wiki/' . $entity['qid'];
			}
			return $data;
		}

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
