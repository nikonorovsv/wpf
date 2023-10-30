<?php

namespace wpf\app\observers;

use wpf\App;
use InvalidArgumentException;

/**
 * Class EntityUrlsWhiteList
 *
 * @package wpf\app\observers
 */
class EntityUrlsWhiteList
  extends EntityUrls
{

  /**
   * @param App $app
   */
  public function doUpdate(App $app)
  {
    if (!$app->entity_urls) {
      return;
    } elseif (!is_array($app->entity_urls)) {
      throw new InvalidArgumentException(
        __("Parameter 'entity_urls' should be an array.", 'wpf'));
    }

    $update = function () use ($app) {
      foreach ($app->entity_urls as $pattern) {
        $pattern = explode(':', $pattern);
        $entity = $pattern[0];
        $param = $pattern[1] ?? null;
        if ($this->hasEntity($entity)) {
          if (!$this->isEntity($entity, $param)) {
            $this->set404();
            // Signaling manual handling of page status
            return TRUE;
          }
        }
      }

      // Signaling manual handling of page status
      return FALSE;
    };

    add_action('pre_handle_404', $update);
  }
}
