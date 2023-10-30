<?php

namespace wpf\wp;

use wpf\helpers\Cache;
use wpf\helpers\WP;
use wpf\helpers\Html;

/**
 * Class Menu
 * @package wpf\wp
 */
class Menu
{
  use Cache;

  /**
   * @var string
   */
  private $_items, $_name;

  /**
   * WPMenu constructor.
   *
   * @param string $name
   */
  public function __construct(string $name)
  {
    $this->_name = $name;
    $this->_items = $this->wpData();
  }

  /**
   * @return array
   */
  public function items(): array
  {
    $fallback = function () {
      return $this->_items ? $this->walk($this->_items[0]) : [];
    };

    return static::cache($fallback, $this->_name);
  }

  /**
   * @return array
   */
  private function wpData(): array
  {
    global $_wp_registered_nav_menus;
    if (!$_wp_registered_nav_menus) {
      return [];
    }
    $locations = get_nav_menu_locations();
    if (!$locations || !isset($locations[$this->_name])) {
      return [];
    }
    $menu = wp_get_nav_menu_object($locations[$this->_name]);
    $items = (array)wp_get_nav_menu_items($menu, ['update_post_term_cache' => false]);
    unset($locations, $menu);
    if (!$items) {
      return [];
    }
    _wp_menu_item_classes_by_context($items);
    $prepared_items = [];
    foreach ($items as $item) {
      $prepared_items[$item->menu_item_parent][] = $item;
    }
    unset($items);

    return $prepared_items;
  }

  /**
   * @param array $items
   *
   * @return array
   */
  private function walk(array $items): array
  {
    $_items = [];
    foreach ($items as $item) {
      $item->classes = array_filter($item->classes);
      if ($item->current) {
        $item->classes[] = app()->menu['active_class'];
      }

      $_item = [];
      $_item['text'] = $item->title;
      $_item['url'] = $item->url;
      if (array_key_exists($item->ID, $this->_items)) {
        $item->classes[] = app()->menu['dropdown_class'];
        $_item['items'] = $this->walk($this->_items[$item->ID]);
        // unset($_item['url']);
      }

      $_item['options'] = [
        'id' => sprintf(app()->menu['item_id'], $item->ID),
        'class' => $item->classes,
        'title' => $item->attr_title,
        'target' => $item->target,
        'rel' => $item->xfn
      ];

      $_items[] = $_item;
    }

    return $_items;
  }
}
