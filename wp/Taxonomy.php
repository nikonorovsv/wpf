<?php

namespace wpf\wp;

use wpf\base\Entity;
use wpf\helpers\ArrayHelper;
use InvalidArgumentException;

/**
 * Class Taxonomy.
 *
 * @package wpf\wp
 */
abstract class Taxonomy extends Entity
{
    protected $args = [];
    protected $deps = [];

    public function register(): void
    {
        if (!($this->args && ArrayHelper::isAssociative($this->args))) {
            throw new InvalidArgumentException(
        __('The "args" property of Entity class inheritors needs to be associative array.', 'wpf'));
        } elseif (!$this->deps) {
            throw new InvalidArgumentException(
        __('The "deps" property of Taxonomy class inheritors needs to be set.', 'wpf'));
        }
        register_taxonomy(static::NAME, $this->deps, $this->args);
    }
}
