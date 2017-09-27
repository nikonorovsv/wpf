<?php
namespace wpf\base;

use \wpf\helpers\WP;

/**
 * Class Entity
 * @package wpf\wp
 */
abstract class Entity implements IEntity
{
	abstract public function register();
	abstract public static function getLabels();
}