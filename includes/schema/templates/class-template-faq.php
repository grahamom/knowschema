<?php

namespace KnowSchema\Schema\Templates;

class Template_FAQPage {

	public function generate( $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$faqs = get_post_meta( $post_id, '_ks_faqs', true );
		if ( empty( $faqs ) || ! is_array( $faqs ) ) {
			return array();
		}

		$questions = array();
		foreach ( $faqs as $faq ) {
			if ( empty( $faq['question'] ) || empty( $faq['answer'] ) ) {
				continue;
			}
			$questions[] = array(
				'@type' => 'Question',
				'name'  => $faq['question'],
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $faq['answer'],
				),
			);
		}

		if ( empty( $questions ) ) {
			return array();
		}

		$url = get_permalink( $post_id );

		return array(
			'@type' => 'FAQPage',
			'@id'   => $url . '#faq',
			'mainEntity' => $questions,
		);
	}
}
