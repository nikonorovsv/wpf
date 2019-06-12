<?php

namespace wpf\wp;

use \wpf\base\Entity;
use \wpf\helpers\ArrayHelper;
use \InvalidArgumentException;

/**
 * Class PostType
 * @package wpf\wp
 */
abstract class PostType extends Entity
{
  protected $args = [];

  /**
   * @return void
   */
  public function register(): void
  {
    if (!$this->args || !ArrayHelper::isAssociative($this->args)) {
      throw new InvalidArgumentException(
        __('The "args" property of Entity class inheritors needs to be associative array.', 'wpf'));
    }
    register_post_type(static::NAME, $this->args);
  }
}