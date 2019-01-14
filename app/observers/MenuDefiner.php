<?php
namespace wpf\app\observers;

use \wpf\app\Observer;
use \wpf\App;
use \wpf\base\ConfigException;

/**
 * Class MenuDefiner
 * @package wpf\app\observers
 */
class MenuDefiner
	extends Observer {

	/**
	 * @param App $app
	 *
	 * @throws ConfigException
	 */
	public function doUpdate( App $app ) {
		$update = function () use ( $app ) {
			if ( ! $app->nav_menus ) {
				return FALSE;
			}
			if ( ! is_array( $app->nav_menus ) ) {
				throw new ConfigException(
				    __("Value of 'nav_menus' parameter in '*.config.json' must be an object.", 'wpf') );
			}
			register_nav_menus( $app->nav_menus );
		};
		add_action('after_setup_theme', $update );
	}
}