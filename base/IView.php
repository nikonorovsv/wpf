<?php
namespace wpf\base;

/**
 * Interface IView
 * @package wpf\base
 */
interface IView {
	/**
	 * @param $tpl
	 * @param array $vars
	 *
	 * @return mixed
	 */
	public function render( $tpl, array $vars = [] );
}