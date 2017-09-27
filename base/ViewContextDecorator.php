<?php
namespace wpf\base;

/**
 * Class ViewContextDecorator
 * @package wpf\base
 */
abstract class ViewContextDecorator
{
	/**
	 * @var IViewContext
	 */
	protected $wrapped;
	
	/**
	 * ViewContextDecorator constructor.
	 *
	 * @param IViewContext $renderer
	 */
	public function __construct( IViewContext $renderer ) {
		$this->wrapped = $renderer;
	}
}