<?php
namespace wpf\base;

use \wpf\App;
use \IteratorAggregate;
use \Generator;

/**
 * Class Component
 * @package wpf\base
 */
class Component
	implements IConfigurable, IteratorAggregate {
	/**
	 * @var array
	 */
	private $_attributes = [];
	
	/**
	 * Component constructor.
	 *
	 * @param array $conf
	 */
	public function __construct( array $conf = [] ) {
		$this->loadAttributes( $conf );
	}
	
	/**
	 * @param $name
	 *
	 * @return null
	 */
	public function __get( string $name ) {
		return $this->_attributes[ $name ] ?? NULL;
	}
	
	/**
	 * @param $name
	 * @param $value
	 */
	public function __set( string $name, $value ) {
		$this->_attributes[ $name ] = $value;
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
		if ( isset( $this->_attributes[ $name ] ) ) {
			unset( $this->_attributes[ $name ] );
		}
	}
	
	/**
	 * @return array
	 */
	public function getAttributes() {
		return $this->_attributes;
	}
	
	/**
	 * @param array $atts
	 */
	public function loadAttributes( array $atts ) {
		foreach ( $atts as $name => $value ) {
			$this->$name = $value;
		}
	}

	/**
	 * @return Generator
	 */
	public function getIterator() {
		foreach ( $this->_attributes as $name => $value ) {
			yield $name => $value;
		}
	}
}