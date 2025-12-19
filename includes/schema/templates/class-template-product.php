<?php

namespace KnowSchema\Schema\Templates;

class Template_Product {

	public function generate( $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$data = get_post_meta( $post_id, '_ks_product_data', true );
		
		// Map from WooCommerce if available and no manual data
		if ( empty( $data ) && function_exists( 'wc_get_product' ) ) {
			$product = wc_get_product( $post_id );
			if ( $product ) {
				$data = array(
					'name'  => $product->get_name(),
					'sku'   => $product->get_sku(),
					'price' => $product->get_price(),
					'currency' => get_woocommerce_currency(),
					'availability' => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
				);
			}
		}

		if ( empty( $data ) ) {
			return array();
		}

		$url = get_permalink( $post_id );
		$home_url = home_url( '/' );

		return array(
			'@type' => 'Product',
			'@id'   => $url . '#product',
			'name'  => ! empty( $data['name'] ) ? $data['name'] : get_the_title( $post_id ),
			'image' => array( get_the_post_thumbnail_url( $post_id, 'full' ) ),
			'description' => ! empty( $data['description'] ) ? $data['description'] : get_the_excerpt( $post_id ),
			'sku'   => ! empty( $data['sku'] ) ? $data['sku'] : '',
			'brand' => array(
				'@type' => 'Brand',
				'name'  => ! empty( $data['brand'] ) ? $data['brand'] : get_bloginfo( 'name' ),
			),
			'offers' => array(
				'@type' => 'Offer',
				'url'   => $url,
				'price' => ! empty( $data['price'] ) ? $data['price'] : '',
				'priceCurrency' => ! empty( $data['currency'] ) ? $data['currency'] : 'USD',
				'availability'  => ! empty( $data['availability'] ) ? $data['availability'] : 'https://schema.org/InStock',
				'seller' => array(
					'@id' => $home_url . '#organization',
				)
			)
		);
	}
}
