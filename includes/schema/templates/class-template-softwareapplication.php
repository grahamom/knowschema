<?php

namespace KnowSchema\Schema\Templates;

class Template_SoftwareApplication {

	public function generate( $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$data = get_post_meta( $post_id, '_ks_software_data', true );
		if ( empty( $data ) ) {
			return array();
		}

		$url = get_permalink( $post_id );

		return array(
			'@type' => 'SoftwareApplication',
			'@id'   => $url . '#software',
			'name'  => ! empty( $data['name'] ) ? $data['name'] : get_the_title( $post_id ),
			'operatingSystem' => ! empty( $data['os'] ) ? $data['os'] : 'Windows, macOS, Linux',
			'applicationCategory' => ! empty( $data['category'] ) ? $data['category'] : 'BusinessApplication',
			'offers' => array(
				'@type' => 'Offer',
				'price' => ! empty( $data['price'] ) ? $data['price'] : '0',
				'priceCurrency' => ! empty( $data['currency'] ) ? $data['currency'] : 'USD',
			)
		);
	}
}
