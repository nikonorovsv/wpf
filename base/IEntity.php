<?php
namespace wpf\base;

/**
 * Interface IEntity
 * @package wpf\base
 */
interface IEntity {
    
	/**
	 * @return mixed
	 */
	public function register();
	
	/**
	 * @return mixed
	 */
	public static function getLabels();
}