<?php

namespace KnowSchema\Admin;

class Admin_Pages {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function add_plugin_admin_menu() {
		add_menu_page(
			'KnowSchema Settings',
			'KnowSchema',
			'manage_options',
			'knowschema',
			array( $this, 'display_settings_page' ),
			'dashicons-networking', // Graph icon
			100
		);

		add_submenu_page(
			'knowschema',
			'Settings',
			'Settings',
			'manage_options',
			'knowschema',
			array( $this, 'display_settings_page' )
		);
		
		// Note: The CPT 'ks_entity' will add itself here if show_in_menu is set to 'knowschema'
		// But show_in_menu = 'knowschema' requires the parent menu to be registered first.
		// Since we register CPT on 'init' and menu on 'admin_menu', it usually works if correct slug is used.
	}

	public function display_settings_page() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'knowschema_options' );
				do_settings_sections( 'knowschema' );
				submit_button( 'Save Settings' );
				?>
			</form>
		</div>
		<?php
	}

	public function register_settings() {
		register_setting( 'knowschema_options', 'knowschema_options', array( $this, 'validate_options' ) );

		add_settings_section(
			'knowschema_main_section',
			__( 'Global Settings', 'knowschema' ),
			null,
			'knowschema'
		);

		add_settings_field(
			'knowschema_org_name',
			__( 'Organization Name', 'knowschema' ),
			array( $this, 'render_text_field' ),
			'knowschema',
			'knowschema_main_section',
			array(
				'label_for' => 'knowschema_org_name',
				'name'      => 'org_name',
			)
		);
	}

	public function render_text_field( $args ) {
		$options = get_option( 'knowschema_options' );
		$val     = isset( $options[ $args['name'] ] ) ? $options[ $args['name'] ] : '';
		?>
		<input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="knowschema_options[<?php echo esc_attr( $args['name'] ); ?>]" value="<?php echo esc_attr( $val ); ?>">
		<?php
	}

	public function validate_options( $input ) {
		$valid = array();
		$valid['org_name'] = sanitize_text_field( $input['org_name'] );
		return $valid;
	}

}
