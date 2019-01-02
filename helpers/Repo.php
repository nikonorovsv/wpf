<?php

namespace wpf\helpers;

use \wpf\base\IConfigurable;
use \IteratorAggregate;

/**
 * Class Repo
 * @package wpf\helpers
 */
class Repo implements IConfigurable, IteratorAggregate
{
	private static $_data = [];

	private static $_instance_name = null;
	private static $_instance = null;

	private static $_current = [];

	/**
	 * Repo constructor.
	 *
	 * @param string $name
	 * @param array $args
	 */
	private function __construct( string $name, array $args ) {
		$glob = strtoupper("_{$name}");
		global $$glob;
		if ( isset( $$glob ) ) {
			self::$_current = &$$glob;
		} else {
			if ( ! isset( self::$_data[ $name ] ) ) {
				self::$_data[ $name ] = [];
			}
			self::$_current = &self::$_data[ $name ];
		}
		self::$_instance_name = $name;

		$this->load( $args );
	}

	/**
	 * @param string $name
	 * @param $args
	 *
	 * @return mixed
	 */
	public static function __callStatic( string $name, $args ) {
		if ( ( $name != self::$_instance_name ) || is_null( self::$_instance ) ) {
			self::$_instance = new self( $name, $args[0] );
		}

		return self::$_instance;
	}

	/**
	 * @param $name
	 *
	 * @return null
	 */
	public function __get( string $name ) {
		return self::$_current[ $name ] ?? null;
	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function __set( string $name, $value ) {
		self::$_current[ $name ] = $value;
	}

	/**
	 * @param $name
	 *
	 * @return bool
	 */
	public function __isset( string $name ) {
		return (bool) $this->$name;
	}

	/**
	 * @param $name
	 */
	public function __unset( string $name ) {
		if ( isset( self::$_current[ $name ] ) ) {
			unset( self::$_current[ $name ] );
		}
	}

	/**
	 * @return array
	 */
	public function data(): array {
		return self::$_current;
	}

	/**
	 * @param array $atts
	 */
	public function load( array $atts ) {
		foreach ( $atts as $name => $value ) {
			$this->$name = $value;
		}
	}

	/**
	 * @return Generator
	 */
	public function getIterator() {
		foreach ( self::$_current as $name => $value ) {
			yield $name => $value;
		}
	}
}