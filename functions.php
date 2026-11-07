<?php

use wpf\App;
use wpf\base\Component;
use wpf\base\View;
use wpf\helpers\Date;
use wpf\helpers\Icon;
use wpf\helpers\WP;
use wpf\helpers\Debug;
use wpf\helpers\Repo;
use wpf\wp\Crumbs;
use wpf\wp\Menu;


if (!function_exists('vd')) {
    /**
     * @param ...$object
     * @return void
     */
    function vd(...$object): void
    {
        Debug::varDump($object);
    }
}

if (!function_exists('ve')) {
    /**
     * @param $object
     * @return void
     */
    function ve($object): void
    {
        Debug::varExport($object);
    }
}


if (!function_exists('log_it')) {
    /**
     * @param string $msg
     * @param string $title
     * @return void
     */
    function log_it(string $msg, string $title = ''): void
    {
        Debug::logIt($msg, $title, 'debug.log');
    }
}


if (!function_exists('clear_string')) {
    /**
     * @param $string
     * @return string
     */
    function clear_string($string): string
    {
        return WP::clearString($string);
    }
}

if (!function_exists('run_on_blog')) {
    /**
     * @param int $blog_id
     * @param callable $callback
     * @param ...$args
     * @return mixed
     */
    function run_on_blog(int $blog_id, callable $callback, ...$args): mixed
    {
        return WP::runOnBlog($blog_id, $callback, ...$args);
    }
}

if (!function_exists('icon')) {
    /**
     * @param ...$args
     * @return string
     */
    function icon(...$args): string
    {
        return Icon::ui(...$args);
    }
}

if (!function_exists('__rd')) {

    /**
     * @param string $datetime
     * @return string
     */
    function __rd(string $datetime)
    {
        return Date::rd($datetime);
    }
}

if (!function_exists('app')) {
    function app(): App
    {
        return App::instance();
    }
}


if (!function_exists('view')) {
    /**
     * @param string|null $dir
     *
     * @return View
     */
    function view(string $dir = NULL): View
    {
        if ($dir) {
            return new View($dir);
        }

        return app()->getRenderer();
    }
}

if (!function_exists('render')) {
    /**
     * @param $tpl
     * @param array $vars
     * @param string|null $dir
     *
     * @return string
     */
    function render($tpl, array $vars = [], string $dir = NULL): string
    {
        return view($dir)->render($tpl, $vars);
    }
}


if (!function_exists('get_component')) {
    /**
     * @param array $conf
     *
     * @return Component
     */
    function get_component(array $conf = []): Component
    {
        return new Component($conf);
    }
}


if (!function_exists('repo')) {
    /**
     * @param string $name
     * @param array $args
     * @return array
     */
    function repo(string $name, array $args = []): array
    {
        return Repo::$name($args);
    }
}

if (!function_exists('wp_menu_items')) {
    /**
     * @param string $name
     *
     * @return array
     */
    function wp_menu_items(string $name): array
    {
        return (new Menu($name))->items();
    }
}

if (!function_exists('wp_breadcrumbs')) {
    /**
     * @return array
     */
    function wp_breadcrumbs(): array
    {
        return (new Crumbs())->items();
    }
}

if (!function_exists('wpf_widget')) {
    /**
     * @param string $widget_class_name
     * @param array $conf
     * @return string
     */
    function wpf_widget(string $widget_class_name, array $conf = []): string
    {
        return (string)new $widget_class_name($conf);
    }
}
