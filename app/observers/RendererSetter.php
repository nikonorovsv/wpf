<?php

namespace wpf\app\observers;

use \InvalidArgumentException;
use \wpf\App;
use \wpf\app\Observer;
use \wpf\base\FileNotFoundException;
use \wpf\base\View;
use \wpf\helpers\WP;

/**
 * Class RendererSetter
 * @package wpf\app\observers
 */
class RendererSetter
  extends Observer
{

  /**
   * @param App $app
   *
   * @throws FileNotFoundException
   */
  public function doUpdate(App $app)
  {
    // Init App View Component
    if (!$app->views_dir) {
      throw new InvalidArgumentException(
        __("Parameter 'views_dir' must have been defined in '/wpf/config.json' file.", 'wpf'));
    }
    // By default
    $dir = WP::path($app->views_dir);
    if (!is_dir($dir)) {
      throw new FileNotFoundException(
        __("Parameter 'views_dir' in '/wpf/config.json' file must be correct path to folder.", 'wpf'));
    }
    $app->setRenderer(new View($dir));
  }
}