<?php

use \wpf\helpers\WP;
use \wpf\helpers\Debug;
use \wpf\helpers\Repo;

/**
 * @param $object
 */
if (!function_exists('vd')) {
  function vd(...$object)
  {
    Debug::varDump($object);
  }
}
/**
 * @param $object
 */
if (!function_exists('ve')) {
  function ve($object)
  {
    Debug::varExport($object);
  }
}
/**
 * @param $msg
 * @param string $title
 *
 * @return bool
 */
if (!function_exists('log_it')) {
  function log_it($msg, $title = '')
  {
    return Debug::logIt($msg, $title, 'debug.log');
  }
}
/**
 * @param $data
 *
 * @return string
 */
if (!function_exists('clear_string')) {
  function clear_string($string)
  {
    return WP::clearString($string);
  }
}
/**
 * @param int $blog_id
 * @param Closure $callback
 * @param array ...$args
 *
 * @return mixed
 */
if (!function_exists('run_on_blog')) {
  function run_on_blog(int $blog_id, callable $callback, ...$args)
  {
    return WP::runOnBlog($blog_id, $callback, ...$args);
  }
}
/**
 * @param array ...$args
 *
 * @return mixed
 */
if (!function_exists('icon')) {
  function icon(...$args)
  {
    return \wpf\helpers\Icon::ui(...$args);
  }
}

/**
 * @param string $datetime
 *
 * @return mixed|string
 */
if (!function_exists('__rd')) {
  function __rd(string $datetime)
  {
    return \wpf\helpers\Date::rd($datetime);
  }
}

/**
 * @return mixed
 */
if (!function_exists('app')) {
  function app()
  {
    return \wpf\App::instance();
  }
}

/**
 * @param string|null $dir
 *
 * @return \wpf\base\View
 */
if (!function_exists('view')) {
  function view(string $dir = NULL)
  {
    if ($dir) {
      return new \wpf\base\View($dir);
    }

    return app()->getRenderer();
  }
}

/**
 * @param $tpl
 * @param array $vars
 * @param string|null $dir
 *
 * @return string
 */
if (!function_exists('render')) {
  function render($tpl, array $vars = [], string $dir = NULL)
  {
    return view($dir)->render($tpl, $vars);
  }
}

/**
 * @param array $conf
 *
 * @return \wpf\base\Component
 */
if (!function_exists('get_component')) {
  function get_component(array $conf = [])
  {
    return new \wpf\base\Component($conf);
  }
}

/**
 * @param string $name
 *
 * @return array
 */
if (!function_exists('repo')) {
  function repo(string $name, array $args = [])
  {
    return Repo::$name($args);
  }
}

/**
 * @param string $name
 *
 * @return array
 */
if (!function_exists('wp_menu_items')) {
  function wp_menu_items(string $name)
  {
    return (new \wpf\wp\Menu($name))->items();
  }
}


/**
 * @return array
 */
if (!function_exists('wp_breadcrumbs')) {
  function wp_breadcrumbs()
  {
    return (new \wpf\wp\Crumbs())->items();
  }
}

/**
 * @param string $class_name
 * @param array $conf
 * @return string
 */
if (!function_exists('wpf_widget')) {
  function wpf_widget(string $widget_class_name, array $conf = []): string
  {
    return (String)new $widget_class_name($conf);
  }
}
