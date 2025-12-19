<?php
/**
 * PHPUnit Bootstrap
 */

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Custom Autoloader for KnowSchema (WP Naming Convention)
spl_autoload_register( function( $class ) {
	$prefix = 'KnowSchema\\';
	$base_dir = dirname( __DIR__ ) . '/includes/';

	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return;
	}

	$relative_class = substr( $class, $len );
	
	// Split into parts
	$parts = explode( '\\', $relative_class );
	$class_name = array_pop( $parts );
	
	// Convert namespace parts to lowercase directory paths
	$path = $base_dir;
	foreach ( $parts as $part ) {
		$path .= strtolower( $part ) . '/';
	}
	
	// Convert class name to file name: Template_WebSite -> class-template-website.php
	$filename = 'class-' . str_replace( '_', '-', strtolower( $class_name ) ) . '.php';
	
	$file = $path . $filename;
	
	if ( file_exists( $file ) ) {
		require_once $file;
	}
} );

// Mock WordPress functions
if ( ! function_exists( 'add_action' ) ) {
	function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {}
}
if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {}
}
if ( ! function_exists( '_e' ) ) {
	function _e( $text, $domain = 'default' ) { echo $text; }
}
if ( ! function_exists( '__' ) ) {
	function __( $text, $domain = 'default' ) { return $text; }
}
if ( ! function_exists( 'plugin_dir_path' ) ) {
	function plugin_dir_path( $file ) {
		return trailingslashit( dirname( $file ) );
	}
}
if ( ! function_exists( 'trailingslashit' ) ) {
	function trailingslashit( $string ) {
		return rtrim( $string, '/\\' ) . '/';
	}
}
if ( ! function_exists( 'home_url' ) ) {
	function home_url( $path = '' ) { return 'https://example.com' . $path; }
}
if ( ! function_exists( 'get_bloginfo' ) ) {
	function get_bloginfo( $show = '' ) { return 'Test Site'; }
}
if ( ! function_exists( 'get_site_icon_url' ) ) {
	function get_site_icon_url( $size = 512 ) { return 'https://example.com/icon.png'; }
}
if ( ! function_exists( 'get_option' ) ) {
	function get_option( $option, $default = false ) { return is_array($default) ? $default : array(); }
}
if ( ! function_exists( 'get_post' ) ) {
	function get_post( $post = null, $output = OBJECT, $filter = 'raw' ) {
		return null; // Return null to simulate no post found
	}
}
if ( ! function_exists( 'get_posts' ) ) {
	function get_posts( $args = null ) {
		return array();
	}
}
if ( ! function_exists( 'get_post_meta' ) ) {
	function get_post_meta( $post_id, $key = '', $single = false ) {
		return $single ? '' : array();
	}
}
if ( ! function_exists( 'get_the_post_thumbnail_url' ) ) {
	function get_the_post_thumbnail_url( $post = null, $size = 'post-thumbnail' ) {
		return '';
	}
}
if ( ! function_exists( 'esc_url_raw' ) ) {
	function esc_url_raw( $url, $protocols = null ) { return $url; }
}
if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $str ) { return trim( $str ); }
}