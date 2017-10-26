<?php

namespace wpf\helpers;

use \InvalidArgumentException;

/**
 * Class Safe
 * @package wpf\helpers
 */
class Safe {

	/**
	 * @param string $name
	 * @param array $args
	 */
	public static function __callStatic( string $name, $args ) {
		if ( function_exists( "filter_{$name}" ) ) {
			$name = "filter_{$name}";
		} elseif ( function_exists( "ctype_{$name}" ) ) {
			$name = "ctype_{$name}";
		} else {
			$class = self::class;
			throw new InvalidArgumentException( __( "Method '{$class}'::'{$name}()' not found." ) );
		}

		call_user_func( $name, ...$args );
	}
}