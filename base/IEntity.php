<?php

namespace wpf\base;

/**
 * Interface IEntity.
 *
 * @package wpf\base
 */
interface IEntity
{
    public function register(): void;

    /**
     * @return array
     */
    public static function getLabels(): array;
}
