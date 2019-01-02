<?php
namespace wpf\app\observers;

use \wpf\app\Observer;
use \wpf\App;
use \wpf\base\ConfigException;

/**
 * Class SidebarsDefiner
 * @package wpf\app\observers
 */
class SidebarsDefiner
	extends Observer {

    /**
     * @param App $app
     * @return mixed|void
     */
	public function doUpdate( App $app ) {
		$update = function () use ( $app ) {
			if ( ! $app->sidebars ) {
				return FALSE;
			}
			foreach ( $app->sidebars as $args ) {
				if ( ! is_array( $args ) ) {
					throw new ConfigException( __("All elements of 'sidebars' array in '*.config.json' must be objects.") );
				}
				register_sidebar( $args );
			}
		};
		add_action('after_setup_theme', $update );
	}
}