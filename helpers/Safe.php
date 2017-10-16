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
		if ( ! function_exists( "filter_{$name}" ) ) {
			$class = self::class;
			throw new InvalidArgumentException( __( "Method '{$class}'::'{$name}()' not found." ) );
		}
		call_user_func( "filter_{$name}", ...$args );
	}
}