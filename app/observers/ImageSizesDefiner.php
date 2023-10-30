<?php

namespace wpf\app\observers;

use wpf\app\Observer;
use wpf\App;
use wpf\base\ConfigException;

/**
 * Class ImageSizesDefiner
 * @package wpf\app\observers
 */
class ImageSizesDefiner
  extends Observer
{

  /**
   * @param App $app
   *
   * @throws ConfigException
   */
  public function doUpdate(App $app)
  {
    $update = function () use ($app) {
      if (!$app->image_sizes) {
        return FALSE;
      }
      foreach ($app->image_sizes as $name => $args) {
        if (!is_array($args)) {
          throw new ConfigException(
            __("All elements of 'image_sizes' property in '*.config.json' must be arrays.", 'wpf'));
        } elseif ($name == 'default') {
          set_post_thumbnail_size(...$args);
          continue;
        }
        add_image_size($name, ...$args);
      }
    };
    add_action('after_setup_theme', $update);
  }
}