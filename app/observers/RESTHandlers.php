<?php
namespace wpf\app\observers;

use \wpf\App;
use \wpf\app\Observer;
use \wpf\base\RESTHandler;
use \wpf\base\ConfigException;
use \wpf\base\FileNotFoundException;
use \wpf\base\HandlerNotFoundException;
use \wpf\helpers\WP;
use \InvalidArgumentException;
use \ReflectionClass;

/**
 * Class RESTHandlers
 * @package wpf\app\observers
 */
class RESTHandlers
    extends Observer {
    
    /**
     * @param App $app
     * @return bool|mixed
     * @throws ConfigException
     * @throws FileNotFoundException
     * @throws \ReflectionException
     */
    public function doUpdate( App $app ) {
        if ( ! $app->rest_handlers ) {
            return FALSE;
        } elseif ( ! $app->rest_namespace ) {
            throw new InvalidArgumentException(
                __("The value of 'rest_namespace' parameter must be set.") );
        } elseif ( ! is_array( $app->rest_handlers ) ) {
            throw new InvalidArgumentException(
                __("The value of 'rest_handlers' parameter must be array.") );
        } else if ( ! $app->rest_handlers_dir ) {
            throw new InvalidArgumentException(
                __("Parameter 'rest_handlers_dir' must have been defined in '/wpf/wpf.config.json' file.") );
        }  elseif ( ! is_dir( WP::path( $app->rest_handlers_dir ) ) ) {
            throw new FileNotFoundException(
                __("Parameter 'rest_handlers_dir' in '/wpf/wpf.config.json' file must be correct path to folder.") );
        }
        foreach ( $app->rest_handlers as $handler ) {
            $class   = str_replace('/', '\\', "{$app->rest_handlers_dir}/{$handler}");
            $reflect = new ReflectionClass( $class );
            if ( ! $reflect->isSubclassOf( RESTHandler::getName() ) ) {
                throw new ConfigException( __("Class '{$reflect->getName()}' must be inherited of RESTHandler class.") );
            }
            add_action('rest_api_init', function () use ( $class, $app ) {
                register_rest_route( $app->rest_namespace, $class::ROUTE, [
                    'methods'  => $class::methods(),
                    'callback' => [ $class, 'response'],
                    'args'     => $class::validateRules(),
                    'permission_callback' => [ $class, 'can']
                ], $class::OVERRIDE );
            });
        }
    }
}
