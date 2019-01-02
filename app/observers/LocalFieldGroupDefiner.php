<?php
namespace wpf\app\observers;

use \wpf\App;
use \wpf\app\Observer;
use \wpf\base\ConfigException;

/**
 * Class LocalFieldGroupDefiner
 * @package wpf\app\observers
 */
class LocalFieldGroupDefiner
	extends Observer {

    /**
     * @param App $app
     * @return bool|mixed
     * @throws ConfigException
     */
	public function doUpdate( App $app ) {
		if ( ! $app->local_field_groups ) {
			return FALSE;
		} elseif ( ! function_exists('acf_add_local_field_group') ) {
			return FALSE;
		}
		foreach ( $app->local_field_groups as $args ) {
			if ( ! is_array( $args ) ) {
				throw new ConfigException( __("All elements of 'local_field_groups' array in '*.config.json' must be objects.") );
			}
			acf_add_local_field_group( $args );
		}
	}
}