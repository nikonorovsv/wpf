<?php

namespace wpf\base;

/**
 * Class View.
 *
 * @package wpf\base
 */
class View implements IView
{
    public $dir;

    /**
     * View constructor.
     *
     * @param string $dir
     */
    public function __construct($dir = '')
    {
        $this->dir = $dir;
    }

    /**
     * @param $tpl
     * @param array $vars
     *
     * @return string
     */
    public function render($tpl, array $vars = [])
    {
        $name = null;
        $slug = $tpl;
        if (is_array($tpl)) {
            $slug = array_shift($tpl);
            $name = array_shift($tpl);
        }
        self::begin();
        $this->part($slug, $name, $vars);

        return self::end();
    }

    /**
     * Открывает буфер
     */
    public static function begin()
    {
        ob_start();
        ob_implicit_flush(false);
    }

    /**
     * Закрывает буфер и возвращает содержимое.
     *
     * @return string
     */
    public static function end(): string
    {
        return ob_get_clean();
    }

    /**
     * @param string      $slug
     * @param string|null $name
     * @param array       $vars
     */
    public function part(string $slug, string $name = null, array $vars = [])
    {
        $templates = [];
        if ($name) {
            $templates[] = "$slug-$name.php";
        }
        $templates[] = "$slug.php";
        extract($vars, EXTR_OVERWRITE);
        require $this->locate($templates);
    }

    /**
     * @param string $slug
     * @param array  $files
     *
     * @return mixed
     */
    public function template(string $slug, array $files = [])
    {
        $files = $files ?: (array) "{$slug}.php";

        return apply_filters("{$slug}_template", $this->locate($files));
    }

    /**
     * @param $files
     * @param bool $load
     * @param bool $once
     *
     * @return string
     */
    private function locate(array $files, $load = false, $once = true)
    {
        $located = '';
        foreach ($files as $file) {
            if ($file && $this->exists($file)) {
                $located = $this->path($file);
                break;
            }
        }
        if ($load && $located) {
            $this->load($located, $once);
        }

        return $located;
    }

    /**
     * @param string $file
     * @param bool   $once
     */
    private function load(string $file, $once = true)
    {
        global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;
        if (is_array($wp_query->query_vars)) {
            extract($wp_query->query_vars, EXTR_SKIP);
        }
        if ($s) {
            $s = esc_attr($s);
        }
        if ($once) {
            require_once $file;
        } else {
            require $file;
        }
    }

    /**
     * @param string $file_name
     *
     * @return string
     */
    private function path(string $file_name)
    {
        $file_name = ltrim($file_name, '/');

        return "{$this->dir}/{$file_name}";
    }

    /**
     * @param $chunk
     * @param array $data
     *
     * @return string
     */
    public function renderChunk($chunk, $data = [])
    {
        foreach ($data as $key => $value) {
            $data['{' . strtoupper($key) . '}'] = $value;
        }

        return strtr($chunk, $data);
    }

    /**
     * Проверяет наличие шаблона.
     *
     * @param $file_name
     *
     * @return bool
     */
    public function exists($file_name)
    {
        return file_exists($this->path($file_name));
    }
}
