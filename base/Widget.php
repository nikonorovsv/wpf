<?php

namespace wpf\base;

use wpf\wp\Filters;

/**
 * Class Widget
 * @package wpf\base
 */
abstract class Widget
  extends ViewContext
{

  /**
   * @param string $tag
   * @param int $priority
   *
   * @return true
   */
  public function printOnTag(string $tag, int $priority = 10)
  {
    $html = $this->render();
    $function_to_add = function () use ($html) {
      echo $html;
    };

    return Filters::add($tag, $function_to_add, $priority, 0);
  }
}