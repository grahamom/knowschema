<?php

namespace KnowSchema\Schema\Templates;

class Template_Event {

	public function generate( $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$data = get_post_meta( $post_id, '_ks_event_data', true );
		if ( empty( $data ) ) {
			return array();
		}

		$url = get_permalink( $post_id );
		$home_url = home_url( '/' );

		// Ensure Template_Organization is loaded
		if ( ! class_exists( 'KnowSchema\Schema\Templates\Template_Organization' ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'class-template-organization.php';
		}
		$organizer_id = \KnowSchema\Schema\Templates\Template_Organization::get_publisher_id();

		$event = array(
			'@type' => 'Event',
			'@id'   => $url . '#event',
			'name'  => ! empty( $data['name'] ) ? $data['name'] : get_the_title( $post_id ),
			'startDate' => ! empty( $data['start_date'] ) ? $data['start_date'] : '',
			'location'  => array(
				'@type' => 'Place',
				'name'  => ! empty( $data['location_name'] ) ? $data['location_name'] : '',
				'address' => array(
					'@type' => 'PostalAddress',
					'streetAddress' => ! empty( $data['location_address'] ) ? $data['location_address'] : '',
				)
			),
			'image' => array( get_the_post_thumbnail_url( $post_id, 'full' ) ),
			'description' => get_the_excerpt( $post_id ),
			'organizer' => array(
				'@id' => $organizer_id,
			)
		);

		if ( ! empty( $data['end_date'] ) ) {
			$event['endDate'] = $data['end_date'];
		}

		return $event;
	}
}
