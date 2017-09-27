<?php
namespace wpf\app\observers;

use http\Env\Request;
use \wpf\app\Observer;
use \wpf\App;
use \wpf\base\AjaxHandler;
use \wpf\base\ConfigException;
use \wpf\base\FileNotFoundException;
use \InvalidArgumentException;
use \wpf\helpers\WP;
use \ReflectionClass;

/**
 * Class AjaxHandlers
 * @package wpf\app\observers
 */
class AjaxHandlers
	extends Observer {
	/**
	 * @param App $app
	 *
	 * @return bool
	 */
	public function doUpdate( App $app ) {
		if ( ! wp_doing_ajax() ) {
			return FALSE;
		} elseif ( ! $app->ajax_handlers ) {
			return FALSE;
		} elseif ( ! is_array( $app->ajax_handlers ) ) {
			throw new InvalidArgumentException( __( "The value of 'ajax_handlers' parameter must be array." ) );
		} elseif ( ! $app->ajax_handlers_dir ) {
			throw new InvalidArgumentException( __( "Parameter 'ajax_handlers_dir' must have been defined in '/wpf/wpf.config.json' file." ) );
		} elseif ( ! is_dir( WP::path( $app->ajax_handlers_dir ) ) ) {
			throw new FileNotFoundException( __( "Parameter 'ajax_handlers_dir' in '/wpf/wpf.config.json' file must be correct path to folder." ) );
		}
		foreach ( $app->ajax_handlers as $handler ) {
			$class   = str_replace( '/', '\\', "{$app->ajax_handlers_dir}/{$handler}" );
			$reflect = new ReflectionClass( $class );
			if ( ! $reflect->isSubclassOf( AjaxHandler::getName() ) ) {
				throw new ConfigException( __( "Class '{$reflect->getName()}' must be inherited of AjaxHandler class." ) );
			}
			$action = $class::ACTION_NAME;
			add_action( "wp_ajax_{$action}", [ $class, 'run' ] );
			add_action( "wp_ajax_nopriv_{$action}", [ $class, 'run' ] );
		}
	}
}