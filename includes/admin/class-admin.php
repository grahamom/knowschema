<?php

namespace KnowSchema\Admin;

class Admin {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . 'assets/css/knowschema-admin.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . 'assets/js/knowschema-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'knowschema_vars', array(
			'nonce' => wp_create_nonce( 'knowschema_preview_nonce' ),
		) );
	}

}
