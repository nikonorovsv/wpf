<?php

namespace wpf\base;

use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * Class RESTHandler
 * @package wpf\base
 */
abstract class RESTHandler extends WP_REST_Controller
{
    const OVERRIDE = false;
    const ROUTE = NULL;

    /**
     * read | create | edit | delete
     */
    const METHODS = NULL;

    /**
     * @param WP_REST_Request $request
     * @return mixed
     */
    abstract public static function handle(WP_REST_Request $request);

    /**
     * @param $data
     * @return WP_REST_Response
     */
    protected static function response($data): WP_REST_Response
    {
        return rest_ensure_response($data);
    }

    /**
     * @param string $key
     * @param string $message
     * @param int $status
     * @return WP_Error
     */
    protected static function error(string $key, string $message, int $status = 404): WP_Error
    {
        return new WP_Error($key, __($message), compact('status'));
    }

    /**
     * Get the query params for collections
     *
     * @return array
     */
    public static function validateRules(): array
    {
        return [];
    }

    /**
     * @param WP_REST_Request $request
     * @return bool
     */
    public static function can(WP_REST_Request $request): bool
    {
        return true;
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
