<?php

namespace KnowSchema\Schema\Templates;

class Template_Recipe {

	public function generate( $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$data = get_post_meta( $post_id, '_ks_recipe_data', true );
		if ( empty( $data ) ) {
			return array();
		}

		$url = get_permalink( $post_id );

		$recipe = array(
			'@type' => 'Recipe',
			'@id'   => $url . '#recipe',
			'name'  => ! empty( $data['name'] ) ? $data['name'] : get_the_title( $post_id ),
			'image' => array( get_the_post_thumbnail_url( $post_id, 'full' ) ),
			'description' => get_the_excerpt( $post_id ),
			'recipeIngredient' => ! empty( $data['ingredients'] ) ? array_map( 'trim', explode( "\n", str_replace( "\r", '', $data['ingredients'] ) ) ) : array(),
			'recipeInstructions' => $this->format_instructions( $data['instructions'] ?? '' ),
		);

		if ( ! empty( $data['prep_time'] ) ) {
			$recipe['prepTime'] = $data['prep_time'];
		}
		if ( ! empty( $data['cook_time'] ) ) {
			$recipe['cookTime'] = $data['cook_time'];
		}
		if ( ! empty( $data['recipe_yield'] ) ) {
			$recipe['recipeYield'] = $data['recipe_yield'];
		}

		return $recipe;
	}

	private function format_instructions( $text ) {
		if ( empty( $text ) ) {
			return array();
		}
		$lines = array_filter( array_map( 'trim', explode( "\n", str_replace( "\r", '', $text ) ) ) );
		$steps = array();
		foreach ( $lines as $line ) {
			$steps[] = array(
				'@type' => 'HowToStep',
				'text'  => $line,
			);
		}
		return $steps;
	}
}
