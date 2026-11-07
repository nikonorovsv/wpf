<?php

namespace wpf\base;

/**
 * Class View
 * @package wpf\base
 */
class View
    implements IView
{
    public string $dir;

    /**
     * View constructor.
     *
     * @param string $dir
     */
    public function __construct(string $dir = '')
    {
        $this->dir = $dir;
    }

    /**
     * @param $tpl
     * @param array $vars
     *
     * @return string
     */
    public function render($tpl, array $vars = []): string
    {
        $name = NULL;
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
    public static function begin(): void
    {
        ob_start();
        ob_implicit_flush(FALSE);
    }

    /**
     * Закрывает буфер и возвращает содержимое
     *
     * @return string|false
     */
    public static function end(): string|false
    {
        return ob_get_clean();
    }

    /**
     * @param string $slug
     * @param string|NULL $name
     * @param array $vars
     * @return void
     */
    public function part(string $slug, string $name = NULL, array $vars = []): void
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
     * @param array $files
     *
     * @return mixed
     */
    public function template(string $slug, array $files = []): mixed
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
     */
    private function locate(array $files, bool $load = FALSE, bool $once = TRUE): string
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
     * @param bool $once
     * @return void
     */
    private function load(string $file, bool $once = TRUE): void
    {
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
     * @param string $chunk
     * @param array $data
     *
     * @return string
     */
    public function renderChunk(string $chunk, array $data = []): string
    {
        foreach ($data as $key => $value) {
            $data['{' . strtoupper($key) . '}'] = $value;
        }

        return strtr($chunk, $data);
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