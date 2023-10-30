<?php

namespace wpf\app\observers;

use wpf\app\Observer;
use wpf\App;

/**
 * Class AllowCORS
 * @package \wpf\app\observers
 */
class AllowCORS
  extends Observer
{

  /**
   * @param App $app
   */
  public function doUpdate(App $app)
  {
    // Return if accessed directly.
    if (!defined('ABSPATH')) {
      return;
    }
    // Allow all CORS.
    $update = function () use ($app) {
      // Remove the default filter.
      remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
      // Add a Custom filter.
      add_filter('rest_pre_serve_request', function ($value) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
        header('Access-Control-Allow-Credentials: true');

        return $value;
      });
    };
    // Hook.
    add_action('rest_api_init', $update, 15);
  }
}
