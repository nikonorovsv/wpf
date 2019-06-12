<?php

namespace wpf\wp;

use \wpf\base\Status;
use \wpf\helpers\ArrayHelper;
use \InvalidArgumentException;

/**
 * Class PostStatus
 * @package wpf\wp
 */
abstract class PostStatus extends Status
{
  protected $args = [];

  /**
   * @return void
   */
  public function register(): void
  {
    if (!$this->args || !ArrayHelper::isAssociative($this->args)) {
      throw new InvalidArgumentException(
        __('The "args" property of Status class inheritors needs to be associative array.', 'wpf'));
    }

    register_post_status(static::NAME, $this->args);
  }
}