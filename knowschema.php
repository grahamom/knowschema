<?php
/**
 * Plugin Name:       KnowSchema
 * Plugin URI:        https://knowschema.com
 * Description:       WordPress Schema Manager with AI Automation and Entity Publishing.
 * Version:           2.0.0
 * Requires at least: 6.2
 * Requires PHP:      8.0
 * Author:            Graham
 * Author URI:        https://graham.om
 * Text Domain:       knowschema
 * Domain Path:       /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'KNOWSCHEMA_VERSION', '2.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/core/class-activator.php
 */
function activate_knowschema() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/core/class-activator.php';
	KnowSchema_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/core/class-deactivator.php
 */
function deactivate_knowschema() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/core/class-deactivator.php';
	KnowSchema_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_knowschema' );
register_deactivation_hook( __FILE__, 'deactivate_knowschema' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/core/class-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
function run_knowschema() {

	$plugin = new KnowSchema\Core\Plugin();
	$plugin->run();

}
run_knowschema();
