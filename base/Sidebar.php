<?php
namespace wpf\base;

/**
 * Class Sidebar
 * @package wpf\base
 */
class Sidebar
	extends CompositeWidget {
	/**
	 * @return string
	 */
	public function render(): string {
		$out = parent::render();
		// Add WP dynamic sidebar widgets support
		if ( $this->dynamic_sidebar ) {
			View::begin();
			dynamic_sidebar( $this->dynamic_sidebar );
			$out .= View::end();
		}
		
		return $out;
	}
}