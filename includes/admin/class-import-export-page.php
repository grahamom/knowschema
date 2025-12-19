<?php

namespace KnowSchema\Admin;

class Import_Export_Page {

	private $plugin_name;
	private $version;
	private $importer;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'core/class-import-export.php';
		$this->importer = new \KnowSchema\Core\Import_Export( $plugin_name, $version );
	}

	public function display() {
		// Handle Export Download
		if ( isset( $_POST['knowschema_export'] ) && check_admin_referer( 'knowschema_export_action', 'knowschema_export_nonce' ) ) {
			$data = $this->importer->get_export_data();
			$json = wp_json_encode( $data, JSON_PRETTY_PRINT );
			header( 'Content-disposition: attachment; filename=knowschema-export-' . date( 'Y-m-d' ) . '.json' );
			header( 'Content-type: application/json' );
			echo $json;
			exit;
		}

		// Handle Import
		$import_log = array();
		if ( isset( $_POST['knowschema_import'] ) && check_admin_referer( 'knowschema_import_action', 'knowschema_import_nonce' ) ) {
			if ( ! empty( $_FILES['import_file']['tmp_name'] ) ) {
				$json = file_get_contents( $_FILES['import_file']['tmp_name'] );
				$dry_run = isset( $_POST['dry_run'] );
				$import_log = $this->importer->process_import( $json, $dry_run );
			}
		}
		?>
		<div class="wrap">
			<h1><?php _e( 'Import / Export', 'knowschema' ); ?></h1>
			
			<div style="background:#fff; padding:20px; border:1px solid #ccd0d4; margin-bottom:20px;">
				<h2><?php _e( 'Export', 'knowschema' ); ?></h2>
				<p><?php _e( 'Export your KnowSchema settings, entities, and post schema data.', 'knowschema' ); ?></p>
				<form method="post">
					<?php wp_nonce_field( 'knowschema_export_action', 'knowschema_export_nonce' ); ?>
					<input type="hidden" name="knowschema_export" value="1">
					<?php submit_button( __( 'Download Export File', 'knowschema' ) ); ?>
				</form>
			</div>

			<div style="background:#fff; padding:20px; border:1px solid #ccd0d4;">
				<h2><?php _e( 'Import', 'knowschema' ); ?></h2>
				<p><?php _e( 'Import a JSON file.', 'knowschema' ); ?></p>
				<form method="post" enctype="multipart/form-data">
					<?php wp_nonce_field( 'knowschema_import_action', 'knowschema_import_nonce' ); ?>
					<input type="hidden" name="knowschema_import" value="1">
					<p><input type="file" name="import_file" accept=".json" required></p>
					<p><label><input type="checkbox" name="dry_run" value="1" checked> <?php _e( 'Dry Run (Preview only)', 'knowschema' ); ?></label></p>
					<?php submit_button( __( 'Run Import', 'knowschema' ) ); ?>
				</form>

				<?php if ( ! empty( $import_log ) ) : ?>
					<div style="margin-top:20px; padding:10px; background:#f0f0f1; border-left:4px solid #00a0d2;">
						<h3><?php _e( 'Import Log', 'knowschema' ); ?></h3>
						<ul>
							<?php foreach ( $import_log as $entry ) : ?>
								<li><?php echo esc_html( $entry ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
