<?php

namespace wpf\app\observers;

use \wpf\app\Observer;
use \wpf\App;

/**
 * Class AdminBlocker
 * @package \app\observers
 */
class AdminBlocker
  extends Observer
{

  /**
   * @param App $app
   */
  public function doUpdate(App $app)
  {
    $update = function () use ($app) {
      if ( is_admin() && !current_user_can('administrator') ) {
        // header('HTTP/1.0 404 Not Found');
        wp_redirect( home_url() );
        exit();
      }
    };

    add_action('init', $update);
  }
}
