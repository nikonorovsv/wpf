<?php

namespace wpf\base;

use WP_REST_Request as Request;

/**
 * Class RESTField.
 *
 * @package wpf\base
 */
abstract class RESTField
{
    /**
     * post, term, meta, user, comment, etc.
     */
    const OBJECT_TYPE = null;

    /**
     * The name of field.
     */
    const NAME = null;

    /**
     * @param object  $object
     * @param string  $field_name
     * @param Request $request
     *
     * @return mixed
     */
    abstract public static function get(object $object, string $field_name, Request $request);

    /**
     * @param $value
     * @param object $object
     * @param string $field_name
     */
    public static function update($value, object $object, string $field_name)
    {
        return null;
    }

    /**
     * @return array
     */
    public static function schema(): array
    {
        return null;
    }

    /**
     * Get class name.
     *
     * @return string
     */
    public static function getName()
    {
        return static::class;
    }
}
