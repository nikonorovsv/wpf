<?php
namespace wpf;

use \ReflectionClass;
use \SplSubject;
use \SplObjectStorage;
use \SplObserver;
use \InvalidArgumentException;
use \wpf\base\IConfigurable;
use \wpf\base\View;
use \wpf\base\Action;
use \wpf\base\FileNotFoundException;
use \wpf\base\ConfigException;
use \wpf\helpers\ArrayHelper;
use \wpf\helpers\WP;

/**
 * Class App
 * @package wpf
 */
class App
	implements SplSubject, IConfigurable {
	private static $_instance;
	/**
	 * @var array
	 */
	private $_attributes = [];
	private $_notified   = FALSE;
	private $_storage;
	private $_renderer;
	
	/**
	 * App constructor.
	 */
	private function __construct() {
		// Create SplObject storage
		$this->_storage = new SplObjectStorage();
	}
	
	/**
	 * @return App
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * @return View
	 */
	public function getRenderer() {
		return $this->_renderer;
	}
	
	/**
	 * @param View $renderer
	 */
	public function setRenderer( View $renderer ) {
		$this->_renderer = $renderer;
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
		return (bool) $this->$name ?? $this->_attributes[ $name ] ?? FALSE;
	}
	
	/**
	 * @param $name
	 */
	public function __unset( string $name ) {
		if ( $this->_attributes[ $name ] ) {
			unset( $this->_attributes[ $name ] );
		} elseif ( $this->$name ) {
			unset( $this->$name );
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
	 * @param $object
	 * @param $atts
	 *
	 * @return mixed
	 */
	public static function configure( $object, array $atts ) {
		$class = new ReflectionClass( $object );
		if ( ! $class->implementsInterface( '\wpf\base\IConfigurable' ) ) {
			throw new InvalidArgumentException( __( "Class '{$class->getName()}' must implement IConfigurable interface." ) );
		}
		foreach ( $atts as $name => $value ) {
			$object->$name = $value;
		}
		
		return $object;
	}

	/**
	 * @param array $observers
	 *
	 * @throws ConfigException
	 */
	public function applyObservers( array $observers = [] ) {
		// Only one instance
		if ( $this->_notified ) {
			throw new ConfigException( __( "Method 'App::applyObservers' had already been called. It is only possible one time." ) );
		}
		// Add Default Observers
		if ( $this->observers ) {
			$observers = array_merge( $this->observers, $observers );
		}
		// Attach all
		$this->attachArray( $observers );
		$this->notify();
		//
		$this->_notified = TRUE;
	}
	
	/**
	 * @param array $observers
	 */
	public function attachArray( array $observers ) {
		foreach ( $observers as $observer ) {
			$class = new ReflectionClass( $observer );
			if ( ! $class->implementsInterface( '\SplObserver' ) ) {
				throw new InvalidArgumentException( __( "Class '{$class->getName()}' must implement SplObserver interface." ) );
			}
			new $observer( $this );
		}
	}
	
	/**
	 * @param SplObserver $observer
	 */
	public function attach( SplObserver $observer ) {
		$this->_storage->attach( $observer );
	}
	
	/**
	 * @param SplObserver $observer
	 */
	public function detach( SplObserver $observer ) {
		$this->_storage->detach( $observer );
	}
	
	/**
	 * @param SplObserver $observer
	 */
	public function contains( SplObserver $observer ) {
		$this->_storage->contains( $observer );
	}
	
	/**
	 *
	 */
	public function notify() {
		foreach ( $this->_storage as $observer ) {
			$observer->update( $this );
		}
	}

	/**
	 * @param $conf
	 *
	 * @return array
	 * @throws FileNotFoundException
	 */
	public static function readJsonConf( $conf ) {
		$result = [];
		foreach ( (array) $conf as $item ) {
			$file = WP::path( $item );
			if ( ! file_exists( $file ) ) {
				throw new FileNotFoundException( "File '{$file}' not found." );
			}
			$result = ArrayHelper::merge( $result, json_decode( file_get_contents( $file ), TRUE ) );
		}
		
		return $result;
	}
	
	/**
	 * @return string
	 */
	public static function requestUri() {
		$current_url = trim( esc_url_raw( add_query_arg( [] ) ), '/' );
		$home_path   = trim( parse_url( home_url(), PHP_URL_PATH ), '/' );
		if ( $home_path && strpos( $current_url, $home_path ) === 0 ) {
			$current_url = trim( substr( $current_url, strlen( $home_path ) ), '/' );
		}
		
		return $current_url;
	}

	/**
	 * @return string
	 */
	public static function uri() {
		$url  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://'.$_SERVER["SERVER_NAME"] :  'https://'.$_SERVER["SERVER_NAME"];
		$url .= ( $_SERVER["SERVER_PORT"] != 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
		$url .= $_SERVER["REQUEST_URI"];

		return $url;
	}
	
	/**
	 * Adds a route to the collection.
	 *
	 * The syntax used in the $route string depends on the used route parser.
	 *
	 * @param string|string[] $httpMethod
	 * @param string $route
	 * @param mixed $handler
	 */
//	public function addRoute( $httpMethod, $route, $handler ) {
//		$this->_route_collector->addRoute( $httpMethod, $route, $handler );
//	}
	
	/**
	 * @return mixed
	 */
//	public function getRoute() {
//		$dispatcher = new GroupCountDispatcher( $this->_route_collector->getData() );
//		// Fetch method and URI from somewhere
//		$httpMethod = $_SERVER[ 'REQUEST_METHOD' ];
//		$uri        = $_SERVER[ 'REQUEST_URI' ];
//		// Strip query string (?foo=bar) and decode URI
//		if ( FALSE !== $pos = strpos( $uri, '?' ) ) {
//			$uri = substr( $uri, 0, $pos );
//		}
//		$uri = rawurldecode( $uri );
//
//		return $dispatcher->dispatch( $httpMethod, $uri );
//	}
}