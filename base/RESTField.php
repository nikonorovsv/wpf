<?php

namespace wpf\base;

use WP_REST_Request;

/**
 * Class RESTField
 * @package wpf\base
 */
abstract class RESTField
{
    /**
     * post, term, meta, user, comment, etc.
     */
    const OBJECT_TYPE = NULL;

    /**
     * The name of field
     */
    const NAME = NULL;

    /**
     * @param object $object
     * @param string $field_name
     * @param WP_REST_Request $request
     * @return mixed
     */
    abstract public static function get(object $object, string $field_name, WP_REST_Request $request): mixed;

    /**
     * @param $value
     * @param object $object
     * @param string $field_name
     * @return void
     */
    public static function update($value, object $object, string $field_name): void
    {
        return;
    }

    /**
     * @return array
     */
    public static function schema(): array
    {
        return [];
    }

    /**
     * Get class name
     *
     * @return string
     */
    public static function getName(): string
    {
        return static::class;
    }
}
