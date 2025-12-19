<?php

namespace KnowSchema\Admin;

class Metabox {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function add_meta_boxes() {
		$screens = array( 'post', 'page', 'ks_entity' );
		foreach ( $screens as $screen ) {
			add_meta_box(
				'knowschema_metabox',
				__( 'KnowSchema Settings', 'knowschema' ),
				array( $this, 'render_metabox' ),
				$screen,
				'normal', // Context
				'high'    // Priority
			);
		}
	}

	public function render_metabox( $post ) {
		wp_nonce_field( 'knowschema_save_metabox_data', 'knowschema_metabox_nonce' );

		// Retrieve existing value from db
		$template = get_post_meta( $post->ID, '_ks_schema_template', true );

		echo '<p><label for="knowschema_schema_template">';
		_e( 'Schema Template', 'knowschema' );
		echo '</label> ';
		echo '<select id="knowschema_schema_template" name="knowschema_schema_template">';
		echo '<option value="">' . __( 'Default', 'knowschema' ) . '</option>';
		echo '<option value="Article" ' . selected( $template, 'Article', false ) . '>Article</option>';
		echo '<option value="WebPage" ' . selected( $template, 'WebPage', false ) . '>WebPage</option>';
		echo '<option value="Product" ' . selected( $template, 'Product', false ) . '>Product</option>';
		echo '</select></p>';

		echo '<p><em>' . __( 'More fields will appear here based on template selection.', 'knowschema' ) . '</em></p>';
	}

	public function save_meta_box_data( $post_id ) {
		if ( ! isset( $_POST['knowschema_metabox_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_POST['knowschema_metabox_nonce'], 'knowschema_save_metabox_data' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['knowschema_schema_template'] ) ) {
			update_post_meta( $post_id, '_ks_schema_template', sanitize_text_field( $_POST['knowschema_schema_template'] ) );
		}
	}

}
