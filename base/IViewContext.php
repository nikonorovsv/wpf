<?php
namespace wpf\base;

/**
 * Interface IViewContext
 * @package wpf\base
 */
interface IViewContext {
	/**
	 * @return string
	 */
	public function render(): string;
	
	/**
	 * @return string
	 */
	public function before(): string;
	
	/**
	 * @return string
	 */
	public function after(): string;
	
	/**
	 * @param string $template
	 *
	 * @return mixed
	 */
	public function setTemplate( string $template );
}