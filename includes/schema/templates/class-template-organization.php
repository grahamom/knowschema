<?php

namespace KnowSchema\Schema\Templates;

class Template_Organization {

	public function generate( $post_id = null ) {
		$home_url = home_url( '/' );
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
