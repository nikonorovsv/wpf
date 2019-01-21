<?php
namespace wpf\base;

/**
 * Interface IEntity
 * @package wpf\base
 */
interface IEntity {

    /**
     * @return void
     */
	public function register(): void;

    /**
     * @return array
     */
	public static function getLabels(): array;
}