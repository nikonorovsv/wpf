<?php
namespace wpf\app\observers;

use \wpf\app\Observer;
use \wpf\App;
use \wpf\base\ConfigException;

/**
 * Class ThemeSupportsDefiner
 * @package wpf\app\observers
 */
class ThemeSupportsDefiner
	extends Observer {
	/**
	 * @param App $app
	 */
	public function doUpdate( App $app ) {
		$update = function () use ( $app ) {
			if ( ! $app->supports ) {
				return FALSE;
			} elseif ( ! is_array( $app->supports ) ) {
				throw new ConfigException( __( "'Supports' property in '*.config.json' must be an array." ) );
			}
			foreach ( $app->supports as $args ) {
				if ( ! is_array( $args ) ) {
					throw new ConfigException( __( "All items of 'supports' property in '*.config.json' must be arrays too." ) );
				}
				add_theme_support( ...$args );
			}
		};
		add_action( 'after_setup_theme', $update );
	}
}