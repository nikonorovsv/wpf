<?php

namespace wpf\app\observers;

use \wpf\app\Observer;
use \wpf\App;
use \InvalidArgumentException;

/**
 * Class EntityUrls
 *
 * @package wpf\app\observers
 */
class EntityUrls
  extends Observer
{

  protected $allowed_entities = [
    'is_archive',
    'is_category', 'is_tag', 'is_tax',
    'is_day', 'is_month', 'is_year', 'is_date', 'is_time',
    'is_post_type_archive',
    'is_author',
    'is_feed',
    'is_search',
    'is_comment_feed',
    'is_singular',
    'is_page', 'page_template', 'single', 'preview', 'parent', 'attachment',
    'is_embed',
    'is_paged',
    'is_front_page',
    'is_home',
  ];

  /**
   * @param App $app
   */
  public function doUpdate(App $app)
  {
  }

  /**
   *
   */
  protected function set404()
  {
    global $wp_query;

    $wp_query->set_404();
    status_header(404);
    nocache_headers();
  }

  /**
   * @param string|null $pattern
   * @return array|bool
   */
  protected function prepareParam(string $pattern = null)
  {
    if (isset($pattern)) {
      if ($n = explode(',', $pattern)) {

        return (count($n) === 1) ? $n[0] : $n;
      }
    }

    return FALSE;
  }

  /**
   * @param $entity
   * @param null $param
   * @return bool
   */
  protected function isEntity($entity, $param = null): bool
  {
    if ($param = $this->prepareParam($param)) {
      return call_user_func($entity, $param);
    }

    return call_user_func($entity);
  }

  /**
   * @param string $entity
   * @return bool
   */
  protected function hasEntity(string $entity)
  {
    if (in_array($entity, $this->allowed_entities)) {
      if (function_exists($entity)) {
        return TRUE;
      }
    }

    throw new InvalidArgumentException(
      __("Unknown entity '{$entity}'.", 'wpf'));
  }
}
