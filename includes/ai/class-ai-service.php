<?php

namespace KnowSchema\AI;

class AI_Service {

	private $provider;

	public function __construct() {
		// Allow external plugins (Pro) to register a provider
		$this->provider = apply_filters( 'knowschema_ai_provider', null );
	}

	public function is_active() {
		return $this->provider instanceof AI_Provider_Interface && $this->provider->is_connected();
	}

	public function generate( $task, $input ) {
		if ( ! $this->is_active() ) {
			return new \WP_Error( 'ai_not_connected', __( 'AI service is not connected.', 'knowschema' ) );
		}

		// Check credits locally (if applicable) or delegate to provider
		// For now, pass through
		return $this->provider->generate( $task, $input );
	}
}
