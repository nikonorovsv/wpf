<?php
namespace wpf\wp;

use \wpf\helpers\WP;

/**
 * Class Filters
 * @package wpf\wp
 */
class Filters {
	/**
	 * @var null|Filters
	 */
	private static $_instance;
	
	/**
	 * Filters constructor.
	 */
	private function __construct() { }
	
	/**
	 * @return null|Filters
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * @param string $name
	 * @param array $args
	 */
	public static function __callStatic( string $name, $args ) {
		if ( ! function_exists("{$name}_filter") || ! function_exists("{$name}_action") ) {
			$class = self::class;
			throw new InvalidArgumentException( WP::l18n("Method '$class'::'$name' not found.") );
		}
		call_user_func("{$name}_filter", ...$args );
	}
	
	/**
	 * @param $tag
	 * @param bool $priority
	 *
	 * @return bool|true
	 */
	public static function removeAll( $tag, $priority = FALSE ) {
		return remove_all_filters( $tag, $priority );
	}
	
	/**
	 * @param $tag
	 * @param $value
	 *
	 * @return mixed
	 */
	public function apply( $tag, $value ) {
		return apply_filters( $tag, $value );
	}
	
	/**
	 * @param $tag
	 * @param $args
	 *
	 * @return mixed
	 */
	public function applyRefArray( $tag, $args ) {
		return apply_filters_ref_array( $tag, $args );
	}
}