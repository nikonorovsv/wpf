<?php

namespace wpf\base;

defined('ABSPATH') || exit;

/**
 * Class View
 * @package wpf\base
 */
class View implements IView
{

    protected string $dir;

    /**
     * View constructor.
     *
     * @param string $dir From theme directory
     */
    public function __construct(string $dir = '')
    {
        $this->dir = get_theme_file_path($dir);
    }

    /**
     * @param $tpl
     * @param array $props
     *
     * @return string
     * @throws FileNotFoundException
     */
    public function render($tpl, array $props = []): string
    {
        $name = null;
        $slug = $tpl;
        if (is_array($tpl)) {
            $slug = array_shift($tpl);
            $name = array_shift($tpl);
        }
        self::begin();
        $this->part($slug, $name, $props);

        return self::end();
    }

    /**
     * Открывает буфер
     */
    public static function begin(): void
    {
        ob_start();
        ob_implicit_flush(false);
    }

    /**
     * Закрывает буфер и возвращает содержимое
     *
     * @return string
     */
    public static function end(): string
    {
        return ob_get_clean();
    }

    /**
     * @param string $slug
     * @param string|null $name
     * @param array $props
     *
     * @return void
     * @throws FileNotFoundException
     */
    public function part(string $slug, string $name = null, array $props = []): void
    {
        $templates = [];
        if ($name) {
            $templates[] = "$slug-$name.php";
        }
        $templates[] = "$slug.php";
        extract($props, EXTR_OVERWRITE);
        $locate = $this->locate($templates);
        if (!empty($locate)) {
            require $locate;
        }
    }

    /**
     * @param string $slug
     * @param array $files
     * @return string
     *
     * @throws FileNotFoundException
     */
    public function template(string $slug, array $files = []): string
    {
        $files = $files ?: (array)"$slug.php";

        return apply_filters("{$slug}_template", $this->locate($files));
    }

    /**
     * @param array $files
     * @param bool $load
     * @param bool $once
     *
     * @return string
     * @throws FileNotFoundException
     */
    private function locate(array $files, bool $load = false, bool $once = true): string
    {
        $located = '';
        foreach ($files as $file) {
            if ($file && $this->exists($file)) {
                $located = $this->path($file);
                break;
            } else {
                throw new FileNotFoundException("Template $file not found!");
            }
        }
        if ($load && $located) {
            $this->load($located, $once);
        }

        return $located;
    }

    /**
     * @param string $file
     * @param bool $once
     *
     * @return void
     */
    private function load(string $file, bool $once = true): void
    {
        // Доступ внутри шаблонов к глобальным переменным
        global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;

        if (is_array($wp_query->query_vars)) {
            extract($wp_query->query_vars, EXTR_SKIP);
        }
        if ($s) {
            $s = esc_attr($s);
        }
        if ($once) {
            require_once($file);
        } else {
            require($file);
        }
    }

    /**
     * @param string $file_name
     *
     * @return string
     */
    private function path(string $file_name): string
    {
        $file_name = ltrim($file_name, '/');

        return "$this->dir/$file_name";
    }

    /**
     * @param $chunk
     * @param array $props
     *
     * @return string
     */
    public function renderChunk($chunk, array $props = []): string
    {
        foreach ($props as $key => $value) {
            $props['{' . strtoupper($key) . '}'] = $value;
        }

        return strtr($chunk, $props);
    }

    /**
     * Проверяет наличие шаблона
     *
     * @param $file_name
     *
     * @return bool
     */
    public function exists($file_name): bool
    {
        return file_exists($this->path($file_name));
    }
}