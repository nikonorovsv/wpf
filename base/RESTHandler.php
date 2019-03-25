<?php

namespace wpf\base;

use \WP_REST_Controller as Controller;
use \WP_REST_Request as Request;
use \WP_REST_Server as Server;
use \WP_Error as Error;

/**
 * Class RESTHandler
 * @package wpf\base
 */
abstract class RESTHandler extends Controller
{
    const OVERRIDE = false;
    const ROUTE = NULL;

    /**
     * read | create | edit | delete
     */
    const METHODS = NULL;

    /**
     * @param $request
     * @return mixed
     */
    abstract public static function response( Request $request );

    /**
     * @param string $key
     * @param string $message
     * @param int $status
     * @return Error
     */
    protected static function error( string $key, string $message, int $status = 404 ): Error {
        return new Error( $key, __( $message ), compact('status') );
    }

    /**
     * Get the query params for collections
     *
     * @return array
     */
    public static function validateRules(): array {
        return [];
    }

    /**
     * @param $args
     * @return bool
     */
    public static function can(): bool {
        return true;
    }

    /**
     * Get class name
     *
     * @return string
     */
    public static function getName() {
        return static::class;
    }

    /**
     * @param string $type
     * @return bool|mixed
     */
    public static function methods() {
        $available = [
            'all'    => Server::ALLMETHODS,
            'read'   => Server::READABLE,
            'create' => Server::CREATABLE,
            'edit'   => Server::EDITABLE,
            'delete' => Server::DELETABLE
        ];
        $type = array_key_exists( self::METHODS, $available ) ? self::METHODS : 'read';

        return $available[ $type ];
    }
}
