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
		echo '<option value="FAQPage" ' . selected( $template, 'FAQPage', false ) . '>FAQPage</option>';
		echo '<option value="Review" ' . selected( $template, 'Review', false ) . '>Review</option>';
		echo '<option value="Event" ' . selected( $template, 'Event', false ) . '>Event</option>';
		echo '</select></p>';

		echo '<div id="ks-readiness-badge" style="margin-bottom:15px; display:none;">';
		echo '<span class="ks-badge" style="padding:5px 10px; border-radius:3px; color:#fff; font-weight:bold;"></span>';
		echo '<ul class="ks-missing-fields" style="font-size:11px; margin-top:5px; color:#666;"></ul>';
		echo '</div>';

		// Template Specific Data
		echo '<div id="ks-template-data" style="margin-top:20px;">';
		
		// Product Data
		$product_data = get_post_meta( $post->ID, '_ks_product_data', true );
		echo '<div class="ks-group ks-group-Product" style="display:' . ( $template === 'Product' ? 'block' : 'none' ) . ';">';
		echo '<h4>' . __( 'Product Details', 'knowschema' ) . '</h4>';
		echo '<p><input type="text" name="ks_product[sku]" value="' . esc_attr( isset( $product_data['sku'] ) ? $product_data['sku'] : '' ) . '" placeholder="SKU" style="width:100%"></p>';
		echo '<p><input type="text" name="ks_product[price]" value="' . esc_attr( isset( $product_data['price'] ) ? $product_data['price'] : '' ) . '" placeholder="Price" style="width:48%"> ';
		echo '<input type="text" name="ks_product[currency]" value="' . esc_attr( isset( $product_data['currency'] ) ? $product_data['currency'] : 'USD' ) . '" placeholder="Currency (USD)" style="width:48%"></p>';
		echo '</div>';

		// Review Data
		$review_data = get_post_meta( $post->ID, '_ks_review_data', true );
		echo '<div class="ks-group ks-group-Review" style="display:' . ( $template === 'Review' ? 'block' : 'none' ) . ';">';
		echo '<h4>' . __( 'Review Details', 'knowschema' ) . '</h4>';
		echo '<p><input type="number" name="ks_review[rating]" value="' . esc_attr( isset( $review_data['rating'] ) ? $review_data['rating'] : '5' ) . '" min="1" max="5" style="width:100px"> ' . __( 'Rating (1-5)', 'knowschema' ) . '</p>';
		echo '<p><textarea name="ks_review[body]" placeholder="Review Summary" style="width:100%">' . esc_textarea( isset( $review_data['body'] ) ? $review_data['body'] : '' ) . '</textarea></p>';
		echo '</div>';

		// Event Data
		$event_data = get_post_meta( $post->ID, '_ks_event_data', true );
		echo '<div class="ks-group ks-group-Event" style="display:' . ( $template === 'Event' ? 'block' : 'none' ) . ';">';
		echo '<h4>' . __( 'Event Details', 'knowschema' ) . '</h4>';
		echo '<p><input type="datetime-local" name="ks_event[start_date]" value="' . esc_attr( isset( $event_data['start_date'] ) ? $event_data['start_date'] : '' ) . '" style="width:100%"> ' . __( 'Start Date', 'knowschema' ) . '</p>';
		echo '<p><input type="text" name="ks_event[location_name]" value="' . esc_attr( isset( $event_data['location_name'] ) ? $event_data['location_name'] : '' ) . '" placeholder="Location Name" style="width:100%"></p>';
		echo '</div>';

		echo '</div>';

		// FAQ Editor (Simplified)
		$faqs = get_post_meta( $post->ID, '_ks_faqs', true );
		if ( ! is_array( $faqs ) ) { $faqs = array(); }
		
		echo '<div id="ks-faq-editor" style="margin-top:20px; border-top:1px solid #ccc; padding-top:10px;">';
		echo '<h4>' . __( 'FAQ Questions', 'knowschema' ) . '</h4>';
		echo '<div class="ks-faq-items">';
		foreach ( $faqs as $index => $faq ) {
			echo '<div class="ks-faq-item" style="margin-bottom:10px; border:1px solid #eee; padding:10px;">';
			echo '<input type="text" name="ks_faqs[' . $index . '][question]" value="' . esc_attr( $faq['question'] ) . '" placeholder="Question" style="width:100%; margin-bottom:5px;"><br>';
			echo '<textarea name="ks_faqs[' . $index . '][answer]" placeholder="Answer" style="width:100%;">' . esc_textarea( $faq['answer'] ) . '</textarea>';
			echo '</div>';
		}
		// Add one empty one for new entries
		$next_index = count( $faqs );
		echo '<div class="ks-faq-item" style="margin-bottom:10px; border:1px solid #eee; padding:10px;">';
		echo '<input type="text" name="ks_faqs[' . $next_index . '][question]" value="" placeholder="New Question" style="width:100%; margin-bottom:5px;"><br>';
		echo '<textarea name="ks_faqs[' . $next_index . '][answer]" placeholder="New Answer" style="width:100%;"></textarea>';
		echo '</div>';
		echo '</div>';
		echo '</div>';

		echo '<p><em>' . __( 'More fields will appear here based on template selection.', 'knowschema' ) . '</em></p>';
		
		// AI Actions (Pro Placeholder)
		echo '<div id="ks-ai-actions" style="margin-top:20px; padding:15px; background:#f0f0f1; border:1px dashed #ccc;">';
		echo '<strong>' . __( 'AI Assistant', 'knowschema' ) . '</strong>';
		
		$ai_service = new \KnowSchema\AI\AI_Service();
		if ( $ai_service->is_active() ) {
			echo '<button type="button" class="button button-secondary" id="ks-ai-draft-btn">' . __( 'Draft Schema with AI', 'knowschema' ) . '</button>';
		} else {
			echo '<p style="margin-top:5px; margin-bottom:10px; font-size:12px;">' . __( 'Automate schema creation with AI. Upgrade to Pro to enable drafting.', 'knowschema' ) . '</p>';
			echo '<button type="button" class="button button-secondary" disabled>' . __( 'Draft Schema with AI (Pro)', 'knowschema' ) . '</button>';
		}
		echo '</div>';

		// Wikidata Edit Plan (Free)
		if ( $post->post_type === 'ks_entity' ) {
			echo '<div id="ks-wikidata-actions" style="margin-top:20px; padding:15px; background:#e7f5fe; border:1px solid #b8e6ff;">';
			echo '<strong>' . __( 'Wikidata Integration', 'knowschema' ) . '</strong>';
			echo '<p style="font-size:12px;">' . __( 'Generate a structured list of statements for Wikidata.', 'knowschema' ) . '</p>';
			echo '<button type="button" class="button button-secondary" id="ks-wikidata-plan-btn">' . __( 'Export Edit Plan', 'knowschema' ) . '</button>';
			echo '<div id="ks-wikidata-plan-container" style="display:none; margin-top:10px;"><textarea readonly style="width:100%; height:150px; font-size:11px;"></textarea></div>';
			echo '</div>';
		}
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

		if ( isset( $_POST['ks_faqs'] ) && is_array( $_POST['ks_faqs'] ) ) {
			$faqs = array();
			foreach ( $_POST['ks_faqs'] as $faq ) {
				if ( ! empty( $faq['question'] ) ) {
					$faqs[] = array(
						'question' => sanitize_text_field( $faq['question'] ),
						'answer'   => wp_kses_post( $faq['answer'] ),
					);
				}
			}
			update_post_meta( $post_id, '_ks_faqs', $faqs );
		}

		if ( isset( $_POST['ks_product'] ) ) {
			update_post_meta( $post_id, '_ks_product_data', array_map( 'sanitize_text_field', $_POST['ks_product'] ) );
		}
		if ( isset( $_POST['ks_review'] ) ) {
			update_post_meta( $post_id, '_ks_review_data', array_map( 'sanitize_text_field', $_POST['ks_review'] ) );
		}
		if ( isset( $_POST['ks_event'] ) ) {
			update_post_meta( $post_id, '_ks_event_data', array_map( 'sanitize_text_field', $_POST['ks_event'] ) );
		}
	}

}
