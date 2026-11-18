<?php

use wpf\App;
use wpf\base\Component;
use wpf\base\FileNotFoundException;
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
     * Возвращает новый рендерер.
     * Может потребоваться для использования шаблонов не из директории по-умолчанию
     * см. /functions.php
     *
     * $app = app();
     * $app->loadAttributes( require_once get_theme_file_path( 'app/config.php' ) );
     *
     * // Установим View объект для функций рендера
     * $app->setRenderer( 'modules' );
     *
     * @param string|null $dir
     *
     * @return View
     */
    function view(?string $dir = null): View
    {
        if (is_null($dir)) {
            return app()->getRenderer();
        }

        return new View($dir);
    }
}

if (!function_exists('render')) {
    /**
     * Возвращает отрендеренный шаблон
     *
     * @param $template
     * @param array|null $props
     * @param string|null $dir
     * @return string
     */
    function render($template, ?array $props = [], ?string $dir = null): string
    {
        if (is_null($props)) {
            return '';
        }
        $view = view($dir);

        $template_name = null;
        if (is_string($template)) {
            $template_name = trim($template, '/\\');
        } elseif (is_array($template)) {
            $template_name = join('-', $template);
        }

        if (!$template_name) {
            do_action('template/error', json_encode($template));

            return '';
        }

        /**
         * Хуки для виджетов. Использование в других местах не исследовано.
         * Может привести к каше в коде.
         *
         * Но, когда виджет реализовывает свой метод рендер, можно удобно накинуть
         * настройки прямо из виджета, в конструкторе, например.
         */
        do_action("template/$template_name/view", $view, $dir);
        do_action("template/$template_name/filter", $props);

        try {
            $rendered = $view->render($template, $props);

            do_action("template/$template_name/rendered", $rendered);

            return $rendered;
        } catch (FileNotFoundException $e) {
            error_log($e->getMessage());
        }

        return '';
    }
}

if (function_exists('render_cache')) {
    /**
     * Include template with parameters of context
     * Function can have some arguments
     *
     * @param string $template
     * @param array $props
     *
     * @return string
     */
    function render_cache(string $template, array $props = []): string
    {
        $cache_key = 'tpl_' . md5($template . serialize($props));
        if (!$output = get_transient($cache_key)) {

            $output = render($template, $props);

            set_transient($cache_key, $output, HOUR_IN_SECONDS);
        }

        return $output;
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
