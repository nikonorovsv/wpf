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
  implements SplSubject, IConfigurable
{
  private static $_instance;
  /**
   * @var array
   */
  private $_attributes = [];
  private $_notified = FALSE;
  private $_storage;
  private $_renderer;

  /**
   * App constructor.
   */
  private function __construct()
  {
    // Create SplObject storage
    $this->_storage = new SplObjectStorage();
  }

  /**
   * @return App
   */
  public static function instance()
  {
    if (!self::$_instance) {
      self::$_instance = new self();
    }

    return self::$_instance;
  }

  /**
   * @return View
   */
  public function getRenderer()
  {
    return $this->_renderer;
  }

  /**
   * @param View $renderer
   */
  public function setRenderer(View $renderer)
  {
    $this->_renderer = $renderer;
  }

    /**
     * @param string $name
     *
     * @param null   $default
     *
     * @return null
     */
  public function __get(string $name, $default = null)
  {
    return array_key_exists($name, $this->_attributes)
        ? $this->_attributes[$name]
        : $default;
  }

  /**
   * @param $name
   * @param $value
   */
  public function __set(string $name, $value)
  {
    $this->_attributes[$name] = $value;
  }

  /**
   * @param $name
   *
   * @return bool
   */
  public function __isset(string $name)
  {
    return (bool)$this->$name ?? $this->_attributes[$name] ?? FALSE;
  }

  /**
   * @param $name
   */
  public function __unset(string $name)
  {
    if (array_key_exists($name, $this->_attributes)) {
      unset($this->_attributes[$name]);
    } elseif ($this->$name) {
      unset($this->$name);
    }
  }

  /**
   * @return array
   */
  public function getAttributes()
  {
    return $this->_attributes;
  }

  /**
   * @param array $attributes
   */
  public function loadAttributes(array $attributes)
  {
    foreach ($attributes as $name => $value) {
      $this->$name = $value;
    }
  }

  /**
   * @param $object
   * @param array $atts
   * @return mixed
   * @throws \ReflectionException
   */
  public static function configure($object, array $atts)
  {
    $class = new ReflectionClass($object);
    if (!$class->implementsInterface('\wpf\base\IConfigurable')) {
      throw new InvalidArgumentException(
        __("Class '{$class->getName()}' must implement IConfigurable interface.", 'wpf'));
    }
    foreach ($atts as $name => $value) {
      $object->$name = $value;
    }

    return $object;
  }

  /**
   * @param array $observers
   * @throws ConfigException
   * @throws \ReflectionException
   */
  public function applyObservers(array $observers = [])
  {
    // Only one instance
    if ($this->_notified) {
      throw new ConfigException(
        __("Method 'App::applyObservers' had already been called. It is only possible one time.", 'wpf'));
    }
    // Add Default Observers
    if ($this->observers) {
      $observers = array_merge($this->observers, $observers);
    }
    // Attach all
    $this->attachArray($observers);
    $this->notify();
    //
    $this->_notified = TRUE;
  }

  /**
   * @param array $observers
   * @throws \ReflectionException
   */
  public function attachArray(array $observers)
  {
    foreach ($observers as $observer) {
      $class = new ReflectionClass($observer);
      if (!$class->implementsInterface('\SplObserver')) {
        throw new InvalidArgumentException(
          __("Class '{$class->getName()}' must implement SplObserver interface.", 'wpf'));
      }
      new $observer($this);
    }
  }

  /**
   * @param SplObserver $observer
   */
  public function attach(SplObserver $observer)
  {
    $this->_storage->attach($observer);
  }

  /**
   * @param SplObserver $observer
   */
  public function detach(SplObserver $observer)
  {
    $this->_storage->detach($observer);
  }

  /**
   * @param SplObserver $observer
   */
  public function contains(SplObserver $observer)
  {
    $this->_storage->contains($observer);
  }

  /**
   *
   */
  public function notify()
  {
    foreach ($this->_storage as $observer) {
      $observer->update($this);
    }
  }

  /**
   * @param $conf
   *
   * @return array
   * @throws FileNotFoundException
   */
  public static function readJsonConf($conf)
  {
    $result = [];
    foreach ((array)$conf as $file) {
      if (!file_exists($file)) {
        throw new FileNotFoundException(
          __("File '{$file}' not found.", 'wpf'));
      }
      $result = ArrayHelper::merge($result, json_decode(file_get_contents($file), TRUE));
    }

    return $result;
  }

  /**
   * @return string
   */
  public static function requestUri()
  {
    $current_url = trim(esc_url_raw(add_query_arg([])), '/');
    $home_path = trim(parse_url(home_url(), PHP_URL_PATH), '/');
    if ($home_path && strpos($current_url, $home_path) === 0) {
      $current_url = trim(substr($current_url, strlen($home_path)), '/');
    }

    return $current_url;
  }

  /**
   * @return string
   */
  public static function uri(): string
  {
    $server = repo('server');
    $url = [
      @($server->HTTPS != 'on') ? 'http://' : 'https://',
      $server->SERVER_NAME,
      ($server->SERVER_PORT != 80) ? ":{$server->SERVER_PORT}" : '',
      $server->REQUEST_URI
    ];

    return join('', $url);
  }
}
