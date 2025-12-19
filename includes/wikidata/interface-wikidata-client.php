<?php

namespace KnowSchema\Wikidata;

interface Wikidata_Client_Interface {
	/**
	 * Search for entities on Wikidata.
	 *
	 * @param string $term
	 * @return array
	 */
	public function search( $term );

	/**
	 * Get details for a QID.
	 *
	 * @param string $qid
	 * @return array
	 */
	public function get_entity( $qid );

	/**
	 * Publish or edit an entity.
	 *
	 * @param array $data
	 * @return string|WP_Error The QID or error.
	 */
	public function publish( $data );
}
