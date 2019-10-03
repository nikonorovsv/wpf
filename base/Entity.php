<?php

namespace wpf\base;

/**
 * Class Entity.
 *
 * @package wpf\wp
 */
abstract class Entity implements IEntity
{
    abstract public function register(): void;

    abstract public static function getLabels(): array;
}
