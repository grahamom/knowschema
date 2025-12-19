<?php

namespace KnowSchema\Core;

/**
 * The file that defines the core plugin class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 */
class Plugin {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @var      Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 */
	public function __construct() {
		if ( defined( 'KNOWSCHEMA_VERSION' ) ) {
			$this->version = KNOWSCHEMA_VERSION;
		} else {
			$this->version = '2.0.0';
		}
		$this->plugin_name = 'knowschema';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_rest_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Loader. Orchestrates the hooks of the plugin.
	 * - Settings. Manages global plugin settings.
	 * - Admin. Defines all hooks for the admin area.
	 * - Entity_CPT. Registers the custom post type.
	 * - Graph_Builder. Generates the schema graph.
	 *
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'core/class-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-admin-pages.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-metabox.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-preview-panel.php';

		/**
		 * The class responsible for the Entity CPT.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'entities/class-entity-cpt.php';

		/**
		 * The class responsible for building the schema graph.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'schema/class-graph-builder.php';

		/**
		 * Interfaces for Pro extensions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'ai/interface-ai-provider.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'ai/class-ai-service.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'wikidata/interface-wikidata-client.php';

		$this->loader = new Loader();

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 */
	private function define_admin_hooks() {

		$plugin_admin = new \KnowSchema\Admin\Admin( $this->get_plugin_name(), $this->get_version() );
		$admin_pages = new \KnowSchema\Admin\Admin_Pages( $this->get_plugin_name(), $this->get_version() );
		$metabox = new \KnowSchema\Admin\Metabox( $this->get_plugin_name(), $this->get_version() );
		$preview_panel = new \KnowSchema\Admin\Preview_Panel( $this->get_plugin_name(), $this->get_version() );
		$entity_cpt = new \KnowSchema\Entities\Entity_CPT( $this->get_plugin_name(), $this->get_version() );

		// Enqueue styles and scripts
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		// AJAX Preview
		$this->loader->add_action( 'wp_ajax_knowschema_preview_schema', $preview_panel, 'ajax_preview_schema' );
		$this->loader->add_action( 'wp_ajax_knowschema_wikidata_plan', $preview_panel, 'ajax_wikidata_plan' );

		// Register CPT
		$this->loader->add_action( 'init', $entity_cpt, 'register_cpt' );
		$this->loader->add_action( 'add_meta_boxes_ks_entity', $entity_cpt, 'add_meta_boxes' );
		$this->loader->add_action( 'save_post_ks_entity', $entity_cpt, 'save_entity_data' );

		// Add Admin Menu
		$this->loader->add_action( 'admin_menu', $admin_pages, 'add_plugin_admin_menu' );
		$this->loader->add_action( 'admin_init', $admin_pages, 'register_settings' );

		// Meta boxes
		$this->loader->add_action( 'add_meta_boxes', $metabox, 'add_meta_boxes' );
		$this->loader->add_action( 'save_post', $metabox, 'save_meta_box_data' );
		
		// Cache Invalidation (Simple)
		$this->loader->add_action( 'update_option_knowschema_options', $this, 'clear_global_cache' );
	}
	
	public function clear_global_cache() {
		delete_transient( 'ks_schema_graph_home' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 */
	private function define_public_hooks() {

		$graph_builder = new \KnowSchema\Schema\Graph_Builder( $this->get_plugin_name(), $this->get_version() );

		// Output JSON-LD in head or footer (default head)
		// We'll assume head for now, adjustable via settings later
		$this->loader->add_action( 'wp_head', $graph_builder, 'output_schema' );

	}

	/**
	 * Register REST hooks
	 */
	private function define_rest_hooks() {
		// Future REST API hooks
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
