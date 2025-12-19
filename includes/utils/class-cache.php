<?php

namespace KnowSchema\Utils;

class Cache {

	public static function get( $key, $group = 'knowschema' ) {
		return wp_cache_get( $key, $group );
	}

	public static function set( $key, $value, $group = 'knowschema', $expire = 0 ) {
		return wp_cache_set( $key, $value, $group, $expire );
	}

	public static function delete( $key, $group = 'knowschema' ) {
		return wp_cache_delete( $key, $group );
	}
	
	// Persistent cache via transient
	public static function get_transient( $key ) {
		return get_transient( 'ks_' . $key );
	}

	public static function set_transient( $key, $value, $expiration = HOUR_IN_SECONDS ) {
		return set_transient( 'ks_' . $key, $value, $expiration );
	}

	public static function delete_transient( $key ) {
		return delete_transient( 'ks_' . $key );
	}
}
