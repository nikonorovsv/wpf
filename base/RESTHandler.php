<?php

namespace wpf\base;

use WP_REST_Controller as Controller;
use WP_REST_Request as Request;
use WP_REST_Response as Response;
use WP_Error as Error;

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
  abstract public static function handle(Request $request);

  /**
   * @param $data
   * @return Response
   */
  protected static function response($data): Response
  {
    return rest_ensure_response($data);
  }

  /**
   * @param string $key
   * @param string $message
   * @param int $status
   * @return Error
   */
  protected static function error(string $key, string $message, int $status = 404): Error
  {
    return new Error($key, __($message), compact('status'));
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
   * @param Request $request
   * @return bool
   */
  public static function can(Request $request): bool
  {
    return true;
  }

  /**
   * Get class name
   *
   * @return string
   */
  public static function getName()
  {
    return static::class;
  }
}
