<?php

namespace wpf\wp;

use InvalidArgumentException;

/**
 * Class Filters
 * @package wpf\wp
 */
class Filters
{
    /**
     * @var null|Filters
     */
    private static ?Filters $_instance;

    /**
     * Filters constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return Filters
     */
    public static function instance(): Filters
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @param string $name
     * @param array $args
     */
    public static function __callStatic(string $name, array $args)
    {
        if (!function_exists("{$name}_filter") || !function_exists("{$name}_action")) {
            $class = self::class;
            throw new InvalidArgumentException(
                __("Method '$class'::'$name' not found.", 'wpf'));
        }
        call_user_func("{$name}_filter", ...$args);
    }

    /**
     * @param string $tag
     * @param int|false $priority
     *
     * @return true
     */
    public static function removeAll(string $tag, int|bool $priority = false): true
    {
        return remove_all_filters($tag, $priority);
    }

    /**
     * @param string $tag
     * @param mixed $value
     * @return mixed
     */
    public function apply(string $tag, mixed $value): mixed
    {
        return apply_filters($tag, $value);
    }

    /**
     * @param string $tag
     * @param array $args
     * @return mixed
     */
    public function applyRefArray(string $tag, array $args): mixed
    {
        return apply_filters_ref_array($tag, $args);
    }
}