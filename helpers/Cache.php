<?php

namespace wpf\helpers;

use \Closure;

/**
 * Trait Cache
 * @package wpf\helpers
 */
trait Cache {
	/**
	 * @param string $key
	 * @param Closure $fallback
	 *
	 * @return mixed
	 */
	protected static function cache( Closure $fallback, string $key = 'single' ) {
		global $wp_object_cache;

		$d_bt  = debug_backtrace()[1];
		$class = $d_bt['class'];
		$key   = join( '/', [ $d_bt['function'], md5( $key ) ] );

		$cache = $wp_object_cache->get( $class, $key );
		if ( ! $cache ) {
			$cache = $fallback();
			$wp_object_cache->set( $class, $cache, $key );
		}

		return $cache;
	}
}