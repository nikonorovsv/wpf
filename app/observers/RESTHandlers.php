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
use \WP_REST_Server as Server;

/**
 * Class RESTHandlers
 * @package wpf\app\observers
 */
class RESTHandlers
    extends Observer {

    /**
     * @param App $app
     * @throws ConfigException
     * @throws FileNotFoundException
     * @throws \ReflectionException
     */
    public function doUpdate( App $app ): void {
        if ( ! $app->rest_handlers ) {
            return;
        } elseif ( ! $app->rest_namespace ) {
            throw new InvalidArgumentException(
                __("The value of 'rest_namespace' parameter must be set.", 'wpf') );
        } elseif ( ! is_array( $app->rest_handlers ) ) {
            throw new InvalidArgumentException(
                __("The value of 'rest_handlers' parameter must be array.", 'wpf') );
        } else if ( ! $app->rest_handlers_dir ) {
            throw new InvalidArgumentException(
                __("Parameter 'rest_handlers_dir' must have been defined in '/wpf/wpf.config.json' file.", 'wpf') );
        }  elseif ( ! is_dir( WP::path( $app->rest_handlers_dir ) ) ) {
            throw new FileNotFoundException(
                __("Parameter 'rest_handlers_dir' in '/wpf/wpf.config.json' file must be correct path to folder.", 'wpf') );
        }
        foreach ( $app->rest_handlers as $handler ) {
            $class   = str_replace('/', '\\', "{$app->rest_handlers_dir}/{$handler}");
            $reflect = new ReflectionClass( $class );
            if ( ! $reflect->isSubclassOf( RESTHandler::getName() ) ) {
                throw new ConfigException(
                    __("Class '{$reflect->getName()}' must be inherited of RESTHandler class.", 'wpf') );
            }
            add_action('rest_api_init', function () use ( $class, $app ) {
                register_rest_route( $app->rest_namespace, $class::ROUTE, [
                    'methods'  => $this->getValidMethods( $class::METHODS ),
                    'callback' => [ $class, 'response'],
                    'args'     => $class::validateRules(),
                    'permission_callback' => [ $class, 'can']
                ], $class::OVERRIDE );
            });
        }
    }

    /**
     * @param string|NULL $local
     * @return mixed
     */
    private function getValidMethods( string $local = NULL ) {
        $available = [
            'all'    => Server::ALLMETHODS,
            'read'   => Server::READABLE,
            'create' => Server::CREATABLE,
            'edit'   => Server::EDITABLE,
            'delete' => Server::DELETABLE
        ];
        $type = ( $local && array_key_exists( $local, $available ) ) ? $local : 'read';

        return $available[ $type ];
    }
}
