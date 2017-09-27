<?php
namespace wpf\app\observers;

use \wpf\app\Observer;
use \wpf\App;
use \wpf\base\ConfigException;

/**
 * Class ConstantsDefiner
 * @package wpf\app\observers
 */
class ConstantsDefiner
	extends Observer {
	/**
	 * @param App $app
	 *
	 * @return bool
	 * @throws ConfigException
	 */
	public function doUpdate( App $app ) {
		if ( ! $app->constants ) {
			return FALSE;
		} elseif ( ! is_array( $app->constants ) ) {
			throw new ConfigException( __( "Value of 'constants' property in '*.config.json' must be an object." ) );
		}
		foreach ( $app->constants as $name => $value ) {
			define( $name, $value );
		}
	}
}