<?php

namespace KnowSchema\Schema\Templates;

class Template_VideoObject {

	public function generate( $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$data = get_post_meta( $post_id, '_ks_video_data', true );
		if ( empty( $data ) ) {
			return array();
		}

		$url = get_permalink( $post_id );

		return array(
			'@type' => 'VideoObject',
			'@id'   => $url . '#video',
			'name'  => ! empty( $data['name'] ) ? $data['name'] : get_the_title( $post_id ),
			'description' => ! empty( $data['description'] ) ? $data['description'] : get_the_excerpt( $post_id ),
			'thumbnailUrl' => ! empty( $data['thumbnail_url'] ) ? $data['thumbnail_url'] : get_the_post_thumbnail_url( $post_id, 'full' ),
			'uploadDate' => ! empty( $data['upload_date'] ) ? $data['upload_date'] : get_the_date( 'c', $post_id ),
			'contentUrl' => ! empty( $data['content_url'] ) ? $data['content_url'] : '',
			'embedUrl' => ! empty( $data['embed_url'] ) ? $data['embed_url'] : '',
		);
	}
}
