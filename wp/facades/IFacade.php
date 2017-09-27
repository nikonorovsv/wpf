<?php
namespace wpf\wp\facades;

/**
 * Interface IFacade
 * @package wpf\wp\facades
 */
interface IFacade {
	/**
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function label( string $name );
	
	/**
	 * @return string|bool
	 */
	public function url();
	
	/**
	 * @return string|bool
	 */
	public function editUrl();
	
	/**
	 * @param string $name
	 * @param $value
	 *
	 * @return mixed
	 */
	public function update( string $name, $value );
}