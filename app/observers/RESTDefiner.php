<?php
namespace wpf\app\observers;

use \wpf\App;
use \wpf\app\Observer;
use \wpf\base\FileNotFoundException;
use \wpf\helpers\WP;
use \InvalidArgumentException;
use \WP_REST_Server as Server;

/**
 * Class RESTDefiner
 * @package wpf\app\observers
 */
class RESTDefiner
    extends Observer {

    const BASE_HANDLER = '\wpf\base\RESTHandler';
    const BASE_FIELD = '\wpf\base\RESTField';

    private $_dir;

    /**
     * @param App $app
     * @throws FileNotFoundException
     */
    public function doUpdate( App $app ): void {
        if ( ! $app->rest_dir ) {
            throw new InvalidArgumentException(
                __("Parameter 'rest_dir' must have been defined in '/wpf/wpf.config.json' file.", 'wpf') );
        }
        $this->_dir = $app->rest_dir;

        if ( $app->rest_handlers ) {
            if ( ! is_array( $app->rest_handlers ) ) {
                throw new InvalidArgumentException(
                    __("The value of 'rest_handlers' parameter must be array.", 'wpf') );
            } elseif ( ! $app->rest_namespace ) {
                throw new InvalidArgumentException(
                    __("The value of 'rest_namespace' parameter must be set.", 'wpf') );
            } elseif ( ! is_dir( WP::path("{$this->_dir}/handlers") ) ) {
                throw new FileNotFoundException(
                    __("'rest_dir/handlers' in '/wpf/wpf.config.json' file must be correct path to folder.", 'wpf') );
            }

            $this->registerHandlers( $app );
        }

        if ( $app->rest_fields ) {
            if ( ! is_array( $app->rest_fields ) ) {
                throw new InvalidArgumentException(
                    __("The value of 'rest_fields' parameter must be array.", 'wpf') );
            } if ( ! is_dir( WP::path("{$this->_dir}/fields") ) ) {
                throw new FileNotFoundException(
                    __("'rest_dir/fields' in '/wpf/wpf.config.json' file must be correct path to folder.", 'wpf') );
            }

            $this->registerFields( $app );
        }
    }

    /**
     * @param App $app
     */
    private function registerHandlers( App $app ) {
        foreach ( $app->rest_handlers as $handler ) {
            $class = $this->getClassName("{$this->_dir}/handlers", $handler, [
                'subclass_of' => self::BASE_HANDLER
            ]);
            $register = function () use ( $class, $app ) {
                $h = new $class;
                register_rest_route( $app->rest_namespace, $h::ROUTE, [
                    'methods'  => $this->getValidMethods( $h::METHODS ),
                    'callback' => [ $h, 'handle'],
                    'args'     => [ $h, 'validateRules'],
                    'permission_callback' => [ $h, 'can']
                ], $h::OVERRIDE );
            };
            add_action('rest_api_init', $register );
        }
    }

    /**
     * @param App $app
     */
    private function registerFields( App $app ) {
        foreach ( $app->rest_fields as $field ) {
            $class = $this->getClassName("{$this->_dir}/fields", $field, [
                'subclass_of' => self::BASE_FIELD
            ]);
            $register = function () use ( $class ) {
                $f = new $class;
                register_rest_field( $f::OBJECT_TYPE, $f::NAME, [
                    'get_callback'    => [ $f, 'get'],
                    'update_callback' => [ $f, 'update'],
                    'schema'          => [ $f, 'schema']
                ] );
            };
            add_action('rest_api_init', $register );
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
