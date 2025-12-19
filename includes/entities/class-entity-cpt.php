<?php

namespace KnowSchema\Entities;

class Entity_CPT {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function register_cpt() {
		$labels = array(
			'name'                  => _x( 'Schema Entities', 'Post Type General Name', 'knowschema' ),
			'singular_name'         => _x( 'Schema Entity', 'Post Type Singular Name', 'knowschema' ),
			'menu_name'             => __( 'Schema Entities', 'knowschema' ),
			'name_admin_bar'        => __( 'Schema Entity', 'knowschema' ),
			'archives'              => __( 'Entity Archives', 'knowschema' ),
			'attributes'            => __( 'Entity Attributes', 'knowschema' ),
			'parent_item_colon'     => __( 'Parent Entity:', 'knowschema' ),
			'all_items'             => __( 'All Entities', 'knowschema' ),
			'add_new_item'          => __( 'Add New Entity', 'knowschema' ),
			'add_new'               => __( 'Add New', 'knowschema' ),
			'new_item'              => __( 'New Entity', 'knowschema' ),
			'edit_item'             => __( 'Edit Entity', 'knowschema' ),
			'update_item'           => __( 'Update Entity', 'knowschema' ),
			'view_item'             => __( 'View Entity', 'knowschema' ),
			'view_items'            => __( 'View Entities', 'knowschema' ),
			'search_items'          => __( 'Search Entity', 'knowschema' ),
			'not_found'             => __( 'Not found', 'knowschema' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'knowschema' ),
			'featured_image'        => __( 'Entity Logo/Image', 'knowschema' ),
			'set_featured_image'    => __( 'Set entity image', 'knowschema' ),
			'remove_featured_image' => __( 'Remove entity image', 'knowschema' ),
			'use_featured_image'    => __( 'Use as entity image', 'knowschema' ),
			'insert_into_item'      => __( 'Insert into entity', 'knowschema' ),
			'uploaded_to_this_item' => __( 'Uploaded to this entity', 'knowschema' ),
			'items_list'            => __( 'Entities list', 'knowschema' ),
			'items_list_navigation' => __( 'Entities list navigation', 'knowschema' ),
			'filter_items_list'     => __( 'Filter entities list', 'knowschema' ),
		);
		$args   = array(
			'label'               => __( 'Schema Entity', 'knowschema' ),
			'description'         => __( 'Reusable Person or Organization records', 'knowschema' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'revisions', 'thumbnail', 'custom-fields' ), // Title is used for internal reference name
			'hierarchical'        => false,
			'public'              => false, // Not public facing on their own URLs by default
			'show_ui'             => true,
			'show_in_menu'        => 'knowschema', // Will be submenu of main plugin page if possible, or separate
			'menu_position'       => 5,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
			'show_in_rest'        => true, // Block editor support if needed
		);
		register_post_type( 'ks_entity', $args );
	}

	public function add_meta_boxes() {
		add_meta_box(
			'ks_entity_data',
			__( 'Entity Data', 'knowschema' ),
			array( $this, 'render_metabox' ),
			'ks_entity',
			'normal',
			'high'
		);
	}

	public function render_metabox( $post ) {
		wp_nonce_field( 'ks_save_entity_data', 'ks_entity_nonce' );

		$type = get_post_meta( $post->ID, '_ks_entity_type', true );
		if ( ! $type ) { $type = 'Organization'; }
		
		$url = get_post_meta( $post->ID, '_ks_entity_url', true );
		$qid = get_post_meta( $post->ID, '_ks_entity_qid', true );
		$sameas = get_post_meta( $post->ID, '_ks_entity_sameas', true );

		?>
		<p>
			<label for="ks_entity_type"><strong><?php _e( 'Entity Type', 'knowschema' ); ?></strong></label><br>
			<select name="ks_entity_type" id="ks_entity_type" style="width:100%">
				<option value="Organization" <?php selected( $type, 'Organization' ); ?>>Organization</option>
				<option value="Person" <?php selected( $type, 'Person' ); ?>>Person</option>
			</select>
		</p>
		<p>
			<label for="ks_entity_url"><strong><?php _e( 'Official URL', 'knowschema' ); ?></strong></label><br>
			<input type="url" name="ks_entity_url" id="ks_entity_url" value="<?php echo esc_attr( $url ); ?>" style="width:100%">
		</p>
		<p>
			<label for="ks_entity_qid"><strong><?php _e( 'Wikidata QID', 'knowschema' ); ?></strong></label><br>
			<input type="text" name="ks_entity_qid" id="ks_entity_qid" value="<?php echo esc_attr( $qid ); ?>" placeholder="e.g. Q12345" style="width:100%">
		</p>
		<p>
			<label for="ks_entity_sameas"><strong><?php _e( 'SameAs Links (one per line)', 'knowschema' ); ?></strong></label><br>
			<textarea name="ks_entity_sameas" id="ks_entity_sameas" rows="4" style="width:100%"><?php echo esc_textarea( is_array( $sameas ) ? implode( "\n", $sameas ) : $sameas ); ?></textarea>
		</p>
		<?php
	}

	public function save_entity_data( $post_id ) {
		if ( ! isset( $_POST['ks_entity_nonce'] ) || ! wp_verify_nonce( $_POST['ks_entity_nonce'], 'ks_save_entity_data' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( isset( $_POST['ks_entity_type'] ) ) {
			update_post_meta( $post_id, '_ks_entity_type', sanitize_text_field( $_POST['ks_entity_type'] ) );
		}
		if ( isset( $_POST['ks_entity_url'] ) ) {
			update_post_meta( $post_id, '_ks_entity_url', esc_url_raw( $_POST['ks_entity_url'] ) );
		}
		if ( isset( $_POST['ks_entity_qid'] ) ) {
			update_post_meta( $post_id, '_ks_entity_qid', sanitize_text_field( $_POST['ks_entity_qid'] ) );
		}
		if ( isset( $_POST['ks_entity_sameas'] ) ) {
			$sameas = array_filter( array_map( 'esc_url_raw', explode( "\n", str_replace( "\r", '', $_POST['ks_entity_sameas'] ) ) ) );
			update_post_meta( $post_id, '_ks_entity_sameas', $sameas );
		}
	}
}
