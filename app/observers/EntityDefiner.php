<?php
namespace wpf\app\observers;

use \ReflectionClass;
use \InvalidArgumentException;
use \wpf\App;
use \wpf\app\Observer;
use \wpf\base\ConfigException;
use \wpf\base\FileNotFoundException;
use \wpf\helpers\WP;

/**
 * Class EntityDefiner
 * @package wpf\app\observers
 */
class EntityDefiner
	extends Observer {
	/**
         * @param App $app
         * @return bool
         * @throws \ReflectionException
         */
	public function doUpdate( App $app ) {
		if ( ! $app->entities ) {
			return FALSE;
		} elseif ( ! $app->entities_dir ) {
			throw new InvalidArgumentException( __( "Parameter 'entities_dir' must have been defined in '/wpf/wpf.config.json' file." ) );
		} elseif ( ! is_dir( WP::path( $app->entities_dir ) ) ) {
			throw new FileNotFoundException( __( "Parameter 'entities_dir' in '/wpf/wpf.config.json' file must be correct path to folder." ) );
		}
		
		$update = function () use ( $app ) {
			foreach ( $app->entities as $entity ) {
				$class   = str_replace( '/', '\\', "{$app->entities_dir}/{$entity}" );
				$reflect = new ReflectionClass( $class );
				if ( ! $reflect->implementsInterface( '\wpf\base\IEntity' ) ) {
					throw new ConfigException( __( "Class '{$reflect->getName()}' must implement IEntity interface." ) );
				}
				$entity = new $class();
				$entity->register();
			}
		};
		
		add_action('init', $update );
	}
}
