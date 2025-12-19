<?php

namespace KnowSchema\AI;

interface AI_Provider_Interface {
	/**
	 * Generate content using AI.
	 *
	 * @param string $task    The type of task (e.g., 'draft_schema', 'suggest_template').
	 * @param array  $input   The input data (e.g., post content, title).
	 * @param array  $options Optional parameters.
	 * @return array|WP_Error Result containing 'content', 'usage', etc.
	 */
	public function generate( $task, $input, $options = array() );

	/**
	 * Check if the provider is ready/connected.
	 *
	 * @return bool
	 */
	public function is_connected();
}
