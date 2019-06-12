<?php

namespace wpf\app\observers;

use \wpf\app\Observer;
use \wpf\App;

/**
 * Class RemoveRedirects
 *
 * @package wpf\app\observers
 */
class RemoveRedirects
  extends Observer
{
  /**
   * @param App $app
   */
  public function doUpdate(App $app)
  {
    $update = function () use ($app) {
      // Redirect all requests to index.php so the Vue app is loaded and 404s aren't thrown
      add_rewrite_rule('^/(.+)/?', 'index.php', 'top');
    };
    add_action('init', $update);
  }
}
