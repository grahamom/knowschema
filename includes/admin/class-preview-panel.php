<?php

namespace KnowSchema\Admin;

class Preview_Panel {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function ajax_preview_schema() {
		check_ajax_referer( 'knowschema_preview_nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( 'Permission denied' );
		}

		$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
		$template = isset( $_POST['template'] ) ? sanitize_text_field( $_POST['template'] ) : '';

		if ( ! $post_id ) {
			wp_send_json_error( 'Invalid Post ID' );
		}

		// Load Graph Builder if not already loaded (it should be via plugin class, but let's be safe)
		if ( ! class_exists( 'KnowSchema\Schema\Graph_Builder' ) ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'schema/class-graph-builder.php';
		}

		// We need to set the global post object for some template tags to work correctly
		global $post;
		$post = get_post( $post_id );
		setup_postdata( $post );

		$graph_builder = new \KnowSchema\Schema\Graph_Builder( $this->plugin_name, $this->version );
		
		$overrides = array(
			'post_id' => $post_id,
			'template' => $template
		);

		$graph = $graph_builder->build_graph( $overrides );

		// Run Validator
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'schema/class-validator.php';
		$validator = new \KnowSchema\Schema\Validator();
		
		$main_node = array();
		if ( ! empty( $graph['@graph'] ) ) {
			foreach ( $graph['@graph'] as $node ) {
				if ( isset( $node['@type'] ) && $node['@type'] === $template ) {
					$main_node = $node;
					break;
				}
			}
		}

		$readiness = $validator->check_readiness( $template, $main_node );

		wp_send_json_success( array(
			'graph'     => $graph,
			'readiness' => $readiness
		) );
	}

}
