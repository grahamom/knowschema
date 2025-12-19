<?php

namespace KnowSchema\Schema;

class Validator {

	public function get_readiness_rules( $template ) {
		$rules = array(
			'Review' => array(
				'required' => array( 'ratingValue', 'author', 'datePublished', 'reviewBody' ),
				'recommended' => array( 'bestRating', 'worstRating', 'publisher', 'image' ),
			),
			'Event' => array(
				'required' => array( 'name', 'startDate', 'location' ),
				'recommended' => array( 'endDate', 'eventStatus', 'image', 'description' ),
			),
			'Product' => array(
				'required' => array( 'name', 'price', 'priceCurrency', 'availability' ),
				'recommended' => array( 'sku', 'brand', 'image', 'review' ),
			),
			'Article' => array(
				'required' => array( 'headline', 'datePublished', 'author' ),
				'recommended' => array( 'dateModified', 'image', 'publisher' ),
			),
		);

		return isset( $rules[ $template ] ) ? $rules[ $template ] : array( 'required' => array(), 'recommended' => array() );
	}

	public function check_readiness( $template, $data ) {
		$rules = $this->get_readiness_rules( $template );
		$results = array(
			'missing_required' => array(),
			'missing_recommended' => array(),
			'status' => 'green',
		);

		foreach ( $rules['required'] as $field ) {
			if ( ! $this->has_field( $data, $field ) ) {
				$results['missing_required'][] = $field;
				$results['status'] = 'red';
			}
		}

		foreach ( $rules['recommended'] as $field ) {
			if ( ! $this->has_field( $data, $field ) ) {
				$results['missing_recommended'][] = $field;
				if ( $results['status'] === 'green' ) {
					$results['status'] = 'amber';
				}
			}
		}

		return $results;
	}

	private function has_field( $data, $field ) {
		// Basic check for existence in flat or nested array
		// This is a simplification for MVP
		if ( isset( $data[ $field ] ) && ! empty( $data[ $field ] ) ) {
			return true;
		}

		// Check nested (e.g. reviewRating.ratingValue)
		if ( strpos( $field, '.' ) !== false ) {
			$parts = explode( '.', $field );
			$temp = $data;
			foreach ( $parts as $part ) {
				if ( ! isset( $temp[ $part ] ) || empty( $temp[ $part ] ) ) {
					return false;
				}
				$temp = $temp[ $part ];
			}
			return true;
		}
		
		// Check common nested structures like reviewRating, author, location
		$nested_mappings = array(
			'ratingValue' => 'reviewRating',
			'author'      => 'author',
			'location'    => 'location',
			'price'       => 'offers',
		);
		
		if ( isset( $nested_mappings[ $field ] ) ) {
			$parent = $nested_mappings[ $field ];
			if ( isset( $data[ $parent ] ) && ! empty( $data[ $parent ] ) ) {
				return true;
			}
		}

		return false;
	}
}
